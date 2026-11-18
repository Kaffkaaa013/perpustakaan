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

// Root proyek (satu level di atas folder api/)
$projectRoot = dirname(__DIR__);
$publicPath  = $projectRoot . '/public';

// Layani berkas statis (Vite build CSS, JS, aset publik) secara langsung
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// Hapus query string jika ada
$uri = strtok($uri, '?') ?: '/';

$publicFile = $publicPath . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'json'  => 'application/json; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'map'   => 'application/json',
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }

    header('Cache-Control: public, max-age=31536000, immutable');
    header('X-Static-File: ' . $uri); // debug header
    readfile($publicFile);
    exit;
}

// Forward seluruh rute dinamis ke public/index.php bawaan Laravel
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
$_SERVER['DOCUMENT_ROOT']   = $publicPath;

require $publicPath . '/index.php';
