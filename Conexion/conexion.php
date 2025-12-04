<?php
$host = 'localhost';
$username = 'tuusuario';
$password = 'tucontraseña'; // Comillas simples para evitar interpretación de variables
$database = "tubasededatos";
$port = 3306;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $username, $password, $database, $port);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Error en la conexión: " . $e->getMessage());
    die("Conexión fallida. Inténtalo más tarde.");
}
?>