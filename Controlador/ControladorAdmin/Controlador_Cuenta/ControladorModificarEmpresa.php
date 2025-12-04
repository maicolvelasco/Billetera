<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Función para obtener los datos de la empresa por ID
function obtenerEmpresa($conexion, $id)
{
    $sql = $conexion->prepare("SELECT Id_Empresa, Nombre, Usuario, Password, Foto FROM empresa WHERE Id_Empresa = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: Empresa.php");
        exit();
    }

    $sql->bind_param("i", $id);
    $sql->execute();

    // Usamos bind_result() para obtener los resultados
    $sql->bind_result($idEmpresa, $nombre, $usuario, $password, $foto);
    $empresa = [];

    if ($sql->fetch()) {
        // Asignamos los resultados al array $empresa
        $empresa = [
            'Id_Empresa' => $idEmpresa,
            'Nombre' => $nombre,
            'Usuario' => $usuario,
            'Password' => $password,
            'Foto' => $foto
        ];
    }

    $sql->close(); // Cerrar la consulta
    return $empresa;
}

// Verificar si el ID se ha pasado correctamente en la URL
if (isset($_GET['id'])) {
    $idEmpresa = intval($_GET['id']);  // Aseguramos que sea un entero
    $empresa = obtenerEmpresa($conexion, $idEmpresa);

    // Si no se encuentra la empresa, redirigir de vuelta a la lista con mensaje de error
    if (!$empresa) {
        $_SESSION['error'] = "Empresa no encontrada.";
        header("Location: Empresa.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ID de empresa no proporcionado.";
    header("Location: Empresa.php");
    exit();
}

// Si el formulario se ha enviado, manejar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['Nombre']);
    $usuario = trim($_POST['Usuario']);
    $password = trim($_POST['Password']);
    $foto = $empresa['Foto']; // Mantener la foto actual

    // Validar campos obligatorios
    if (empty($nombre) || empty($usuario)) {
        $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
        header("Location: Empresa.php");
        exit();
    }

    // Verificar si se subió una nueva foto
    if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $uploadFile = $uploadDir . basename($_FILES['Foto']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Validar el tipo de archivo (opcional pero recomendado)
        $validExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($imageFileType, $validExtensions)) {
            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
                $foto = $uploadFile; // Actualizar la foto si se subió una nueva

                // Opcional: Eliminar la foto anterior si no es la predeterminada
                if ($empresa['Foto'] != '../../src/sinfoto.png' && file_exists($empresa['Foto'])) {
                    unlink($empresa['Foto']); // Eliminar el archivo de la foto anterior
                }
            } else {
                $_SESSION['error'] = "Error al subir la imagen.";
                header("Location: Empresa.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Tipo de archivo de imagen no permitido. Solo se permiten JPG, JPEG, PNG y GIF.";
            header("Location: Empresa.php");
            exit();
        }
    }

    // **NUEVO**: Verificar si se ha proporcionado una nueva contraseña
    if (!empty($password)) {
        // Validar la nueva contraseña (ejemplo: mínimo 6 caracteres)
        if (strlen($password) < 6) {
            $_SESSION['error'] = "La nueva contraseña debe tener al menos 6 caracteres.";
            header("Location: Empresa.php");
            exit();
        }
    } else {
        // Si no se proporciona una nueva contraseña, mantener la actual
        $password = $empresa['Password'];
    }

    // Actualizar los datos en la base de datos
    $sql = $conexion->prepare("UPDATE empresa SET Nombre = ?, Usuario = ?, Password = ?, Foto = ? WHERE Id_Empresa = ?");
    if ($sql === false) {
        $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: Empresa.php");
        exit();
    }

    // Usar la contraseña (sin encriptar o actual) en la consulta
    $sql->bind_param("ssssi", $nombre, $usuario, $password, $foto, $idEmpresa);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Empresa actualizada exitosamente.";
        header("Location: Empresa.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al actualizar la empresa: " . $sql->error;
        header("Location: Empresa.php");
        exit();
    }
}
?>