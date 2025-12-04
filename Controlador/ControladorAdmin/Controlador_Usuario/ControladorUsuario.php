<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Función para listar usuarios según el filtro de estado
function listarUsuariosFiltrados($conexion, $filter) {
    // Consulta SQL base
    $sql_base = "SELECT usuario.*, roles.Nombres, area_trabajo.Nombres AS Nombre_Area, puesto.Nombre AS Nombre_Puesto 
                 FROM usuario 
                 INNER JOIN roles ON usuario.Rol = roles.Id_Rol
                 INNER JOIN area_trabajo ON usuario.Area = area_trabajo.Id_Area
                 INNER JOIN puesto ON usuario.Puesto = puesto.Id_Puesto";

    // Modificar la consulta según el filtro seleccionado
    switch ($filter) {
        case 'active':
            $sql_base .= " WHERE usuario.Estado = 1"; // Mostrar solo usuarios activos
            break;
        case 'inactive':
            $sql_base .= " WHERE usuario.Estado = 0"; // Mostrar solo usuarios inactivos
            break;
        case 'all':
        default:
            // No se añade ninguna cláusula WHERE adicional
            break;
    }

    $sql = $conexion->query($sql_base);
    return $sql;
}