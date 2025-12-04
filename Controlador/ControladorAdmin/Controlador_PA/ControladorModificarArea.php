<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Función para obtener los datos del área por ID
function obtenerArea($conexion, $id)
{
    $sql = $conexion->prepare("SELECT Id_Area, Nombres FROM area_trabajo WHERE Id_Area = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: Area.php");
        exit();
    }

    $sql->bind_param("i", $id);
    $sql->execute();

    // Usamos bind_result() en lugar de get_result()
    $sql->bind_result($idArea, $nombre);
    $area = [];

    if ($sql->fetch()) {
        // Asignamos los resultados al array $area
        $area = [
            'Id_Area' => $idArea,
            'Nombres' => $nombre
        ];
    }

    $sql->close(); // Cerramos la consulta
    return $area;
}

// Verificar si el ID se ha pasado correctamente en la URL
if (isset($_GET['id'])) {
    $idArea = intval($_GET['id']);
    $area = obtenerArea($conexion, $idArea);

    // Si no se encuentra el área, redirigir de vuelta a la lista con mensaje de error
    if (!$area) {
        $_SESSION['error'] = "Área no encontrada.";
        header("Location: Area.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ID de área no proporcionado.";
    header("Location: Area.php");
    exit();
}

// Si el formulario se ha enviado, manejar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['Nombres']);

    // Validar el campo obligatorio
    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del área es obligatorio.";
        header("Location: Area.php");
        exit();
    }

    // Actualizar los datos en la base de datos
    $sql = $conexion->prepare("UPDATE area_trabajo SET Nombres = ? WHERE Id_Area = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: Area.php");
        exit();
    }

    $sql->bind_param("si", $nombre, $idArea);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Área de trabajo actualizada exitosamente.";
        header("Location: Area.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al actualizar el área de trabajo: " . $sql->error;
        header("Location: Area.php");
        exit();
    }
}
?>