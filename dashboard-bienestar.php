<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar que sea bienestar
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'bienestar') {
    header("Location: login.html");
    exit();
}

// Obtener datos del bienestar
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM usuarios WHERE id_usuario = '$user_id'";
$result = $conn->query($sql);
$user_data = $result->fetch_assoc();

// Obtener lista de aprendices
$aprendices_sql = "SELECT * FROM usuarios WHERE tipo_usuario = 'aprendiz' ORDER BY nombre_usuario";
$aprendices_result = $conn->query($aprendices_sql);

// Obtener comparendos recientes
$comparendos_sql = "SELECT c.*, u.nombre_usuario as aprendiz_nombre 
                    FROM comparendos c 
                    JOIN usuarios u ON c.id_aprendiz = u.id_usuario 
                    WHERE c.id_bienestar = '$user_id' 
                    ORDER BY c.fecha_creacion DESC 
                    LIMIT 5";
$comparendos_result = $conn->query($comparendos_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bienestar - SENATRACK</title>
</head>
<body>
    <h1>🏥 Dashboard BIENESTAR</h1>
    
    <div style="background: #e8f4fd; padding: 15px; margin: 20px 0;">
        <h3>Información del Personal:</h3>
        <p><strong>Nombre:</strong> <?php echo $user_data['nombre_usuario']; ?></p>
        <p><strong>Email:</strong> <?php echo $user_data['correo_electronico']; ?></p>
    </div>

    <h2>📝 Registrar Comparendo</h2>
    <form action="Api/Backend/registrar_comparendo.php" method="POST" enctype="multipart/form-data">
        <div>
            <label>Aprendiz:</label>
            <select name="id_aprendiz" required>
                <option value="">-- Seleccionar Aprendiz --</option>
                <?php while($aprendiz = $aprendices_result->fetch_assoc()): ?>
                <option value="<?php echo $aprendiz['id_usuario']; ?>">
                    <?php echo $aprendiz['nombre_usuario'] . ' ' . $aprendiz['apellido_usuario']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div>
            <label>Tipo de Falta:</label>
            <select name="tipo_falta" required>
                <option value="leve">Falta Leve</option>
                <option value="media">Falta Media</option>
                <option value="grave">Falta Grave</option>
            </select>
        </div>
        
        <div>
            <label>Descripción de la Falta:</label><br>
            <textarea name="descripcion" rows="4" cols="50" required></textarea>
        </div>
        
        <div>
            <label>Evidencia (opcional):</label>
            <input type="file" name="evidencia">
        </div>
        
        <br>
        <button type="submit">📄 Registrar Comparendo</button>
    </form>

    <h2>📋 Comparendos Recientes</h2>
    <?php if ($comparendos_result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Aprendiz</th>
                <th>Tipo Falta</th>
                <th>Descripción</th>
                <th>Fecha</th>
            </tr>
            <?php while($comparendo = $comparendos_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $comparendo['aprendiz_nombre']; ?></td>
                <td><?php echo ucfirst($comparendo['tipo_falta']); ?></td>
                <td><?php echo $comparendo['descripcion']; ?></td>
                <td><?php echo $comparendo['fecha_creacion']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay comparendos registrados.</p>
    <?php endif; ?>
    
    <br>
    <a href="Api/Login/cerrar_session.php">Cerrar Sesión</a>
    
    <?php $conn->close(); ?>
</body>
</html>