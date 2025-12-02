<?php
include 'Api/conexiones/conexion.php';

// Verificar si la tabla programas_formacion existe
$result = $conn->query("SHOW TABLES LIKE 'programas_formacion'");
if ($result->num_rows > 0) {
    echo "✅ La tabla 'programas_formacion' EXISTE<br><br>";
    
    // Mostrar estructura de la tabla
    $structure = $conn->query("DESCRIBE programas_formacion");
    echo "<h3>Estructura de la tabla:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ La tabla 'programas_formacion' NO EXISTE";
}

$conn->close();
?>