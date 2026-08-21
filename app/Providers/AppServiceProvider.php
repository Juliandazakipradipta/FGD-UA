<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
            $this->app->useStoragePath('/tmp/storage');
            $this->app->useBootstrapPath('/tmp/bootstrap');

            // Override hashing to SHA-256 (Vercel PHP has no bcrypt/libssl support)
            config(['hashing.driver' => 'sha256']);
            $this->app->extend('hash', function ($hash, $app) {
                $hash->extend('sha256', function () {
                    return new \App\Hashing\Sha256Hasher();
                });
                return $hash;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
            URL::forceScheme('https');
        }
    }
}
