<?php
echo "<h2>🔍 Verificando archivos dashboard</h2>";

$archivos = [
    'dashboard-admin.php',
    'dashboard-bienestar.php', 
    'dashboard-aprendiz.php'
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo - EXISTE<br>";
    } else {
        echo "❌ $archivo - NO EXISTE<br>";
    }
}
?>