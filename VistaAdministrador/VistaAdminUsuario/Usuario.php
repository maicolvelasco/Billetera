<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
  <!-- Font Awesome desde CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
  <title>Lista de Usuarios</title>

</head>

<body>
<?php
    include "../../Controlador/ControladorAdmin/Controlador_Usuario/ControladorUsuario.php";
    include "../../Conexion/conexion.php";
    include "../../VistaSecciones/Topbar.php";

    // Mostrar mensajes de notificación si existen
    if (isset($_SESSION['success'])) {
        echo "<script>
            Toastify({
                text: '" . addslashes($_SESSION['success']) . "',
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
        echo "<script>
            Toastify({
                text: '" . addslashes($_SESSION['error']) . "',
                duration: 3000,
                gravity: 'top', // 'top' o 'bottom'
                position: 'right', // 'left', 'center' o 'right'
                backgroundColor: '#f44336', // Rojo para errores
                close: true
            }).showToast();
        </script>";
        unset($_SESSION['error']);
    }
?>
  <div class="container mt-3">
    <h1 class="text-center text-secondary" style="font-size: 2rem;">LISTA DE USUARIOS</h1>

    <!-- Botones para filtrar usuarios -->
    <div class="row text-center my-4">
      <div class="col-4 col-md-4 mb-1">
        <a href="Usuario.php?filter=all" class="btn btn-outline-primary btn-block">Todos</a>
      </div>
      <div class="col-4 col-md-4 mb-1">
        <a href="Usuario.php?filter=active" class="btn btn-outline-success btn-block">Activos</a>
      </div>
      <div class="col-4 col-md-4 mb-1">
        <a href="Usuario.php?filter=inactive" class="btn btn-outline-danger btn-block">Inactivos</a>
      </div>
    </div>

    <?php
    // Filtrar usuarios según el estado
    if (isset($_GET['filter'])) {
      $filter = $_GET['filter'];
    } else {
      $filter = 'all'; // Valor por defecto
    }

    // Obtener la lista de usuarios según el filtro
    $usuarios = listarUsuariosFiltrados($conexion, $filter);

    // Imagen predeterminada si no hay foto
    $imgDefault = "../../src/sinfoto.png";
    $basePath = '/uploads/'; // Ruta base para las fotos
    ?>
    <div style="max-height: 420px; overflow-y: auto;">
    <ul class="list-group">
      <?php while ($usuario = $usuarios->fetch_object()) {
        // Si el usuario tiene foto, mostrarla, si no, usar la imagen predeterminada
        $fotoUsuario = !empty($usuario->Foto) ? $basePath . $usuario->Foto : $imgDefault;
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <img src="<?= htmlspecialchars($fotoUsuario) ?>" class="rounded-circle mr-3" alt="Foto del usuario" width="50" height="50">
            <div>
              <h5 class="mb-1"><?= htmlspecialchars($usuario->Nombre_Completo) ?></h5>
              <p class="mb-0 small">Código: <?= htmlspecialchars($usuario->Codigo_empleado) ?></p>
              <p class="mb-0 small text-truncate" style="max-width: 200px;">Correo: <?= htmlspecialchars($usuario->Correo_Electronico) ?></p>
            </div>
          </div>
          <!-- Botón con el símbolo ">" -->
          <a href="ModificarUsuario.php?id=<?= htmlspecialchars($usuario->Id_Usuario) ?>" class="btn btn-lg" style="color: blue;">
            &gt;
          </a>
        </li>
      <?php } ?>
    </ul>
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
      <!-- Botón flotante grande y centrado con icono de QR en la parte inferior -->
      <a href="RegistroUsuario.php" class="btn btn-primary btn-lg rounded-circle position-fixed"
         style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-person-add fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
      </a>
    </footer>

</body>

</html>