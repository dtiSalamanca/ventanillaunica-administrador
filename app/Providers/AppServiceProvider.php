<?php

namespace App\Providers;

use App\Auth\AdUserProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('active_directory', fn ($app, $config) => new AdUserProvider);

        // Límite de peticiones para el sistema externo de pagos: 20 por minuto.
        RateLimiter::for('ordenes-pago', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
