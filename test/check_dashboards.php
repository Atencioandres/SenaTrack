<?php
$dashboards = ['dashboard-admin.html', 'dashboard-aprendiz.html', 'dashboard-bienestar.html', 'dashboard.html'];

echo "<h3>Verificando archivos dashboard:</h3>";
foreach ($dashboards as $dashboard) {
    if (file_exists("../$dashboard")) {
        echo "✅ $dashboard - EXISTE<br>";
    } else {
        echo "❌ $dashboard - NO EXISTE<br>";
    }
}
?>