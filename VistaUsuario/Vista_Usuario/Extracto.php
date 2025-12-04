<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de la Empresa</title>
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-bottom: 75px;
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
include "../../Controlador/ControladorUsuario/ControladorExtracto.php";
include "../../VistaSecciones/Topbar.php";
?>
<div class="container mt-3">
    <div class="text-center">
        <h3>Saldo en <?php echo htmlspecialchars($nombre_empresa); ?></h3>
        <h1 class="text-primary"><?php echo isset($saldo_total) ? "Bs " . number_format($saldo_total, 2) : '0.00'; ?></h1>
    </div>
</div>

<div class="container mt-3 text-center">
    <form method="GET" action="">
        <input type="hidden" name="empresa" value="<?php echo isset($id_empresa) ? htmlspecialchars($id_empresa) : ''; ?>">
        <div class="row">
            <div class="col-5 col-md-5">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
            </div>
            <div class="col-5 col-md-5">
                <label for="fecha_final">Fecha Final:</label>
                <input type="date" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo isset($_GET['fecha_final']) ? htmlspecialchars($_GET['fecha_final']) : ''; ?>">
            </div>
            <div class="col-1 mt-4 col-md-1 d-flex align-items-center justify-content-center">
                <button type="submit" class="btn btn-secondary rounded-circle">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="container mt-4 text-center">
    <p>Movimientos</p>
    <div style="max-height: 300px; overflow-y: auto;">  
        <ul class="list-group" id="lista-movimientos">
            <?php
            if (!empty($movimientos)) {
                foreach ($movimientos as $movimiento) {
                    $fecha_formateada = date('d-m-Y', strtotime($movimiento['Fecha']));
                    $descripcion = htmlspecialchars($movimiento['Tipo']);
                    $color = $movimiento['Monto'] < 0 ? 'text-danger' : 'text-success';
                    $monto_formateado = "Bs. " . number_format(abs($movimiento['Monto']), 2);
                    if ($movimiento['Monto'] < 0) {
                        $monto_formateado = "-" . $monto_formateado;
                    }

                    echo "<li class='list-group-item'>
                            <div class='row'>
                                <div class='col-8 text-left'>
                                    <div><strong>$fecha_formateada</strong></div>
                                    <div>$descripcion</div>
                                </div>
                                <div class='col-4 text-right'>
                                    <div class='$color' style='font-size: 1.3em; font-weight: bold;'>$monto_formateado</div>
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

<footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0; z-index: 1000;">
    <div class="container d-flex justify-content-between">
        <a href="DashboardU.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <form method="POST" action="../../Controlador/ControladorUsuario/generate_pdf_movimientos.php" class="position-fixed" 
          style="bottom: 25px; left: 50%; transform: translateX(-50%);">
        <input type="hidden" name="empresa" value="<?php echo isset($id_empresa) ? htmlspecialchars($id_empresa) : ''; ?>">
        <input type="hidden" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
        <input type="hidden" name="fecha_final" value="<?php echo isset($_GET['fecha_final']) ? htmlspecialchars($_GET['fecha_final']) : ''; ?>">
        <button type="submit" name="download_pdf" class="btn btn-danger rounded-circle" 
                style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-file-pdf fa-3x" style="margin: 0;"></i>
        </button>
    </form>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>