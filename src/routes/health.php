<?php
declare(strict_types=1);

function showDatabaseStatus(string $method): never
{
    if ($method !== 'GET') {
        header('Allow: GET');
        respond(405, ['error' => 'Method tidak diizinkan']);
    }

    $connected = false;
    try {
        getDB()->query('SELECT 1');
        $connected = true;
    } catch (Throwable $exception) {
        error_log((string) $exception);
    }

    http_response_code($connected ? 200 : 503);
    header('Content-Type: text/html; charset=utf-8');
    $checkedAt = date('d-m-Y H:i:s');
    require __DIR__ . '/../views/db-status.php';
    exit;
}
