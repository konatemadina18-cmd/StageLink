<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
    }
}