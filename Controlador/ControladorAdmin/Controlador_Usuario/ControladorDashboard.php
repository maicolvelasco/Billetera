<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php");
    exit();
}

include("../../Conexion/conexion.php");

// Obtener el mes y año actual
$currentYear = date('Y');
$currentMonth = date('m');

// Obtener el nombre del mes actual en español
$meses = array(
    1 => 'Enero', 'Febrero', 'Marzo', 'Abril',
    'Mayo', 'Junio', 'Julio', 'Agosto',
    'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
);
$currentMonthName = $meses[intval($currentMonth)];

// ======================
// Obtener el Total de Asignación del mes actual
// ======================
$sqlTotalAsignacion = "SELECT IFNULL(SUM(Importes), 0) AS total_importe 
                       FROM empleado_importe 
                       WHERE YEAR(Ultimo_agregado) = ? AND MONTH(Ultimo_agregado) = ?";
$stmt = $conexion->prepare($sqlTotalAsignacion);
$stmt->bind_param('ii', $currentYear, $currentMonth);
$stmt->execute();
$stmt->bind_result($totalAsignacion);
$stmt->fetch();
$stmt->close();

// ======================
// Obtener el Total de Gastos del mes actual
// ======================
$sqlTotalGastos = "SELECT IFNULL(SUM(Gastos), 0) AS total_gastos 
                   FROM consumo 
                   WHERE YEAR(Ultimo_Agregado) = ? AND MONTH(Ultimo_Agregado) = ?";
$stmt = $conexion->prepare($sqlTotalGastos);
$stmt->bind_param('ii', $currentYear, $currentMonth);
$stmt->execute();
$stmt->bind_result($totalGastos);
$stmt->fetch();
$stmt->close();

// ======================
// Calcular la Diferencia
// ======================
$diferencia = $totalAsignacion - $totalGastos;

// ======================
// Obtener los Importes por Empresa (Mes Actual)
// ======================
$sqlImportesPorEmpresa = "SELECT empresa.Nombre AS empresa_nombre, 
                           IFNULL(SUM(empleado_importe.Importes), 0) AS total_importes
                    FROM empresa
                    LEFT JOIN empleado_importe 
                        ON empleado_importe.Id_Empresa = empresa.Id_Empresa 
                        AND YEAR(empleado_importe.Ultimo_agregado) = ? 
                        AND MONTH(empleado_importe.Ultimo_agregado) = ?
                    GROUP BY empresa.Nombre
                    ORDER BY empresa.Nombre ASC";

$stmt = $conexion->prepare($sqlImportesPorEmpresa);
$stmt->bind_param('ii', $currentYear, $currentMonth);
$stmt->execute();
$stmt->bind_result($empresa_nombre, $total_importes);

// Crear un array para almacenar los importes por empresa
$empresaImportes = array();

while ($stmt->fetch()) {
    $empresaImportes[$empresa_nombre] = $total_importes !== null ? $total_importes : 0.00;
}

$stmt->close();

// ======================
// Obtener los Gastos por Empresa (Mes Actual)
// ======================
$sqlGastosPorEmpresa = "SELECT empresa.Nombre AS empresa_nombre,
        IFNULL(SUM(consumo.Gastos), 0) AS total_gastos
    FROM empresa
    LEFT JOIN consumo 
        ON consumo.Id_Empresa = empresa.Id_Empresa 
        AND YEAR(consumo.Ultimo_agregado) = ? 
        AND MONTH(consumo.Ultimo_agregado) = ?
    GROUP BY empresa.Nombre
    ORDER BY empresa.Nombre ASC";

$stmt = $conexion->prepare($sqlGastosPorEmpresa);
$stmt->bind_param('ii', $currentYear, $currentMonth);
$stmt->execute();
$stmt->bind_result($empresa_nombre, $total_gastos);

// Crear un array para almacenar los gastos por empresa
$empresaGastos = array();

while ($stmt->fetch()) {
    $empresaGastos[$empresa_nombre] = $total_gastos !== null ? $total_gastos : 0.00;
}

$stmt->close();

// ======================
// Combinar Importes y Gastos por Empresa
// ======================
$companyData = array();
$allCompanies = array_unique(array_merge(array_keys($empresaImportes), array_keys($empresaGastos)));
sort($allCompanies);

foreach ($allCompanies as $companyName) {
    $companyData[$companyName] = array(
        'importe' => isset($empresaImportes[$companyName]) ? $empresaImportes[$companyName] : 0,
        'gasto' => isset($empresaGastos[$companyName]) ? $empresaGastos[$companyName] : 0,
    );
}

// Preparar los datos para el gráfico de barras
$labelsBarChart = array();
$importesBarData = array();
$gastosBarData = array();

foreach ($companyData as $companyName => $data) {
    $labelsBarChart[] = $companyName;
    $importesBarData[] = $data['importe'];
    $gastosBarData[] = $data['gasto'];
}

// ======================
// Obtener los Datos para el Gráfico de Líneas (Importes y Gastos por Mes)
// ======================

// Consulta para obtener la suma de importes por mes para el año actual
$sqlImportesPorMes = "SELECT MONTH(Ultimo_agregado) AS mes, 
                           IFNULL(SUM(Importes), 0) AS total_importes
                    FROM empleado_importe
                    WHERE YEAR(Ultimo_agregado) = ?
                    GROUP BY mes
                    ORDER BY mes ASC";

$stmt = $conexion->prepare($sqlImportesPorMes);
$stmt->bind_param('i', $currentYear);
$stmt->execute();
$stmt->bind_result($mes, $total_importes);

// Inicializar un array con 12 meses
$importesPorMes = array_fill(1, 12, 0.00);

while ($stmt->fetch()) {
    $importesPorMes[(int)$mes] = $total_importes !== null ? $total_importes : 0.00;
}

$stmt->close();

// Consulta para obtener la suma de gastos por mes para el año actual
$sqlGastosPorMes = "SELECT MONTH(Ultimo_agregado) AS mes, 
                           IFNULL(SUM(Gastos), 0) AS total_gastos
                    FROM consumo
                    WHERE YEAR(Ultimo_agregado) = ?
                    GROUP BY mes
                    ORDER BY mes ASC";

$stmt = $conexion->prepare($sqlGastosPorMes);
$stmt->bind_param('i', $currentYear);
$stmt->execute();
$stmt->bind_result($mes, $total_gastos);

// Inicializar un array con 12 meses
$gastosPorMes = array_fill(1, 12, 0.00);

while ($stmt->fetch()) {
    $gastosPorMes[(int)$mes] = $total_gastos !== null ? $total_gastos : 0.00;
}

$stmt->close();

// Preparar las etiquetas y los datos para el gráfico de líneas
$labels = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$importesData = array_values($importesPorMes);
$gastosData = array_values($gastosPorMes);
?>