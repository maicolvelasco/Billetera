<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <title>Perfil de Usuario</title>
</head>
<body>
<?php
// Incluir el archivo del controlador y la base de datos
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorUsuario/ControladorCompras.php";
include "../../VistaSecciones/Topbar.php";
?>
<div class="container mt-0">

    <!-- Sección del Código QR -->
    <div class="card text-center mt-0">
        <div class="card-header">
            <h4>MI Código QR</h4>
        </div>
        <div class="card-body">
            <img id="qrImage" src="../qr/qr_code.png?random=<?php echo uniqid(); ?>" alt="Código QR" class="img-fluid" width="300" height="300">
        </div>
    </div>

    <!-- Sección del PIN -->
    <div class="card text-center mt-1">
        <div class="card-header">
            <h4>PIN Actual</h4>
        </div>
        <div class="card-body">
            <h3><?php echo $current_pin; ?></h3>
        </div>
    </div>
    
<!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
    <div class="container d-flex justify-content-between">
        <a href="DashboardU.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <a href="Transferencia.php" class="btn btn-primary btn-lg rounded-circle position-fixed" 
        style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-arrow-left-right fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
    </a>
</footer>
</div>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    $(document).ready(function(){
        // Forzar la actualización del QR cada vez que se carga la página
        function actualizarQR() {
            let timestamp = new Date().getTime(); // Obtener un timestamp único
            $('#qrImage').attr('src', '../qr/qr_code.png?random=' + timestamp);
        }

        // Actualizar QR cada vez que se cargue la página
        actualizarQR();

        // Refrescar la página cada 10 segundos
        setInterval(function() {
            location.reload();
        }, 60000);
    });
</script>
</body>
</html>