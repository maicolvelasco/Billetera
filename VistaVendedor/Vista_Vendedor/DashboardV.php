<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <title>Historial de Consumos</title>
</head>

<body>

    <?php include "../../Conexion/conexion.php"; ?>
    <?php include "../../Controlador/ControladorVendedor/ControladorDashboard.php"; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

    <div class="container mt-3">
        <div class="alert alert-info text-center">
            <h3>Ingresos: <?php echo number_format($suma_gastos, 2); ?> <i class="fas fa-coins"></i></h3>
            <small><?php echo ucfirst($nombre_mes); ?></small> <!-- Mostramos el nombre del mes -->
        </div>

        <h4 class="text-center text-secondary mb-4">Historial de Ingresos Mensual</h4>
  
        <div style="max-height: 380px; overflow-y: auto;">          
            <ul class="list-group">
                <?php
                // Preparar la consulta para obtener el historial de consumos
                $stmt_historial = $conexion->prepare("
                    SELECT ei.Nombre_Completo, c.Importe_Total, c.Gastos, c.Ultimo_Agregado
                    FROM empleado_importe ei
                    JOIN consumo c ON ei.Id_Importe = c.Id_Importe
                    WHERE c.Id_Empresa = ?
                    AND c.Ultimo_Agregado BETWEEN ? AND ?
                ");
                $stmt_historial->bind_param("iss", $id_empresa_logueada, $primer_dia_mes_actual, $fecha_actual);
                $stmt_historial->execute();
                $stmt_historial->bind_result($nombre_completo, $importe_total, $gastos, $ultimo_agregado);

                // Sumar los gastos del mes actual y mostrarlos en la lista
                while ($stmt_historial->fetch()): 
                    // Formatear la fecha para mostrar solo la parte de la fecha
                    $fecha_formateada = date('Y-m-d H:i:s', strtotime($ultimo_agregado));
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo $fecha_formateada; ?></strong><br>
                    <span><?php echo htmlspecialchars($nombre_completo, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <span class="h3"><?php echo number_format($gastos, 2); ?></span>
                    </li>
                <?php endwhile; ?>

                <?php $stmt_historial->close(); // Cerrar la declaración ?>
            </ul>
        </div>
    </div>

<!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
    <div class="container d-flex justify-content-between">
        <a href="Extracto.php" class="text-dark" style="margin-left: 10%;">
    <i class="bi bi-file-earmark-spreadsheet fa-2x" style="margin: 0;"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <!-- Boton flotante grande y centrado con icono de QR en la parte inferior -->
    <a href="Camara.php" class="btn btn-secondary btn-lg rounded-circle position-fixed" 
       style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-camera fa-3x" style="line-height: 70px;" style="margin: 0;"></i>
    </a>
</footer>
    <script>
        // Función para mostrar notificaciones de éxito
        function mostrarExito(mensaje) {
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top", // 'top' o 'bottom'
                position: "right", // 'left', 'center' o 'right'
                backgroundColor: "#4caf50", // Verde para éxito
                close: true
            }).showToast();
        }

        // Función para mostrar notificaciones de error
        function mostrarError(mensaje) {
            Toastify({
                text: mensaje,
                duration: 5000,
                gravity: "top", // 'top' o 'bottom'
                position: "right", // 'left', 'center' o 'right'
                backgroundColor: "#f44336", // Rojo para errores
                close: true
            }).showToast();
        }

        // Mostrar notificaciones basadas en las variables de sesión
        <?php
            if (isset($_SESSION['success'])) {
                echo "mostrarExito('" . addslashes($_SESSION['success']) . "');";
                unset($_SESSION['success']);
            }

            if (isset($_SESSION['error'])) {
                echo "mostrarError('" . addslashes($_SESSION['error']) . "');";
                unset($_SESSION['error']);
            }
        ?>
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>