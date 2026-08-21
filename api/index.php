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
        @mkdir($dir, 0777, true);
    }
    @chmod($dir, 0777);
}

// Remove public/hot if present to force Vite production mode
if (file_exists(__DIR__ . '/../public/hot')) {
    @unlink(__DIR__ . '/../public/hot');
}

// 2. Set environment variables for Vercel Serverless
putenv("VERCEL=1");
putenv("APP_ENV=production");
putenv("APP_DEBUG=false");
putenv("APP_NAME=FGD");
putenv("SESSION_COOKIE=fgd_session");
putenv("SESSION_SECURE_COOKIE=true");
putenv("APP_STORAGE_PATH={$tmpStorage}");
putenv("VIEW_COMPILED_PATH=/tmp/views");
putenv("LOG_CHANNEL=stderr");
putenv("CACHE_STORE=array");
putenv("SESSION_DRIVER=cookie");
putenv("APP_MAINTENANCE_DRIVER=file");
putenv("ASSET_URL=/public");

$_ENV['VERCEL']                 = '1';
$_ENV['APP_ENV']                = 'production';
$_ENV['APP_DEBUG']              = 'false';
$_ENV['APP_NAME']               = 'FGD';
$_ENV['SESSION_COOKIE']         = 'fgd_session';
$_ENV['SESSION_SECURE_COOKIE']  = 'true';
$_ENV['APP_STORAGE_PATH']       = $tmpStorage;
$_ENV['VIEW_COMPILED_PATH']     = '/tmp/views';
$_ENV['LOG_CHANNEL']            = 'stderr';
$_ENV['CACHE_STORE']            = 'array';
$_ENV['SESSION_DRIVER']         = 'cookie';
$_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
$_ENV['ASSET_URL']              = '/public';

$_SERVER['VERCEL']                 = '1';
$_SERVER['APP_ENV']                = 'production';
$_SERVER['APP_DEBUG']              = 'false';
$_SERVER['APP_NAME']               = 'FGD';
$_SERVER['SESSION_COOKIE']         = 'fgd_session';
$_SERVER['SESSION_SECURE_COOKIE']  = 'true';
$_SERVER['APP_STORAGE_PATH']       = $tmpStorage;
$_SERVER['VIEW_COMPILED_PATH']     = '/tmp/views';
$_SERVER['LOG_CHANNEL']            = 'stderr';
$_SERVER['CACHE_STORE']            = 'array';
$_SERVER['SESSION_DRIVER']         = 'cookie';
$_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';
$_SERVER['ASSET_URL']              = '/public';

// 3. Fallback APP_KEY if needed
if (!getenv('APP_KEY')) {
    $key = 'base64:4Mo3JcKI/HpmRzc3dsVG8GsiLl+JRYtFz60B75D8tBc=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

// 4. Set Database connection (supports external Cloud DB if set in Vercel Env Vars, else defaults to SQLite)
$externalConn = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION');
$dbHost       = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST');

if (!empty($dbHost) && (empty($externalConn) || $externalConn === 'sqlite')) {
    $externalConn = 'pgsql';
    putenv("DB_CONNECTION=pgsql");
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
}

if (!empty($externalConn) && $externalConn !== 'sqlite') {
    // Using external database configured via Vercel Environment Variables
} else {
    $tmpDb = '/tmp/database/database.sqlite';
    $srcDb = __DIR__ . '/../database/database.sqlite';

    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        if (file_exists($srcDb) && filesize($srcDb) > 0) {
            @copy($srcDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }

    // Ensure database file and directory are fully writable
    @chmod($tmpDb, 0666);
    @chmod(dirname($tmpDb), 0777);

    // Force the admin passwords and scope columns in the SQLite DB to be compatible with Vercel runtime
    try {
        $db = new \PDO("sqlite:{$tmpDb}");
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        try { $db->exec("ALTER TABLE admins ADD COLUMN scope VARCHAR DEFAULT 'all'"); } catch (\Throwable $e) {}
        try { $db->exec("ALTER TABLE minutes ADD COLUMN scope VARCHAR DEFAULT 'ulul_albab'"); } catch (\Throwable $e) {}

        $admins = [
            ['name' => 'Super Admin', 'email' => 'admin@notulensi.test', 'raw_pass' => 'admin123', 'scope' => 'all'],
            ['name' => 'Admin ULUL ALBAB', 'email' => 'ululalbab@notulensi.test', 'raw_pass' => 'UA123', 'scope' => 'ulul_albab'],
            ['name' => 'Admin Perumnas 2', 'email' => 'perumnas2@notulensi.test', 'raw_pass' => 'perumnas123', 'scope' => 'perumnas_2'],
        ];

        foreach ($admins as $a) {
            $stmt = $db->prepare("SELECT password FROM admins WHERE email = ?");
            $stmt->execute([$a['email']]);
            $existingPass = $stmt->fetchColumn();

            if ($existingPass !== false) {
                if (!password_verify($a['raw_pass'], $existingPass)) {
                    $hashed = password_hash($a['raw_pass'], PASSWORD_BCRYPT);
                    $up = $db->prepare("UPDATE admins SET name = ?, password = ?, scope = ? WHERE email = ?");
                    $up->execute([$a['name'], $hashed, $a['scope'], $a['email']]);
                }
            } else {
                $hashed = password_hash($a['raw_pass'], PASSWORD_BCRYPT);
                $ins = $db->prepare("INSERT INTO admins (name, email, password, scope, created_at, updated_at) VALUES (?, ?, ?, ?, datetime('now'), datetime('now'))");
                $ins->execute([$a['name'], $a['email'], $hashed, $a['scope']]);
            }
        }
    } catch (\Throwable $e) {
        // Silently ignore if DB not initialized yet
    }

    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION']  = 'sqlite';
    $_ENV['DB_DATABASE']    = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

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
