<?php

session_start();
if (!isset($_SESSION["Nombre_Empresa"])) {
    header("location: ../../index.php");
    exit;
}

$fecha_actual = date('Y-m-d H:i:s'); // Cambiado a DATETIME
$primer_dia_mes_actual = date('Y-m-01 00:00:00'); // Incluye la hora para cubrir todo el primer día

// Consulta para obtener el Id de la empresa logueada
$sql_empresa = "SELECT Id_Empresa FROM empresa WHERE Nombre = ?";
$stmt_empresa = $conexion->prepare($sql_empresa);
$stmt_empresa->bind_param("s", $_SESSION["Nombre_Empresa"]);
$stmt_empresa->execute();
$stmt_empresa->bind_result($id_empresa_logueada);
$stmt_empresa->fetch();
$stmt_empresa->close();

$suma_gastos = 0;

// Consulta para obtener los datos solo del mes actual
$sql_historial = "
    SELECT ei.Nombre_Completo, c.Importe_Total, c.Gastos, c.Ultimo_Agregado
    FROM empleado_importe ei
    JOIN consumo c ON ei.Id_Importe = c.Id_Importe
    WHERE c.Id_Empresa = ?
    AND c.Ultimo_Agregado BETWEEN ? AND ?
";

$stmt_historial = $conexion->prepare($sql_historial);
$stmt_historial->bind_param("iss", $id_empresa_logueada, $primer_dia_mes_actual, $fecha_actual);
$stmt_historial->execute();
$stmt_historial->bind_result($nombre_completo, $importe_total, $gastos, $ultimo_agregado);

// Sumar los gastos del mes actual
while ($stmt_historial->fetch()) {
    $suma_gastos += $gastos;
}

$stmt_historial->close();

setlocale(LC_TIME, 'es_ES.UTF-8'); // Configura el locale para mostrar el mes en español
$nombre_mes = strftime('%B', strtotime($fecha_actual)); // Obtén el nombre del mes actual en español
?>