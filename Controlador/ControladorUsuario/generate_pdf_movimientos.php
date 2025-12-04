<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Incluir FPDF
require('../../libs/fpdf/fpdf.php'); // Asegúrate de que la ruta sea correcta

// Incluir conexión a la base de datos
include "../../Conexion/conexion.php";

// Obtener parámetros de filtrado desde POST
$id_empresa = isset($_POST['empresa']) ? $_POST['empresa'] : null;
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fecha_fin = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : null;

// Validar las fechas
if ($fecha_inicio && $fecha_fin && $fecha_inicio > $fecha_fin) {
    // Invertir las fechas si es necesario
    list($fecha_inicio, $fecha_fin) = array($fecha_fin, $fecha_inicio);
}

if ($id_empresa) {
    // Obtener el ID del usuario logueado
    $id_usuario = $_SESSION['Id_Usuario'];

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
            e.Nombre AS Empresa
        FROM empleado_importe ei
        JOIN empresa e ON ei.Id_Empresa = e.Id_Empresa
        WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ? AND ei.Importes > 0
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    // Agregar filtro de fecha si está presente
    if ($fecha_inicio && $fecha_fin) {
        $sql_movimientos_empresa .= " AND ei.Ultimo_agregado BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }

    // Segunda parte: Gastos (Gastos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            c.Ultimo_agregado AS Fecha, 
            'Compra Realizada' AS Tipo, 
            -c.Gastos AS Monto, 
            e.Nombre AS Empresa
        FROM consumo c
        JOIN empresa e ON c.Id_Empresa = e.Id_Empresa
        WHERE c.Id_Usuario = ? AND c.Id_Empresa = ? AND c.Gastos > 0
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_movimientos_empresa .= " AND c.Ultimo_agregado BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }

    // Tercera parte: Transferencias Enviadas (Gastos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            te.Fecha AS Fecha, 
            CONCAT('Transferencia a ', u.Nombre_Completo) AS Tipo, 
            -te.Monto AS Monto, 
            e.Nombre AS Empresa
        FROM transferencias_enviadas te
        JOIN usuario u ON te.Id_Usuario_Receptor = u.Id_Usuario
        JOIN empresa e ON te.Id_Empresa = e.Id_Empresa
        WHERE te.Id_Usuario_Emisor = ? AND te.Id_Empresa = ?
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_movimientos_empresa .= " AND te.Fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }

    // Cuarta parte: Transferencias Recibidas (Abonos)
    $sql_movimientos_empresa .= " UNION ALL ";
    $sql_movimientos_empresa .= "
        SELECT 
            tr.Fecha AS Fecha, 
            CONCAT('Transferencia de ', u.Nombre_Completo) AS Tipo, 
            tr.Monto AS Monto, 
            e.Nombre AS Empresa
        FROM transferencias_recibidas tr
        JOIN usuario u ON tr.Id_Usuario_Emisor = u.Id_Usuario
        JOIN empresa e ON tr.Id_Empresa = e.Id_Empresa
        WHERE tr.Id_Usuario_Receptor = ? AND tr.Id_Empresa = ?
    ";

    $params[] = $id_usuario;
    $params[] = $id_empresa;
    $types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_movimientos_empresa .= " AND tr.Fecha BETWEEN ? AND ?";
        $params[] = $fecha_inicio . " 00:00:00";
        $params[] = $fecha_fin . " 23:59:59";
        $types .= "ss";
    }

    // Ordenar por fecha
    $sql_movimientos_empresa .= " ORDER BY Fecha ASC";

    // Preparar la consulta
    $stmt = $conexion->prepare($sql_movimientos_empresa);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Bind de parámetros
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fecha, $tipo, $monto, $nombre_empresa);

    $movimientos = [];
    while ($stmt->fetch()) {
        $movimientos[] = [
            'Fecha' => $fecha,
            'Tipo' => $tipo,
            'Monto' => $monto,
            'Empresa' => $nombre_empresa
        ];
    }

    // Obtener el nombre de la empresa
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
            $nombre_empresa = "Empresa No Identificada";
        }
    }

    // Consulta para obtener el saldo total
    $sql_saldo_empresa = "
    SELECT SUM(Monto) AS saldo_total FROM (
        SELECT ei.Importes AS Monto
        FROM empleado_importe ei
        WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ? AND ei.Importes > 0
    ";

    $saldo_params = [];
    $saldo_types = "ii";
    $saldo_params[] = $id_usuario;
    $saldo_params[] = $id_empresa;

    if ($fecha_inicio && $fecha_fin) {
        $sql_saldo_empresa .= " AND ei.Ultimo_agregado BETWEEN ? AND ?";
        $saldo_params[] = $fecha_inicio . " 00:00:00";
        $saldo_params[] = $fecha_fin . " 23:59:59";
        $saldo_types .= "ss";
    }

    $sql_saldo_empresa .= "
        UNION ALL
        SELECT -c.Gastos AS Monto
        FROM consumo c
        WHERE c.Id_Usuario = ? AND c.Id_Empresa = ? AND c.Gastos > 0
    ";

    $saldo_params[] = $id_usuario;
    $saldo_params[] = $id_empresa;
    $saldo_types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_saldo_empresa .= " AND c.Ultimo_agregado BETWEEN ? AND ?";
        $saldo_params[] = $fecha_inicio . " 00:00:00";
        $saldo_params[] = $fecha_fin . " 23:59:59";
        $saldo_types .= "ss";
    }

    $sql_saldo_empresa .= "
        UNION ALL
        SELECT -te.Monto AS Monto
        FROM transferencias_enviadas te
        WHERE te.Id_Usuario_Emisor = ? AND te.Id_Empresa = ?
    ";

    $saldo_params[] = $id_usuario;
    $saldo_params[] = $id_empresa;
    $saldo_types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_saldo_empresa .= " AND te.Fecha BETWEEN ? AND ?";
        $saldo_params[] = $fecha_inicio . " 00:00:00";
        $saldo_params[] = $fecha_fin . " 23:59:59";
        $saldo_types .= "ss";
    }

    $sql_saldo_empresa .= "
        UNION ALL
        SELECT tr.Monto AS Monto
        FROM transferencias_recibidas tr
        WHERE tr.Id_Usuario_Receptor = ? AND tr.Id_Empresa = ?
    ";

    $saldo_params[] = $id_usuario;
    $saldo_params[] = $id_empresa;
    $saldo_types .= "ii";

    if ($fecha_inicio && $fecha_fin) {
        $sql_saldo_empresa .= " AND tr.Fecha BETWEEN ? AND ?";
        $saldo_params[] = $fecha_inicio . " 00:00:00";
        $saldo_params[] = $fecha_fin . " 23:59:59";
        $saldo_types .= "ss";
    }

    $sql_saldo_empresa .= ") AS movimientos";

    $stmt_saldo = $conexion->prepare($sql_saldo_empresa);
    if (!$stmt_saldo) {
        die("Error en la preparación de la consulta de saldo: " . $conexion->error);
    }

    $stmt_saldo->bind_param($saldo_types, ...$saldo_params);
    $stmt_saldo->execute();
    $stmt_saldo->store_result();
    $stmt_saldo->bind_result($saldo_total);

    if (!$stmt_saldo->fetch()) {
        $saldo_total = 0;
    }

    $stmt->close();
    $stmt_saldo->close();
    $conexion->close();
}

