<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id_usuario = $_GET['eliminar'];
    
    // No permitir eliminar al propio admin
    if ($id_usuario != $_SESSION['user_id']) {
        $delete_sql = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        if ($conn->query($delete_sql)) {
            $mensaje = "✅ Usuario eliminado exitosamente";
        } else {
            $mensaje = "❌ Error al eliminar usuario: " . $conn->error;
        }
    } else {
        $mensaje = "❌ No puedes eliminar tu propio usuario";
    }
}

// Obtener todos los usuarios
$usuarios_sql = "SELECT u.*, p.nombre_programa 
                 FROM usuarios u 
                 LEFT JOIN programas_formacion p ON u.id_programa = p.id_programa 
                 ORDER BY u.fecha_creacion DESC";
$usuarios_result = $conn->query($usuarios_sql);
?>

    <h1>Gestión de Usuarios</h1>
    
    <?php if (isset($mensaje)): ?>
        <div class="mensaje <?php echo strpos($mensaje, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <p><strong>Total de usuarios:</strong> <?php echo $usuarios_result->num_rows; ?></p>
    
    <?php if ($usuarios_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Programa</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($usuario = $usuarios_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $usuario['id_usuario']; ?></td>
                    <td><?php echo $usuario['nombre_usuario']; ?></td>
                    <td><?php echo $usuario['apellido_usuario']; ?></td>
                    <td><?php echo $usuario['correo_electronico']; ?></td>
                    <td><?php echo $usuario['tipo_usuario']; ?></td>
                    <td><?php echo $usuario['nombre_programa'] ?? 'No asignado'; ?></td>
                    <td><?php echo $usuario['fecha_creacion']; ?></td>
                    <td>
                        <a href="editar-usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-editar">✏️ Editar</a>
                        <?php if ($usuario['id_usuario'] != $_SESSION['user_id']): ?>
                            <a href="gestion-usuarios.php?eliminar=<?php echo $usuario['id_usuario']; ?>" 
                               class="btn btn-eliminar" 
                               onclick="return confirm('¿Estás seguro de eliminar a <?php echo $usuario['nombre_usuario']; ?>?')">
                               🗑️ Eliminar
                            </a>
                        <?php else: ?>
                            <span style="color: #666;">(Tú)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados en el sistema.</p>
    <?php endif; ?>
    
    <br>
    <a href="dashboard-admin.php">← Volver al Dashboard</a>
    
    <?php $conn->close(); ?>
</body>
</html>