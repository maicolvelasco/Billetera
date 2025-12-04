<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php"); 
    exit();
}

// Listar las áreas de trabajo
function listarAreas($conexion) {
    $sql = $conexion->query("SELECT * FROM area_trabajo");
    return $sql;
}