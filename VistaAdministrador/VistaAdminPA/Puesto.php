<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <title>Lista de Puestos</title>
</head>

<body>
    <?php
    include "../../Controlador/ControladorAdmin/Controlador_PA/ControladorPuesto.php";
    include "../../Conexion/conexion.php";
    include "../../VistaSecciones/Topbar.php";

    // Obtener la lista de puestos
    $puestos = listarPuestos($conexion);
    
    // Mostrar mensajes de notificación si existen
    if (isset($_SESSION['success'])) {
        // Escapar comillas para evitar romper el JavaScript
        $mensaje = addslashes($_SESSION['success']);
        echo "<script>
            Toastify({
                text: '{$mensaje}',
                duration: 3000,
                gravity: 'top', // 'top' o 'bottom'
                position: 'right', // 'left', 'center' o 'right'
                backgroundColor: '#4caf50', // Verde para éxito
                close: true
            }).showToast();
        </script>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        $mensaje = addslashes($_SESSION['error']);
        echo "<script>
            Toastify({
                text: '{$mensaje}',
                duration: 5000,
                gravity: 'top', // 'top' o 'bottom'
                position: 'right', // 'left', 'center' o 'right'
                backgroundColor: '#f44336', // Rojo para errores
                close: true
            }).showToast();
        </script>";
        unset($_SESSION['error']);
    }
    ?>
    
  <div class="container mt-3 mb-5">
    <h1 class="text-center text-secondary" style="font-size: 2rem;">LISTA DE PUESTOS</h1>

    <div class="row">
      <?php while ($puesto = $puestos->fetch_object()) { ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($puesto->Nombre) ?></h5>
            </div>
            <div class="card-footer text-center">
              <a href="EditarPuesto.php?id=<?= htmlspecialchars($puesto->Id_Puesto) ?>" class="btn btn-primary w-50">Editar</a>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <!-- Footer fijo con iconos -->
    <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
      <div class="container d-flex justify-content-between">
        <!-- Icono de casa con margen a la izquierda -->
        <a href="../VistaAdmin/Dashboard.php" class="text-dark" style="margin-left: 10%;">
          <i class="bi bi-house-door fa-2x"></i>
        </a>
        <!-- Espacio flexible para centrar el icono del QR -->
        <div class="flex-grow-1 text-center"></div>
        <!-- Icono de usuario con margen a la derecha -->
        <a href="../VistaAdmin/Gestion.php" class="text-dark" style="margin-right: 10%;">
          <i class="bi bi-nut fa-2x"></i>
        </a>
      </div>
      <!-- Bot車n flotante grande y centrado con icono de QR en la parte inferior -->
      <a href="RegistroPuesto.php" class="btn btn-primary btn-lg rounded-circle position-fixed"
         style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-briefcase-fill fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
      </a>
    </footer>

</body>

</html>