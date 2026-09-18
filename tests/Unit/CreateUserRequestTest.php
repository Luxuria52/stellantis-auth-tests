<?php

declare(strict_types=1);

namespace Stellantis\Tests\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stellantis\Tests\Api\CreateUserRequest;

final class CreateUserRequestTest extends TestCase
{
    #[Test]
    public function buildsCreateUserRequest(): void
    {
        $request = new CreateUserRequest('https://example.test/', '/api/v1/users', 'test-token');

        $result = $request->build('new.user@example.test', 'Тестовый пользователь', 'StrongPass_123!');

        self::assertSame('POST', $result['method']);
        self::assertSame('https://example.test/api/v1/users', $result['url']);
        self::assertContains('Authorization: Bearer test-token', $result['headers']);
        self::assertSame([
            'email' => 'new.user@example.test',
            'name' => 'Тестовый пользователь',
            'password' => 'StrongPass_123!',
            'status' => 'active',
        ], json_decode($result['body'], true, flags: JSON_THROW_ON_ERROR));
    }
}
