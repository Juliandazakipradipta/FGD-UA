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
            
            // Bind the hash manager singleton to return the sha256 hasher as default
            $this->app->singleton('hash', function ($app) {
                $manager = new \Illuminate\Hashing\HashManager($app);
                $manager->extend('sha256', function () {
                    return new \App\Hashing\Sha256Hasher();
                });
                return $manager;
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
