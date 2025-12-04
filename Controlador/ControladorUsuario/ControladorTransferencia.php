<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    header("Location: ../../index.php");
    exit();
}

include "../../Conexion/conexion.php"; // Incluir la conexión a la base de datos

$id_usuario_emisor = $_SESSION['Id_Usuario'];
$mensaje = "";

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener y sanitizar los datos del formulario
    $id_usuario_receptor = intval($_POST['id_usuario_receptor']);
    $id_empresa = intval($_POST['id_empresa']);
    $monto = floatval($_POST['monto']);

    // Validar los campos
    if (empty($id_usuario_receptor) || empty($id_empresa) || empty($monto) || $monto <= 0) {
        $mensaje = "Por favor, complete todos los campos correctamente.";
    } elseif ($id_usuario_emisor == $id_usuario_receptor) {
        $mensaje = "No puedes transferirte fondos a ti mismo.";
    } else {
        // Verificar que el usuario receptor existe y tiene el rol 'usuario' (Rol = 2)
        $sql_receptor = "SELECT Nombre_Completo FROM usuario WHERE Id_Usuario = ? AND Rol = 2";
        $stmt_receptor = $conexion->prepare($sql_receptor);
        if ($stmt_receptor) {
            $stmt_receptor->bind_param("i", $id_usuario_receptor);
            $stmt_receptor->execute();
            $stmt_receptor->bind_result($nombre_receptor);
            if ($stmt_receptor->fetch()) {
                $stmt_receptor->close();

                // Verificar que el usuario emisor tiene suficiente saldo en la empresa seleccionada
                $sql_saldo = "
                    SELECT ei.Importe_total
                    FROM empleado_importe ei
                    WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ?
                    ORDER BY ei.Id_Importe DESC
                    LIMIT 1
                ";
                $stmt_saldo = $conexion->prepare($sql_saldo);
                if ($stmt_saldo) {
                    $stmt_saldo->bind_param("ii", $id_usuario_emisor, $id_empresa);
                    $stmt_saldo->execute();
                    $stmt_saldo->bind_result($saldo_emisor);
                    if ($stmt_saldo->fetch()) {
                        $stmt_saldo->close();

                        if ($saldo_emisor >= $monto) {
                            // Iniciar una transacción
                            $conexion->begin_transaction();

                            try {
                                // Obtener información del emisor
                                $sql_emisor_info = "SELECT Codigo_empleado, Nombre_Completo FROM usuario WHERE Id_Usuario = ?";
                                $stmt_emisor_info = $conexion->prepare($sql_emisor_info);
                                $stmt_emisor_info->bind_param("i", $id_usuario_emisor);
                                $stmt_emisor_info->execute();
                                $stmt_emisor_info->bind_result($codigo_empleado_emisor, $nombre_completo_emisor);
                                $stmt_emisor_info->fetch();
                                $stmt_emisor_info->close();

                                // Restar el monto del saldo del usuario emisor
                                $nuevo_saldo_emisor = $saldo_emisor - $monto;
                                $sql_update_emisor = "
                                    INSERT INTO empleado_importe (Id_Usuario, Codigo_empleado, Nombre_Completo, Importes, Importe_total, Ultimo_agregado, Id_Empresa)
                                    VALUES (?, ?, ?, 0, ?, NOW(), ?)
                                ";
                                $stmt_update_emisor = $conexion->prepare($sql_update_emisor);
                                if ($stmt_update_emisor) {
                                    $stmt_update_emisor->bind_param("issdi", $id_usuario_emisor, $codigo_empleado_emisor, $nombre_completo_emisor, $nuevo_saldo_emisor, $id_empresa);
                                    $stmt_update_emisor->execute();
                                    $stmt_update_emisor->close();
                                } else {
                                    throw new Exception("Error al preparar la actualización del emisor: " . $conexion->error);
                                }

                                // Obtener el saldo actual y la información del receptor
                                $sql_saldo_receptor = "
                                    SELECT ei.Importe_total, u.Codigo_empleado, u.Nombre_Completo
                                    FROM empleado_importe ei
                                    JOIN usuario u ON ei.Id_Usuario = u.Id_Usuario
                                    WHERE ei.Id_Usuario = ? AND ei.Id_Empresa = ?
                                    ORDER BY ei.Id_Importe DESC
                                    LIMIT 1
                                ";
                                $stmt_saldo_receptor = $conexion->prepare($sql_saldo_receptor);
                                if ($stmt_saldo_receptor) {
                                    $stmt_saldo_receptor->bind_param("ii", $id_usuario_receptor, $id_empresa);
                                    $stmt_saldo_receptor->execute();
                                    $stmt_saldo_receptor->bind_result($saldo_receptor, $codigo_empleado_receptor, $nombre_completo_receptor);
                                    if ($stmt_saldo_receptor->fetch()) {
                                        $stmt_saldo_receptor->close();
                                        $nuevo_saldo_receptor = $saldo_receptor + $monto;
                                    } else {
                                        // Si no tiene saldo previo, obtener datos desde usuario
                                        $stmt_saldo_receptor->close();
                                        $sql_receptor_info = "SELECT Codigo_empleado, Nombre_Completo FROM usuario WHERE Id_Usuario = ?";
                                        $stmt_receptor_info = $conexion->prepare($sql_receptor_info);
                                        $stmt_receptor_info->bind_param("i", $id_usuario_receptor);
                                        $stmt_receptor_info->execute();
                                        $stmt_receptor_info->bind_result($codigo_empleado_receptor, $nombre_completo_receptor);
                                        $stmt_receptor_info->fetch();
                                        $stmt_receptor_info->close();
                                        $nuevo_saldo_receptor = $monto;
                                    }
                                } else {
                                    throw new Exception("Error al preparar la consulta del saldo del receptor: " . $conexion->error);
                                }

                                // Actualizar el saldo del usuario receptor
                                $sql_update_receptor = "
                                    INSERT INTO empleado_importe (Id_Usuario, Codigo_empleado, Nombre_Completo, Importes, Importe_total, Ultimo_agregado, Id_Empresa)
                                    VALUES (?, ?, ?, 0, ?, NOW(), ?)
                                ";
                                $stmt_update_receptor = $conexion->prepare($sql_update_receptor);
                                if ($stmt_update_receptor) {
                                    $stmt_update_receptor->bind_param("issdi", $id_usuario_receptor, $codigo_empleado_receptor, $nombre_completo_receptor, $nuevo_saldo_receptor, $id_empresa);
                                    $stmt_update_receptor->execute();
                                    $stmt_update_receptor->close();
                                } else {
                                    throw new Exception("Error al preparar la actualización del receptor: " . $conexion->error);
                                }

                                // Registrar la transferencia en transferencias_enviadas
                                $sql_insert_enviada = "
                                    INSERT INTO transferencias_enviadas (Id_Usuario_Emisor, Id_Usuario_Receptor, Id_Empresa, Monto, Fecha)
                                    VALUES (?, ?, ?, ?, NOW())
                                ";
                                $stmt_insert_enviada = $conexion->prepare($sql_insert_enviada);
                                if ($stmt_insert_enviada) {
                                    $stmt_insert_enviada->bind_param("iiid", $id_usuario_emisor, $id_usuario_receptor, $id_empresa, $monto);
                                    $stmt_insert_enviada->execute();
                                    $stmt_insert_enviada->close();
                                } else {
                                    throw new Exception("Error al preparar la inserción en transferencias_enviadas: " . $conexion->error);
                                }

                                // Registrar la transferencia en transferencias_recibidas
                                $sql_insert_recibida = "
                                    INSERT INTO transferencias_recibidas (Id_Usuario_Receptor, Id_Usuario_Emisor, Id_Empresa, Monto, Fecha)
                                    VALUES (?, ?, ?, ?, NOW())
                                ";
                                $stmt_insert_recibida = $conexion->prepare($sql_insert_recibida);
                                if ($stmt_insert_recibida) {
                                    $stmt_insert_recibida->bind_param("iiid", $id_usuario_receptor, $id_usuario_emisor, $id_empresa, $monto);
                                    $stmt_insert_recibida->execute();
                                    $stmt_insert_recibida->close();
                                } else {
                                    throw new Exception("Error al preparar la inserción en transferencias_recibidas: " . $conexion->error);
                                }

                                // Confirmar la transacción
                                $conexion->commit();
                                $mensaje = "Transferencia realizada con éxito.";
                            } catch (Exception $e) {
                                // Revertir la transacción en caso de error
                                $conexion->rollback();
                                $mensaje = "Error al realizar la transferencia: " . $e->getMessage();
                            }
                        } else {
                            $mensaje = "No tienes suficiente saldo para realizar esta transferencia.";
                        }
                    } else {
                        $stmt_saldo->close();
                        $mensaje = "No se pudo obtener tu saldo actual.";
                    }
                } else {
                    $mensaje = "Error al preparar la consulta de saldo: " . $conexion->error;
                }
            } else {
                $stmt_receptor->close();
                $mensaje = "El usuario receptor no existe o no tiene el rol adecuado.";
            }
        } else {
            $mensaje = "Error al preparar la consulta del receptor: " . $conexion->error;
        }
    }
}

