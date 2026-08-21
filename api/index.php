<?php

// 1. Prepare writable /tmp directories
$tmpStorage   = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap';

$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
    $tmpBootstrap . '/cache',
    '/tmp/views',
    '/tmp/database',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Remove public/hot if present to force Vite production mode
if (file_exists(__DIR__ . '/../public/hot')) {
    @unlink(__DIR__ . '/../public/hot');
}

// 2. Set environment variables
putenv("VERCEL=1");
putenv("APP_ENV=production");
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH=/tmp/views");
putenv("LOG_CHANNEL=stderr");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");
putenv("APP_MAINTENANCE_DRIVER=cache");
putenv("APP_MAINTENANCE_STORE=array");

// Force relative asset URL so CSS/JS load cleanly on all devices
putenv("ASSET_URL=/");
$_ENV['ASSET_URL'] = '/';
$_SERVER['ASSET_URL'] = '/';

if (isset($_SERVER['HTTP_HOST'])) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'https';
    $appUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}";
    putenv("APP_URL={$appUrl}");
    $_ENV['APP_URL'] = $appUrl;
    $_SERVER['APP_URL'] = $appUrl;
}

$_ENV['VERCEL']                 = '1';
$_ENV['APP_ENV']                = 'production';
$_ENV['APP_STORAGE_PATH']       = $tmpStorage;
$_ENV['VIEW_COMPILED_PATH']     = '/tmp/views';
$_ENV['LOG_CHANNEL']            = 'stderr';
$_ENV['CACHE_STORE']            = 'array';
$_ENV['SESSION_DRIVER']         = 'cookie';
$_ENV['APP_MAINTENANCE_DRIVER'] = 'cache';
$_ENV['APP_MAINTENANCE_STORE']  = 'array';

// 3. Fallback APP_KEY if needed
if (!getenv('APP_KEY')) {
    $key = 'base64:4Mo3JcKI/HpmRzc3dsVG8GsiLl+JRYtFz60B75D8tBc=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

// 4. Set SQLite DB path
$tmpDb = '/tmp/database/database.sqlite';
$srcDb = __DIR__ . '/../database/database.sqlite';

if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
    if (file_exists($srcDb) && filesize($srcDb) > 0) {
        @copy($srcDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}

putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$tmpDb}");
$_ENV['DB_CONNECTION']  = 'sqlite';
$_ENV['DB_DATABASE']    = $tmpDb;
$_SERVER['DB_DATABASE'] = $tmpDb;

// 5. Bootstrap Laravel and handle request
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force storage AND bootstrap paths to writable /tmp
$app->useStoragePath($tmpStorage);
$app->useBootstrapPath($tmpBootstrap);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
