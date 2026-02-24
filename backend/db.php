<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Usar DATABASE_URL si existe (Railway)
if ($databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_PUBLIC_URL')) {
    // Formato: mysql://user:pass@host:port/db
    $url = parse_url($databaseUrl);
    $host = $url['host'] ?? 'localhost';
    $port = $url['port'] ?? 3306;
    $db   = ltrim($url['path'], '/') ?? 'railway';
    $user = $url['user'] ?? 'root';
    $pass = $url['pass'] ?? '';
} else {
    // Variables de entorno separadas (por compatibilidad)
    $host = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: 3306;
    $db   = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'railway';
    $user = getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
    $pass = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '';
}

$charset = 'utf8mb4';

// DSN PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 60,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

// Conexión con reintento
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Fallo inicial conexión BD: " . $e->getMessage());
    sleep(2);
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e2) {
        error_log("Fallo reintento conexión BD: " . $e2->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            "error" => "Error de conexión a base de datos",
            "details" => $e2->getMessage(),
            "host_attempted" => $host,
            "port_attempted" => $port,
            "db_attempted" => $db
        ]);
        exit;
    }
}
?>