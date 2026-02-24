<?php
// Permitir solicitudes desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos usando variables de entorno de Render
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT') ?: 3306;
$db   = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');    
$pass = getenv('MYSQL_PASSWORD');
$charset = 'utf8mb4';

// DSN de PDO incluyendo puerto
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Intentar conexión
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Mostrar error de conexión (útil para depuración)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>