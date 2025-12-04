<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta etiquetas y enlaces a estilos y scripts -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlaces a Bootstrap CSS y otros estilos -->
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Enlaces a iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <title>Movimientos de la Empresa</title>
    <style>
        body {
            padding-bottom: 100px; /* Espacio para el footer */
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f0f0f0;
            border-radius: 15px 15px 0 0;
        }
        .list-group-item {
            border: none;
        }
        .list-group-item:not(:last-child) {
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>
<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorUsuario/ControladorCuentas.php";
include "../../VistaSecciones/Topbar.php";
?>
<div class="container mt-3">
    <div class="text-center">
        <h3>Saldo en <?php echo htmlspecialchars($nombre_empresa); ?></h3>
        <h1 class="text-primary"><?php echo "Bs " . number_format($importe_total, 2); ?></h1>
    </div>
</div>

<div class="container mt-3 text-center">
    <p>12 Ultimos Movimientos</p>

    <!-- Elimina la secci¨®n de t¨ªtulos -->
    <!--
    <div class="row mb-2 text-center font-weight-bold">
        <div class="col-4">Fecha</div>
        <div class="col-4">Descripci¨®n</div>
        <div class="col-4">Importe</div>
    </div>
    -->

    <div style="max-height: 400px; overflow-y: auto;">  
        <!-- Lista de movimientos -->
        <ul class="list-group">
            <?php
            if (!empty($movimientos)) {
                foreach ($movimientos as $movimiento) {
                    $fecha_formateada = date('d-m-Y', strtotime($movimiento['Fecha']));
                    $descripcion = htmlspecialchars($movimiento['Tipo']);

                    // Determinar el color del importe
                    $color = ($movimiento['Monto'] < 0) ? 'text-danger' : 'text-success';

                    // Formatear el monto
                    $amount_formatted = "Bs. " . number_format(abs($movimiento['Monto']), 2);
                    if ($movimiento['Monto'] < 0) {
                        $amount_formatted = "-" . $amount_formatted;
                    }

                    echo "<li class='list-group-item'>
                            <div class='row'>
                                <div class='col-8 text-left'>
                                    <div><strong>$fecha_formateada</strong></div>
                                    <div>$descripcion</div>
                                </div>
                                <div class='col-4 text-right'>
                                    <div class='$color' style='font-size: 1.2em; font-weight: bold;'>$amount_formatted</div>
                                </div>
                            </div>
                          </li>";
                }
            } else {
                echo "<li class='list-group-item text-center'>No se encontraron movimientos.</li>";
            }
            ?>
        </ul>
    </div>
</div>

<!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2">
    <div class="container d-flex justify-content-between">
        <a href="DashboardU.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <!-- Bot¨®n flotante grande y centrado con icono de Extracto en la parte inferior -->
    <a href="Extracto.php?empresa=<?php echo $id_empresa; ?>" class="btn btn-primary btn-lg rounded-circle position-fixed" 
       style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-file-earmark-spreadsheet fa-2x" style="margin: 0;"></i>
    </a>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>