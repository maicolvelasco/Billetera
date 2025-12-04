<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Nombre_Completo'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: ../../index.php"); // Cambia la ruta a la página de login si es necesario
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap desde CDN -->
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Gestionamiento</title>

  <style>

    .icon-large {
      font-size: 3rem;
      color: #007bff;
      display: flex;
      justify-content: center;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

    <?php
    include("../../Conexion/conexion.php");
    include "../../VistaSecciones/Topbar.php";
    ?>

  <div class="container mt-3 mb-5">
    <h1 class="text-center text-secondary" style="font-size: 3rem;">Configuracion</h1>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="icon-large">
            <i class="bi bi-person-circle"></i>
          </div>
          <div class="card-body">
            <h5 class="card-title">Gestion de Usuarios</h5>
            <p class="card-text">Registra, Modifica y Elimina Usuarios de la empresa.</p>
          </div>
          <div class="card-footer text-center">
            <a href="../VistaAdminUsuario/Usuario.php" class="btn btn-primary w-50">Ingresar</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="icon-large">
            <i class="bi bi-briefcase"></i>
          </div>
          <div class="card-body">
            <h5 class="card-title">Gestion de Puestos</h5>
            <p class="card-text">Crea, Modifica y Elimina Puestos de la empresa.</p>
          </div>
          <div class="card-footer text-center">
            <a href="../VistaAdminPA/Puesto.php" class="btn btn-primary w-50">Ingresar</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="icon-large">
            <i class="bi bi-diagram-3"></i>
          </div>
          <div class="card-body">
            <h5 class="card-title">Gestion de Areas</h5>
            <p class="card-text">Crea, Modifica y Elimina Areas de Trabajo de la empresa.</p>
          </div>
          <div class="card-footer text-center">
            <a href="../VistaAdminPA/Area.php" class="btn btn-primary w-50">Ingresar</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="icon-large">
            <i class="bi bi-building"></i>
          </div>
          <div class="card-body">
            <h5 class="card-title">Gestion de Empresas</h5>
            <p class="card-text">Crea, Modifica y Elimina Empresas que tienen convenio con nuestra empresa.</p>
          </div>
          <div class="card-footer text-center">
            <a href="../VistaAdminEmpresa/Empresa.php" class="btn btn-primary w-50">Ingresar</a>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
          <div class="icon-large">
            <i class="bi bi-person-fill"></i>
          </div>
          <div class="card-body">
            <h5 class="card-title">Perfil</h5>
            <p class="card-text">Acceso a tu informacion como Usuario.</p>
          </div>
          <div class="card-footer text-center">
            <a href="Seguridad.php" class="btn btn-primary w-50">Ingresar</a>
          </div>
        </div>
      </div>
      
        <div class="col-12 col-md-6 col-lg-4 mb-4 mt-5">
            <div class="">
                <div class="col-12 text-center">
                    <form action="backup.php" method="post">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-database"></i> Realizar Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>
      
      <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
    <div class="container d-flex justify-content-between">
        <!-- Icono de casa con margen a la izquierda -->
        <a href="Dashboard.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <!-- Espacio flexible para centrar el icono del QR -->
        <div class="flex-grow-1 text-center"></div>
        <!-- Icono de usuario con margen a la derecha -->
        <a href="Gestion.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-nut fa-2x"></i>
        </a>
    </div>
    <!-- Botón flotante grande y centrado con icono de QR en la parte inferior -->
    <a href="../VistaAdminAsignacion/Asignacion.php" class="btn btn-primary btn-lg rounded-circle position-fixed" 
       style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-cash-coin fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
    </a>
</footer>
    </div>

</body>

</html>