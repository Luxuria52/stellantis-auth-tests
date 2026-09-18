<?php

declare(strict_types=1);

namespace Stellantis\Tests\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stellantis\Tests\Support\TestUserHelper;
use Stellantis\Tests\Support\UserDatabase;

final class TestUserHelperTest extends TestCase
{
    #[Test]
    public function helperCreatesAndRemovesUserWithoutARealDatabase(): void
    {
        $database = new class implements UserDatabase {
            /** @var array<int, array<string, string>> */
            public array $rows = [];

            public function insert(array $user): int
            {
                $id = count($this->rows) + 1;
                $this->rows[$id] = $user;

                return $id;
            }

            public function deleteById(int $id): void
            {
                unset($this->rows[$id]);
            }
        };

        $helper = new TestUserHelper($database);
        $user = $helper->createActiveUser('qa.user@example.test');

        self::assertSame(1, $user->id);
        self::assertSame('qa.user@example.test', $user->email);
        self::assertTrue(password_verify($user->plainPassword, $database->rows[1]['password_hash']));
        self::assertSame('active', $database->rows[1]['status']);

        $helper->cleanUp();

        self::assertSame([], $database->rows);
    }
}
