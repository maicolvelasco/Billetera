<?php
session_start();

// Verificar si la empresa está logueada
if (!isset($_SESSION['Nombre_Empresa'])) {
    header("Location: ../../index.php");
    exit();
}

$idEmpresaLogueada = $_SESSION['Id_Empresa'];

// Consulta para obtener la información de la empresa logueada
$sql = "SELECT Nombre, Usuario, Password, Foto FROM empresa WHERE Id_Empresa = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idEmpresaLogueada);
$stmt->execute();

// Obtener los datos
$stmt->bind_result($nombreEmpresa, $usuarioEmpresa, $passwordEmpresa, $fotoActual);
$stmt->fetch();

// Si no se encontró la empresa
if (empty($nombreEmpresa)) {
    $_SESSION['error'] = "Empresa no encontrada.";
    $stmt->close(); // Cerrar la consulta antes de salir
    header("Location: ../../index.php"); // Redirigir al login o página de error
    exit();
}

$stmt->close(); // Cerrar la consulta aquí para evitar el error de "Commands out of sync"

// Manejar la subida del archivo y actualización de la contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener la nueva contraseña del formulario
    $nuevaPassword = isset($_POST['passwordEmpresa']) ? trim($_POST['passwordEmpresa']) : '';

    // Validar y actualizar la contraseña
    if (!empty($nuevaPassword)) {
        // Actualizar la contraseña en la base de datos
        $stmtUpdatePassword = $conexion->prepare("UPDATE empresa SET Password = ? WHERE Id_Empresa = ?");
        if ($stmtUpdatePassword === false) {
            $_SESSION['error'] = "Error en la preparación de la consulta de contraseña: " . $conexion->error;
            header("Location: Perfil.php"); // Redirigir de vuelta a la página de perfil
            exit();
        }

        $stmtUpdatePassword->bind_param("si", $nuevaPassword, $idEmpresaLogueada);

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

        // Verificar si hay errores en la subida
        if ($foto["error"] === UPLOAD_ERR_OK) {
            // Validar el tipo de archivo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $foto["tmp_name"]);
            finfo_close($finfo);

            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mime, $tiposPermitidos)) {
                $_SESSION['error'] = "Tipo de archivo no permitido.";
                header("Location: Perfil.php"); // Redirigir de vuelta a la página de perfil
                exit();
            }

            // Validar el tamaño del archivo (máximo 2MB)
            $tamañoMaximo = 2 * 1024 * 1024; // 2MB
            if ($foto["size"] > $tamañoMaximo) {
                $_SESSION['error'] = "El archivo excede el tamaño máximo permitido de 2MB.";
                header("Location: Perfil.php"); // Redirigir de vuelta a la página de perfil
                exit();
            }

            // Generar un nombre único para la imagen
            $ext = strtolower(pathinfo($foto["name"], PATHINFO_EXTENSION));
            $nombreUnico = uniqid('foto_', true) . '.' . $ext;

            // Definir la ruta de sistema para guardar el archivo
            $uploadDir = __DIR__ . '/../../uploads/'; // Ruta absoluta en el servidor
            $rutaSistemaDestino = $uploadDir . $nombreUnico;

            // Crear la carpeta 'uploads' si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($foto["tmp_name"], $rutaSistemaDestino)) {
                // Opcional: Eliminar la foto antigua si no es la por defecto
                if (!empty($fotoActual) && $fotoActual !== 'sinfoto.png') {
                    $rutaFotoAntigua = $uploadDir . basename($fotoActual);
                    if (file_exists($rutaFotoAntigua)) {
                        unlink($rutaFotoAntigua);
                    }
                }

                // Guardar la nueva ruta de la foto en la base de datos
                $stmtUpdateFoto = $conexion->prepare("UPDATE empresa SET Foto = ? WHERE Id_Empresa = ?");
                if ($stmtUpdateFoto === false) {
                    $_SESSION['error'] = "Error en la preparación de la consulta de foto: " . $conexion->error;
                    header("Location: Perfil.php"); // Redirigir de vuelta a la página de perfil
                    exit();
                }

                $stmtUpdateFoto->bind_param("si", $nombreUnico, $idEmpresaLogueada);

                if ($stmtUpdateFoto->execute()) {
                    if ($stmtUpdateFoto->affected_rows > 0) {
                        $_SESSION['success'] = "Foto actualizada exitosamente.";
                    } else {
                        $_SESSION['error'] = "No se realizó ninguna actualización de la foto.";
                    }
                } else {
                    $_SESSION['error'] = "Error al actualizar la foto: " . $stmtUpdateFoto->error;
                }

                $stmtUpdateFoto->close();
            } else {
                $_SESSION['error'] = "Error al mover el archivo subido.";
            }
        } else {
            $_SESSION['error'] = "Error al subir la imagen. Código de error: " . $foto["error"];
        }
    }

    // Redirigir para actualizar la vista y evitar reenvío del formulario
    header("Location: Perfil.php"); // Ajusta la ruta según corresponda
    exit();
}

$conexion->close();
?>