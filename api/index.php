<?php

// Set env vars FIRST - before anything is loaded
putenv("VERCEL=1");
putenv("LOG_CHANNEL=stderr");
putenv("LOG_LEVEL=error");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");
putenv("VIEW_COMPILED_PATH=/tmp/views");

$_ENV['VERCEL']              = '1';
$_ENV['LOG_CHANNEL']         = 'stderr';
$_ENV['LOG_LEVEL']           = 'error';
$_ENV['CACHE_STORE']         = 'array';
$_ENV['SESSION_DRIVER']      = 'cookie';
$_ENV['VIEW_COMPILED_PATH']  = '/tmp/views';

// Ensure APP_KEY is available (fallback if not set in Vercel dashboard)
if (!getenv('APP_KEY')) {
    $key = 'base64:4Mo3JcKI/HpmRzc3dsVG8GsiLl+JRYtFz60B75D8tBc=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
}

// Prepare writable /tmp directories
$tmpDirs = [
    '/tmp/views',
    '/tmp/storage/logs',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/database',
];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy sqlite database to /tmp if needed
$tmpDb    = '/tmp/database/database.sqlite';
$sourceDb = __DIR__ . '/../database/database.sqlite';
if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
    if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
        @copy($sourceDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}
putenv("DB_DATABASE={$tmpDb}");
$_ENV['DB_DATABASE']    = $tmpDb;
$_SERVER['DB_DATABASE'] = $tmpDb;

// Bootstrap Laravel directly (bypassing public/index.php to control storage path)
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force storage to writable /tmp/storage BEFORE handling request
$app->useStoragePath('/tmp/storage');

$app->handleRequest(\Illuminate\Http\Request::capture());
