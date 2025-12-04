<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta etiquetas y enlaces a estilos y scripts -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlaces a Bootstrap CSS y otros estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Enlaces a iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Enlaces a scripts de JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>   
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <title>Home Usuario</title>
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
    </style>
</head>
<body>

<?php
// Incluir el archivo del controlador y la base de datos
include "../../Controlador/ControladorUsuario/ControladorDashboard.php";
include "../../VistaSecciones/Topbar.php";
?>

<!-- Mostrar el saldo total -->
<div class="container mt-4">
    <div class="text-center">
        <h3>Saldo</h3>
        <h1 class="text-primary"><?php echo "Bs " . number_format($saldo_total, 2); ?></h1>
    </div>
</div>

<!-- Lista de empresas con icono en lugar de botón -->
<div class="container mt-4">
    <h4 class="mb-3">MIS CUENTAS</h4>
    <ul class="list-group">
        <?php
        if (!empty($empresas)) {
            foreach ($empresas as $fila) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        <a href='Cuentas.php?empresa=" . $fila['Id_Empresa'] . "' class='d-flex align-items-center'>
                        <div>
                            <strong class='h4'>" . htmlspecialchars($fila['Nombre']) . "</strong> 
                            <p class='mb-0 text-muted'>Bs " . number_format($fila['Importe_Total'], 2) . "</p>
                        </div>
                        </a>
                        <a href='Cuentas.php?empresa=" . $fila['Id_Empresa'] . "' class='text-info'>
                            <i class='fas fa-chevron-right'></i>
                        </a>
                      </li>";
            }
        } else {
            echo "<li class='list-group-item'>No hay empresas disponibles.</li>";
        }
        ?>
    </ul>
</div>

<!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
    <div class="container d-flex justify-content-between">
        <!-- Icono de casa con margen a la izquierda -->
        <a href="DashboardU.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <!-- Espacio flexible para centrar el icono del QR -->
        <div class="flex-grow-1 text-center"></div>
        <!-- Icono de usuario con margen a la derecha -->
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <!-- Botón flotante grande y centrado con icono de QR en la parte inferior -->
    <a href="Compras.php" class="btn btn-primary btn-lg rounded-circle position-fixed" 
       style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-qrcode fa-3x" style="margin: 0;"></i>
    </a>
</footer>

</body>
</html>