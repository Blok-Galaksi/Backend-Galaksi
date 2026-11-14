<?php
declare(strict_types=1);

function handleAuthRoute(string $path, string $method): never
{
    if (!in_array($path, ['/api/register', '/api/login'], true)) {
        respond(404, ['error' => 'Endpoint tidak ditemukan']);
    }

    if ($method !== 'POST') {
        header('Allow: POST');
        respond(405, ['error' => 'Method tidak diizinkan']);
    }

    $controller = new AuthController(getDB());
    $input = readJsonBody();

    if ($path === '/api/register') {
        $controller->register($input);
    }

    $controller->login($input);
}
