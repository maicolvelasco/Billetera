<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

function obtenerUsuario($conexion, $id)
{
    $sql = $conexion->prepare("SELECT Id_Usuario, Nombre_Completo, Codigo_empleado, CI, Fecha_Nacimiento, Correo_Electronico, Telefono, Password, Rol, Area, Puesto, Estado, Foto FROM usuario WHERE Id_Usuario = ?");
    $sql->bind_param("i", $id);
    $sql->execute();

    // Usar bind_result() para obtener los resultados
    $sql->bind_result($idUsuario, $nombreCompleto, $codigoEmpleado, $ci, $fechaNacimiento, $correoElectronico, $telefono, $password, $rol, $area, $puesto, $estado, $foto);
    $usuario = [];

    if ($sql->fetch()) {
        // Asignar los resultados al array $usuario
        $usuario = [
            'Id_Usuario' => $idUsuario,
            'Nombre_Completo' => $nombreCompleto,
            'Codigo_empleado' => $codigoEmpleado,
            'CI' => $ci,
            'Fecha_Nacimiento' => $fechaNacimiento,
            'Correo_Electronico' => $correoElectronico,
            'Telefono' => $telefono,
            'Password' => $password,
            'Rol' => $rol,
            'Area' => $area,
            'Puesto' => $puesto,
            'Estado' => $estado,
            'Foto' => $foto
        ];
    }

    $sql->close(); // Cerrar la consulta
    return $usuario;
}

// Verificar si el ID se ha pasado correctamente en la URL
if (isset($_GET['id'])) {
    $idUsuario = intval($_GET['id']);
    $usuario = obtenerUsuario($conexion, $idUsuario);

    // Si no se encuentra el usuario, redirigir de vuelta a la lista con mensaje de error
    if (!$usuario) {
        $_SESSION['error'] = "Usuario no encontrado.";
        header("Location: Usuario.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ID de usuario no proporcionado.";
    header("Location: Usuario.php");
    exit();
}

// Si el formulario se ha enviado, manejar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['Nombre_Completo'];
    $codigoEmpleado = $_POST['Codigo_empleado'];
    $ci = $_POST['CI'];
    $fechaNacimiento = $_POST['Fecha_Nacimiento'];
    $correoElectronico = $_POST['Correo_Electronico'];
    $telefono = $_POST['Telefono'];
    $password = $_POST['Password'];
    $rol = $_POST['Rol'];
    $area = $_POST['Area'];
    $puesto = $_POST['Puesto'];
    $estado = $_POST['Estado']; // Obtener el estado del formulario
    $foto = $usuario['Foto']; // Mantener la foto actual

    // Verificar si se subió una nueva foto
    if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $uploadFile = $uploadDir . basename($_FILES['Foto']['name']);
        if (move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
            $foto = $uploadFile; // Actualizar la foto si se subió una nueva
        } else {
            // Reemplazar el echo por una notificación de error y redirigir
            $_SESSION['error'] = "Error al subir la imagen.";
            header("Location: Usuario.php");
            exit();
        }
    }

    // Verificar si se ingresó una nueva contraseña
    if (!empty($password)) {
        $plainPassword = $password; // Usar la nueva contraseña sin encriptar
    } else {
        $plainPassword = $usuario['Password']; // Mantener la contraseña actual
    }

    // Actualizar los datos en la base de datos
    $sql = $conexion->prepare("UPDATE usuario SET Rol = ?, Area = ?, Puesto = ?, Codigo_empleado = ?, CI = ?, Nombre_Completo = ?, Estado = ?, Fecha_Nacimiento = ?, Correo_Electronico = ?, Telefono = ?, Password = ?, Foto = ? WHERE Id_Usuario = ?");
    $sql->bind_param("iiiissississi", $rol, $area, $puesto, $codigoEmpleado, $ci, $nombre, $estado, $fechaNacimiento, $correoElectronico, $telefono, $plainPassword, $foto, $idUsuario);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Usuario actualizado exitosamente.";
        header("Location: Usuario.php");
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al actualizar el usuario.";
        header("Location: Usuario.php");
    }
}
?>