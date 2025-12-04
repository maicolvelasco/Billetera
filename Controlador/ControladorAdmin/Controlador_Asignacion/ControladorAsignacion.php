<?php
session_start();
include "../../Conexion/conexion.php"; 

if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

$sql_empresas = "SELECT * FROM empresa";
$result_empresas = $conexion->query($sql_empresas);

$id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : '';

$importe_todos = isset($_POST['importe_todos']) ? $_POST['importe_todos'] : '';
$importe_diario = isset($_POST['importe_diario']) ? $_POST['importe_diario'] : [];
$ids_usuarios = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : [];

function obtener_importe_total_anterior($conexion, $id_usuario, $id_empresa) {
    $sql_importe_anterior = "SELECT Importe_total FROM empleado_importe WHERE Id_Usuario = ? AND Id_Empresa = ? ORDER BY Id_Importe DESC LIMIT 1";
    $stmt = $conexion->prepare($sql_importe_anterior);
    $stmt->bind_param("ii", $id_usuario, $id_empresa);
    $stmt->execute();
    $stmt->bind_result($total_anterior);
    $stmt->fetch();
    $stmt->close(); // Asegúrate de cerrar el statement
    return $total_anterior ? $total_anterior : 0;
}

if (!empty($id_empresa)) {
    $sql_usuarios = "SELECT Id_Usuario, Nombre_Completo FROM usuario WHERE Rol = 2 AND Estado != 0";
    $result_usuarios = $conexion->query($sql_usuarios);

    if (!empty($importe_todos)) {
        foreach ($ids_usuarios as $index => $id_usuario) {
            if ($importe_todos > 0) {
                $importe_total_anterior = obtener_importe_total_anterior($conexion, $id_usuario, $id_empresa);
                $nuevo_importe_total = $importe_total_anterior + $importe_todos;

                $sql_insertar = "INSERT INTO empleado_importe (Id_Usuario, Codigo_empleado, Nombre_Completo, Importes, Importe_total, Ultimo_agregado, Id_Empresa) 
                                 SELECT Id_Usuario, Codigo_empleado, Nombre_Completo, ?, ?, NOW(), ? 
                                 FROM usuario 
                                 WHERE Id_Usuario = ? AND Estado != 0";
                $stmt_insertar = $conexion->prepare($sql_insertar);
                $stmt_insertar->bind_param("ddii", $importe_todos, $nuevo_importe_total, $id_empresa, $id_usuario);
                $stmt_insertar->execute();
                $stmt_insertar->close();
            }
        }
    }

    if (!empty($importe_diario)) {
        foreach ($importe_diario as $index => $importe) {
            $id_usuario = $ids_usuarios[$index];
            if ($importe > 0) {
                $importe_total_anterior = obtener_importe_total_anterior($conexion, $id_usuario, $id_empresa);
                $nuevo_importe_total = $importe_total_anterior + $importe;

                $sql_insertar_individual = "INSERT INTO empleado_importe (Id_Usuario, Codigo_empleado, Nombre_Completo, Importes, Importe_total, Ultimo_agregado, Id_Empresa) 
                                            SELECT Id_Usuario, Codigo_empleado, Nombre_Completo, ?, ?, NOW(), ? 
                                            FROM usuario 
                                            WHERE Id_Usuario = ? AND Estado != 0";
                $stmt_insertar_individual = $conexion->prepare($sql_insertar_individual);
                $stmt_insertar_individual->bind_param("ddii", $importe, $nuevo_importe_total, $id_empresa, $id_usuario);
                $stmt_insertar_individual->execute();
                $stmt_insertar_individual->close();
            }
        }
    }
}
?>