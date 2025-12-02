<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar que sea aprendiz
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'aprendiz') {
    header("Location: login.html");
    exit();
}

// Obtener datos del aprendiz
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.*, p.nombre_programa, p.numero_ficha 
        FROM usuarios u 
        LEFT JOIN programas_formacion p ON u.id_programa = p.id_programa 
        WHERE u.id_usuario = '$user_id'";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();

// Obtener comparendos del aprendiz
$comparendos_sql = "SELECT * FROM comparendos WHERE id_aprendiz = '$user_id' ORDER BY fecha_creacion DESC";
$comparendos_result = $conn->query($comparendos_sql);
$total_comparendos = $comparendos_result->num_rows;

// Determinar mensaje según cantidad de comparendos
$mensaje_alerta = '';
if ($total_comparendos == 2) {
    $mensaje_alerta = '⚠️ <strong>ADVERTENCIA:</strong> Tienes 2 comparendos. Si recibes un tercero, serás citado a comité.';
} elseif ($total_comparendos >= 3) {
    $mensaje_alerta = '🚨 <strong>ALERTA:</strong> Tienes ' . $total_comparendos . ' comparendos. Debes presentarte a comité.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Aprendiz - SENATRACK</title>
</head>
<body>
    <h1>🎓 Dashboard APRENDIZ</h1>
    
    <div style="background: #e8f4fd; padding: 15px; margin: 20px 0;">
        <h3>Información del Aprendiz:</h3>
        <p><strong>Nombre:</strong> <?php echo $user_data['nombre_usuario']; ?></p>
        <p><strong>Email:</strong> <?php echo $user_data['correo_electronico']; ?></p>
        <?php if ($user_data['nombre_programa']): ?>
            <p><strong>Programa:</strong> <?php echo $user_data['nombre_programa']; ?></p>
            <p><strong>Ficha:</strong> <?php echo $user_data['numero_ficha']; ?></p>
        <?php endif; ?>
    </div>

    <?php if ($mensaje_alerta): ?>
        <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 5px solid #ffc107;">
            <?php echo $mensaje_alerta; ?>
        </div>
    <?php endif; ?>

    <h2>📋 Mis Comparendos</h2>
    <p><strong>Total de comparendos:</strong> <?php echo $total_comparendos; ?></p>
    
    <?php if ($comparendos_result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Tipo de Falta</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Evidencia</th>
            </tr>
            <?php while($comparendo = $comparendos_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo ucfirst($comparendo['tipo_falta']); ?></td>
                <td><?php echo $comparendo['descripcion']; ?></td>
                <td><?php echo $comparendo['fecha_creacion']; ?></td>
                <td>
                    <?php if ($comparendo['archivo_evidencia']): ?>
                        <a href="uploads/<?php echo $comparendo['archivo_evidencia']; ?>" target="_blank">📎 Ver evidencia</a>
                    <?php else: ?>
                        Sin evidencia
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tienes comparendos registrados.</p>
    <?php endif; ?>
    
    <br>
    <a href="Api/Login/cerrar_session.php">Cerrar Sesión</a>
    
    <?php $conn->close(); ?>
</body>
</html>