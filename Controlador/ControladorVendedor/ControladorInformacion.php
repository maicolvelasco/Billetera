<?php
session_start();

if (isset($_SESSION['Id_Empresa'])) {
    $id_empresa_logueada = $_SESSION['Id_Empresa'];

    if (isset($_GET['ci'])) {
        $codigoQR = $_GET['ci'];
        $partes = explode('-', $codigoQR);
        $nombreCompleto = trim($partes[0]);
        $ci = isset($partes[1]) ? trim($partes[1]) : '';

        if (empty($nombreCompleto) || empty($ci)) {
            $_SESSION['error'] = "El código QR no contiene información válida.";
            header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
            exit();
        } else {
            // Preparar la consulta con AND para coincidencia exacta
            $stmt = $conexion->prepare("SELECT Id_Usuario, Nombre_Completo, CI FROM usuario WHERE Nombre_Completo = ? AND CI = ?");
            if (!$stmt) {
                $_SESSION['error'] = "Error en la preparación de la consulta: " . $conexion->error;
                header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                exit();
            }

            $stmt->bind_param('ss', $nombreCompleto, $ci);
            $stmt->execute();
            $stmt->store_result();

            $Id_usuario = null;
            $Nombre_Completo = null;
            $CI_Usuario = null;

            $stmt->bind_result($Id_usuario, $Nombre_Completo, $CI_Usuario);

            if ($stmt->fetch()) {
                // Obtener el último Importe_total del usuario en la empresa
                $stmt_importe_total = $conexion->prepare("SELECT Importe_total FROM empleado_importe WHERE Id_Usuario = ? AND Id_Empresa = ? ORDER BY Id_Importe DESC LIMIT 1");
                if (!$stmt_importe_total) {
                    $_SESSION['error'] = "Error en la preparación de la consulta de Importe_total: " . $conexion->error;
                    header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                    exit();
                }

                $stmt_importe_total->bind_param('ii', $Id_usuario, $id_empresa_logueada);
                $stmt_importe_total->execute();
                $stmt_importe_total->bind_result($importe_total);
                if ($stmt_importe_total->fetch()) {
                    // $importe_total ya está asignado
                } else {
                    $importe_total = 0;
                }
                $stmt_importe_total->close();

                if (isset($_POST['aceptar'])) {
                    $gasto = floatval($_POST['gasto']);
                    $pinIngresado = $_POST['pin'];

                    $stmtPin = $conexion->prepare("SELECT PIN FROM user_pins WHERE Id_Usuario = ?");
                    if (!$stmtPin) {
                        $_SESSION['error'] = "Error en la preparación de la consulta de PIN: " . $conexion->error;
                        header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                        exit();
                    }

                    $stmtPin->bind_param('i', $Id_usuario);
                    $stmtPin->execute();
                    $stmtPin->store_result();

                    $pinCorrecto = null;
                    $stmtPin->bind_result($pinCorrecto);
                    $stmtPin->fetch();
                    $stmtPin->close();

                    if ($pinIngresado === $pinCorrecto) {
                        if ($gasto > $importe_total) {
                            $diferencia = $gasto - $importe_total;
                            $_SESSION['error'] = "El gasto de Bs." . number_format($gasto, 2) . " excede tu saldo disponible por Bs." . number_format($diferencia, 2) . ". Compra no realizada.";
                            header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                            exit();
                        } else {
                            $nuevoImporteTotal = $importe_total - $gasto;

                            // Obtener el Id_Importe correspondiente
                            $stmtImporte = $conexion->prepare("SELECT Id_Importe FROM empleado_importe WHERE Id_Usuario = ? AND Id_Empresa = ? ORDER BY Id_Importe DESC LIMIT 1");
                            if (!$stmtImporte) {
                                $_SESSION['error'] = "Error en la preparación de la consulta de Id_Importe: " . $conexion->error;
                                header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                                exit();
                            }

                            $stmtImporte->bind_param('ii', $Id_usuario, $id_empresa_logueada);
                            $stmtImporte->execute();
                            $stmtImporte->bind_result($registroIdImporte);
                            $stmtImporte->fetch();
                            $stmtImporte->close();

                            // Insertar en la tabla consumo
                            $stmtInsertConsumo = $conexion->prepare("INSERT INTO consumo (Id_Importe, Id_Usuario, Importe_Total, Gastos, Ultimo_Agregado, Id_Empresa) VALUES (?, ?, ?, ?, NOW(), ?)");
                            if (!$stmtInsertConsumo) {
                                $_SESSION['error'] = "Error en la preparación de la inserción de consumo: " . $conexion->error;
                                header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                                exit();
                            }

                            $stmtInsertConsumo->bind_param('iiddi', $registroIdImporte, $Id_usuario, $nuevoImporteTotal, $gasto, $id_empresa_logueada);
                            $stmtInsertConsumo->execute();
                            $stmtInsertConsumo->close();

                            // Actualizar el importe total en empleado_importe
                            $stmtActualizar = $conexion->prepare("UPDATE empleado_importe SET Importe_total = ? WHERE Id_Importe = ? AND Id_Empresa = ?");
                            if (!$stmtActualizar) {
                                $_SESSION['error'] = "Error en la preparación de la actualización del importe: " . $conexion->error;
                                header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                                exit();
                            }

                            $stmtActualizar->bind_param('dii', $nuevoImporteTotal, $registroIdImporte, $id_empresa_logueada);
                            $stmtActualizar->execute();
                            $stmtActualizar->close();

                            $_SESSION['success'] = "El gasto de " . number_format($gasto, 2) . " ha sido registrado.";
                            header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = "El PIN ingresado es incorrecto.";
                        header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                        exit();
                    }
                }

                // Pasar información del usuario a la vista si es necesario
                $usuario = [
                    'Nombre_Completo' => $Nombre_Completo,
                    'CI' => $CI_Usuario
                ];
            } else {
                $_SESSION['error'] = "No se encontró al usuario en la base de datos.";
                header("Location: ../../VistaVendedor/Vista_Vendedor/DashboardV.php");
                exit();
            }

            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "No hay empresa logueada.";
        header("Location: ../../index.php"); // Redirigir al login o página de error
        exit();
    }

    $conexion->close();
}
?>