<?php
session_start();
include '../conexiones/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../../login.html");
    exit();
}

if ($_POST) {
    $nombre_programa = $_POST['nombre_programa'];
    $numero_ficha = $_POST['numero_ficha'];
    
    // Insertar programa en la base de datos
    $sql = "INSERT INTO programas_formacion (nombre_programa, numero_ficha) 
            VALUES ('$nombre_programa', '$numero_ficha')";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Programa agregado exitosamente";
        echo "<br><br>";
        echo "<strong>Programa:</strong> $nombre_programa<br>";
        echo "<strong>Ficha:</strong> $numero_ficha<br>";
        echo "<br>";
        echo "<a href='../../agregar-programa.html'>Agregar otro programa</a><br>";
        echo "<a href='../../dashboard-admin.php'>Volver al Dashboard</a>";
    } else {
        echo "❌ Error al agregar programa: " . $conn->error;
    }
} else {
    echo "❌ No se recibieron datos";
}

$conn->close();
?>