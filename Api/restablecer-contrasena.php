<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$correo = $data['correo'] ?? '';
$codigo = $data['codigo'] ?? '';
$nueva_contrasena = $data['nueva_contrasena'] ?? '';

if (empty($correo) || empty($codigo) || empty($nueva_contrasena)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$conn = getConnection();

// Verificar código nuevamente
$stmt = $conn->prepare("
    SELECT id FROM codigos_verificacion 
    WHERE correo_electronico = ? 
    AND codigo = ? 
    AND fecha_expiracion > NOW() 
    AND usado = FALSE
    ORDER BY fecha_creacion DESC 
    LIMIT 1
");
$stmt->bind_param("ss", $correo, $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Código inválido']);
    $stmt->close();
    closeConnection($conn);
    exit;
}

$codigo_id = $result->fetch_assoc()['id'];

// Actualizar contraseña
$contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE usuario SET contrasena = ? WHERE correo_electronico = ?");
$stmt->bind_param("ss", $contrasena_hash, $correo);

if ($stmt->execute()) {
    // Marcar código como usado
    $stmt = $conn->prepare("UPDATE codigos_verificacion SET usado = TRUE WHERE id = ?");
    $stmt->bind_param("i", $codigo_id);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar contraseña']);
}

$stmt->close();
closeConnection($conn);
?>
