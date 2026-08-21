<?php

// Prepare writable /tmp directories required for Vercel serverless environment
$tmpDirs = [
    '/tmp/views',
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
if (!file_exists($tmpDb)) {
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb)) {
        @copy($sourceDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}

putenv("DB_DATABASE={$tmpDb}");
$_ENV['DB_DATABASE'] = $tmpDb;

// Forward requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
