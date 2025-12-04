<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Obtener el ID de la empresa seleccionada y el ID del usuario logueado
$id_empresa = isset($_GET['empresa']) ? $_GET['empresa'] : null;
$id_usuario = $_SESSION['Id_Usuario'];

$nombre_empresa = ""; // Inicializar variable para el nombre de la empresa
$importe_total = 0.00; // Inicializar importe_total

if ($id_empresa) {
    // Consulta para obtener el último Importe_Total para esta empresa y usuario
    $sql_importe_total = "
        SELECT ei.Importe_Total, e.Nombre
        FROM empleado_importe ei
        JOIN empresa e ON ei.Id_Empresa = e.Id_Empresa
        WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ?
        ORDER BY ei.Id_Importe DESC
        LIMIT 1
    ";

    $stmt_importe_total = $conexion->prepare($sql_importe_total);

    if (!$stmt_importe_total) {
        error_log("Error al preparar la consulta de Importe_Total: " . $conexion->error);
        die("Error en la preparación de la consulta de Importe_Total.");
    }

    $stmt_importe_total->bind_param("ii", $id_usuario, $id_empresa);

    if ($stmt_importe_total->execute()) {
        $stmt_importe_total->bind_result($importe_total_db, $nombre_empresa);
        if ($stmt_importe_total->fetch()) {
            $importe_total = $importe_total_db;
        } else {
            // Si no hay registros, obtener el nombre de la empresa
            $stmt_importe_total->close(); // Cerrar el statement antes de preparar otro
            // Obtener el nombre de la empresa
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
            $importe_total = 0.00;
            // No cerramos $stmt_importe_total aquí porque ya se cerró arriba
            goto skip_close_stmt_importe_total; // Saltar el cierre adicional
        }
    } else {
        error_log("Error en la ejecución de la consulta de Importe_Total: " . $stmt_importe_total->error);
        $importe_total = 0.00;
    }

    $stmt_importe_total->close();
    skip_close_stmt_importe_total:

    // Consulta para obtener los movimientos (importes, gastos y transferencias) para esta empresa y usuario
    $sql_movimientos_empresa = "
        SELECT 
            ei.Ultimo_agregado AS Fecha, 
            'Transferencia de Duralit' AS Tipo, 
            ei.Importes AS Monto, 
            e.Nombre AS Empresa
        FROM empleado_importe ei
        JOIN empresa e ON ei.Id_Empresa = e.Id_Empresa
        WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ? AND ei.Importes > 0

        UNION ALL

        SELECT 
            c.Ultimo_agregado AS Fecha, 
            'Compra Realizada' AS Tipo, 
            -c.Gastos AS Monto, 
            e.Nombre AS Empresa
        FROM consumo c
        JOIN empresa e ON c.Id_Empresa = e.Id_Empresa
        WHERE c.Id_Usuario = ? AND c.Id_Empresa = ? AND c.Gastos > 0

        UNION ALL

        SELECT 
            te.Fecha AS Fecha,
            CONCAT('Transferencia a ', u.Nombre_Completo) AS Tipo,
            -te.Monto AS Monto,
            e.Nombre AS Empresa
        FROM transferencias_enviadas te
        JOIN usuario u ON te.Id_Usuario_Receptor = u.Id_Usuario
        JOIN empresa e ON te.Id_Empresa = e.Id_Empresa
        WHERE te.Id_Usuario_Emisor = ? AND te.Id_Empresa = ?

        UNION ALL

        SELECT
            tr.Fecha AS Fecha,
            CONCAT('Transferencia de ', u.Nombre_Completo) AS Tipo,
            tr.Monto AS Monto,
            e.Nombre AS Empresa
        FROM transferencias_recibidas tr
        JOIN usuario u ON tr.Id_Usuario_Emisor = u.Id_Usuario
        JOIN empresa e ON tr.Id_Empresa = e.Id_Empresa
        WHERE tr.Id_Usuario_Receptor = ? AND tr.Id_Empresa = ?

        ORDER BY Fecha DESC
        LIMIT 12
    ";

    // Preparar la consulta
    $stmt = $conexion->prepare($sql_movimientos_empresa);

    if (!$stmt) {
        error_log("Error al preparar la consulta de movimientos: " . $conexion->error);
        die("Error en la preparación de la consulta.");
    }

    // Vincular parámetros: 8 enteros (id_usuario, id_empresa) x4
    $stmt->bind_param("iiiiiiii", $id_usuario, $id_empresa, $id_usuario, $id_empresa, $id_usuario, $id_empresa, $id_usuario, $id_empresa);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $stmt->bind_result($fecha, $tipo, $monto, $empresa);
        $movimientos = [];
        while ($stmt->fetch()) {
            $movimientos[] = [
                'Fecha' => $fecha,
                'Tipo' => $tipo,
                'Monto' => $monto,
                'Empresa' => $empresa
            ];
        }
    } else {
        error_log("Error en la ejecución de la consulta de movimientos: " . $stmt->error);
        $movimientos = [];
    }

    $stmt->close();
    $conexion->close();
}
?>