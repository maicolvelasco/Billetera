<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

$idUsuarioLogueado = $_SESSION['Id_Usuario'];

// Consulta para obtener la información del usuario logueado
$sql = "SELECT Nombre_Completo, Codigo_empleado, Password, Foto FROM usuario WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idUsuarioLogueado);
$stmt->execute();

// Usar bind_result() para obtener los datos
$stmt->bind_result($nombreCompleto, $codigoEmpleado, $passwordActual, $fotoActual);
$stmt->fetch();

// Si no se encontró al usuario
if (empty($nombreCompleto)) {
    $_SESSION['error'] = "Usuario no encontrado.";
    $stmt->close(); // Cerrar la consulta antes de salir
    header("Location: ../../index.php"); // Redirigir al login o página de error
    exit();
}

$stmt->close(); // Cerrar la consulta aquí para evitar el error de "Commands out of sync"

// Manejar la subida del archivo y actualización de la contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener la nueva contraseña del formulario
    $nuevaPassword = isset($_POST['passwordUsuario']) ? trim($_POST['passwordUsuario']) : '';

    // Verificar si el usuario modificó la contraseña
    if (!empty($nuevaPassword) && $nuevaPassword !== $passwordActual) {
        // Validar la contraseña (ejemplo: mínimo 6 caracteres)
        if (strlen($nuevaPassword) < 6) {
            $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
            header("Location: Perfil.php");
            exit();
        }

        // Actualizar la contraseña en la base de datos sin encriptarla
        $stmtUpdatePassword = $conexion->prepare("UPDATE usuario SET Password = ? WHERE Id_Usuario = ?");
        if ($stmtUpdatePassword === false) {
            $_SESSION['error'] = "Error en la preparación de la consulta de contraseña: " . $conexion->error;
            header("Location: Perfil.php");
            exit();
        }

        // Usar la contraseña sin encriptar en la consulta
        $stmtUpdatePassword->bind_param("si", $nuevaPassword, $idUsuarioLogueado);

        if ($stmtUpdatePassword->execute()) {
            if ($stmtUpdatePassword->affected_rows > 0) {
                $_SESSION['success'] = "Contraseña actualizada exitosamente.";
            } else {
                $_SESSION['error'] = "No se realizó ninguna actualización de la contraseña.";
            }
        } else {
            $_SESSION['error'] = "Error al actualizar la contraseña: " . $stmtUpdatePassword->error;
        }

        $stmtUpdatePassword->close();
    }

    // Manejar la subida de la imagen si se seleccionó un archivo
    if (isset($_FILES["profile-upload"]) && $_FILES["profile-upload"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $foto = $_FILES["profile-upload"];

        // Resto del código para la imagen permanece igual...
    }

    // Redirigir para actualizar la vista y evitar reenvío del formulario
    header("Location: Perfil.php");
    exit();
}

$conexion->close();
?>