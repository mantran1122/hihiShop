<?php

/**
 * Entrypoint cho Vercel Serverless Function (runtime vercel-php).
 *
 * Nhiệm vụ:
 *  1. Trả file tĩnh (CSS/JS/ảnh) nếu request trỏ tới file có thật trong /public.
 *  2. Trên Vercel filesystem là read-only (chỉ /tmp ghi được) nên trỏ các thư mục
 *     cache của Laravel (view đã compile, packages, services) sang /tmp trước khi boot.
 *  3. Giao request cho public/index.php của Laravel xử lý.
 */

$publicDir = dirname(__DIR__).'/public';
$publicReal = realpath($publicDir);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$requested = realpath($publicDir.$uri);

// ---------------------------------------------------------------------------
// 1. File tĩnh (chỉ trong /public, chặn path traversal và file .php)
// ---------------------------------------------------------------------------
if (
    $uri !== '/'
    && $publicReal !== false
    && $requested !== false
    && is_file($requested)
    && str_starts_with($requested, $publicReal.DIRECTORY_SEPARATOR)
    && strtolower(pathinfo($requested, PATHINFO_EXTENSION)) !== 'php'
) {
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'mjs' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'mp4' => 'video/mp4',
        'txt' => 'text/plain; charset=UTF-8',
        'xml' => 'application/xml',
    ];

    $extension = strtolower(pathinfo($requested, PATHINFO_EXTENSION));

    header('Content-Type: '.($mimeTypes[$extension] ?? 'application/octet-stream'));
    header('Content-Length: '.filesize($requested));
    header('Cache-Control: public, max-age=31536000, immutable');

    readfile($requested);

    return;
}

// ---------------------------------------------------------------------------
// 2. Thư mục ghi tạm cho môi trường serverless
// ---------------------------------------------------------------------------
$tmpBase = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'hihi-shop';

foreach (['views', 'sessions', 'cache', 'logs'] as $folder) {
    $path = $tmpBase.DIRECTORY_SEPARATOR.$folder;
    if (!is_dir($path)) {
        @mkdir($path, 0777, true);
    }
}

$defaults = [
    'VIEW_COMPILED_PATH' => $tmpBase.'/views',
    'SESSION_FILES_PATH' => $tmpBase.'/sessions',
    // Ghi log ra stderr để Vercel hiển thị trong Functions → Logs
    'LOG_CHANNEL' => 'stderr',
    'LOG_STACK' => 'stderr',
];

// Manifest bắt buộc phải ghi được. Trên Vercel bootstrap/cache là read-only nên
// trỏ sang /tmp để Laravel tự build lại theo đúng dependency production (--no-dev).
if (str_starts_with($tmpBase, '/')) {
    $defaults['APP_PACKAGES_CACHE'] = $tmpBase.'/packages.php';
    $defaults['APP_SERVICES_CACHE'] = $tmpBase.'/services.php';
}

foreach ($defaults as $key => $value) {
    $alreadySet = getenv($key) !== false
        || (isset($_ENV[$key]) && $_ENV[$key] !== '')
        || (isset($_SERVER[$key]) && $_SERVER[$key] !== '');

    if (!$alreadySet) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// ---------------------------------------------------------------------------
// 3. Boot Laravel
// ---------------------------------------------------------------------------
require $publicDir.'/index.php';
