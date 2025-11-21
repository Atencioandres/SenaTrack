<?php
// Configuración de la base de datos (constantes)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'senatrack_db');

/**
 * Clase Database (singleton) para manejo de la conexión MySQLi en OOP.
 * Mantiene compatibilidad con funciones legacy getConnection() / closeConnection().
 */
class Database {
    private static $instance = null;
    private $conn = null;

    private function __construct() {
        // Intentar conectar
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->conn->connect_error) {
                throw new Exception('Error de conexión: ' . $this->conn->connect_error);
            }
            $this->conn->set_charset('utf8mb4');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al conectar con la base de datos'
            ]);
            exit();
        }
    }

    /**
     * Obtener la instancia singleton
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Obtener el objeto mysqli conectado
     * @return mysqli
     */
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }

    /**
     * Cerrar la conexión (y resetear la instancia)
     */
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
        self::$instance = null;
    }
}

// Wrappers legacy para compatibilidad con código existente
function getConnection() {
    return Database::getInstance()->getConnection();
}

function closeConnection($conn = null) {
    // Si se pasó un objeto mysqli, cerrarlo.
    if ($conn instanceof mysqli) {
        $conn->close();
    }
    // Además cerrar la instancia singleton si existe
    if (class_exists('Database')) {
        $db = Database::getInstance();
        $db->closeConnection();
    }
}

// Función para generar ID único
function generateId($prefix) {
    return $prefix . uniqid() . rand(100, 999);
}

// Función para validar sesión
function validateSession() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Sesión no válida'
        ]);
        exit();
    }
    return $_SESSION;
}

?>
