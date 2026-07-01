<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
echo 'DB_DATABASE=' . getenv('DB_DATABASE') . PHP_EOL;
echo 'DB_USERNAME=' . getenv('DB_USERNAME') . PHP_EOL;
echo 'DB_PASSWORD=' . (getenv('DB_PASSWORD') ? '***' : '') . PHP_EOL;
