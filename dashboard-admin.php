<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Verificar que sea admin
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: login.html?error=permisos");
    exit();
}

// Obtener datos del admin actual
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM usuarios WHERE id_usuario = '$user_id'";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();

// Obtener TODOS los usuarios registrados
$all_users_sql = "SELECT id_usuario, nombre_usuario, apellido_usuario, correo_electronico, tipo_usuario, fecha_creacion FROM usuarios ORDER BY fecha_creacion DESC";
$all_users_result = $conn->query($all_users_sql);

// Obtener TODOS los programas registrados
$programas_sql = "SELECT * FROM programas_formacion ORDER BY fecha_creacion DESC";
$programas_result = $conn->query($programas_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - SENATRACK</title>
</head>
<body>
    <h1>Dashboard ADMINISTRADOR</h1>
    
    <h2>Información del Administrador:</h2>
    <p><strong>ID:</strong> <?php echo $user_data['id_usuario']; ?></p>
    <p><strong>Nombre:</strong> <?php echo $user_data['nombre_usuario']; ?></p>
    <p><strong>Email:</strong> <?php echo $user_data['correo_electronico']; ?></p>
    
    <h2>Usuarios Registrados en el Sistema:</h2>
    
    <?php if ($all_users_result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID Usuario</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Fecha Registro</th>
            </tr>
            <?php while($user = $all_users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id_usuario']; ?></td>
                <td><?php echo $user['nombre_usuario']; ?></td>
                <td><?php echo $user['apellido_usuario']; ?></td>
                <td><?php echo $user['correo_electronico']; ?></td>
                <td><?php echo $user['tipo_usuario']; ?></td>
                <td><?php echo $user['fecha_creacion']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <p><strong>Total de usuarios:</strong> <?php echo $all_users_result->num_rows; ?></p>
    <?php else: ?>
        <p>No hay usuarios registrados en el sistema.</p>
    <?php endif; ?>

    <h2>Programas de Formación Registrados:</h2>
    
    <?php if ($programas_result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID Programa</th>
                <th>Nombre del Programa</th>
                <th>Número de Ficha</th>
                <th>Fecha de Creación</th>
            </tr>
            <?php while($programa = $programas_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $programa['id_programa']; ?></td>
                <td><?php echo $programa['nombre_programa']; ?></td>
                <td><?php echo $programa['numero_ficha']; ?></td>
                <td><?php echo $programa['fecha_creacion']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <p><strong>Total de programas:</strong> <?php echo $programas_result->num_rows; ?></p>
    <?php else: ?>
        <p>No hay programas de formación registrados en el sistema.</p>
    <?php endif; ?>

<h3>Funciones de Administrador:</h3>
<ul>
    <li><a href="gestion-usuarios.php">Gestión de Usuarios (Editar/Eliminar)</a></li>
    <li><a href="agregar-programa.html">Agregar Programa de Formación</a></li>
    <li><a href="registro-bienestar.html">Registrar Personal de Bienestar</a></li>
    <li><a href="registro-aprendiz.html">Registrar Aprendices</a></li>
    <li>Ver reportes del sistema</li>
</ul>
    
    <br>
    <a href="Api/Login/cerrar_session.php">Cerrar Sesión</a>
    
    <?php $conn->close(); ?>
</body>
</html>