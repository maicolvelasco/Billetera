<?php
session_start();
include "../../../Conexion/conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../../index.php");
    exit();
}

// Verificar si se ha recibido el ID del puesto para eliminar
if (isset($_GET['id'])) {
    $idPuesto = intval($_GET['id']);

    // Preparar la consulta SQL para eliminar el puesto
    $sql = $conexion->prepare("DELETE FROM puesto WHERE Id_Puesto = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Puesto.php");
        exit();
    }

    $sql->bind_param("i", $idPuesto);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Puesto eliminado exitosamente.";
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Puesto.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al eliminar el puesto: " . $sql->error;
        header("Location: ../../../VistaAdministrador/VistaAdminPA/Puesto.php");
        exit();
    }
} else {
    // Si no se recibe un ID válido, redirigir a la lista de puestos con mensaje de error
    $_SESSION['error'] = "ID de puesto no proporcionado o inválido.";
    header("Location: ../../../VistaAdministrador/VistaAdminPA/Puesto.php");
    exit();
}
?>