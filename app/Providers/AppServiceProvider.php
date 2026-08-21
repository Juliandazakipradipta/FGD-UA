<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
            $this->app->useStoragePath('/tmp/storage');
            $this->app->useBootstrapPath('/tmp/bootstrap');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
