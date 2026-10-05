<?php
declare(strict_types=1);

$root = dirname(__DIR__);
foreach ([
    'VERSION',
    'database/schema.sql',
    'database/seed.sql',
    'public_html/index.php',
    'public_html/.htaccess',
    'config/config.php',
    'config/config.local.php.example',
    'Docs/QAVNS.md',
    'Docs/19-versioning.md',
    'Docs/21-shared-host-deployment.md',
] as $f) {
    if (!is_file($root . '/' . $f)) {
        throw new RuntimeException("Missing $f");
    }
}

$v = trim((string) file_get_contents($root . '/VERSION'));
if (!preg_match('/^\d{3}\.\d+\.\d+\.\d+(?:-(?:a|b|rc)[1-9])?(?:-.+)?$/', $v)) {
    throw new RuntimeException("Invalid QAVNS version: $v");
}

foreach (['Dockerfile', 'docker-compose.yml', '.env.example'] as $forbidden) {
    if (is_file($root . '/' . $forbidden)) {
        throw new RuntimeException("Shared-host build must not contain $forbidden.");
    }
}

echo "Smoke checks passed: $v\n";
