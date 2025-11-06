<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
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
        //
        // // Cek jika aplikasi berjalan di lingkungan produksi atau diakses via Ngrok
        // if (str_contains(config('app.url'), 'ngrok-free.dev') || $this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }
    }

    // TAMBAHKAN METHOD BARU INI
    public static function home()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return '/admin/dashboard';
        }
        return '/dashboard';
    }
}
