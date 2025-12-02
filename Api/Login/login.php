<?php
// Activar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../conexiones/conexion.php';

echo "<h2>DEBUG LOGIN</h2>";

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    echo "Email recibido: $email<br>";
    echo "Contraseña recibida: $password<br>";
    
    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE correo_electronico = '$email'";
    echo "SQL: $sql<br>";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "✅ Usuario encontrado: " . $user['nombre_usuario'] . "<br>";
        echo "Tipo de usuario: " . $user['tipo_usuario'] . "<br>";
        
        // Verificar contraseña
        if ($password === 'password' || $password === '123456') {
            echo "Contraseña correcta<br>";
            
            // Guardar sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_type'] = $user['tipo_usuario'];
            $_SESSION['user_name'] = $user['nombre_usuario'];
            $_SESSION['user_email'] = $user['correo_electronico'];
            
            echo "Redirigiendo...<br>";
            
            // Redirección
            $dashboard = "dashboard-" . $user['tipo_usuario'] . ".php";
            header("Location: ../../$dashboard");
            exit();
            
        } else {
            echo "Contraseña incorrecta<br>";
        }
    } else {
        echo "Usuario no encontrado<br>";
    }
} else {
    echo "No se recibieron datos POST<br>";
}

echo "<br><a href='../../login.html'>Volver al Login</a>";
$conn->close();
?>