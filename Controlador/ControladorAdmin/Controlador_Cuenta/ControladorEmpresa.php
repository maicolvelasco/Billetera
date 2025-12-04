<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php"); // Cambia la ruta a la página de login si es necesario
    exit();
}
// Manejar la lógica de visualización y listado de empresas
function listarEmpresas($conexion)
{
    $sql = $conexion->query("SELECT * FROM empresa");
    return $sql;
}

// Manejar la lógica de registrar empresas
function registrarEmpresa($conexion)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
        $nombre = $_POST['Nombre'];
        $usuario = $_POST['Usuario'];
        $password = $_POST['Password'];
        $foto = '';

        // Verificar si se subió una foto
        if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../uploads/';
            $uploadFile = $uploadDir . basename($_FILES['Foto']['name']);
            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($_FILES['Foto']['tmp_name'], $uploadFile)) {
                $foto = $uploadFile; // Guardar la ruta de la foto
            } else {
                echo "Error al subir la imagen.";
            }
        }

        // Si no se subió foto, se asigna una predeterminada
        if (empty($foto)) {
            $foto = '../../src/sinfoto.png';
        }

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO empresa (Nombre, Usuario, Password, Foto) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $usuario, $password, $foto);

        if ($stmt->execute()) {
            header("Location: Empresa.php"); // Redirigir de vuelta a la lista de empresas
        } else {
            echo "Error al registrar la empresa.";
        }
    }
}