// Clase extendida de FPDF para encabezados y pies de página personalizados
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        if (file_exists('../../src/LOGO ESQUINA WEB.png')) { // Verificar si el logo existe
            $this->Image('../../src/LOGO ESQUINA WEB.png', 10, 6, 30); // Ajusta la ruta y el tamaño según sea necesario
        }
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Movimientos Realizados', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        
        // Línea horizontal
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, 35, 200, 35);
        $this->Ln(5);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Inicializar PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Colores
$headerColor = [220, 220, 220]; // Gris claro para los encabezados
$textColor = [0, 0, 0]; // Negro para el texto
$fillColor = [245, 245, 245]; // Gris muy claro para filas alternas

// Título del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 102, 204); // Azul oscuro
$pdf->Cell(0, 10, 'Movimientos de ' . utf8_decode($nombre_empresa), 0, 1, 'C');

// Salto de línea
$pdf->Ln(5);

// Establecer colores para la tabla
$pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
$pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(.3);

// Agregar encabezados de tabla con color de fondo
$header = ['Fecha', 'Descripcion', 'Importe (Bs.)'];
$w = [40, 120, 30]; // Ancho de las columnas ajustado

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Agregar datos a la tabla
$pdf->SetFont('Arial', '', 12);
$fill = false; // Para filas alternas

if (!empty($movimientos)) {
    foreach ($movimientos as $movimiento) {
        // Establecer color de fondo alterno
        if ($fill) {
            $pdf->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        } else {
            $pdf->SetFillColor(255, 255, 255); // Blanco
        }
        $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);

        $fecha_formateada = date('d-m-Y', strtotime($movimiento['Fecha']));
        $descripcion = utf8_decode($movimiento['Tipo']);
        $importe = number_format($movimiento['Monto'], 2);

        // Ajustar el ancho de la descripción si es muy larga
        $pdf->Cell($w[0], 6, $fecha_formateada, 'LR', 0, 'C', true);
        $pdf->Cell($w[1], 6, $descripcion, 'LR', 0, 'L', true);
        $pdf->Cell($w[2], 6, $importe, 'LR', 1, 'R', true);
        $fill = !$fill; // Cambiar el estado de relleno
    }
} else {
    // Si no hay movimientos, añadir una fila indicando esto
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(array_sum($w), 6, 'No se encontraron movimientos.', 'LR', 0, 'C', true);
    $pdf->Ln();
}

// Línea de cierre de la tabla
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln(10);

// Saldo total
$pdf->SetFont('Arial', 'B', 12);
if ($saldo_total >= 0) {
    $pdf->SetTextColor(0, 102, 0); // Verde para saldos positivos
} else {
    $pdf->SetTextColor(204, 0, 0); // Rojo para saldos negativos
}
$pdf->Cell(160, 10, 'Saldo Total:', 1, 0, 'R', true);
$pdf->Cell(30, 10, number_format($saldo_total, 2), 1, 1, 'R', true);

// Agregar footer personalizado o notas adicionales si es necesario
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(128, 128, 128); // Gris para notas
$pdf->Cell(0, 10, 'Generado por Sistema de Administracion', 0, 1, 'C');

// Salvar el PDF
$pdf->Output('D', 'movimientos_' . $nombre_empresa . '_' . date('YmdHis') . '.pdf');
exit();
?>