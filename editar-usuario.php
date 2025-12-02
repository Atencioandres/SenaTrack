<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Obtener datos del usuario a editar
$id_usuario = $_GET['id'] ?? '';
$usuario = null;

if ($id_usuario) {
    $sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
}

// Procesar actualización
if ($_POST && $usuario) {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $id_programa = $_POST['id_programa'] ?? null;
    
    $update_sql = "UPDATE usuarios SET 
                   nombre_usuario = '$nombres',
                   apellido_usuario = '$apellidos',
                   correo_electronico = '$email',
                   tipo_usuario = '$tipo_usuario',
                   id_programa = " . ($id_programa ? "'$id_programa'" : "NULL") . "
                   WHERE id_usuario = '$id_usuario'";
    
    if ($conn->query($update_sql)) {
        $mensaje = "✅ Usuario actualizado exitosamente";
        // Actualizar datos locales
        $usuario['nombre_usuario'] = $nombres;
        $usuario['apellido_usuario'] = $apellidos;
        $usuario['correo_electronico'] = $email;
        $usuario['tipo_usuario'] = $tipo_usuario;
        $usuario['id_programa'] = $id_programa;
    } else {
        $mensaje = "❌ Error al actualizar: " . $conn->error;
    }
}

// Obtener programas para el select
$programas_sql = "SELECT * FROM programas_formacion ORDER BY nombre_programa";
$programas_result = $conn->query($programas_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario - SENATRACK</title>
</head>
<body>
    <h1>✏️ Editar Usuario</h1>
    
    <?php if (!$usuario): ?>
        <p>❌ Usuario no encontrado</p>
        <a href="gestion-usuarios.php">← Volver a Gestión de Usuarios</a>
        <?php exit(); ?>
    <?php endif; ?>
    
    <?php if (isset($mensaje)): ?>
        <p style="color: green;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>ID Usuario:</label>
            <input type="text" value="<?php echo $usuario['id_usuario']; ?>" disabled>
            <small>(No editable)</small>
        </div>
        
        <div>
            <label>Nombres:</label>
            <input type="text" name="nombres" value="<?php echo $usuario['nombre_usuario']; ?>" required>
        </div>
        
        <div>
            <label>Apellidos:</label>
            <input type="text" name="apellidos" value="<?php echo $usuario['apellido_usuario']; ?>" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $usuario['correo_electronico']; ?>" required>
        </div>
        
        <div>
            <label>Tipo de Usuario:</label>
            <select name="tipo_usuario" required>
                <option value="admin" <?php echo $usuario['tipo_usuario'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                <option value="bienestar" <?php echo $usuario['tipo_usuario'] == 'bienestar' ? 'selected' : ''; ?>>Bienestar</option>
                <option value="aprendiz" <?php echo $usuario['tipo_usuario'] == 'aprendiz' ? 'selected' : ''; ?>>Aprendiz</option>
            </select>
        </div>
        
        <div>
            <label>Programa de Formación:</label>
            <select name="id_programa">
                <option value="">-- Sin programa --</option>
                <?php while($programa = $programas_result->fetch_assoc()): ?>
                <option value="<?php echo $programa['id_programa']; ?>" 
                    <?php echo $usuario['id_programa'] == $programa['id_programa'] ? 'selected' : ''; ?>>
                    <?php echo $programa['nombre_programa'] . " - " . $programa['numero_ficha']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <br>
        <button type="submit">💾 Guardar Cambios</button>
    </form>
    
    <br>
    <a href="gestion-usuarios.php">← Volver a Gestión de Usuarios</a>
    
    <?php $conn->close(); ?>
</body>
</html>