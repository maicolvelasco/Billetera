<?php
// generate_pdf.php

session_start();
if (!isset($_SESSION["Nombre_Empresa"])) {
    header("location: ../../index.php");
    exit();
}

require('../../libs/fpdf/fpdf.php'); // Asegúrate de que la ruta sea correcta
include "../../Conexion/conexion.php";

// Obtener el Id de la empresa logueada
$sql_empresa = "SELECT Id_Empresa, Foto FROM empresa WHERE Nombre = ?";
$stmt_empresa = $conexion->prepare($sql_empresa);
if (!$stmt_empresa) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt_empresa->bind_param("s", $_SESSION["Nombre_Empresa"]);
$stmt_empresa->execute();
$stmt_empresa->bind_result($id_empresa_logueada, $foto_empresa);
$stmt_empresa->fetch();
$stmt_empresa->close();

if (empty($id_empresa_logueada)) {
    die("Empresa no encontrada.");
}

// Verificar si la foto existe, si no, usar una imagen por defecto
if (empty($foto_empresa) || !file_exists('../../' . $foto_empresa)) {
    $foto_empresa = '../../src/sinfoto.png'; // Ruta a una imagen por defecto
}

// Obtener parámetros de filtrado
$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

// Clase extendida de FPDF para encabezados y pies de página personalizados
class PDF extends FPDF
{
    public $foto_empresa; // Ruta de la foto de la empresa

    // Método para establecer la foto de la empresa
    public function setFotoEmpresa($ruta)
    {
        $this->foto_empresa = $ruta;
    }

    // Cabecera de página
    function Header()
    {
        // Verificar si se ha establecido una foto
        if (!empty($this->foto_empresa) && file_exists($this->foto_empresa)) {
            // Ajustar la posición y tamaño según sea necesario
            $this->Image($this->foto_empresa, 10, 6, 30);
        }

        // Fuente Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'Historial de Consumos', 0, 0, 'C');
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
$pdf->setFotoEmpresa('../../' . $foto_empresa); // Establecer la foto de la empresa
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Colores
$headerColor = [220, 220, 220]; // Gris claro para los encabezados
$textColor = [0, 0, 0]; // Negro para el texto
$fillColor = [245, 245, 245]; // Gris muy claro para filas alternas

// Título del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0); // Color negro
$pdf->Cell(0, 10, 'Reporte de Consumos', 0, 1, 'C');

// Subtítulo con fechas si están disponibles
$pdf->SetFont('Arial', '', 12);
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $pdf->Cell(0, 10, "Desde: $fecha_inicio Hasta: $fecha_fin", 0, 1, 'C');
} else {
    $pdf->Cell(0, 10, "Fecha: " . date('Y-m-d'), 0, 1, 'C');
}

// Salto de línea
$pdf->Ln(10);

// Encabezados de la tabla con color de fondo
$headers = ['Fecha', 'Nombre Completo', 'Ingresos'];
$widths = [50, 100, 40]; // Ancho de las columnas

$pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
$pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(.3);
$pdf->SetFont('Arial', 'B', 12);

for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($widths[$i], 7, $headers[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Preparar la consulta similar a la del filtro
$sql = "
    SELECT ei.Nombre_Completo, c.Gastos, c.Ultimo_Agregado
    FROM empleado_importe ei
    JOIN consumo c ON ei.Id_Importe = c.Id_Importe
    WHERE c.Id_Empresa = ? AND c.Gastos > 0
";

$params = array();
$types = "i"; // Asumiendo que Id_Empresa es entero
$params[] = $id_empresa_logueada;

if (!empty($busqueda)) {
    $sql .= " AND (ei.Codigo_empleado LIKE ? OR ei.Nombre_Completo LIKE ?)";
    $types .= "ss";
    $busqueda_param = "%" . $busqueda . "%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND c.Ultimo_Agregado BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Usar bind_param
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->store_result(); // Almacenar resultados para verificar el número de filas
$stmt->bind_result($nombre_completo, $gastos, $ultimo_agregado);

// Agregar datos a la tabla
$pdf->SetFont('Arial', '', 12);
$fill = false; // Para filas alternas
$suma = 0;

while ($stmt->fetch()) {
    // Establecer color de fondo alterno
    if ($fill) {
        $pdf->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
    } else {
        $pdf->SetFillColor(255, 255, 255); // Blanco
    }
    $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);

    $pdf->Cell($widths[0], 6, $ultimo_agregado, 'LR', 0, 'C', true);
    $pdf->Cell($widths[1], 6, utf8_decode($nombre_completo), 'LR', 0, 'L', true);
    $pdf->Cell($widths[2], 6, number_format($gastos, 2), 'LR', 0, 'R', true);
    $pdf->Ln();
    $fill = !$fill; // Cambiar el estado de relleno

    $suma += $gastos;
}

// Línea de cierre de la tabla
$pdf->Cell(array_sum($widths), 0, '', 'T');
$pdf->Ln(10);

// Suma total
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0); // Negro
$pdf->Cell(150, 10, 'Suma Total de Ingresos:', 1, 0, 'R', true);
$pdf->Cell($widths[2], 10, number_format($suma, 2), 1, 1, 'R', true);

// Notas adicionales o footer personalizado
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(128, 128, 128); // Gris para notas
$pdf->Cell(0, 10, 'Generado por Sistema de Administracion', 0, 1, 'C');

// Salida del PDF
$pdf->Output('D', 'Historial_Consumos_' . date('YmdHis') . '.pdf');
exit();
?>