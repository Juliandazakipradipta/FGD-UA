<?php

// Display errors for debugging on Vercel
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set env vars FIRST - before anything is loaded
putenv("VERCEL=1");
putenv("APP_ENV=production");
putenv("APP_DEBUG=true");
putenv("LOG_CHANNEL=stderr");
putenv("LOG_LEVEL=debug");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");
putenv("VIEW_COMPILED_PATH=/tmp/views");

$_ENV['VERCEL']              = '1';
$_ENV['APP_ENV']             = 'production';
$_ENV['APP_DEBUG']           = 'true';
$_ENV['LOG_CHANNEL']         = 'stderr';
$_ENV['LOG_LEVEL']           = 'debug';
$_ENV['CACHE_STORE']         = 'array';
$_ENV['SESSION_DRIVER']      = 'cookie';
$_ENV['VIEW_COMPILED_PATH']  = '/tmp/views';

// Ensure APP_KEY is available
if (!getenv('APP_KEY')) {
    $key = 'base64:4Mo3JcKI/HpmRzc3dsVG8GsiLl+JRYtFz60B75D8tBc=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

try {
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
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION']  = 'sqlite';
    $_ENV['DB_DATABASE']    = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;

    define('LARAVEL_START', microtime(true));

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Force storage path to /tmp/storage
    $app->useStoragePath('/tmp/storage');

    $response = $app->handleRequest(\Illuminate\Http\Request::capture());
    $response->send();

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Server Error</h1>";
    echo "<h3>" . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
