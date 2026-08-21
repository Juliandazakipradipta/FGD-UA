<?php

// 1. Prepare writable /tmp directories
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
    '/tmp/views',
    '/tmp/database',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Set environment variables
putenv("VERCEL=1");
putenv("APP_ENV=production");
putenv("APP_DEBUG=true");
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH=/tmp/views");
putenv("LOG_CHANNEL=stderr");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");

$_ENV['VERCEL']           = '1';
$_ENV['APP_ENV']         = 'production';
$_ENV['APP_DEBUG']       = 'true';
$_ENV['APP_STORAGE_PATH'] = $tmpStorage;
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_ENV['LOG_CHANNEL']      = 'stderr';
$_ENV['CACHE_STORE']      = 'array';
$_ENV['SESSION_DRIVER']   = 'cookie';

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

try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');

    // Intercept exception handler to capture primary exception without relying on 'view'
    $app->singleton(
        \Illuminate\Contracts\Debug\ExceptionHandler::class,
        class extends \Illuminate\Foundation\Exceptions\Handler {
            public function __construct() {}
            public function render($request, \Throwable $e) {
                http_response_code(500);
                echo "<h1>PRIMARY EXCEPTION CAPTURED</h1>";
                echo "<h2>" . get_class($e) . ": " . htmlspecialchars($e->getMessage()) . "</h2>";
                echo "<p>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
                if ($prev = $e->getPrevious()) {
                    echo "<h3>Previous: " . get_class($prev) . ": " . htmlspecialchars($prev->getMessage()) . "</h3>";
                    echo "<p>File: " . htmlspecialchars($prev->getFile()) . ":" . $prev->getLine() . "</p>";
                }
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                exit;
            }
            public function report(\Throwable $e) {}
        }
    );

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>BOOTSTRAP EXCEPTION</h1>";
    echo "<h2>" . get_class($e) . ": " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
