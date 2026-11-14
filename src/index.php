<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/health.php';

try {
    $path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    if ($path === '/db-status') {
        showDatabaseStatus($_SERVER['REQUEST_METHOD'] ?? '');
    }

    handleAuthRoute($path, $_SERVER['REQUEST_METHOD'] ?? '');
} catch (Throwable $exception) {
    error_log((string) $exception);
    respond(500, ['error' => 'Terjadi kesalahan pada server']);
}
