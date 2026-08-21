<?php

putenv("VERCEL=1");
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

// Prepare writable /tmp directories required for Vercel serverless environment
$tmpDirs = [
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

// Copy sqlite database if it doesn't exist in /tmp
$tmpDb = '/tmp/database/database.sqlite';
if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
        @copy($sourceDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}

putenv("DB_DATABASE={$tmpDb}");
$_ENV['DB_DATABASE'] = $tmpDb;
$_SERVER['DB_DATABASE'] = $tmpDb;

// Ensure APP_KEY is available
if (!getenv('APP_KEY') && empty($_ENV['APP_KEY'])) {
    $key = 'base64:4Mo3JcKI/HpmRzc3dsVG8GsiLl+JRYtFz60B75D8tBc=';
    putenv("APP_KEY={$key}");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
}

// Forward requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
