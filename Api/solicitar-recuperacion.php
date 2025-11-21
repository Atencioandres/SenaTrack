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
$correo_destino = $data['correo_destino'] ?? '';
$mensaje = $data['mensaje'] ?? '';

if (empty($usuario) || empty($tipo_usuario) || empty($correo_destino)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$conn = getConnection();

// Verificar que el usuario existe
$stmt = $conn->prepare("SELECT correo_electronico, nombre_usuario, apellido_usuario FROM usuario WHERE usuario = ? AND tipo_usuario = ?");
$stmt->bind_param("ss", $usuario, $tipo_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    $stmt->close();
    closeConnection($conn);
    exit();
}

$user_data = $result->fetch_assoc();
$stmt->close();

// Verificar que el correo destino existe y es de personal autorizado
$stmt = $conn->prepare("SELECT tipo_usuario FROM usuario WHERE correo_electronico = ? AND tipo_usuario IN ('admin', 'bienestar')");
$stmt->bind_param("s", $correo_destino);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'El correo no pertenece a personal autorizado']);
    $stmt->close();
    closeConnection($conn);
    exit();
}

$stmt->close();

// En un sistema real, aquí se enviaría un correo electrónico
// Por ahora, solo simulamos el envío
$email_subject = "Solicitud de recuperación de contraseña - SenaTrack";
$email_body = "Usuario: $usuario\n";
$email_body .= "Nombre: {$user_data['nombre_usuario']} {$user_data['apellido_usuario']}\n";
$email_body .= "Correo: {$user_data['correo_electronico']}\n";
$email_body .= "Tipo: $tipo_usuario\n\n";
$email_body .= "Mensaje: " . ($mensaje ?: "Sin mensaje adicional");

// Simulación de envío exitoso
// En producción, usar mail() o una librería como PHPMailer
$envio_exitoso = true; // Simular envío exitoso

if ($envio_exitoso) {
    echo json_encode([
        'success' => true,
        'message' => 'Solicitud enviada exitosamente. El personal te contactará pronto.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al enviar la solicitud']);
}

closeConnection($conn);
?>
