<?php
declare(strict_types=1);

function respond(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonBody(): array
{
    if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== 0) {
        respond(415, ['error' => 'Content-Type harus application/json']);
    }

    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
        respond(400, ['error' => 'Body harus berupa JSON yang valid']);
    }

    return $data;
}
