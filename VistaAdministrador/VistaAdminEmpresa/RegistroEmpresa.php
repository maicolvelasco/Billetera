<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
  <title>Registrar Empresa</title>
</head>

<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorAdmin/Controlador_Cuenta/ControladorRegistroEmpresa.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-5"> <!-- Margen superior ajustado para evitar que lo tape el topbar -->
  <h1 class="text-center text-secondary">Nueva Empresa</h1>

  <!-- Formulario de registro -->
  <form action="RegistroEmpresa.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Nombre" required>
    </div>

    <div class="form-group">
      <input type="text" class="form-control" id="Usuario" name="Usuario" placeholder="Usuario" required>
    </div>

    <div class="form-group">
      <input type="password" class="form-control" id="Password" name="Password" placeholder="Contrasena" required>
    </div>

    <div class="form-group">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="Foto" name="Foto" required>
        <label class="custom-file-label" for="Foto">Seleccionar archivo</label>
      </div>
    </div>

    <div class="text-center mt-4">
    <div class="row">
            <div class="col-6">
              <button type="submit" name="btnregistrar" class="btn btn-primary w-100">Registrar</button>
            </div>
            <div class="col-6">
              <button type="button" class="btn btn-secondary w-100" onclick="history.back();">Retroceder</button>
            </div>
        </div>
    </div>
  </form>
</div>

<script>
  // Mostrar el nombre del archivo seleccionado en el input file
  $('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
  });
</script>

</body>

</html>