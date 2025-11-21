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
$contrasena = $data['contrasena'] ?? '';
$tipo_usuario = $data['tipo_usuario'] ?? '';

if (empty($usuario) || empty($contrasena) || empty($tipo_usuario)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

// Usamos la clase Database (OOP)
$db = Database::getInstance();
$conn = $db->getConnection();

// Buscar usuario
$stmt = $conn->prepare("SELECT * FROM usuario WHERE usuario = ? AND tipo_usuario = ?");
$stmt->bind_param("ss", $usuario, $tipo_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    $stmt->close();
    closeConnection($conn);
    exit();
}

$user = $result->fetch_assoc();

// Verificar contraseña
if (!password_verify($contrasena, $user['contrasena'])) {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    $stmt->close();
    closeConnection($conn);
    exit();
}

// Obtener datos adicionales según el tipo de usuario
$userData = [
    'id_usuario' => $user['id_usuario'],
    'usuario' => $user['usuario'],
    'nombre' => $user['nombre_usuario'],
    'apellido' => $user['apellido_usuario'],
    'correo' => $user['correo_electronico'],
    'tipo_usuario' => $user['tipo_usuario'],
    'foto_perfil' => $user['foto_perfil']
];

if ($tipo_usuario === 'aprendiz') {
    $stmt2 = $conn->prepare("SELECT * FROM aprendiz WHERE id_usuario = ?");
    $stmt2->bind_param("s", $user['id_usuario']);
    $stmt2->execute();
    $aprendiz = $stmt2->get_result()->fetch_assoc();
    if ($aprendiz) {
        $userData['programa'] = $aprendiz['programa_formacion'];
        $userData['ficha'] = $aprendiz['ficha'];
        $userData['telefono'] = $aprendiz['telefono'];
    }
    $stmt2->close();
} elseif ($tipo_usuario === 'bienestar') {
    $stmt2 = $conn->prepare("SELECT * FROM personal_bienestar WHERE id_usuario = ?");
    $stmt2->bind_param("s", $user['id_usuario']);
    $stmt2->execute();
    $bienestar = $stmt2->get_result()->fetch_assoc();
    if ($bienestar) {
        $userData['especialidad'] = $bienestar['especialidad'];
    }
    $stmt2->close();
}

// Iniciar sesión
session_start();
$_SESSION['user_id'] = $user['id_usuario'];
$_SESSION['tipo_usuario'] = $user['tipo_usuario'];

echo json_encode([
    'success' => true,
    'message' => 'Inicio de sesión exitoso',
    'user' => $userData
]);

// cerrar recursos
$stmt->close();
$db->closeConnection();
?>
