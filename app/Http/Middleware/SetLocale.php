<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // On prend la langue choisie par l'utilisateur, sinon la langue de session.
        $locale = Auth::check()
            ? data_get(Auth::user()->settings, 'language', session('locale', 'fr'))
            : session('locale', 'fr');

        if (!in_array($locale, ['fr', 'en'], true)) {
            $locale = 'fr';
        }

        // Laravel et Carbon utilisent la meme langue pour les textes et les dates.
        App::setLocale($locale);
        Carbon::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
