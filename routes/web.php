<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\RHController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OffreController;

/*
|--------------------------------------------------------------------------
| ACCUEIL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('accueil');
});

Route::get('/a-propos', function () {
    return view('apropos');
})->name('a.propos');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::get('/connexion', [AuthController::class, 'showLogin'])
    ->name('connexion');

Route::post('/connexion', [AuthController::class, 'login'])
    ->name('connexion.store');

Route::get('/connexion/verification', [AuthController::class, 'showTwoFactorChallenge'])
    ->name('two-factor.challenge');

Route::post('/connexion/verification', [AuthController::class, 'verifyTwoFactorCode'])
    ->name('two-factor.verify');

Route::post('/connexion/verification/renvoyer', [AuthController::class, 'resendTwoFactorCode'])
    ->name('two-factor.resend');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {
    // Routes communes aux utilisateurs connectes.
    Route::get('/nom-affichage', [AuthController::class, 'editDisplayName'])
        ->name('display-name.edit');
    Route::post('/nom-affichage', [AuthController::class, 'updateDisplayName'])
        ->name('display-name.update');
    // Routes pour activer/desactiver la double authentification par application.
    Route::post('/authenticator-app/setup', [AuthController::class, 'startAuthenticatorAppSetup'])
        ->name('two-factor.app.setup');
    Route::post('/authenticator-app/confirm', [AuthController::class, 'confirmAuthenticatorAppSetup'])
        ->name('two-factor.app.confirm');
    Route::delete('/two-factor', [AuthController::class, 'disableTwoFactor'])
        ->name('two-factor.disable');
});

/*
|--------------------------------------------------------------------------
| CHOIX PROFIL
|--------------------------------------------------------------------------
*/

Route::get('/choix-profil', function () {
    return view('choix_profil');
})->name('choix.profil');

/*
|--------------------------------------------------------------------------
| INSCRIPTION CANDIDAT
|--------------------------------------------------------------------------
*/

Route::get('/inscription/candidat', [AuthController::class, 'showRegisterCandidat'])
    ->name('inscription.candidat');

Route::post('/inscription/candidat', [AuthController::class, 'storeRegisterCandidat'])
    ->name('inscription.candidat.store');

/*
|--------------------------------------------------------------------------
| INSCRIPTION RH (Entreprise + RH_ADMIN)
|--------------------------------------------------------------------------
*/

Route::get('/inscription/rh', [AuthController::class, 'showRegisterRH'])
    ->name('inscription.rh');

Route::post('/inscription/rh', [AuthController::class, 'storeRegisterRH'])
    ->name('inscription.rh.store');

/*
|--------------------------------------------------------------------------
| DASHBOARD CANDIDAT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Espace candidat : dashboard, profil, documents, messages et candidatures.

    Route::get('/dashboard/candidat', [CandidatController::class, 'dashboard'])
        ->name('dashboard.candidat');

    Route::put('/candidat/profil', [CandidatController::class, 'updateProfil'])
        ->name('candidat.profil.update');

    Route::put('/candidat/password', [CandidatController::class, 'updatePassword'])
        ->name('candidat.password.update');

    Route::put('/candidat/preferences', [CandidatController::class, 'updatePreferences'])
        ->name('candidat.preferences.update');

    Route::post('/candidat/documents', [CandidatController::class, 'storeDocument'])
        ->name('candidat.documents.store');

    Route::patch('/candidat/documents/{document}/default', [CandidatController::class, 'setDefaultDocument'])
        ->name('candidat.documents.default');

    Route::delete('/candidat/documents/{document}', [CandidatController::class, 'destroyDocument'])
        ->name('candidat.documents.destroy');

    Route::post('/candidat/messages', [CandidatController::class, 'sendMessage'])
        ->name('candidat.messages.send');

    Route::post('/candidat/sections/read', [CandidatController::class, 'markSectionRead'])
        ->name('candidat.sections.read');

    Route::patch('/candidat/notifications/{notification}/toggle', [CandidatController::class, 'toggleNotification'])
        ->name('candidat.notifications.toggle');

    Route::get('/offres/disponibles', [OffreController::class, 'disponibles'])
        ->name('offres.disponibles');

    Route::get('/candidatures/postuler/{offre?}', [CandidatureController::class, 'create'])
        ->name('candidatures.create');

    Route::post('/candidatures', [CandidatureController::class, 'store'])
        ->name('candidatures.store');

    Route::get('/candidatures/mes', [CandidatureController::class, 'mesCandidatures'])
        ->name('candidatures.mes');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD RH
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Espace RH : gestion des offres, candidatures, entretiens et parametres.

    Route::get('/dashboard/rh', [RHController::class, 'dashboard'])
        ->name('dashboard.rh');

    Route::post('/offres', [OffreController::class, 'store'])
        ->name('offres.store');

    Route::patch('/offres/{offre}/toggle', [OffreController::class, 'toggleStatut'])
        ->name('offres.toggle');

    Route::delete('/offres/{offre}', [OffreController::class, 'destroy'])
        ->name('offres.destroy');

    Route::patch('/candidatures/{candidature}/statut', [CandidatureController::class, 'updateStatut'])
        ->name('candidatures.statut');

    Route::post('/candidatures/{candidature}/entretien', [CandidatureController::class, 'planifierEntretien'])
        ->name('candidatures.entretien'); 

    Route::post('/rh/users', [RHController::class, 'storeRHUser'])->name('rh.users.store')->middleware('auth');

    Route::patch('/rh/users/{user}/role', [RHController::class, 'updateUserRole'])
        ->name('rh.users.role');

    Route::post('/rh/messages', [RHController::class, 'sendMessage'])
        ->name('rh.messages.send');

    Route::post('/rh/sections/read', [RHController::class, 'markSectionRead'])
        ->name('rh.sections.read');

    Route::put('/rh/profil', [RHController::class, 'updateProfil'])
        ->name('rh.profil.update');

    Route::put('/rh/password', [RHController::class, 'updatePassword'])
        ->name('rh.password.update');

    Route::put('/rh/preferences', [RHController::class, 'updatePreferences'])
        ->name('rh.preferences.update');

    Route::put('/rh/entreprise', [RHController::class, 'updateEntreprise'])
        ->name('rh.entreprise.update');

    Route::patch('/rh/notifications/{notification}/toggle', [RHController::class, 'toggleNotification'])
        ->name('rh.notifications.toggle');
        
});
