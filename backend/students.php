<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM students");
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['name']) || !isset($input['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }
        $sql = "INSERT INTO students (name, email) VALUES (?, ?)";
        $stmt= $pdo->prepare($sql);
        try {
            $stmt->execute([$input['name'], $input['email']]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Estudiante creado']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Estudiante eliminado']);
        break;
}
?>