<?php
declare(strict_types=1);

// Có thể ghi đè trong config.local.php hoặc bằng biến môi trường DB_*.
$config = [
    'db_host' => getenv('DB_HOST') ?: '127.0.0.1',
    'db_port' => getenv('DB_PORT') ?: '3306',
    'db_name' => getenv('DB_DATABASE') ?: 'online_shop',
    'db_user' => getenv('DB_USERNAME') ?: 'root',
    'db_password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
];
if (is_file(__DIR__ . '/config.local.php')) {
    $config = array_replace($config, require __DIR__ . '/config.local.php');
}
return $config;
