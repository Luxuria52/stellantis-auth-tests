<?php

declare(strict_types=1);

use Stellantis\Tests\Support\PdoUserDatabase;
use Stellantis\Tests\Support\TestUserHelper;

require dirname(__DIR__) . '/vendor/autoload.php';

$dsn = getenv('DB_DSN');
if ($dsn === false || $dsn === '') {
    fwrite(STDERR, "Set DB_DSN, DB_USER and DB_PASSWORD to run this integration example.\n");
    exit(1);
}

$pdo = new PDO(
    $dsn,
    getenv('DB_USER') ?: '',
    getenv('DB_PASSWORD') ?: '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);

$helper = new TestUserHelper(new PdoUserDatabase($pdo));
$user = $helper->createActiveUser();

try {
    printf("User %s is ready for an authorization test.\n", $user->email);
    // Here the test logs in through the UI with $user->email and $user->plainPassword.
} finally {
    $helper->cleanUp();
}
