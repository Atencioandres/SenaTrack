<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$usuario = $data['usuario'] ?? '';
$tipo_usuario = $data['tipo_usuario'] ?? '';

if (empty($usuario) || empty($tipo_usuario)) {
    echo json_encode(['success' => false, 'message' => 'Usuario y tipo son requeridos']);
    exit();
}

$conn = getConnection();

$stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE usuario = ? AND tipo_usuario = ?");
$stmt->bind_param("ss", $usuario, $tipo_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Usuario encontrado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
closeConnection($conn);
?>
