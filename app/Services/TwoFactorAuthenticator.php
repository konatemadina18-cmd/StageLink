<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticator
{
    public function __construct(private readonly Google2FA $google2fa)
    {
    }

    public function startSetup(User $user): string
    {
        // Nouvelle cle temporaire affichee une seule fois avec le QR Code.
        $secret = $this->google2fa->generateSecretKey(32);

        $user->forceFill([
            'two_factor_pending_secret' => $secret,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return $secret;
    }

    public function confirmSetup(User $user, string $code): bool
    {
        // Si le code est bon, la cle temporaire devient la cle active.
        $secret = $user->two_factor_pending_secret;

        if (!$secret || !$this->verify($secret, $code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_pending_secret' => null,
            'two_factor_enabled_at' => now(),
            'two_factor_method' => 'app',
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return true;
    }

    public function verifyLogin(User $user, string $code): bool
    {
        // Verification du code a chaque connexion avec l'application.
        return (bool) $user->two_factor_secret && $this->verify($user->two_factor_secret, $code);
    }

    public function disable(User $user): void
    {
        // Desactivation complete : on efface les cles et les anciens codes.
        $user->forceFill([
            'two_factor_enabled_at' => null,
            'two_factor_method' => null,
            'two_factor_secret' => null,
            'two_factor_pending_secret' => null,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    public function setupData(User $user): ?array
    {
        // Donnees necessaires pour afficher le QR Code dans les parametres.
        if (!$user->two_factor_pending_secret) {
            return null;
        }

        $issuer = config('app.name', 'StageLink');
        $label = $user->email;
        $url = $this->google2fa->getQRCodeUrl($issuer, $label, $user->two_factor_pending_secret);

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd()
        );

        return [
            'secret' => $user->two_factor_pending_secret,
            'qr_svg' => (new Writer($renderer))->writeString($url),
        ];
    }

    private function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, preg_replace('/\s+/', '', $code), 1);
    }
}
