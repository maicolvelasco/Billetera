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
  <title>Editar Puesto</title>
</head>

<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorAdmin/Controlador_PA/ControladorModificarPuesto.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-5">
  <h1 class="text-center text-secondary">Modificar Puesto</h1>

  <!-- Formulario de edición de puesto -->
  <form action="EditarPuesto.php?id=<?= htmlspecialchars($puesto['Id_Puesto']) ?>" method="POST" class="mt-4">
    <div class="form-group">
      <label for="Nombre">Nombre del Puesto</label>
      <input type="text" class="form-control" id="Nombre" name="Nombre" value="<?= htmlspecialchars($puesto['Nombre']) ?>" required>
    </div>

    <div class="text-center mt-4">
        <div class="row">
            <div class="col-6">
                <button type="submit" name="btnEditar" class="btn btn-primary w-100 d-inline-block mx-1">Guardar Cambios</button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-secondary w-100 d-inline-block mx-1" onclick="history.back();">Retroceder</button>
            </div>
        </div>
    </div>
  </form>

  <!-- Botón para eliminar el puesto -->
  <div class="text-center mt-4">
    <a href="../../Controlador/ControladorAdmin/Controlador_PA/ControladorEliminarPuesto.php?id=<?= htmlspecialchars($puesto['Id_Puesto']) ?>"
      class="btn btn-danger w-100"
      onclick="return confirm('Estas seguro de que deseas eliminar este puesto?');">
      Eliminar Puesto
    </a>
  </div>
</div>

</body>

</html>