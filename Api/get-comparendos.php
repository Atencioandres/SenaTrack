<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

session_start();

$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
$id_usuario = $_SESSION['user_id'] ?? '';

if (empty($tipo_usuario) || empty($id_usuario)) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

// Usamos la clase Database (OOP)
$db = Database::getInstance();
$conn = $db->getConnection();

if ($tipo_usuario === 'aprendiz') {
    // Obtener comparendos del aprendiz
    $stmt = $conn->prepare("
        SELECT c.*, i.tipo_informe, pb.nombre as nombre_bienestar
        FROM comparendo c
        JOIN aprendiz a ON c.id_aprendiz = a.id_aprendiz
        JOIN informe i ON c.id_informe = i.id_informe
        JOIN personal_bienestar pb ON c.id_bienestar = pb.id_bienestar
        WHERE a.id_usuario = ?
        ORDER BY c.fecha_comparendo DESC
    ");
    $stmt->bind_param("s", $id_usuario);
    
} else {
    // Obtener todos los comparendos para admin y bienestar
    $stmt = $conn->prepare("
        SELECT c.*, i.tipo_informe, a.nombre_aprendiz, pb.nombre as nombre_bienestar
        FROM comparendo c
        JOIN aprendiz a ON c.id_aprendiz = a.id_aprendiz
        JOIN informe i ON c.id_informe = i.id_informe
        JOIN personal_bienestar pb ON c.id_bienestar = pb.id_bienestar
        ORDER BY c.fecha_comparendo DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();

$comparendos = [];
while ($row = $result->fetch_assoc()) {
    $comparendos[] = $row;
}

echo json_encode([
    'success' => true,
    'comparendos' => $comparendos
]);

$stmt->close();
$db->closeConnection();
?>
