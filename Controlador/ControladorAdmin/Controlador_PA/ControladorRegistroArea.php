<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

function registrarArea($conexion) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
        $nombre = trim($_POST['Nombres']);

        // Validar el campo obligatorio
        if (empty($nombre)) {
            $_SESSION['error'] = "El nombre del área es obligatorio.";
            header("Location: Area.php"); // Redirigir de vuelta a la lista de áreas
            exit();
        }

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO area_trabajo (Nombres) VALUES (?)";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt === false) {
            $_SESSION['error'] = "Error al preparar la consulta: " . $conexion->error;
            header("Location: Area.php"); // Redirigir de vuelta a la lista de áreas
            exit();
        }

        $stmt->bind_param("s", $nombre);

        if ($stmt->execute()) {
            // Establecer mensaje de éxito en la sesión
            $_SESSION['success'] = "Área de trabajo registrada exitosamente.";
            header("Location: Area.php"); // Redirigir de vuelta a la lista de áreas
            exit();
        } else {
            // Establecer mensaje de error en la sesión
            $_SESSION['error'] = "Error al registrar el área de trabajo: " . $stmt->error;
            header("Location: Area.php"); // Redirigir de vuelta a la lista de áreas
            exit();
        }
    }
}

// Manejar el registro del área
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnregistrar'])) {
    registrarArea($conexion); // Llama a la función de registrar área
}
?>