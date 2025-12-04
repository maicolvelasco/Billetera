<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION["Nombre_Completo"])) {
    header("Location: ../../index.php");
    exit();
}

$nombreCompleto = $_SESSION["Nombre_Completo"];

// Obtener información del usuario
$user_query = "SELECT Nombre_Completo, CI FROM usuario WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($user_query);
$stmt->bind_param('i', $_SESSION["Id_Usuario"]);
$stmt->execute();
$stmt->store_result(); // Almacenar el resultado para verificar filas

// Definir variables para bind_result
$nombre_usuario = null;
$ci_usuario = null;

// Usar bind_result para obtener los datos
$stmt->bind_result($nombre_usuario, $ci_usuario);
$stmt->fetch();

$user_info = [
    'Nombre_Completo' => $nombre_usuario,
    'CI' => $ci_usuario
];

$user_name = $user_info['Nombre_Completo'];
$user_ci = $user_info['CI'];

// Generar código QR
include '../../phpqrcode/qrlib.php';
QRcode::png($user_name . ' - ' . $user_ci, '../../VistaUsuario/qr/qr_code.png', 'L', 4, 2);

// Generar un nuevo PIN aleatorio
function generarPIN($longitud = 6) {
    return str_pad(mt_rand(0, 999999), $longitud, '0', STR_PAD_LEFT);
}

$conexion->begin_transaction(); // Iniciar transacción

try {
    // 1. Eliminar el PIN anterior del usuario
    $delete_pin_query = "DELETE FROM user_pins WHERE Id_Usuario = ?";
    $stmt_delete = $conexion->prepare($delete_pin_query);
    $stmt_delete->bind_param('i', $_SESSION["Id_Usuario"]);
    $stmt_delete->execute();

    // 2. Generar un nuevo PIN
    $nuevo_pin = generarPIN();

    // 3. Insertar el nuevo PIN en la base de datos
    $insert_pin_query = "INSERT INTO user_pins (Id_Usuario, PIN) VALUES (?, ?)";
    $stmt_insert = $conexion->prepare($insert_pin_query);
    $stmt_insert->bind_param('is', $_SESSION["Id_Usuario"], $nuevo_pin);
    $stmt_insert->execute();

    // Si todo está bien, confirmar la transacción
    $conexion->commit();

} catch (Exception $e) {
    // En caso de error, deshacer la transacción
    $conexion->rollback();
    echo "Error: " . $e->getMessage();
    exit; // Salir para evitar seguir con el código
}

// Obtener el PIN actual del usuario
$current_pin_query = "SELECT PIN FROM user_pins WHERE Id_Usuario = ?";
$stmt_current_pin = $conexion->prepare($current_pin_query);
$stmt_current_pin->bind_param('i', $_SESSION["Id_Usuario"]);
$stmt_current_pin->execute();
$stmt_current_pin->store_result(); // Almacenar el resultado para verificar filas

// Definir variable para bind_result
$current_pin = null;

// Usar bind_result para obtener el PIN
$stmt_current_pin->bind_result($current_pin);
$stmt_current_pin->fetch();

if (is_null($current_pin)) {
    echo "No se encontró un PIN para el usuario.";
} else {
    echo "El PIN actual es: " . htmlspecialchars($current_pin);
}

$stmt->close();
$stmt_delete->close();
$stmt_insert->close();
$stmt_current_pin->close();
$conexion->close();
?>