<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$correo = $data['correo'] ?? '';
$codigo = $data['codigo'] ?? '';

if (empty($correo) || empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$conn = getConnection();

// Verificar código válido y no expirado
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

if ($result->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Código verificado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Código incorrecto o expirado']);
}

$stmt->close();
closeConnection($conn);
?>
