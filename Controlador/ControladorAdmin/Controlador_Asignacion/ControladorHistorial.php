<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php"); // Cambia la ruta a la página de login si es necesario
    exit();
}

include "../../Conexion/conexion.php"; // Asegúrate de que $conexion esté disponible

// Obtener todas las empresas para el selector
$sql_empresas = "SELECT * FROM empresa";
$result_empresas = $conexion->query($sql_empresas);

// Variables para la fecha y la empresa seleccionada
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : '';
$id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : '';

// Variables para la búsqueda de nombre y código
$nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';

// Variable para la acción
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Variables para mostrar resultados
$mostrar_abono = false;
$mostrar_consumo = false;
$suma_importes = 0;
$suma_gastos = 0;

// Arrays para almacenar los resultados
$lista_abono = [];
$lista_consumo = [];

// Ajustar las fechas para incluir todo el día en la fecha final
if (!empty($fecha_inicio)) {
    $fecha_inicio_datetime = $fecha_inicio . ' 00:00:00';
}
if (!empty($fecha_final)) {
    $fecha_final_datetime = $fecha_final . ' 23:59:59';
}

// Procesar según la acción
if (!empty($id_empresa) && !empty($accion)) {
    if ($accion == 'abono') {
        $mostrar_abono = true;

        // Consulta base para obtener los registros de abono (empleado_importe)
        $sql_datos_abono = "
        SELECT Nombre_Completo, Codigo_empleado, Importes, Importe_total, Ultimo_agregado
        FROM empleado_importe
        WHERE Id_Empresa = ? AND Importes > 0";

        // Agregar el filtro de fechas si se seleccionan
        if (!empty($fecha_inicio) && !empty($fecha_final)) {
            $sql_datos_abono .= " AND Ultimo_agregado BETWEEN ? AND ?";
        }

        // Agregar el filtro por nombre o código si se proporciona
        if (!empty($nombre_usuario)) {
            $sql_datos_abono .= " AND (Nombre_Completo LIKE ? OR Codigo_empleado LIKE ?)";
            $nombre_usuario_like = '%' . $nombre_usuario . '%';  // Para buscar coincidencias parciales
            $codigo_empleado_like = '%' . $nombre_usuario . '%'; // Asumimos que el usuario puede ingresar parte del código
        }

        // Preparar la consulta
        $stmt_abono = $conexion->prepare($sql_datos_abono);

        // Verificar si la preparación fue exitosa
        if ($stmt_abono === false) {
            die('Error en la preparación de la consulta: ' . htmlspecialchars($conexion->error));
        }

        // Vincular parámetros a la consulta según los filtros aplicados
        if (!empty($fecha_inicio) && !empty($fecha_final) && !empty($nombre_usuario)) {
            $stmt_abono->bind_param("issss", $id_empresa, $fecha_inicio_datetime, $fecha_final_datetime, $nombre_usuario_like, $codigo_empleado_like);
        } elseif (!empty($fecha_inicio) && !empty($fecha_final)) {
            $stmt_abono->bind_param("iss", $id_empresa, $fecha_inicio_datetime, $fecha_final_datetime);
        } elseif (!empty($nombre_usuario)) {
            $stmt_abono->bind_param("iss", $id_empresa, $nombre_usuario_like, $codigo_empleado_like);
        } else {
            $stmt_abono->bind_param("i", $id_empresa);
        }

        // Ejecutar la consulta
        $stmt_abono->execute();

        // Usar bind_result() en lugar de get_result()
        $stmt_abono->bind_result($nombre_completo, $codigo_empleado, $importes, $importe_total, $ultimo_agregado);

        // Recorrer los resultados y sumar los importes, además de agregarlos a la lista
        while ($stmt_abono->fetch()) {
            $suma_importes += $importes;
            $lista_abono[] = [
                'Nombre_Completo' => $nombre_completo,
                'Codigo_empleado' => $codigo_empleado,
                'Importes' => $importes,
                'Importe_total' => $importe_total,
                'Ultimo_agregado' => $ultimo_agregado
            ];
        }

        $stmt_abono->close();  // Cerrar el statement
    } elseif ($accion == 'consumo') {
        $mostrar_consumo = true;

        // Consulta base para obtener los registros de consumo
        $sql_datos_consumo = "
        SELECT e.Nombre_Completo, e.Codigo_empleado, c.Ultimo_Agregado, c.Gastos
        FROM consumo c
        JOIN empleado_importe e ON c.Id_Importe = e.Id_Importe
        WHERE c.Id_Empresa = ? AND c.Gastos > 0";

        // Agregar el filtro de fechas si se seleccionan
        if (!empty($fecha_inicio) && !empty($fecha_final)) {
            $sql_datos_consumo .= " AND c.Ultimo_Agregado BETWEEN ? AND ?";
        }

        // Agregar el filtro por nombre o código si se proporciona
        if (!empty($nombre_usuario)) {
            $sql_datos_consumo .= " AND (e.Nombre_Completo LIKE ? OR e.Codigo_empleado LIKE ?)";
            $nombre_usuario_like = '%' . $nombre_usuario . '%';  // Para buscar coincidencias parciales
            $codigo_empleado_like = '%' . $nombre_usuario . '%'; // Asumimos que el usuario puede ingresar parte del código
        }

        // Preparar la consulta
        $stmt_consumo = $conexion->prepare($sql_datos_consumo);

        // Verificar si la preparación fue exitosa
        if ($stmt_consumo === false) {
            die('Error en la preparación de la consulta: ' . htmlspecialchars($conexion->error));
        }

        // Vincular parámetros a la consulta según los filtros aplicados
        if (!empty($fecha_inicio) && !empty($fecha_final) && !empty($nombre_usuario)) {
            $stmt_consumo->bind_param("issss", $id_empresa, $fecha_inicio_datetime, $fecha_final_datetime, $nombre_usuario_like, $codigo_empleado_like);
        } elseif (!empty($fecha_inicio) && !empty($fecha_final)) {
            $stmt_consumo->bind_param("iss", $id_empresa, $fecha_inicio_datetime, $fecha_final_datetime);
        } elseif (!empty($nombre_usuario)) {
            $stmt_consumo->bind_param("iss", $id_empresa, $nombre_usuario_like, $codigo_empleado_like);
        } else {
            $stmt_consumo->bind_param("i", $id_empresa);
        }

        // Ejecutar la consulta
        $stmt_consumo->execute();

        // Usar bind_result() en lugar de get_result()
        $stmt_consumo->bind_result($nombre_completo, $codigo_empleado, $ultimo_agregado, $gastos);

        // Recorrer los resultados y sumar los gastos, además de agregarlos a la lista
        while ($stmt_consumo->fetch()) {
            $suma_gastos += $gastos;
            $lista_consumo[] = [
                'Nombre_Completo' => $nombre_completo,
                'Codigo_empleado' => $codigo_empleado,
                'Gastos' => $gastos,
                'Ultimo_agregado' => $ultimo_agregado
            ];
        }

        $stmt_consumo->close();  // Cerrar el statement
    }
}
?>