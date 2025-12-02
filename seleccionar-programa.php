<?php
session_start();
include 'Api/conexiones/conexion.php';

// Verificar que sea aprendiz
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'aprendiz') {
    header("Location: login.html");
    exit();
}

// Obtener programas disponibles
$programas_sql = "SELECT * FROM programas_formacion ORDER BY nombre_programa";
$programas_result = $conn->query($programas_sql);

// Procesar selección de programa
if ($_POST && isset($_POST['id_programa'])) {
    $id_programa = $_POST['id_programa'];
    $user_id = $_SESSION['user_id'];
    
    $update_sql = "UPDATE usuarios SET id_programa = '$id_programa' WHERE id_usuario = '$user_id'";
    if ($conn->query($update_sql)) {
        $mensaje = "✅ Programa seleccionado exitosamente";
    } else {
        $mensaje = "❌ Error al seleccionar programa: " . $conn->error;
    }
}

// Obtener programa actual del aprendiz
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT u.*, p.nombre_programa, p.numero_ficha 
             FROM usuarios u 
             LEFT JOIN programas_formacion p ON u.id_programa = p.id_programa 
             WHERE u.id_usuario = '$user_id'";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar Programa - SENATRACK</title>
</head>
<body>
    <h2>Seleccionar Programa de Formación</h2>
    
    <?php if (isset($mensaje)): ?>
        <p style="color: green;"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    
    <?php if ($user_data['id_programa']): ?>
            <h3>Programa Actual:</h3>
            <p><strong>Programa:</strong> <?php echo $user_data['nombre_programa']; ?></p>
            <p><strong>Ficha:</strong> <?php echo $user_data['numero_ficha']; ?></p>
        </div>
    <?php else: ?>
        <p>No has seleccionado un programa aún.</p>
    <?php endif; ?>
    
    <h3>Seleccionar Programa:</h3>
    <form method="POST">
        <select name="id_programa" required>
            <option value="">-- Selecciona un programa --</option>
            <?php while($programa = $programas_result->fetch_assoc()): ?>
            <option value="<?php echo $programa['id_programa']; ?>">
                <?php echo $programa['nombre_programa'] . " - Ficha: " . $programa['numero_ficha']; ?>
            </option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <button type="submit">Seleccionar Programa</button>
    </form>
    
    <br>
    <a href="dashboard-aprendiz.php">Volver al Dashboard</a>
    
    <?php $conn->close(); ?>
</body>
</html>