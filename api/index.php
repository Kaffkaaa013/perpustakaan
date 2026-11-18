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

// Layani berkas statis (Vite build CSS, JS, aset publik) secara langsung
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$publicFile = __DIR__ . '/../public' . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $ext = pathinfo($publicFile, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css'  => 'text/css; charset=UTF-8',
        'js'   => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }

    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($publicFile);
    exit;
}

// Forward seluruh rute dinamis ke public/index.php bawaan Laravel
require __DIR__ . '/../public/index.php';
