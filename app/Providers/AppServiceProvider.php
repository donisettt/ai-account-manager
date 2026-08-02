<?php

namespace App\Providers;

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
        // Force HTTPS URL generation when APP_ENV is not local or when behind proxy like ngrok
        if (config('app.env') !== 'local' || request()->server->get('HTTP_X_FORWARDED_PROTO') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
