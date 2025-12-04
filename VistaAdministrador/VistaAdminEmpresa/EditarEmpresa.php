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
  <title>Editar Empresa</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorAdmin/Controlador_Cuenta/ControladorModificarEmpresa.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-5">
  <h1 class="text-center text-secondary">Modificar Empresa</h1>

  <!-- Formulario de edición -->
  <form action="EditarEmpresa.php?id=<?= htmlspecialchars($empresa['Id_Empresa']) ?>" method="POST" enctype="multipart/form-data" class="mt-4">
    <div class="form-group">
      <label for="Nombre">Nombre</label>
      <input type="text" class="form-control" id="Nombre" name="Nombre" value="<?= htmlspecialchars($empresa['Nombre']) ?>" required>
    </div>

    <div class="form-group">
      <label for="Usuario">Usuario</label>
      <input type="text" class="form-control" id="Usuario" name="Usuario" value="<?= htmlspecialchars($empresa['Usuario']) ?>" required>
    </div>

    <div class="form-group">
      <label for="Password">Contraseña</label>
      <div class="input-group">
        <input type="text" class="form-control" id="Password" name="Password" value="<?= htmlspecialchars($empresa['Password']) ?>">
        <div class="input-group-append">
          </button>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="Foto">Foto</label>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="Foto" name="Foto">
        <label class="custom-file-label" for="Foto"><?= $empresa['Foto'] ? basename($empresa['Foto']) : 'Ningún archivo seleccionado' ?></label>
      </div>
    </div>

    <div class="text-center mt-4">
        <div class="row">
            <div class="col-6">
                <button type="submit" name="btnEditar" class="btn btn-primary w-100">Guardar Cambios</button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-secondary w-100" onclick="history.back();">Retroceder</button>
            </div>
        </div>
    </div>
  </form>

  <!-- Botón para eliminar la empresa -->
  <div class="text-center mt-4">
    <a href="../../Controlador/ControladorAdmin/Controlador_Cuenta/ControladorEliminarEmpresa.php?id=<?= htmlspecialchars($empresa['Id_Empresa']) ?>"
      class="btn btn-danger w-100"
      onclick="return confirm('¿Estás seguro de que deseas eliminar esta empresa?');">
      Eliminar Empresa
    </a>
  </div>
</div>

<script>
  // Mostrar el nombre del archivo seleccionado en el input file
  $('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
  });

  // Mostrar/Ocultar contraseña
  document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('Password');
    const passwordFieldType = passwordField.getAttribute('type');
    if (passwordFieldType === 'password') {
      passwordField.setAttribute('type', 'text');
      this.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
    } else {
      passwordField.setAttribute('type', 'password');
      this.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
    }
  });
</script>

</body>

</html>