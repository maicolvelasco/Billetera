<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

include "../../../Conexion/conexion.php";

// Obtener parámetros de filtrado
$id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : '';
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : '';
$nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Validar que se haya seleccionado una empresa y una acción
if (empty($id_empresa) || empty($accion)) {
    echo "Parámetros insuficientes para generar el archivo Excel.";
    exit();
}

// Ajustar las fechas para incluir todo el día final
if (!empty($fecha_inicio)) {
    $fecha_inicio_datetime = $fecha_inicio . ' 00:00:00';
}
if (!empty($fecha_final)) {
    $fecha_final_datetime = $fecha_final . ' 23:59:59';
}

// Configurar las cabeceras para descargar el archivo como Excel
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_' . $accion . '_' . date('YmdHis') . '.csv');

// Crear un manejador de archivo temporal en memoria
$output = fopen('php://output', 'w');

// Añadir la marca de orden de bytes (BOM) para que Excel reconozca UTF-8 correctamente
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Escribir los encabezados de las columnas
if ($accion == 'abono') {
    fputcsv($output, ['Fecha', 'Nombre Completo', 'Importe']);
} elseif ($accion == 'consumo') {
    fputcsv($output, ['Fecha', 'Nombre Completo', 'Gastos']);
}

// Preparar la consulta según la acción
if ($accion == 'abono') {
    $sql = "SELECT Ultimo_agregado, Nombre_Completo, Importes 
    FROM empleado_importe 
    WHERE Id_Empresa = ? AND Importes > 0";
    $params = [];
    $types = "i"; // Id_Empresa es entero
    $params[] = $id_empresa;

    if (!empty($fecha_inicio) && !empty($fecha_final)) {
        $sql .= " AND Ultimo_agregado BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $fecha_inicio_datetime;
        $params[] = $fecha_final_datetime;
    }

    if (!empty($nombre_usuario)) {
        $sql .= " AND Nombre_Completo LIKE ?";
        $types .= "s";
        $params[] = '%' . $nombre_usuario . '%';
    }

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Usamos bind_result en lugar de get_result()
    $stmt->bind_result($ultimo_agregado, $nombre_completo, $importes);

    // Escribir los datos en el CSV
    while ($stmt->fetch()) {
        // Convertir la fecha al formato deseado
        $fecha_formateada = date('d-m-Y', strtotime($ultimo_agregado));
        $importe_formateado = number_format($importes, 2);
        fputcsv($output, [$fecha_formateada, $nombre_completo, $importe_formateado]);
    }

    $stmt->close();
} elseif ($accion == 'consumo') {
    $sql = "SELECT c.Ultimo_Agregado, e.Nombre_Completo, c.Gastos 
    FROM consumo c
    JOIN empleado_importe e ON c.Id_Importe = e.Id_Importe
    WHERE c.Id_Empresa = ? AND c.Gastos > 0";
    $params = [];
    $types = "i"; // Id_Empresa es entero
    $params[] = $id_empresa;

    if (!empty($fecha_inicio) && !empty($fecha_final)) {
        $sql .= " AND c.Ultimo_Agregado BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $fecha_inicio_datetime;
        $params[] = $fecha_final_datetime;
    }

    if (!empty($nombre_usuario)) {
        $sql .= " AND e.Nombre_Completo LIKE ?";
        $types .= "s";
        $params[] = '%' . $nombre_usuario . '%';
    }

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Usamos bind_result en lugar de get_result()
    $stmt->bind_result($ultimo_agregado, $nombre_completo, $gastos);

    // Escribir los datos en el CSV
    while ($stmt->fetch()) {
        // Convertir la fecha al formato deseado
        $fecha_formateada = date('d-m-Y', strtotime($ultimo_agregado));
        $gasto_formateado = number_format($gastos, 2);
        fputcsv($output, [$fecha_formateada, $nombre_completo, $gasto_formateado]);
    }

    $stmt->close();
}

// Cerrar el manejador de archivo
fclose($output);
exit();
?>