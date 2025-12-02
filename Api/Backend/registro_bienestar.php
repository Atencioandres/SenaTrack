<?php
include '../conexiones/conexion.php';

if ($_POST) {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $identificacion = $_POST['identificacion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    // Generar ID de usuario automático
    $id_usuario = $tipo_usuario . '_' . $identificacion;
    
    // Generar nombre de usuario único
    $usuario_unique = strtolower($nombres . '.' . $apellidos . '.' . $identificacion);
    $usuario_unique = preg_replace('/[^a-z0-9.]/', '', $usuario_unique);
    
    // Contraseña por defecto
    $contrasena = '123456';
    
    // Obtener un ID de administrador existente
    $admin_query = "SELECT id_administrador FROM administrador LIMIT 1";
    $admin_result = $conn->query($admin_query);
    
    if ($admin_result->num_rows > 0) {
        $admin_row = $admin_result->fetch_assoc();
        $id_administrador = $admin_row['id_administrador'];
        
        // Verificar si el usuario ya existe
        $check_sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario' OR correo_electronico = '$email'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            echo "❌ Error: El usuario con ID $id_usuario o email $email ya existe";
            echo "<br><a href='../../registro-aprendiz.html'>Volver al formulario</a>";
        } else {
            // Insertar en la base de datos - versión adaptativa
            $sql = "INSERT INTO usuarios (
                id_usuario, 
                id_administrador,
                tipo_usuario, 
                nombre_usuario, 
                apellido_usuario, 
                correo_electronico, 
                contrasena";
            
            // Agregar campo usuario si existe en la tabla
            if (columnExists($conn, 'usuarios', 'usuario')) {
                $sql .= ", usuario";
            }
            
            $sql .= ") VALUES (
                '$id_usuario',
                '$id_administrador',
                '$tipo_usuario',
                '$nombres', 
                '$apellidos',
                '$email',
                '$contrasena'";
            
            // Agregar valor para campo usuario si existe
            if (columnExists($conn, 'usuarios', 'usuario')) {
                $sql .= ", '$usuario_unique'";
            }
            
            $sql .= ")";
            
            if ($conn->query($sql) === TRUE) {
                echo "✅ Usuario registrado exitosamente";
                echo "<br><br>";
                echo "<strong>Datos del usuario:</strong><br>";
                echo "ID: $id_usuario<br>";
                echo "Nombres: $nombres<br>";
                echo "Apellidos: $apellidos<br>";
                echo "Email: $email<br>";
                echo "Tipo: $tipo_usuario<br>";
                echo "Contraseña: $contrasena<br>";
                if (columnExists($conn, 'usuarios', 'usuario')) {
                    echo "Usuario: $usuario_unique<br>";
                }
                echo "<br>";
                
                if ($tipo_usuario == 'aprendiz') {
                    echo "<a href='../../registro-aprendiz.html'>Registrar otro aprendiz</a><br>";
                } else if ($tipo_usuario == 'bienestar') {
                    echo "<a href='../../registro-bienestar.html'>Registrar otro bienestar</a><br>";
                }
                
                echo "<a href='../../dashboard-admin.php'>Volver al Dashboard</a>";
            } else {
                echo "❌ Error al registrar: " . $conn->error;
            }
        }
    } else {
        echo "❌ Error: No se encontró ningún administrador en la base de datos";
    }
} else {
    echo "❌ No se recibieron datos del formulario";
}

$conn->close();

// Función para verificar si una columna existe
function columnExists($connection, $table, $column) {
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}
?>