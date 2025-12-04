<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php"); // Cambia la ruta a la página de login si es necesario
    exit();
}

function registrarEmpresa($conexion)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
        $nombre = trim($_POST['Nombre']);
        $usuario = trim($_POST['Usuario']);
        $password = trim($_POST['Password']);
        $foto = '';

        // Validar campos obligatorios
        if (empty($nombre) || empty($usuario) || empty($password)) {
            $_SESSION['error'] = "Por favor, completa todos los campos obligatorios.";
            header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
            exit();
        }

        // Verificar si se subió una foto
        if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . basename($_FILES['Foto']['name']);
            $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

            // Validar el tipo de archivo (opcional pero recomendado)
            $validExtensions = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($imageFileType, $validExtensions)) {
                // Mover el archivo a la carpeta de destino
                if (move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
                    $foto = $uploadFile; // Guardar la ruta de la foto
                } else {
                    $_SESSION['error'] = "Error al subir la imagen.";
                    header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
                    exit();
                }
            } else {
                $_SESSION['error'] = "Tipo de archivo de imagen no permitido. Solo se permiten JPG, JPEG, PNG y GIF.";
                header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
                exit();
            }
        }

        // Si no se subió foto, se asigna una predeterminada
        if (empty($foto)) {
            $foto = '../../src/sinfoto.png';
        }

        // Insertar los datos en la base de datos sin encriptar la contraseña
        $sql = "INSERT INTO empresa (Nombre, Usuario, Password, Foto) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);

        if ($stmt === false) {
            $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
            header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
            exit();
        }

        // Usar la contraseña sin encriptar en la consulta
        $stmt->bind_param("ssss", $nombre, $usuario, $password, $foto);

        if ($stmt->execute()) {
            // Establecer mensaje de éxito en la sesión
            $_SESSION['success'] = "Empresa registrada exitosamente.";
            header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
            exit();
        } else {
            // Establecer mensaje de error en la sesión
            $_SESSION['error'] = "Error al registrar la empresa: " . $stmt->error;
            header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
            exit();
        }
    }
}

// Manejar el registro de empresa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
    registrarEmpresa($conexion); // Llama a la función de registrar empresa
}
?>