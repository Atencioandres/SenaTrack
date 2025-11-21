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

$id_usuario = $_SESSION['user_id'] ?? '';
if (empty($id_usuario)) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$correo = $data['correo'] ?? '';
$foto_perfil = $data['foto_perfil'] ?? null;

if (empty($nombre) || empty($apellido) || empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

$conn = getConnection();

// Actualizar usuario
$stmt = $conn->prepare("UPDATE usuario SET nombre_usuario = ?, apellido_usuario = ?, correo_electronico = ?, foto_perfil = ? WHERE id_usuario = ?");
$stmt->bind_param("sssss", $nombre, $apellido, $correo, $foto_perfil, $id_usuario);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Perfil actualizado exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar perfil'
    ]);
}

$stmt->close();
closeConnection($conn);
?>
