<?php
declare(strict_types=1);

$local = __DIR__ . '/config.local.php';

if (is_file($local)) {
    return require $local;
}

return [
    'app_env' => 'production',
    'app_url' => 'https://example.com',
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'restaurant',
        'user' => 'restaurant',
        'pass' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => 'restaurant_session',
        'secure' => true,
    ],
    'mail' => [
        'from' => 'no-reply@example.com',
    ],
];
