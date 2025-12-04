<?php
session_start();
include "../../../Conexion/conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../../index.php");
    exit();
}

// Verificar si se ha recibido el ID del área para eliminar
if (isset($_GET['id'])) {
    $idArea = intval($_GET['id']);

    // Preparar la consulta SQL para eliminar el área
    $sql = $conexion->prepare("DELETE FROM area_trabajo WHERE Id_Area = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Area.php");
        exit();
    }

    $sql->bind_param("i", $idArea);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Área de trabajo eliminada exitosamente.";
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Area.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al eliminar el área de trabajo: " . $sql->error;
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Area.php");
        exit();
    }
} else {
    // Si no se recibe un ID válido, redirigir a la lista de áreas con mensaje de error
    $_SESSION['error'] = "ID de área no proporcionado o inválido.";
    header("Location: ../../../VistaAdministrador/VistaAdminPA/Area.php");
    exit();
}
?>