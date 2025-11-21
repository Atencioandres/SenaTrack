<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
if ($tipo_usuario !== 'bienestar' && $tipo_usuario !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id_aprendiz = $data['id_aprendiz'] ?? '';
$tipo_informe = $data['tipo_informe'] ?? '';
$descripcion = $data['descripcion'] ?? '';

if (empty($id_aprendiz) || empty($tipo_informe) || empty($descripcion)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

$conn = getConnection();

// Obtener id_bienestar del usuario actual
$id_usuario = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id_bienestar FROM personal_bienestar WHERE id_usuario = ?");
$stmt->bind_param("s", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autorizado']);
    $stmt->close();
    closeConnection($conn);
    exit();
}

$bienestar = $result->fetch_assoc();
$id_bienestar = $bienestar['id_bienestar'];
$stmt->close();

// Crear informe
$id_informe = generateId('inf');
$stmt = $conn->prepare("INSERT INTO informe (id_informe, tipo_informe, descripcion) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $id_informe, $tipo_informe, $descripcion);
$stmt->execute();
$stmt->close();

// Crear comparendo
$id_comparendo = generateId('c');
$stmt = $conn->prepare("INSERT INTO comparendo (id_comparendo, id_aprendiz, id_bienestar, id_informe, descripcion) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $id_comparendo, $id_aprendiz, $id_bienestar, $id_informe, $descripcion);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Comparendo registrado exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar comparendo'
    ]);
}

$stmt->close();
closeConnection($conn);
?>
