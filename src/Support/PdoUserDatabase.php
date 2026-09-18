<?php

declare(strict_types=1);

namespace Stellantis\Tests\Support;

use PDO;

final class PdoUserDatabase implements UserDatabase
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function insert(array $user): int
    {
        $statement = $this->connection->prepare(
            <<<'SQL'
            INSERT INTO users (email, password_hash, status, created_at)
            VALUES (:email, :password_hash, :status, :created_at)
            SQL
        );

        $statement->execute([
            ':email' => $user['email'],
            ':password_hash' => $user['password_hash'],
            ':status' => $user['status'],
            ':created_at' => $user['created_at'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function deleteById(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM users WHERE id = :id');
        $statement->execute([':id' => $id]);
    }
}
