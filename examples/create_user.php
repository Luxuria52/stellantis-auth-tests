<?php

declare(strict_types=1);

use Stellantis\Tests\Api\CreateUserRequest;

require dirname(__DIR__) . '/vendor/autoload.php';

$token = getenv('API_TOKEN');
if ($token === false || $token === '' || $token === 'change_me') {
    fwrite(STDERR, "Set API_TOKEN before running the example.\n");
    exit(1);
}

$request = (new CreateUserRequest(
    getenv('API_BASE_URL') ?: 'https://stellantis.autocrm.ru',
    getenv('API_CREATE_USER_PATH') ?: '/api/v1/users',
    $token,
))->build(
    'qa+' . bin2hex(random_bytes(5)) . '@example.test',
    'Тестовый пользователь',
    'StrongPass_123!',
);

$curl = curl_init($request['url']);
curl_setopt_array($curl, [
    CURLOPT_CUSTOMREQUEST => $request['method'],
    CURLOPT_HTTPHEADER => $request['headers'],
    CURLOPT_POSTFIELDS => $request['body'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
]);

$responseBody = curl_exec($curl);
if ($responseBody === false) {
    throw new RuntimeException('API request failed: ' . curl_error($curl));
}

$statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
curl_close($curl);

printf("HTTP %d\n%s\n", $statusCode, $responseBody);

if ($statusCode !== 201) {
    exit(1);
}
