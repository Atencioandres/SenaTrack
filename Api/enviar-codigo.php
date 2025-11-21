<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$correo = $data['correo'] ?? '';

if (empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'Correo requerido']);
    exit;
}

$conn = getConnection();

// Verificar que el correo existe en la base de datos
$stmt = $conn->prepare("SELECT correo_electronico FROM usuario WHERE correo_electronico = ?");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta SELECT', 'error' => $conn->error]);
    closeConnection($conn);
    exit;
}
$stmt->bind_param("s", $correo);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar SELECT', 'error' => $stmt->error]);
    $stmt->close();
    closeConnection($conn);
    exit;
}
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Correo no registrado']);
    $stmt->close();
    closeConnection($conn);
    exit;
}
$stmt->close();

// Generar código de 6 dígitos
$codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Calcular fecha de expiración (15 minutos)
$fecha_expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Guardar código en la base de datos
$stmt = $conn->prepare("INSERT INTO codigos_verificacion (correo_electronico, codigo, fecha_expiracion) VALUES (?, ?, ?)");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta INSERT', 'error' => $conn->error]);
    closeConnection($conn);
    exit;
}
$stmt->bind_param("sss", $correo, $codigo, $fecha_expiracion);

if ($stmt->execute()) {
    // En producción, aquí enviarías el correo electrónico real
    // Por ahora, devolvemos el código para desarrollo
    echo json_encode([
        'success' => true,
        'message' => 'Código enviado exitosamente',
        'codigo_debug' => $codigo // Solo para desarrollo, eliminar en producción
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al generar código', 'error' => $stmt->error]);
}

$stmt->close();
closeConnection($conn);
?>
