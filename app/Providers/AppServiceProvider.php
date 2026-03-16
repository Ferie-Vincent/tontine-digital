<?php

namespace App\Providers;

use App\Models\Tontine;
use App\Models\Tour;
use App\Policies\TontinePolicy;
use App\Policies\TourPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Gate::policy(Tontine::class, TontinePolicy::class);
        Gate::policy(Tour::class, TourPolicy::class);

        // Rate limiter pour les endpoints d'authentification (login, register, reset password)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiter pour les envois SMS (codes de vérification)
        RateLimiter::for('sms', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip() . '|sms');
        });

        // Rate limiter pour les actions sensibles (swaps, contributions, gestion membres)
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
