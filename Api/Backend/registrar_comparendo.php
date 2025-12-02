<?php
session_start();
include '../conexiones/conexion.php';

// Verificar que sea bienestar
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'bienestar') {
    header("Location: ../../login.html");
    exit();
}

if ($_POST) {
    $id_aprendiz = $_POST['id_aprendiz'];
    $tipo_falta = $_POST['tipo_falta'];
    $descripcion = $_POST['descripcion'];
    $id_bienestar = $_SESSION['user_id'];
    
    // Manejar archivo de evidencia
    $archivo_nombre = null;
    if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === 0) {
        $archivo_tmp = $_FILES['evidencia']['tmp_name'];
        $archivo_nombre = 'evidencia_' . time() . '_' . $_FILES['evidencia']['name'];
        $upload_dir = '../../uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        move_uploaded_file($archivo_tmp, $upload_dir . $archivo_nombre);
    }
    
    // Insertar comparendo
    $sql = "INSERT INTO comparendos (id_aprendiz, id_bienestar, tipo_falta, descripcion, archivo_evidencia) 
            VALUES ('$id_aprendiz', '$id_bienestar', '$tipo_falta', '$descripcion', '$archivo_nombre')";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Comparendo registrado exitosamente";
        echo "<br><a href='../../dashboard-bienestar.php'>Volver al Dashboard</a>";
    } else {
        echo "❌ Error al registrar comparendo: " . $conn->error;
    }
} else {
    echo "❌ No se recibieron datos";
}

$conn->close();
?>