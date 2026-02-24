<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Variables de entorno desde Render (soporte para ambos esquemas de nombres comunes)
$host = getenv('MYSQL_HOST') ?: getenv('DB_HOST');
$port = getenv('MYSQL_PORT') ?: getenv('DB_PORT');
$db   = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME');
$user = getenv('MYSQL_USER') ?: getenv('DB_USER');
$pass = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS');

// Fallback a valores por defecto si no existen
if (!$host) $host = 'localhost';
if (!$port) $port = 3306;
if (!$db)   $db   = 'railway'; // Nombre por defecto común en ejemplos
if (!$user) $user = 'root';

// Definir charset
$charset = 'utf8mb4';

    // DSN PDO con protocolo TCP explícito
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 60, // Aumentar timeout
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Deshabilitar verificación SSL para evitar problemas de certificados autofirmados común en dev
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // Log para depuración
        error_log("Fallo inicial conexión BD: " . $e->getMessage());
        // Reintentamos una vez con una pausa
        sleep(2);
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e2) {
            error_log("Fallo reintento conexión BD: " . $e2->getMessage());
            // Mostramos un mensaje JSON amigable en vez de dejar que PHP muestre el Fatal Error HTML
            // Esto es crucial para que el frontend reaccione bien.
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                "error" => "Error de conexión a base de datos",
                "details" => $e2->getMessage(),
                "host_attempted" => $host,
                "port_attempted" => $port,
                "db_attempted" => $db
            ]);
            exit; // Terminamos aquí para evitar stack traces
        }
    }
?>