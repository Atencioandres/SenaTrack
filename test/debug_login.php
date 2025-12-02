<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DEBUG COMPLETO - LOGIN PHP</h1>";

// Verificar si se recibieron datos POST
echo "<h3>1. Datos POST recibidos:</h3>";
if ($_POST) {
    echo "Email: " . ($_POST['email'] ?? 'NO RECIBIDO') . "<br>";
    echo "Password: " . ($_POST['password'] ?? 'NO RECIBIDO') . "<br>";
} else {
    echo "❌ NO se recibieron datos POST<br>";
}

// Probar conexión a la base de datos
echo "<h3>2. Probando conexión a BD:</h3>";
include 'Api/conexiones/conexion.php';

if ($conn) {
    echo "✅ Conexión a BD exitosa<br>";
    
    // Probar consulta
    $test_sql = "SELECT * FROM usuarios WHERE correo_electronico = 'admin@sena.edu.co'";
    $test_result = $conn->query($test_sql);
    
    if ($test_result) {
        echo "✅ Consulta ejecutada<br>";
        echo "Usuarios encontrados: " . $test_result->num_rows . "<br>";
        
        if ($test_result->num_rows > 0) {
            $user = $test_result->fetch_assoc();
            echo "Usuario encontrado: " . $user['nombre_usuario'] . "<br>";
            echo "Contraseña en BD: " . $user['contrasena'] . "<br>";
            
            // Probar contraseña
            $password_test = 'password';
            echo "<h3>3. Probando contraseña '{$password_test}':</h3>";
            
            if (password_verify($password_test, $user['contrasena'])) {
                echo "✅ Contraseña correcta (password_verify)<br>";
            } else if ($password_test === $user['contrasena']) {
                echo "✅ Contraseña correcta (comparación directa)<br>";
            } else {
                echo "❌ Contraseña NO coincide<br>";
            }
        }
    } else {
        echo "❌ Error en consulta: " . $conn->error . "<br>";
    }
    
    $conn->close();
} else {
    echo "❌ Error de conexión a BD<br>";
}

echo "<hr>";
echo "<a href='login.html'>Volver al Login</a>";
?>