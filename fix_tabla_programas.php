<?php
include 'Api/conexiones/conexion.php';

echo "<h2>Solucionando tabla programas_formacion...</h2>";

// Verificar si la tabla existe
$table_check = $conn->query("SHOW TABLES LIKE 'programas_formacion'");

if ($table_check->num_rows > 0) {
    echo "✅ La tabla existe<br>";
    
    // Verificar si la columna numero_ficha existe
    $column_check = $conn->query("SHOW COLUMNS FROM programas_formacion LIKE 'numero_ficha'");
    
    if ($column_check->num_rows > 0) {
        echo "✅ La columna numero_ficha existe<br>";
        
        // Eliminar la tabla y recrearla
        $conn->query("DROP TABLE programas_formacion");
        echo "🗑️ Tabla eliminada<br>";
    }
}

// Crear la tabla correctamente
$sql = "CREATE TABLE programas_formacion (
    id_programa INT AUTO_INCREMENT PRIMARY KEY,
    nombre_programa VARCHAR(200) NOT NULL,
    numero_ficha VARCHAR(20) NOT NULL UNIQUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Tabla creada correctamente<br>";
    
    // Insertar algunos programas de ejemplo
    $programas = [
        ['Análisis y Desarrollo de Software', '2679394'],
        ['Producción de Especies Menores', '2680123'],
        ['Gestión Administrativa', '2678456']
    ];
    
    foreach ($programas as $programa) {
        $insert_sql = "INSERT INTO programas_formacion (nombre_programa, numero_ficha) 
                      VALUES ('$programa[0]', '$programa[1]')";
        if ($conn->query($insert_sql)) {
            echo "✅ Programa agregado: $programa[0] - $programa[1]<br>";
        }
    }
    
    echo "<h3>🎉 Tabla lista para usar</h3>";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();

echo "<br><br>";
echo "<a href='agregar-programa.html'>Ir a Agregar Programa</a><br>";
echo "<a href='dashboard-admin.php'>Volver al Dashboard</a>";
?>