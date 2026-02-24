<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Variables de entorno desde Render
$host = getenv('MYSQL_HOST');       // interchange.proxy.rlwy.net
$port = getenv('MYSQL_PORT') ?: 3306; // 19250
$db   = getenv('MYSQL_DATABASE');   // railway
$user = getenv('MYSQL_USER');       // root
$pass = getenv('MYSQL_PASSWORD');   // contraseña de Railway
$charset = 'utf8mb4';

// DSN PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>