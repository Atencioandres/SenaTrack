<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // pequeña consulta para verificar conectividad
    $res = $conn->query("SELECT 1 as ok");
    if ($res && $res->num_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Conexión OK']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'La consulta de verificación falló']);
    }

    $db->closeConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos']);
}

?>