<?php
session_start();
include "../../../Conexion/conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../../index.php");
    exit();
}

// Verificar si se ha recibido el ID del usuario para eliminar
if (isset($_GET['id'])) {
    $idUsuario = intval($_GET['id']);

    // Preparar la consulta SQL para eliminar el usuario
    $sql = $conexion->prepare("DELETE FROM usuario WHERE Id_Usuario = ?");
    $sql->bind_param("i", $idUsuario);

    if ($sql->execute()) {
        // Opcional: Eliminar la foto del usuario si existe y no es la predeterminada
        // Primero, obtener la ruta de la foto
        $sqlFoto = $conexion->prepare("SELECT Foto FROM usuario WHERE Id_Usuario = ?");
        $sqlFoto->bind_param("i", $idUsuario);
        $sqlFoto->execute();
        $sqlFoto->bind_result($fotoUsuario);
        if ($sqlFoto->fetch()) {
            if ($fotoUsuario && $fotoUsuario != '../../src/sinfoto.png' && file_exists($fotoUsuario)) {
                unlink($fotoUsuario); // Eliminar el archivo de la foto
            }
        }
        $sqlFoto->close();

        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Usuario eliminado exitosamente.";
        header("Location: ../../../VistaAdministrador/VistaAdminUsuario/Usuario.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al eliminar el usuario.";
        header("Location: ../../../VistaAdministrador/VistaAdminUsuario/Usuario.php");
        exit();
    }
} else {
    // Si no se recibe un ID válido, redirigir a la lista de usuarios con mensaje de error
    $_SESSION['error'] = "ID de usuario no proporcionado o inválido.";
    header("Location: ../../../VistaAdministrador/VistaAdminUsuario/Usuario.php");
    exit();
}
?>