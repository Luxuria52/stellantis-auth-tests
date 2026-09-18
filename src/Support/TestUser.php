<?php

declare(strict_types=1);

namespace Stellantis\Tests\Support;

final readonly class TestUser
{
    public function __construct(
        public int $id,
        public string $email,
        public string $plainPassword,
    ) {
    }
}
