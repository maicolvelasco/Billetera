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
  <title>Registrar Área de Trabajo</title>
</head>

<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorAdmin/Controlador_PA/ControladorRegistroArea.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-5">
  <h1 class="text-center text-secondary">Nueva Area</h1>

  <!-- Formulario de registro de área -->
  <form action="RegistroArea.php" method="POST">
    <div class="form-group">
      <input type="text" class="form-control" id="Nombre" name="Nombres" placeholder="Nombre del Área" required>
    </div>

    <div class="text-center mt-4 button-group mb-5">
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

</body>

</html>