<?php

declare(strict_types=1);

namespace Stellantis\Tests\Api;

use JsonException;

final readonly class CreateUserRequest
{
    public function __construct(
        private string $baseUrl,
        private string $path,
        private string $token,
    ) {
    }

    /**
     * @return array{method: string, url: string, headers: list<string>, body: string}
     * @throws JsonException
     */
    public function build(string $email, string $name, string $password): array
    {
        return [
            'method' => 'POST',
            'url' => rtrim($this->baseUrl, '/') . '/' . ltrim($this->path, '/'),
            'headers' => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ],
            'body' => json_encode([
                'email' => $email,
                'name' => $name,
                'password' => $password,
                'status' => 'active',
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ];
    }
}
