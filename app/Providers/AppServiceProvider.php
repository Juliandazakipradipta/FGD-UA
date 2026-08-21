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
        // Auto-create database tables on PostgreSQL/Supabase if not existing
        if (getenv('VERCEL') || isset($_ENV['VERCEL'])) {
            try {
                URL::forceScheme('https');

                $pdo = DB::connection()->getPdo();
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS groups (
                        id BIGSERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL UNIQUE,
                        description TEXT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    );

                    CREATE TABLE IF NOT EXISTS admins (
                        id BIGSERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        scope VARCHAR(255) DEFAULT 'all',
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    );

                    CREATE TABLE IF NOT EXISTS sessions (
                        id VARCHAR(255) NOT NULL PRIMARY KEY,
                        user_id BIGINT NULL,
                        ip_address VARCHAR(45) NULL,
                        user_agent TEXT NULL,
                        payload TEXT NOT NULL,
                        last_activity INTEGER NOT NULL
                    );

                    CREATE TABLE IF NOT EXISTS minutes (
                        id BIGSERIAL PRIMARY KEY,
                        group_id BIGINT NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
                        scope VARCHAR(255) DEFAULT 'ulul_albab',
                        session_topic VARCHAR(255) NOT NULL,
                        notulis_name VARCHAR(255) NULL,
                        session_date DATE NULL,
                        problem TEXT NULL,
                        cause TEXT NULL,
                        solution TEXT NULL,
                        action_ppg TEXT NULL,
                        action_description TEXT NULL,
                        action_name TEXT NULL,
                        action_participants TEXT NULL,
                        action_time TEXT NULL,
                        action_budget TEXT NULL,
                        role_keimaman TEXT NULL,
                        role_pengurus TEXT NULL,
                        role_parents TEXT NULL,
                        role_muballigh TEXT NULL,
                        role_educator TEXT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    );
                ");

                // Seed Groups 1-15 if groups table is empty
                $groupCount = (int)$pdo->query("SELECT COUNT(*) FROM groups")->fetchColumn();
                if ($groupCount === 0) {
                    for ($i = 1; $i <= 15; $i++) {
                        $stmt = $pdo->prepare("INSERT INTO groups (name, created_at, updated_at) VALUES (?, NOW(), NOW()) ON CONFLICT DO NOTHING");
                        $stmt->execute(["Grup {$i}"]);
                    }
                }

                // Seed Admins if admins table is empty, or fix password hashing
                $salt = 'ululalbab_cai47_fgd_salt';
                $admins = [
                    ['name' => 'Super Admin', 'email' => 'admin@notulensi.test', 'pass' => 'admin123', 'scope' => 'all'],
                    ['name' => 'Admin ULUL ALBAB', 'email' => 'ululalbab@notulensi.test', 'pass' => 'UA123', 'scope' => 'ulul_albab'],
                    ['name' => 'Admin Perumnas 2', 'email' => 'perumnas2@notulensi.test', 'pass' => 'perumnas123', 'scope' => 'perumnas_2'],
                ];
                foreach ($admins as $a) {
                    // Use SHA-256 + salt (matches Sha256Hasher::make)
                    $hashed = hash('sha256', $a['pass'] . $salt);
                    $check = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
                    $check->execute([$a['email']]);
                    if ($check->fetchColumn()) {
                        // Always update to ensure correct hash format
                        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE email = ?");
                        $stmt->execute([$hashed, $a['email']]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, scope, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW()) ON CONFLICT DO NOTHING");
                        $stmt->execute([$a['name'], $a['email'], $hashed, $a['scope']]);
                    }
                }
            } catch (\Throwable $e) {
                // Silently handle if DB connection is initializing
            }
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
