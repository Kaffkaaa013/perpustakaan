<?php

/**
 * Entrypoint Vercel Serverless Function untuk Laravel
 */

// Pastikan folder cache sementara tersedia di /tmp yang writable
$dirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward ke public/index.php bawaan Laravel
require __DIR__ . '/../public/index.php';
