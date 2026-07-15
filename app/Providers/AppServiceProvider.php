<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force Laravel à générer tous ses liens (CSS, JS, images, routes...)
        // en HTTPS quand l'application tourne en production (Render, etc.).
        // Nécessaire car Render termine le HTTPS avant que la requête
        // n'atteigne le conteneur, donc Laravel croit être en HTTP simple.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Enregistre le transport Brevo pour l'envoi d'emails via API HTTP
        // (nécessaire car Render bloque les ports SMTP classiques 25/465/587
        // sur son plan gratuit).
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory)->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });
    }
}