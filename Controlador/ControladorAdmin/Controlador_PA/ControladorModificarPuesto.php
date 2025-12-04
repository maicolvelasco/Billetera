<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Funci車n para obtener los datos del puesto por ID
function obtenerPuesto($conexion, $id)
{
    $sql = $conexion->prepare("SELECT Id_Puesto, Nombre FROM puesto WHERE Id_Puesto = ?");
    $sql->bind_param("i", $id);
    $sql->execute();

    // Usar bind_result() para obtener los resultados
    $sql->bind_result($idPuesto, $nombre);
    $puesto = [];

    if ($sql->fetch()) {
        // Asignar los resultados al array $puesto
        $puesto = [
            'Id_Puesto' => $idPuesto,
            'Nombre' => $nombre
        ];
    }

    $sql->close(); // Cerrar la consulta
    return $puesto;
}

// Verificar si el ID se ha pasado correctamente en la URL
if (isset($_GET['id'])) {
    $idPuesto = intval($_GET['id']);
    $puesto = obtenerPuesto($conexion, $idPuesto);

    // Si no se encuentra el puesto, redirigir de vuelta a la lista con mensaje de error
    if (!$puesto) {
        $_SESSION['error'] = "Puesto no encontrado.";
        header("Location: Puesto.php");
        exit();
    }
} else {
    $_SESSION['error'] = "ID de puesto no proporcionado.";
    header("Location: Puesto.php");
    exit();
}

// Si el formulario se ha enviado, manejar la actualizaci車n de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['Nombre'];

    // Validar el campo obligatorio
    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del puesto es obligatorio.";
        header("Location: Puesto.php");
        exit();
    }

    // Actualizar los datos en la base de datos
    $sql = $conexion->prepare("UPDATE puesto SET Nombre = ? WHERE Id_Puesto = ?");
    $sql->bind_param("si", $nombre, $idPuesto);

    if ($sql->execute()) {
        // Establecer mensaje de éxito en la sesión
        $_SESSION['success'] = "Puesto actualizado exitosamente.";
        header("Location: Puesto.php");
        exit();
    } else {
        // Establecer mensaje de error en la sesión
        $_SESSION['error'] = "Error al actualizar el puesto.";
        header("Location: Puesto.php");
        exit();
    }
}
?>