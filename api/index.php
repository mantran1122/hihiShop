<?php

/**
 * Entrypoint cho Vercel Serverless Function (runtime vercel-php).
 *
 * Nhiệm vụ:
 *  1. Trả file tĩnh (CSS/JS/ảnh) nếu Vercel chưa route thẳng tới /public
 *     — tránh trường hợp mất CSS/ảnh khi mọi request đều dồn vào Laravel.
 *  2. Trên Vercel filesystem là read-only (chỉ /tmp ghi được), nên trỏ
 *     thư mục cache view / session / log sang thư mục tạm trước khi boot Laravel.
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
// 3. Chẩn đoán tạm (truy cập /?__probe=1) — chỉ trả boolean/không lộ giá trị
// ---------------------------------------------------------------------------
if (isset($_GET['__probe'])) {
    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode([
        'php' => PHP_VERSION,
        'app_env' => getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? null),
        'has_app_key' => !empty(getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? '')),
        'has_db_host' => !empty(getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '')),
        'db_ssl' => getenv('DB_SSL') ?: ($_ENV['DB_SSL'] ?? null),
        'vendor_autoload' => is_file(dirname(__DIR__).'/vendor/autoload.php'),
        'bootstrap_packages' => is_file(dirname(__DIR__).'/bootstrap/cache/packages.php'),
        'storage_writable' => is_writable(dirname(__DIR__).'/storage'),
        'tmp_writable' => is_writable(sys_get_temp_dir()),
        'extensions' => [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'fileinfo' => extension_loaded('fileinfo'),
            'intl' => extension_loaded('intl'),
        ],
    ], JSON_PRETTY_PRINT);

    return;
}

// ---------------------------------------------------------------------------
// 4. Boot Laravel (bắt lỗi để chẩn đoán khi cần)
// ---------------------------------------------------------------------------
try {
    require $publicDir.'/index.php';
} catch (\Throwable $e) {
    if (isset($_GET['__probe'])) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
        echo json_encode([
            'error' => get_class($e),
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
        ]);
        return;
    }

    throw $e;
}
