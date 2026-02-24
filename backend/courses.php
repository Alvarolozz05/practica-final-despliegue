<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM courses");
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre requerido']);
            exit; 
        }
        $sql = "INSERT INTO courses (name, description) VALUES (?, ?)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute([$input['name'], $input['description'] ?? '']);
        echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Curso creado']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Curso eliminado']);
        break;
}
?>