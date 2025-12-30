<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth; // Harus di sini, di luar class!

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
        // Memaksa Basic Auth untuk mengecek kolom 'username' dan bukan 'email'
        Auth::viaRequest('basic', function (Request $request) {
            return Auth::guard('web')->basic('username');
        });
    }
}