// Obtener la lista de empresas para el formulario
$sql_empresas = "
    SELECT e.Id_Empresa, e.Nombre
    FROM empresa e
    INNER JOIN empleado_importe ei ON e.Id_Empresa = ei.Id_Empresa
    WHERE ei.Id_Usuario = ?
    GROUP BY e.Id_Empresa
";
$stmt_empresas = $conexion->prepare($sql_empresas);
if ($stmt_empresas) {
    $stmt_empresas->bind_param("i", $id_usuario_emisor);
    $stmt_empresas->execute();
    $stmt_empresas->bind_result($empresa_id, $empresa_nombre);

    $empresas_usuario = [];
    while ($stmt_empresas->fetch()) {
        $empresas_usuario[] = ['Id_Empresa' => $empresa_id, 'Nombre' => $empresa_nombre];
    }
    $stmt_empresas->close();
} else {
    $mensaje = "Error al preparar la consulta de empresas: " . $conexion->error;
}

// Obtener la lista de usuarios para el select (solo los que tienen Rol = 2)
$sql_usuarios = "SELECT Id_Usuario, Nombre_Completo FROM usuario WHERE Id_Usuario != ? AND Rol = 2";
$stmt_usuarios = $conexion->prepare($sql_usuarios);
$stmt_usuarios->bind_param("i", $id_usuario_emisor);
$stmt_usuarios->execute();
$stmt_usuarios->bind_result($usuario_id, $usuario_nombre);

$usuarios = [];
while ($stmt_usuarios->fetch()) {
    $usuarios[] = ['Id_Usuario' => $usuario_id, 'Nombre_Completo' => $usuario_nombre];
}
$stmt_usuarios->close();

$conexion->close();
?>