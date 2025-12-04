<?php
session_start();
include "../../../Conexion/conexion.php"; // Ajusta la ruta si es necesario

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../../index.php");
    exit();
}

// Verificar si se ha recibido el ID de la empresa para eliminar
if (isset($_GET['id'])) {
    $idEmpresa = intval($_GET['id']); // Aseguramos que sea un entero

    // Preparar la consulta SQL para eliminar la empresa
    $sql = $conexion->prepare("DELETE FROM empresa WHERE Id_Empresa = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: ../../../VistaAdministrador/VistaAdminEmpresa/Empresa.php");
        exit();
    }

    $sql->bind_param("i", $idEmpresa);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Empresa eliminada exitosamente.";
        header("Location: ../../../VistaAdministrador/VistaAdminEmpresa/Empresa.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al eliminar la empresa: " . $sql->error;
        header("Location: ../../../VistaAdministrador/VistaAdminEmpresa/Empresa.php");
        exit();
    }
} else {
    // Si no se recibe un ID válido, redirigir a la lista de empresas con mensaje de error
    $_SESSION['error'] = "ID de empresa no proporcionado o inválido.";
    header("Location: ../../../VistaAdministrador/VistaAdminEmpresa/Empresa.php");
    exit();
}
?>