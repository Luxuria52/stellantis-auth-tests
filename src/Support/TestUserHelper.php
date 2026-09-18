<?php

declare(strict_types=1);

namespace Stellantis\Tests\Support;

use DateTimeImmutable;

final class TestUserHelper
{
    /** @var list<int> */
    private array $createdUserIds = [];

    public function __construct(private readonly UserDatabase $database)
    {
    }

    public function createActiveUser(?string $email = null): TestUser
    {
        $plainPassword = 'Qa_' . bin2hex(random_bytes(8)) . '!';
        $email ??= sprintf('qa+%s@example.test', bin2hex(random_bytes(6)));

        $id = $this->database->insert([
            'email' => $email,
            'password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
            'status' => 'active',
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        $this->createdUserIds[] = $id;

        return new TestUser($id, $email, $plainPassword);
    }

    public function cleanUp(): void
    {
        foreach (array_reverse($this->createdUserIds) as $id) {
            $this->database->deleteById($id);
        }

        $this->createdUserIds = [];
    }
}
