<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Librería de Font Awesome -->
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <title>Editar Usuario</title>

    <style>
        /* Añadir un margen inferior a los botones para evitar que queden pegados */
        .button-group {
            margin-bottom: 50px;
            /* Agrega margen inferior para espacio */
        }
    </style>
</head>

<body>

    <?php
    include "../../Conexion/conexion.php";
    include "../../Controlador/ControladorAdmin/Controlador_Usuario/ControladorModificarUsuario.php";
    include "../../VistaSecciones/Topbar.php";
    ?>

    <div class="container mt-3">
        <h1 class="text-center text-secondary">Modificar Usuario</h1>

        <!-- Switch toggle para habilitar o deshabilitar el formulario -->
        <div class="text-center mb-4">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="switch-toggle">
                <label class="custom-control-label" for="switch-toggle" id="switch-label">Deshabilitado</label>
            </div>
        </div>

        <!-- Formulario de edición de usuario -->
        <form id="form-usuario" action="ModificarUsuario.php?id=<?= htmlspecialchars($usuario['Id_Usuario']) ?>"
            method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="form-group">
                <label for="Nombre">Nombre Completo</label>
                <input type="text" class="form-control" id="Nombre" name="Nombre_Completo"
                    value="<?= htmlspecialchars($usuario['Nombre_Completo']) ?>" required disabled>
            </div>

            <div class="form-group">
                <label for="CodigoEmpleado">Código de Empleado</label>
                <input type="text" class="form-control" id="CodigoEmpleado" name="Codigo_empleado"
                    value="<?= htmlspecialchars($usuario['Codigo_empleado']) ?>" required disabled>
            </div>

            <div class="form-group">
                <label for="CI">CI</label>
                <input type="text" class="form-control" id="CI" name="CI"
                    value="<?= htmlspecialchars($usuario['CI']) ?>" required disabled>
            </div>

            <div class="form-group">
                <label for="FechaNacimiento">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="FechaNacimiento" name="Fecha_Nacimiento"
                    value="<?= htmlspecialchars($usuario['Fecha_Nacimiento']) ?>"disabled>
            </div>

            <div class="form-group">
                <label for="CorreoElectronico">Correo Electrónico</label>
                <input type="email" class="form-control" id="CorreoElectronico" name="Correo_Electronico"
                    value="<?= htmlspecialchars($usuario['Correo_Electronico']) ?>" disabled>
            </div>

            <div class="form-group">
                <label for="Telefono">Teléfono</label>
                <input type="text" class="form-control" id="Telefono" name="Telefono"
                    value="<?= htmlspecialchars($usuario['Telefono']) ?>" required disabled>
            </div>

            <div class="form-group">
                <label for="Password">Contraseña</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="Password" name="Password" value="<?= htmlspecialchars($usuario['Password']) ?>" disabled>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="Rol">Rol</label>
                <select class="form-control" id="Rol" name="Rol" required disabled>
                    <?php
                    $roles = $conexion->query("SELECT * FROM roles");
                    while ($rol = $roles->fetch_object()) {
                        $selected = $rol->Id_Rol == $usuario['Rol'] ? 'selected' : '';
                        echo "<option value='$rol->Id_Rol' $selected>$rol->Nombres</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="Area">Área de Trabajo</label>
                <select class="form-control" id="Area" name="Area" disabled>
                    <?php
                    $areas = $conexion->query("SELECT * FROM area_trabajo");
                    while ($area = $areas->fetch_object()) {
                        $selected = $area->Id_Area == $usuario['Area'] ? 'selected' : '';
                        echo "<option value='$area->Id_Area' $selected>$area->Nombres</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="Puesto">Puesto</label>
                <select class="form-control" id="Puesto" name="Puesto" disabled>
                    <?php
                    $puestos = $conexion->query("SELECT * FROM puesto");
                    while ($puesto = $puestos->fetch_object()) {
                        $selected = $puesto->Id_Puesto == $usuario['Puesto'] ? 'selected' : '';
                        echo "<option value='$puesto->Id_Puesto' $selected>$puesto->Nombre</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Selector para Estado -->
            <div class="form-group">
                <label for="Estado">Estado</label>
                <select class="form-control" id="Estado" name="Estado" required disabled>
                    <option value="1" <?= $usuario['Estado'] == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $usuario['Estado'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="Foto">Foto</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="Foto" name="Foto" disabled>
                    <label class="custom-file-label"
                        for="Foto"><?= $usuario['Foto'] ? basename($usuario['Foto']) : 'Ningún archivo seleccionado' ?></label>
                </div>
            </div>

            <!-- Botones con margen inferior -->
            <div class="text-center mt-4 button-group">
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

        <!-- Botón para eliminar el usuario con margen inferior -->
        <div class="text-center mt-1 button-group">
            <a href="../../Controlador/ControladorAdmin/Controlador_Usuario/ControladorEliminarUsuario.php?id=<?= htmlspecialchars($usuario['Id_Usuario']) ?>"
                class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                Eliminar Usuario
            </a>
        </div>
    </div>

    <script>
  // Lógica para alternar el switch y habilitar/deshabilitar el formulario
  const switchToggle = document.getElementById('switch-toggle');
  const switchLabel = document.getElementById('switch-label');
  const form = document.getElementById('form-usuario');
  const inputs = form.querySelectorAll('input, select');

  // Función para deshabilitar los campos del formulario
  function toggleFormFields(disabled) {
    inputs.forEach(input => input.disabled = disabled);
  }

  // Mantener el formulario deshabilitado inicialmente
  toggleFormFields(true);

  // Evento de clic en el switch
  switchToggle.addEventListener('change', function() {
    if (switchToggle.checked) {
      toggleFormFields(false); // Habilitar los campos
      switchLabel.innerText = "Habilitado"; // Cambiar el texto a Habilitado
    } else {
      toggleFormFields(true); // Deshabilitar los campos
      switchLabel.innerText = "Deshabilitado"; // Cambiar el texto a Deshabilitado
    }
  });
</script>

</body>

</html>