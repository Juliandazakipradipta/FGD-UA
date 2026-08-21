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
            
            // Set hashing driver config to sha256
            config(['hashing.driver' => 'sha256']);
            
            // Safely extend the resolved hash manager instance with our custom driver
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
        // Force HTTPS on Vercel so form actions and redirects always use https://
        if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
            URL::forceScheme('https');
        }

        // High-concurrency optimization for SQLite (WAL Mode & Busy Timeout)
        if (config('database.default') === 'sqlite' && !getenv('VERCEL') && !isset($_ENV['VERCEL'])) {
            try {
                DB::statement('PRAGMA journal_mode=WAL;');
                DB::statement('PRAGMA busy_timeout=5000;');
                DB::statement('PRAGMA synchronous=NORMAL;');
            } catch (\Throwable $e) {
                // Silently ignore if DB not initialized yet
            }
        }
    }
}
