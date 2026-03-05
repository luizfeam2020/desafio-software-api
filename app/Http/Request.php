<?php declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        return $path ? (rtrim($path, '/') ?: '/') : '/';
    }

    public function query(): array
    {
        return $_GET ?? [];
    }

    public function json(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') return [];

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}