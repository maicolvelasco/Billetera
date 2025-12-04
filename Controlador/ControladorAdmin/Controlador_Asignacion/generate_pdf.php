<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

require('../../../libs/fpdf/fpdf.php'); // Ajusta la ruta según donde hayas colocado FPDF
include "../../../Conexion/conexion.php";

// Obtener parámetros de filtrado
$id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : '';
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : '';
$nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Validar que se haya seleccionado una empresa y una acción
if (empty($id_empresa) || empty($accion)) {
    echo "Parámetros insuficientes para generar el PDF.";
    exit();
}

// Ajustar las fechas para incluir todo el día en la fecha final
if (!empty($fecha_inicio)) {
    $fecha_inicio_datetime = $fecha_inicio . ' 00:00:00';
}
if (!empty($fecha_final)) {
    $fecha_final_datetime = $fecha_final . ' 23:59:59';
}

// Clase extendida de FPDF para encabezados y pies de página personalizados
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        if (file_exists('../../../src/LOGO ESQUINA WEB.png')) {
            $this->Image('../../../src/LOGO ESQUINA WEB.png', 10, 6, 30);
        }
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Reporte de Importes', 0, 0, 'C');
        $this->Ln(20);
        $this->Line(10, 35, 200, 35);
        $this->Ln(5);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Inicializar PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Colores
$headerColor = [220, 220, 220];
$textColor = [0, 0, 0];
$fillColor = [245, 245, 245];

// Título del reporte
if ($accion == 'abono') {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(0, 102, 0); // Verde para abonos
    $pdf->Cell(0, 10, 'Reporte de Abonos', 0, 1, 'C');
} elseif ($accion == 'consumo') {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(204, 0, 0); // Rojo para consumos
    $pdf->Cell(0, 10, 'Reporte de Consumos', 0, 1, 'C');
}

$pdf->Ln(5);
$pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
$pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(.3);

if ($accion == 'abono') {
    $header = ['Fecha', 'Nombre Completo', 'Importe'];
} elseif ($accion == 'consumo') {
    $header = ['Fecha', 'Nombre Completo', 'Gastos'];
}

$w = [40, 100, 40]; // Ancho de las columnas
for($i=0; $i<count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

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
    $stmt->bind_result($ultimo_agregado, $nombre_completo, $importes);

    $pdf->SetFont('Arial', '', 12);
    $fill = false;
    $suma = 0;

    while ($stmt->fetch()) {
        if ($fill) {
            $pdf->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);

        $fecha_formateada = date('d-m-Y', strtotime($ultimo_agregado));
        $pdf->Cell($w[0], 6, $fecha_formateada, 'LR', 0, 'C', true);
        $pdf->Cell($w[1], 6, utf8_decode($nombre_completo), 'LR', 0, 'L', true);
        $pdf->Cell($w[2], 6, number_format($importes, 2), 'LR', 0, 'R', true);
        $pdf->Ln();
        $fill = !$fill;

        $suma += $importes;
    }
    $stmt->close();

    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 102, 0);
    $pdf->Cell(140, 10, 'Suma Total de Importes:', 1, 0, 'R', true);
    $pdf->Cell($w[2], 10, number_format($suma, 2), 1, 1, 'R', true);

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
    $stmt->bind_result($ultimo_agregado, $nombre_completo, $gastos);

    $pdf->SetFont('Arial', '', 12);
    $fill = false;
    $suma = 0;

    while ($stmt->fetch()) {
        if ($fill) {
            $pdf->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);

        $fecha_formateada = date('d-m-Y', strtotime($ultimo_agregado));
        $pdf->Cell($w[0], 6, $fecha_formateada, 'LR', 0, 'C', true);
        $pdf->Cell($w[1], 6, utf8_decode($nombre_completo), 'LR', 0, 'L', true);
        $pdf->Cell($w[2], 6, number_format($gastos, 2), 'LR', 0, 'R', true);
        $pdf->Ln();
        $fill = !$fill;

        $suma += $gastos;
    }
    $stmt->close();

    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(204, 0, 0);
    $pdf->Cell(140, 10, 'Suma Total de Gastos:', 1, 0, 'R', true);
    $pdf->Cell($w[2], 10, number_format($suma, 2), 1, 1, 'R', true);
}

// Salvar el PDF
$pdf->Output('D', 'reporte_' . $accion . '_' . date('YmdHis') . '.pdf');
exit();
?>