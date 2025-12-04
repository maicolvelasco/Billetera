<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Consumos</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
</head>

<body>

    <?php include "../../Conexion/conexion.php"; ?>
    <?php include "../../Controlador/ControladorVendedor/ControladorExtracto.php"; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

    <div class="container mt-3">
        <h3 class="text-center text-secondary mb-3">Historial de Ingresos</h3>

        <div class="container mt-3">
            <!-- Formulario de filtro por fecha -->
            <form method="POST" class="mb-2">
                <div class="row">
                    <!-- Fecha de Inicio -->
                    <div class="col-5 col-md-5 mb-2 mb-md-0">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                    </div>
                    <!-- Fecha Final -->
                    <div class="col-5 col-md-5 mb-2 mb-md-0">
                        <label for="fecha_fin">Fecha Final</label>
                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                    </div>
                    <!-- Botón de Filtrar -->
                    <div class="col-1 mt-4 col-md-2 d-flex align-items-center justify-content-center">
                        <button type="submit" class="btn btn-secondary rounded-circle" title="Filtrar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Mostrar la suma total de gastos -->
        <div class="alert alert-info text-center">
            <h3>Ingresos: Bs. <?php echo number_format($suma_gastos, 2); ?> <i class="fas fa-coins"></i></h3>
        </div>

        <div style="max-height: 300px; overflow-y: auto;">
            <ul class="list-group">
                <?php foreach ($historial as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?php
                                // Formatear la fecha para mostrar solo la parte de la fecha
                                $fecha_formateada = date('Y-m-d H:i:s', strtotime($item['ultimo_agregado']));
                            ?>
                            <strong><?php echo htmlspecialchars($fecha_formateada); ?></strong><br>
                            <span><?php echo htmlspecialchars($item['nombre_completo']); ?></span>
                        </div>
                        <span class="h3"><?php echo number_format($item['gastos'], 2); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Footer fijo con iconos -->
        <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0; z-index: 1000;">
            <div class="container d-flex justify-content-between">
                <!-- Icono de casa con margen a la izquierda -->
                <a href="DashboardV.php" class="text-dark" style="margin-left: 10%;">
                    <i class="bi bi-house-door fa-2x"></i>
                </a>
                <!-- Espacio flexible para centrar el icono del perfil -->
                <div class="flex-grow-1 text-center"></div>
                <!-- Icono de usuario con margen a la derecha -->
                <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
                    <i class="bi bi-person fa-2x"></i>
                </a>
            </div>
            <!-- Botón de Descarga PDF -->
            <form method="POST" action="../../Controlador/ControladorVendedor/generate_consumos_pdf.php" 
                  class="position-fixed" 
                  style="bottom: 25px; left: 50%; transform: translateX(-50%); z-index: 2000;">
                
                <!-- Campos ocultos para enviar las fechas -->
                <input type="hidden" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                <input type="hidden" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                
                <!-- Botón Circular con Icono de PDF -->
                <button type="submit" name="download_pdf" class="btn btn-danger rounded-circle" 
                        style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-pdf fa-3x" style="margin: 0;"></i>
                </button>
            </form>
        </footer>

    </div>

    <!-- Bootstrap JS, Popper.js y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Script para actualizar el texto del label del archivo seleccionado -->
    <script>
        $(document).ready(function () {
            // Mostrar el nombre del archivo seleccionado
            $('#profile-upload').on('change', function () {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
</body>

</html>