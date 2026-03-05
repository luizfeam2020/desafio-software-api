<?php declare(strict_types=1);

namespace App\Http;

final class Json
{
    public static function resposta(int $status, array $payload = null): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        if ($payload === null) return;

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}