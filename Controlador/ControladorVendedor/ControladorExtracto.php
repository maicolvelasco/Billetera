<?php
session_start();
if (!isset($_SESSION["Nombre_Empresa"])) {
    header("location: ../../index.php");
    exit;
}

date_default_timezone_set('America/La_Paz'); // Ajusta según tu zona horaria

$fecha_actual = date('Y-m-d H:i:s'); // Cambiado a DATETIME

// Obtener el Id de la empresa logueada
$sql_empresa = "SELECT Id_Empresa FROM empresa WHERE Nombre = ?";
$stmt_empresa = $conexion->prepare($sql_empresa);
$stmt_empresa->bind_param("s", $_SESSION["Nombre_Empresa"]);
$stmt_empresa->execute();
$stmt_empresa->bind_result($id_empresa_logueada);
$stmt_empresa->fetch();
$stmt_empresa->close();

$busqueda = '';
$fecha_inicio = '';
$fecha_fin = '';
$suma_gastos = 0;
$historial = array();

// Si el formulario de filtro de fecha es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

    // Validar y ajustar las fechas si están presentes
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Convertir las fechas a DATETIME ajustando la fecha_fin para incluir todo el día
        $fecha_inicio_datetime = date('Y-m-d H:i:s', strtotime($fecha_inicio . ' 00:00:00'));
        $fecha_fin_datetime = date('Y-m-d H:i:s', strtotime($fecha_fin . ' 23:59:59'));
    }

    // Consulta para obtener el historial con filtros
    $sql_historial = "
        SELECT ei.Nombre_Completo, c.Importe_Total, c.Gastos, c.Ultimo_Agregado
        FROM empleado_importe ei
        JOIN consumo c ON ei.Id_Importe = c.Id_Importe
        WHERE c.Id_Empresa = ? AND (c.Importe_Total > 0 OR c.Gastos > 0)
    ";

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $sql_historial .= " AND c.Ultimo_Agregado BETWEEN ? AND ?";
    }

    // Crear un nuevo statement
    $stmt = $conexion->prepare($sql_historial);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $stmt->bind_param("iss", $id_empresa_logueada, $fecha_inicio_datetime, $fecha_fin_datetime);
    } else {
        $stmt->bind_param("s", $id_empresa_logueada);
    }

    $stmt->execute();
    $stmt->bind_result($nombre_completo, $importe_total, $gastos, $ultimo_agregado);

    // Sumar los gastos y almacenar los datos en un array
    while ($stmt->fetch()) {
        $suma_gastos += $gastos;
        $historial[] = array(
            'nombre_completo' => $nombre_completo,
            'importe_total' => $importe_total,
            'gastos' => $gastos,
            'ultimo_agregado' => $ultimo_agregado
        );
    }

    $stmt->close();

} else {
    // Si no hay filtro por fecha, mostramos todos los datos
    $sql_historial = "
        SELECT ei.Nombre_Completo, c.Importe_Total, c.Gastos, c.Ultimo_Agregado
        FROM empleado_importe ei
        JOIN consumo c ON ei.Id_Importe = c.Id_Importe
        WHERE c.Id_Empresa = ? AND (c.Importe_Total > 0 OR c.Gastos > 0)
    ";

    // Crear un nuevo statement
    $stmt = $conexion->prepare($sql_historial);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("s", $id_empresa_logueada);
    $stmt->execute();
    $stmt->bind_result($nombre_completo, $importe_total, $gastos, $ultimo_agregado);

    // Sumar los gastos y almacenar los datos en un array
    while ($stmt->fetch()) {
        $suma_gastos += $gastos;
        $historial[] = array(
            'nombre_completo' => $nombre_completo,
            'importe_total' => $importe_total,
            'gastos' => $gastos,
            'ultimo_agregado' => $ultimo_agregado
        );
    }

    $stmt->close();
}
?>