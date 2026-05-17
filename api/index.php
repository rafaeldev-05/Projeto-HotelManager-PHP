<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);
$publicIndex = $basePath . '/public/index.php';

chdir($basePath);

$_SERVER['REQUEST_METHOD'] ??= 'GET';
$_SERVER['REQUEST_URI'] ??= '/';
$_SERVER['SCRIPT_NAME'] = '/api/index.php';
$_SERVER['PHP_SELF'] = '/api/index.php';
$_SERVER['DOCUMENT_ROOT'] = $basePath . '/public';

$requestPath = parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

if (($requestPath === '/api/index.php' || str_starts_with($requestPath, '/api/')) && isset($_GET['__route'])) {
    $route = trim((string) $_GET['__route'], '/');
    unset($_GET['__route'], $_REQUEST['__route']);

    $query = http_build_query($_GET);
    $_SERVER['REQUEST_URI'] = '/' . $route . ($query !== '' ? '?' . $query : '');
}

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

if (($_SERVER['REQUEST_URI'] ?? '/') === '/debug-vercel' && getenv('VERCEL_DEBUG') === '1') {
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>Debug Vercel</title></head><body>';
    echo '<h1>PHP executado na Vercel</h1>';
    echo '<dl>';
    echo '<dt>REQUEST_URI</dt><dd>' . htmlspecialchars((string) ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8') . '</dd>';
    echo '<dt>SCRIPT_NAME</dt><dd>' . htmlspecialchars((string) ($_SERVER['SCRIPT_NAME'] ?? ''), ENT_QUOTES, 'UTF-8') . '</dd>';
    echo '<dt>DOCUMENT_ROOT</dt><dd>' . htmlspecialchars((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), ENT_QUOTES, 'UTF-8') . '</dd>';
    echo '<dt>public/index.php</dt><dd>' . htmlspecialchars($publicIndex, ENT_QUOTES, 'UTF-8') . '</dd>';
    echo '<dt>file_exists</dt><dd>' . (is_file($publicIndex) ? 'sim' : 'nao') . '</dd>';
    echo '</dl>';
    echo '</body></html>';
    return;
}

require $publicIndex;
