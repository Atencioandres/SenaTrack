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

$tipo_usuario = $data['tipo_usuario'] ?? '';
$usuario = $data['usuario'] ?? '';
$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$correo = $data['correo'] ?? '';
$contrasena = $data['contrasena'] ?? '';
$pregunta_seguridad = $data['pregunta_seguridad'] ?? null;
$respuesta_seguridad = $data['respuesta_seguridad'] ?? null;

if (empty($tipo_usuario) || empty($usuario) || empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

$conn = getConnection();

// Verificar si el usuario ya existe
$stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE usuario = ? OR correo_electronico = ?");
$stmt->bind_param("ss", $usuario, $correo);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El usuario o correo ya existe']);
    $stmt->close();
    closeConnection($conn);
    exit();
}
$stmt->close();

// Obtener el administrador por defecto
$admin_result = $conn->query("SELECT id_administrador FROM administrador LIMIT 1");
$admin = $admin_result->fetch_assoc();
$id_administrador = $admin['id_administrador'];

// Generar IDs
$id_usuario = generateId('u');
$id_informe = generateId('inf');

// Hash de contraseña
$hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

// Iniciar transacción
$conn->begin_transaction();

try {
    // Crear informe por defecto
    $stmt = $conn->prepare("INSERT INTO informe (id_informe, tipo_informe, descripcion) VALUES (?, 'Registro', 'Informe de registro inicial')");
    $stmt->bind_param("s", $id_informe);
    $stmt->execute();
    $stmt->close();

    // Crear usuario
    $stmt = $conn->prepare("INSERT INTO usuario (id_usuario, id_administrador, tipo_usuario, usuario, nombre_usuario, apellido_usuario, correo_electronico, contrasena, pregunta_seguridad, respuesta_seguridad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $id_usuario, $id_administrador, $tipo_usuario, $usuario, $nombre, $apellido, $correo, $hashed_password, $pregunta_seguridad, $respuesta_seguridad);
    $stmt->execute();
    $stmt->close();

    // Crear registro específico según tipo de usuario
    if ($tipo_usuario === 'aprendiz') {
        $programa = $data['programa'] ?? 'Sin programa';
        $ficha = $data['ficha'] ?? 0;
        $telefono = $data['telefono'] ?? 3000000000;
        $id_aprendiz = generateId('a');
        
        $stmt = $conn->prepare("INSERT INTO aprendiz (id_aprendiz, id_usuario, id_informe, nombre_aprendiz, programa_formacion, ficha, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $nombre_completo = $nombre . ' ' . $apellido;
        $stmt->bind_param("sssssis", $id_aprendiz, $id_usuario, $id_informe, $nombre_completo, $programa, $ficha, $telefono);
        $stmt->execute();
        $stmt->close();
        
    } elseif ($tipo_usuario === 'bienestar') {
        $especialidad = $data['especialidad'] ?? 'General';
        $id_bienestar = generateId('b');
        
        $stmt = $conn->prepare("INSERT INTO personal_bienestar (id_bienestar, id_usuario, id_informe, nombre, especialidad) VALUES (?, ?, ?, ?, ?)");
        $nombre_completo = $nombre . ' ' . $apellido;
        $stmt->bind_param("sssss", $id_bienestar, $id_usuario, $id_informe, $nombre_completo, $especialidad);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Registro exitoso'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar usuario: ' . $e->getMessage()
    ]);
}

closeConnection($conn);
?>
