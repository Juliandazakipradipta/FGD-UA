<?php

// 1. Prepare writable /tmp directories
$tmpStorage   = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
    $tmpBootstrap,
    '/tmp/views',
    '/tmp/database',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Set environment variables for storage and bootstrap caching
putenv("VERCEL=1");
putenv("APP_ENV=production");
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH=/tmp/views");
putenv("LOG_CHANNEL=stderr");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");

putenv("APP_SERVICES_CACHE={$tmpBootstrap}/services.php");
putenv("APP_PACKAGES_CACHE={$tmpBootstrap}/packages.php");
putenv("APP_ROUTES_CACHE={$tmpBootstrap}/routes.php");
putenv("APP_CONFIG_CACHE={$tmpBootstrap}/config.php");
putenv("APP_EVENTS_CACHE={$tmpBootstrap}/events.php");

$_ENV['VERCEL']             = '1';
$_ENV['APP_ENV']            = 'production';
$_ENV['APP_STORAGE_PATH']   = $tmpStorage;
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_ENV['LOG_CHANNEL']        = 'stderr';
$_ENV['CACHE_STORE']        = 'array';
$_ENV['SESSION_DRIVER']     = 'cookie';

$_ENV['APP_SERVICES_CACHE'] = "{$tmpBootstrap}/services.php";
$_ENV['APP_PACKAGES_CACHE'] = "{$tmpBootstrap}/packages.php";
$_ENV['APP_ROUTES_CACHE']   = "{$tmpBootstrap}/routes.php";
$_ENV['APP_CONFIG_CACHE']   = "{$tmpBootstrap}/config.php";
$_ENV['APP_EVENTS_CACHE']   = "{$tmpBootstrap}/events.php";

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
$app->useStoragePath($tmpStorage);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
