<?php
require_once 'db.php';

header('Content-Type: application/json');

echo json_encode([
    "status" => "success",
    "message" => "Conexión a base de datos exitosa",
    "host" => $host,
    "port" => $port,
    "database" => $db,
    "user" => $user
]);
?>