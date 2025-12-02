<?php
include '../conexiones/conexion.php';

if ($_POST) {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $identificacion = $_POST['identificacion'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    // Generar ID único
    $id_usuario = $tipo_usuario . '_' . $identificacion;
    $contrasena = '123456';
    
    // Obtener admin existente
    $admin_result = $conn->query("SELECT id_administrador FROM administrador LIMIT 1");
    
    if ($admin_result->num_rows > 0) {
        $admin_row = $admin_result->fetch_assoc();
        $id_administrador = $admin_row['id_administrador'];
        
        // SOLUCIÓN: Si existe campo 'usuario', generamos uno único
        $usuario_unique = strtolower($nombres . '.' . $apellidos . '.' . $identificacion);
        $usuario_unique = preg_replace('/[^a-z0-9.]/', '', $usuario_unique);
        
        // Consulta adaptativa - incluye campo 'usuario' si existe
        $sql = "INSERT INTO usuarios (
            id_usuario, 
            id_administrador,
            tipo_usuario, 
            nombre_usuario, 
            apellido_usuario, 
            correo_electronico, 
            contrasena
            " . (columnExists($conn, 'usuarios', 'usuario') ? ", usuario" : "") . "
        ) VALUES (
            '$id_usuario',
            '$id_administrador',
            '$tipo_usuario',
            '$nombres', 
            '$apellidos',
            '$email',
            '$contrasena'
            " . (columnExists($conn, 'usuarios', 'usuario') ? ", '$usuario_unique'" : "") . "
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "✅ Usuario registrado: $nombres $apellidos ($tipo_usuario)";
            echo "<br><a href='../../dashboard-admin.php'>Volver al Dashboard</a>";
        } else {
            echo "❌ Error: " . $conn->error;
        }
    }
}

$conn->close();

// Función para verificar si una columna existe
function columnExists($connection, $table, $column) {
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}
?>