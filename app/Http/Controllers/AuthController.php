<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Entreprise;
use App\Models\RH;
use App\Models\Candidat;
use App\Mail\BienvenuRH;
use App\Mail\BienvenuCandidat;
use App\Mail\TwoFactorCodeMail;
use App\Services\TwoFactorAuthenticator;
use App\Services\CloudinaryUploadService;

class AuthController extends Controller
{
    /* =========================================
       PAGE CONNEXION
    ========================================= */
    public function showLogin()
    {
        return view('connexion');
    }

   public function login(Request $request)
   {
        // Verification classique : email + mot de passe.
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            $rh = RH::where('user_id', $user->id)->first();

            if ($rh && !in_array($user->role, ['rh_admin', 'assistant', 'stagiaire', 'rh_user'])) {
                $ownsEntreprise = Entreprise::where('user_id', $user->id)
                    ->where('id', $rh->entreprise_id)
                    ->exists();

                $user->update(['role' => ($rh->is_admin || $ownsEntreprise) ? 'rh_admin' : 'assistant']);
            }

            // Si la 2FA est active, on bloque le dashboard jusqu'au code.
            if ($user->two_factor_enabled_at && in_array($user->two_factor_method, ['email', 'app'], true)) {
                if ($user->two_factor_method === 'email') {
                    $this->sendTwoFactorCode($user);
                }
                Auth::logout();
                $request->session()->put('two_factor_user_id', $user->id);

                return redirect()->route('two-factor.challenge')
                    ->with('status', $user->two_factor_method === 'email'
                        ? 'Un code de verification vous a ete envoye par e-mail.'
                        : 'Saisissez le code de votre application d authentification.');
            }

            if ($this->needsDisplayName($user)) {
                return redirect()->route('display-name.edit');
            }

            // Fallback pour les anciens comptes RH qui ont un profil RH
            // mais un role utilisateur incorrect.
            if (in_array($user->role, ['rh_admin', 'assistant', 'stagiaire', 'rh_user']) || $rh) {
                return redirect()->route('dashboard.rh');
            }

            return redirect()->route('dashboard.candidat');
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas.',
        ])->onlyInput('email');
    }

    /* =========================================
       INSCRIPTION CANDIDAT
    ========================================= */
    public function showRegisterCandidat()
    {
        return view('inscription-candidat');
    }

    public function storeRegisterCandidat(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'telephone'      => 'required|string|max:20',
            'date_naissance' => 'required|date',
            'filiere'        => 'required|string|max:150',
            'niveau'         => 'required|string',
            'password'       => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'role'      => 'candidat',
            'password'  => Hash::make($request->password),
        ]);

        Candidat::create([
            'user_id'        => $user->id,
            'date_naissance' => $request->date_naissance,
            'telephone'      => $request->telephone,
            'filiere'        => $request->filiere,
            'niveau'         => $request->niveau,
        ]);

        Auth::login($user);

        Mail::to($user->email)->send(new BienvenuCandidat($user));

        return redirect()->route('dashboard.candidat')
            ->with('success', 'Bienvenue sur StageLink !');
    }

    /* =========================================
       PROFIL CANDIDAT
    ========================================= */
    public function showProfilCandidat()
    {
        if (!Auth::check()) {
            return redirect()->route('connexion');
        }

        return view('profil_candidat');
    }

    public function storeProfilCandidat(Request $request, CloudinaryUploadService $cloudinary)
    {
        $request->validate([
            'cv'    => 'required|file|mimes:pdf|max:5120',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Envoi vers Cloudinary au lieu du disque local (Render n'a pas de stockage persistant).
        $cvPath = $cloudinary->upload($request->file('cv'), 'cvs');

        $photoPath = $request->hasFile('photo')
            ? $cloudinary->upload($request->file('photo'), 'photos')
            : null;

        Candidat::create([
            'user_id'        => Auth::id(),
            'date_naissance' => Auth::user()->date_naissance,
            'telephone'      => Auth::user()->telephone,
            'filiere'        => Auth::user()->filiere,
            'niveau'         => Auth::user()->niveau,
            'adresse'        => $request->adresse,
            'photo'          => $photoPath,
            'cv'                          => $cvPath,
            'linkedin'       => $request->linkedin,
            'github'         => $request->github,
            'portfolio'      => $request->portfolio,
            'competences'    => $request->competences,
            'experiences'    => $request->experiences,
            'langues'        => $request->langues,
            'certifications' => $request->certifications,
        ]);

        return redirect()->route('dashboard.candidat')
            ->with('success', 'Bienvenue sur StageLink !');
    }

    /* =========================================
       INSCRIPTION RH (Entreprise + RH_ADMIN)
    ========================================= */
    public function showRegisterRH()
    {
        return view('inscription_rh');
    }

    public function storeRegisterRH(Request $request, CloudinaryUploadService $cloudinary)
    {
        $request->validate([
            'nom_entreprise'       => 'required|string|max:255',
            'display_name'          => 'required|string|max:100',
            'email_entreprise'     => 'required|email|unique:entreprises,email|unique:users,email',
            'telephone_entreprise' => 'required|string|max:20',
            'adresse'              => 'required|string|max:255',
            'site_web'             => 'nullable|url',
            'description'          => 'required|string|max:500',
            'logo'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password'             => 'required|string|min:8|confirmed',
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            // Envoi vers Cloudinary au lieu du disque local (Render n'a pas de stockage persistant).
            $logoPath = $cloudinary->upload($request->file('logo'), 'logos');
        }

        /*
        |--------------------------------------------------------------------------
        | 1. Création entreprise
        |--------------------------------------------------------------------------
        */
        $entreprise = Entreprise::create([
            'nom'         => $request->nom_entreprise,
            'email'       => $request->email_entreprise,
            'telephone'   => $request->telephone_entreprise,
            'adresse'     => $request->adresse,
            'site_web'    => $request->site_web,
            'description' => $request->description,
            'logo'        => $logoPath,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Création RH_ADMIN
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'nom'       => $request->nom_entreprise,
            'prenom'    => 'Admin',
            'display_name' => $request->display_name,
            'email'     => $request->email_entreprise,
            'telephone' => $request->telephone_entreprise,
            'role'      => 'rh_admin',
            'password'  => Hash::make($request->password),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Liaison entreprise -> RH_ADMIN
        |--------------------------------------------------------------------------
        */
        $entreprise->update([
            'user_id' => $user->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Création profil RH
        |--------------------------------------------------------------------------
        */
        RH::create([
            'user_id'       => $user->id,
            'entreprise_id' => $entreprise->id,
            'fonction'      => 'Administrateur RH',
            'is_admin'      => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Connexion automatique
        |--------------------------------------------------------------------------
        */
        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | 6. Mail de bienvenue
        |--------------------------------------------------------------------------
        */
        Mail::to($entreprise->email)->send(
            new BienvenuRH($user, $entreprise)
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Redirection dashboard RH
        |--------------------------------------------------------------------------
        */
        return redirect()->route('dashboard.rh')
            ->with('success', 'Entreprise créée avec succès !');
    }

    /* =========================================
       DECONNEXION
    ========================================= */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showTwoFactorChallenge()
    {
        // Cette page affiche soit le code email, soit le code application.
        abort_unless(session()->has('two_factor_user_id'), 403);

        $user = User::findOrFail(session('two_factor_user_id'));

        return view('auth.two-factor-challenge', [
            'method' => $user->two_factor_method,
        ]);
    }

    public function verifyTwoFactorCode(Request $request, TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $request->validate([
            'code' => 'required|digits_between:6,8',
        ]);

        $user = User::findOrFail($request->session()->get('two_factor_user_id'));

        // Methode application : verification du code TOTP.
        if ($user->two_factor_method === 'app') {
            if (!$twoFactorAuthenticator->verifyLogin($user, $request->code)) {
                return back()->withErrors(['code' => 'Code incorrect. Reessayer.']);
            }
        } else {
            // Methode email : verification du code envoye par mail.
            if (!$user->two_factor_code || !$user->two_factor_expires_at || now()->greaterThan($user->two_factor_expires_at)) {
                return back()->withErrors(['code' => 'Code expire. Renvoyez un code.']);
            }

            if (!Hash::check($request->code, $user->two_factor_code)) {
                return back()->withErrors(['code' => 'Code incorrect. Reessayer.']);
            }
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        $request->session()->forget('two_factor_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        if ($this->needsDisplayName($user)) {
            return redirect()->route('display-name.edit');
        }

        return $this->redirectAfterLogin($user);
    }

    public function resendTwoFactorCode(Request $request)
    {
        $user = User::findOrFail($request->session()->get('two_factor_user_id'));
        abort_unless($user->two_factor_method === 'email', 404);
        $this->sendTwoFactorCode($user);

        return back()->with('status', 'Un nouveau code de verification a ete envoye.');
    }

    public function editDisplayName()
    {
        return view('auth.display-name');
    }

    public function updateDisplayName(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
        ]);

        Auth::user()->update(['display_name' => $request->display_name]);

        return $this->redirectAfterLogin(Auth::user());
    }

    public function startAuthenticatorAppSetup(TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        // On genere une nouvelle cle secrete avant d'afficher le QR Code.
        $twoFactorAuthenticator->startSetup(Auth::user());

        return back()->with('success', 'Scannez le QR Code puis validez le code a 6 chiffres.');
    }

    public function confirmAuthenticatorAppSetup(Request $request, TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Le code saisi doit correspondre a celui de l'application.
        if (!$twoFactorAuthenticator->confirmSetup(Auth::user(), $request->code)) {
            return back()->withErrors(['code' => 'Code incorrect. Reessayez avec le code affiche dans votre application.']);
        }

        return back()->with('success', 'Authentification par application activee.');
    }

    public function disableTwoFactor(TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $twoFactorAuthenticator->disable(Auth::user());

        return back()->with('success', 'Double authentification desactivee.');
    }

    private function sendTwoFactorCode(User $user): void
    {
        // Code temporaire utilise uniquement pour la 2FA par email.
        $code = (string) random_int(100000, 99999999);

        $user->update([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));
    }

    private function needsDisplayName(User $user): bool
    {
        return in_array($user->role, ['rh_admin', 'assistant', 'stagiaire', 'rh_user'], true)
            && blank($user->display_name);
    }

    private function redirectAfterLogin(User $user)
    {
        $rh = RH::where('user_id', $user->id)->first();

        if (in_array($user->role, ['rh_admin', 'assistant', 'stagiaire', 'rh_user'], true) || $rh) {
            return redirect()->route('dashboard.rh');
        }

        return redirect()->route('dashboard.candidat');
    }
}