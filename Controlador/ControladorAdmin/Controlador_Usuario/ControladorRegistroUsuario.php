<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

function registrarUsuario($conexion)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
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
        $estado = $_POST['Estado'];
        $foto = '';

        // Verificar si se subió una foto
        if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . basename($_FILES['Foto']['name']);
            if (move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
                $foto = $uploadFile;
            } else {
                $_SESSION['error'] = "Error al subir la imagen.";
                header("Location: Usuario.php");
                exit();
            }
        }

        // Si no se subió foto, se asigna una predeterminada
        if (empty($foto)) {
            $foto = '../../src/sinfoto.png';
        }

        // Usar la contraseña sin encriptar
        $plainPassword = $password;

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO usuario (Rol, Area, Puesto, Codigo_empleado, CI, Nombre_Completo, Estado, Fecha_Nacimiento, Correo_Electronico, Telefono, Password, Foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiiissississ", $rol, $area, $puesto, $codigoEmpleado, $ci, $nombre, $estado, $fechaNacimiento, $correoElectronico, $telefono, $plainPassword, $foto);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Usuario registrado exitosamente.";
            header("Location: Usuario.php");
        } else {
            $_SESSION['error'] = "Error al registrar el usuario.";
            header("Location: Usuario.php");
        }
    }
}

// Manejar el registro de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
    registrarUsuario($conexion);
}
?>