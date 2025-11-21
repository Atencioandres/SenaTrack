<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Función para probar la conexión sin usar la clase Database
function testDirectConnection() {
    try {
        $conn = new mysqli('localhost', 'root', '', 'senatrack_db');
        if ($conn->connect_error) {
            return [
                'success' => false,
                'error' => $conn->connect_error,
                'error_no' => $conn->connect_errno
            ];
        }
        $conn->close();
        return ['success' => true];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Verificar si el servidor MySQL está respondiendo
function testServerConnection() {
    try {
        $conn = @new mysqli('localhost', 'root', '');
        if ($conn->connect_error) {
            return [
                'success' => false,
                'error' => $conn->connect_error,
                'error_no' => $conn->connect_errno
            ];
        }
        $conn->close();
        return ['success' => true];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Verificar si la base de datos existe
function checkDatabase() {
    try {
        $conn = new mysqli('localhost', 'root', '');
        if ($conn->connect_error) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar al servidor'
            ];
        }
        
        $result = $conn->query("SHOW DATABASES LIKE 'senatrack_db'");
        $exists = $result && $result->num_rows > 0;
        
        $conn->close();
        return [
            'success' => true,
            'exists' => $exists
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Ejecutar diagnósticos
$diagnostico = [
    'server_connection' => testServerConnection(),
    'database_check' => checkDatabase(),
    'direct_connection' => testDirectConnection(),
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($diagnostico, JSON_PRETTY_PRINT);
?>