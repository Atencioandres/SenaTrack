<?php
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Ruta corregida: " . $_SERVER['DOCUMENT_ROOT'] . '/Api/conexiones/conexion.php' . "<br>";
echo "¿Existe el archivo? " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/Api/conexiones/conexion.php') ? 'SÍ' : 'NO');
?>