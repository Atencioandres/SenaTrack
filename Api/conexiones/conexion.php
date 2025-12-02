<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "senatrack_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "senatrack_db";  // Nombre corregido

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>