<?php

declare(strict_types=1);

namespace Stellantis\Tests\Support;

interface UserDatabase
{
    /**
     * @param array{email: string, password_hash: string, status: string, created_at: string} $user
     */
    public function insert(array $user): int;

    public function deleteById(int $id): void;
}
