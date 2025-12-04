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
  <title>Registrar Usuario</title>
  
  <style>
    body {
      padding-bottom: 60px; /* Aumentar el espacio al final de la página */
    }
  </style>
</head>

<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorAdmin/Controlador_Usuario/ControladorRegistroUsuario.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-3" style="margin-top: 100px;">
  <h1 class="text-center text-secondary">Nuevo Usuario</h1>

  <!-- Formulario de registro de usuario -->
  <form action="RegistroUsuario.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <input type="text" class="form-control" id="Nombre" name="Nombre_Completo" placeholder="Nombre Completo" required>
    </div>

    <div class="form-group">
      <input type="text" class="form-control" id="CodigoEmpleado" name="Codigo_empleado" placeholder="Código de Empleado" required>
    </div>

    <div class="form-group">
      <input type="text" class="form-control" id="CI" name="CI" placeholder="Número de CI" required>
    </div>

    <div class="form-group">
      <input type="date" class="form-control" id="FechaNacimiento" name="Fecha_Nacimiento">
    </div>

    <div class="form-group">
      <input type="email" class="form-control" id="CorreoElectronico" name="Correo_Electronico" placeholder="Correo Electrónico">
    </div>

    <div class="form-group">
      <input type="text" class="form-control" id="Telefono" name="Telefono" placeholder="Número de Teléfono" required>
    </div>

    <div class="form-group">
      <input type="password" class="form-control" id="Password" name="Password" placeholder="Contraseña" required>
    </div>

<!-- Selector para Rol -->
<div class="form-group">
  <select class="form-control" id="Rol" name="Rol" required>
    <option value="" disabled selected>Seleccionar Rol</option>
    <?php
    $roles = $conexion->query("SELECT * FROM roles");
    while ($rol = $roles->fetch_object()) {
        echo "<option value='$rol->Id_Rol'>$rol->Nombres</option>";
    }
    ?>
  </select>
</div>

<!-- Selector para Área de Trabajo -->
<div class="form-group">
  <select class="form-control" id="Area" name="Area">
    <option value="" disabled selected>Seleccionar Área</option>
    <?php
    $areas = $conexion->query("SELECT * FROM area_trabajo");
    while ($area = $areas->fetch_object()) {
        echo "<option value='$area->Id_Area'>$area->Nombres</option>";
    }
    ?>
  </select>
</div>

<!-- Selector para Puesto -->
<div class="form-group">
  <select class="form-control" id="Puesto" name="Puesto">
    <option value="" disabled selected>Seleccionar Puesto</option>
    <?php
    $puestos = $conexion->query("SELECT * FROM puesto");
    while ($puesto = $puestos->fetch_object()) {
        echo "<option value='$puesto->Id_Puesto'>$puesto->Nombre</option>";
    }
    ?>
  </select>
</div>

<!-- Selector para Estado -->
<div class="form-group">
  <select class="form-control" id="Estado" name="Estado" required>
    <option value="" disabled selected>Seleccionar Estado</option>
    <option value="1">Activo</option>
    <option value="0">Inactivo</option>
  </select>
</div>

    <div class="form-group">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="Foto" name="Foto">
        <label class="custom-file-label" for="Foto">Seleccionar archivo</label>
      </div>
    </div>

    <!-- Botones con margen inferior adicional -->
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

<script>
  // Mostrar el nombre del archivo seleccionado en el input file
  $('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
  });
</script>

</body>

</html>