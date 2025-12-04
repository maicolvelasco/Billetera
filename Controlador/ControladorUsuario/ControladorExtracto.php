<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Incluir la conexión a la base de datos
include "../../Conexion/conexion.php";

// Obtener el ID de la empresa seleccionada y el ID del usuario logueado
$id_empresa = isset($_GET['empresa']) ? $_GET['empresa'] : null;
$id_usuario = $_SESSION['Id_Usuario'];

$nombre_empresa = ""; // Inicializar variable para el nombre de la empresa

// Obtener las fechas del filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : null;

if ($id_empresa) {
    // Inicializar variables para los parámetros
    $params = [];
    $types = "";

    // Construir la consulta base para los movimientos
    $sql_movimientos_empresa = "";

    // Primera parte: Importes (Abonos)
    $sql_movimientos_empresa .= "
        SELECT 
            ei.Ultimo_agregado AS Fecha, 
            'Transferencia de Duralit' AS Tipo, 
            ei.Importes AS Monto, 
            e.Nombre AS Empresa,
            'abono' AS data_type
        FROM empleado_importe ei
        JOIN empresa e ON ei.Id_Empresa = e.Id_Empresa
        WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ? AND ei.Importes > 0
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    // Agregar filtro de fecha si está presente
    if ($fecha_inicio && $fecha_final) {
        $sql_movimientos_empresa .= " AND ei.Ultimo_agregado BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_final . " 23:59:59";
        $types .= "ss";
    }

    // Segunda parte: Gastos (Gastos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            c.Ultimo_agregado AS Fecha, 
            'Compra Realizada' AS Tipo, 
            -c.Gastos AS Monto, 
            e.Nombre AS Empresa,
            'gasto' AS data_type
        FROM consumo c
        JOIN empresa e ON c.Id_Empresa = e.Id_Empresa
        WHERE c.Id_Usuario = ? AND c.Id_Empresa = ? AND c.Gastos > 0
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_final) {
        $sql_movimientos_empresa .= " AND c.Ultimo_agregado BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_final . " 23:59:59";
        $types .= "ss";
    }

    // Tercera parte: Transferencias Enviadas (Gastos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            te.Fecha AS Fecha, 
            CONCAT('Transferencia a ', u.Nombre_Completo) AS Tipo, 
            -te.Monto AS Monto, 
            e.Nombre AS Empresa,
            'gasto' AS data_type
        FROM transferencias_enviadas te
        JOIN usuario u ON te.Id_Usuario_Receptor = u.Id_Usuario
        JOIN empresa e ON te.Id_Empresa = e.Id_Empresa
        WHERE te.Id_Usuario_Emisor = ? AND te.Id_Empresa = ?
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_final) {
        $sql_movimientos_empresa .= " AND te.Fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_final . " 23:59:59";
        $types .= "ss";
    }

    // Cuarta parte: Transferencias Recibidas (Abonos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            tr.Fecha AS Fecha, 
            CONCAT('Transferencia de ', u.Nombre_Completo) AS Tipo, 
            tr.Monto AS Monto, 
            e.Nombre AS Empresa,
            'abono' AS data_type
        FROM transferencias_recibidas tr
        JOIN usuario u ON tr.Id_Usuario_Emisor = u.Id_Usuario
        JOIN empresa e ON tr.Id_Empresa = e.Id_Empresa
        WHERE tr.Id_Usuario_Receptor = ? AND tr.Id_Empresa = ?
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_final) {
        $sql_movimientos_empresa .= " AND tr.Fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_final . " 23:59:59";
        $types .= "ss";
    }

    // Ordenar los resultados
    $sql_movimientos_empresa .= " ORDER BY Fecha DESC";

    // Preparar la consulta
    $stmt = $conexion->prepare($sql_movimientos_empresa);

    if (!$stmt) {
        // Manejo de errores en la preparación de la consulta
        error_log("Error al preparar la consulta de movimientos: " . $conexion->error);
        die("Error en la preparación de la consulta.");
    }

    // Vincular parámetros
    $stmt->bind_param($types, ...$params);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Utilizar bind_result() y fetch()
        $stmt->bind_result($fecha, $tipo, $monto, $empresa, $data_type);
        $movimientos = [];
        while ($stmt->fetch()) {
            $movimientos[] = [
                'Fecha' => $fecha,
                'Tipo' => $tipo,
                'Monto' => $monto,
                'Empresa' => $empresa,
                'DataType' => $data_type
            ];
        }

        // Obtener el nombre de la empresa de la primera fila de resultados
        if (!empty($movimientos)) {
            $nombre_empresa = $movimientos[0]['Empresa'];
        } else {
            // Si no hay movimientos, obtener el nombre de la empresa
            $sql_nombre_empresa = "SELECT Nombre FROM empresa WHERE Id_Empresa = ?";
            $stmt_nombre = $conexion->prepare($sql_nombre_empresa);
            if ($stmt_nombre) {
                $stmt_nombre->bind_param("i", $id_empresa);
                $stmt_nombre->execute();
                $stmt_nombre->bind_result($nombre_empresa);
                $stmt_nombre->fetch();
                $stmt_nombre->close();
            } else {
                error_log("Error al preparar la consulta del nombre de la empresa: " . $conexion->error);
                $nombre_empresa = "Empresa desconocida";
            }
        }
    } else {
        // Manejo de errores en la ejecución de la consulta
        error_log("Error en la ejecución de la consulta de movimientos: " . $stmt->error);
        $movimientos = [];
    }

    $stmt->close();

    // Consulta para obtener el último importe total
    $sql_saldo_empresa = "
        SELECT Importe_total 
        FROM empleado_importe
        WHERE Id_Usuario = ? AND Id_Empresa = ?
        ORDER BY Id_Importe DESC LIMIT 1
    ";

    $saldo_params = [];
    $saldo_types = "ii";
    $saldo_params[] = $id_usuario;
    $saldo_params[] = $id_empresa;

    // Preparar la consulta de saldo
    $stmt_saldo = $conexion->prepare($sql_saldo_empresa);
    if (!$stmt_saldo) {
        // Manejo de errores en la preparación de la consulta de saldo
        error_log("Error al preparar la consulta de saldo: " . $conexion->error);
        die("Error en la preparación de la consulta de saldo.");
    }

    // Vincular parámetros
    $stmt_saldo->bind_param($saldo_types, ...$saldo_params);

    // Ejecutar la consulta de saldo
    if ($stmt_saldo->execute()) {
        $stmt_saldo->bind_result($saldo_total);
        if ($stmt_saldo->fetch()) {
            $saldo_total = $saldo_total !== null ? $saldo_total : 0;  // Si el saldo es NULL, lo asignamos a 0
        } else {
            $saldo_total = 0;
        }
    } else {
        // Manejo de errores en la ejecución de la consulta de saldo
        error_log("Error en la ejecución de la consulta de saldo: " . $stmt_saldo->error);
        $saldo_total = 0;
    }

    $stmt_saldo->close();
    $conexion->close();
}
?>