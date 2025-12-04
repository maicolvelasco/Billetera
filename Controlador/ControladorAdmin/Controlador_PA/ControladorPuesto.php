<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Listar los puestos
function listarPuestos($conexion) {
    $sql = $conexion->query("SELECT * FROM puesto");
    return $sql;
}