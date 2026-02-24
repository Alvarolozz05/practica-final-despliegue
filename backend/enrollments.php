<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener todas las inscripciones con detalles
        $sql = "SELECT e.id, s.name as student_name, c.name as course_name 
                FROM enrollments e 
                JOIN students s ON e.student_id = s.id 
                JOIN courses c ON e.course_id = c.id";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['student_id']) || !isset($input['course_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos']);
            exit;
        }
        
        try {
            $sql = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
            $stmt= $pdo->prepare($sql);
            $stmt->execute([$input['student_id'], $input['course_id']]);
            echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Inscripción exitosa']);
        } catch (PDOException $e) {
            http_response_code(500);
            // Probablemente duplicado
            echo json_encode(['error' => 'Error al inscribir (posible duplicado)']);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requerido']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Inscripción eliminada']);
        break;
}
?>