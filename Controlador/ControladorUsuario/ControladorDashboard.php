<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include "../../Conexion/conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtiene el ID del usuario logueado
$id_usuario = $_SESSION['Id_Usuario'];

// Consulta para obtener empresas y su último importe total para el usuario
$sql_empresas = "
    SELECT e.Id_Empresa, e.Nombre, 
        COALESCE(importe_total.Importe_Total, 0) AS Importe_Total
    FROM empresa e
    LEFT JOIN (
        SELECT ei.Id_Empresa, ei.Importe_Total
        FROM empleado_importe ei
        WHERE ei.Id_Usuario = ?
          AND ei.Id_Importe = (
              SELECT MAX(ei2.Id_Importe)
              FROM empleado_importe ei2
              WHERE ei2.Id_Usuario = ei.Id_Usuario
                AND ei2.Id_Empresa = ei.Id_Empresa
          )
    ) AS importe_total ON e.Id_Empresa = importe_total.Id_Empresa
    ORDER BY e.Nombre
";

$stmt = $conexion->prepare($sql_empresas);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

// En lugar de usar get_result, utilizamos bind_result para obtener los resultados
$stmt->bind_result($id_empresa, $nombre_empresa, $importe_total);

$empresas = [];

// Recoger los resultados
while ($stmt->fetch()) {
    $empresas[] = [
        'Id_Empresa' => $id_empresa,
        'Nombre' => $nombre_empresa,
        'Importe_Total' => $importe_total
    ];
}
$stmt->close();

// Calcular el saldo total sumando los importes totales de cada empresa
$saldo_total = 0;
if (!empty($empresas)) {
    foreach ($empresas as $empresa) {
        $saldo_total += $empresa['Importe_Total'];
    }
}
?>