<!DOCTYPE html>
<html lang="es"> <!-- Cambiado a 'es' para idioma español -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Empleados</title>
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
</head>

<body>
    <?php 
        include "../../Conexion/conexion.php"; 
        include '../../Controlador/ControladorAdmin/Controlador_Asignacion/ControladorAsignacion.php'; 
        include "../../VistaSecciones/Topbar.php"; 
    ?>

    <div id="contenido" class="container mt-3" style="margin-bottom: 90px;">
        <h2 class="text-center text-secondary">ASIGNACIÓN DE EMPLEADOS</h2>

        <!-- Selector de empresa -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_empresa">Seleccione la Empresa:</label>
                <select name="id_empresa" id="id_empresa" class="form-control" onchange="this.form.submit()">
                    <option value="">Seleccionar Empresa</option>
                    <?php while ($empresa = $result_empresas->fetch_assoc()): ?>
                        <option value="<?php echo $empresa['Id_Empresa']; ?>" <?php echo $empresa['Id_Empresa'] == $id_empresa ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($empresa['Nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <!-- Mostrar el contenido solo si una empresa está seleccionada -->
        <?php if ($id_empresa): ?>
            <div class="text-center mb-4 d-flex justify-content-center align-items-center">
                <input type="number" id="importe_todos" placeholder="Ingrese un importe" value="10.00"
                    class="form-control w-50 w-md-25 mr-2" style="font-size: 1.2em;" />
                <button id="asignar_importe_todos" class="btn btn-warning col-6 col-md-6 mt-0 mt-md-0"
                    style="font-size: 1.2em;">Asignar</button>
            </div>

            <!-- Títulos de la lista de empleados -->
            <div class="row mb-2 text-center">
                <div class="col-1">
                    <input type="checkbox" id="select_all" />
                </div>
                <div class="col-7">
                    <strong>Nombre Completo</strong>
                </div>
                <div class="col-4">
                    <strong>Importe</strong>
                </div>
            </div>

            <!-- Lista de empleados -->
            <form id="formAsignacion" method="POST" action="">
                <input type="hidden" name="id_empresa" value="<?php echo htmlspecialchars($id_empresa); ?>">
                <ul class="list-group" style="max-height: 220px; overflow-y: auto;">
                    <?php
                    // Ordenar empleados por nombre completo
                    $usuarios = [];
                    while ($row = $result_usuarios->fetch_assoc()) {
                        $usuarios[] = $row;
                    }
                    usort($usuarios, function ($a, $b) {
                        return strcmp($a['Nombre_Completo'], $b['Nombre_Completo']);
                    });

                    foreach ($usuarios as $usuario): ?>
                        <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                            <div class="col-1">
                                <input type="checkbox" class="userCheckbox" name="checkbox[]"
                                    value="<?php echo htmlspecialchars($usuario['Id_Usuario']); ?>" />
                            </div>
                            <div class="col-7 text-center">
                                <strong><?php echo htmlspecialchars($usuario['Nombre_Completo']); ?></strong>
                            </div>
                            <div class="col-4">
                                <input type="number" name="importe_diario[]" class="form-control importe_diario"
                                    style="font-size: 1.1em;" value="0.00" step="0.01" />
                                <input type="hidden" name="id_usuario[]" value="<?php echo htmlspecialchars($usuario['Id_Usuario']); ?>" />
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="text-center mt-2">
                    <input type="submit" id="asignarButton" class="btn btn-primary w-100 py-2" value="Abonar">
                </div>
            </form>
        <?php else: ?>
            <!-- Mensaje cuando no se ha seleccionado una empresa -->
            <div class="alert alert-info text-center">
                Por favor, seleccione una empresa para continuar.
            </div>
        <?php endif; ?>
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
        <a href="Historial.php" class="btn btn-primary btn-lg rounded-circle position-fixed"
            style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-file-earmark-spreadsheet fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
        </a>
    </footer>

    <!-- Tu script JavaScript -->
    <script>
        // Función para mostrar notificaciones de éxito
        function mostrarExito(mensaje) {
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top", // 'top' o 'bottom'
                position: "right", // 'left', 'center' o 'right'
                backgroundColor: "#4caf50", // Verde para éxito
                close: true
            }).showToast();
        }

        // Función para mostrar notificaciones de error
        function mostrarError(mensaje) {
            Toastify({
                text: mensaje,
                duration: 5000,
                gravity: "top", // 'top' o 'bottom'
                position: "right", // 'left', 'center' o 'right'
                backgroundColor: "#f44336", // Rojo para errores
                close: true
            }).showToast();
        }

        // Evitar que el formulario se envíe al asignar importe a todos
        document.getElementById("asignar_importe_todos").addEventListener("click", function (e) {
            e.preventDefault(); // Evita que el formulario se envíe.
            var importeTodos = parseFloat(document.getElementById("importe_todos").value);
            if (isNaN(importeTodos) || importeTodos < 0) {
                mostrarError('Por favor, ingrese un importe válido.');
                return;
            }
            var checkboxes = document.querySelectorAll(".userCheckbox");
            var importeInputs = document.querySelectorAll(".importe_diario");

            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) { // Solo asignar importe si el checkbox está marcado
                    importeInputs[i].value = importeTodos.toFixed(2); // Asignar el valor solo a los seleccionados
                } else {
                    importeInputs[i].value = '0.00'; // Limpiar el valor si no está seleccionado
                }
            }
            mostrarExito('Importes asignados a los seleccionados.');
        });

        // Checkbox para seleccionar/desmarcar todos los empleados
        document.getElementById("select_all").addEventListener("change", function () {
            var checkboxes = document.querySelectorAll(".userCheckbox");
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });

        // Enviar formulario de asignación y refrescar la página
        document.getElementById("formAsignacion").addEventListener("submit", function (e) {
            e.preventDefault(); // Evitar el comportamiento por defecto del submit

            var asignarButton = document.getElementById("asignarButton");
            var empresaId = document.getElementById("id_empresa").value; // Obtener el ID de la empresa seleccionada

            // Serializar los datos del formulario
            var formData = new FormData(this);

            // Enviar el formulario mediante AJAX
            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())  // Parsear la respuesta
                .then(data => {
                    // Aquí puedes manejar la respuesta del servidor
                    // En lugar de mostrar el mensaje ahora, lo almacenamos en localStorage
                    localStorage.setItem('toastMessage', 'Importes asignados correctamente.');
                    // Refrescar la página inmediatamente
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Guardar mensaje de error en localStorage
                    localStorage.setItem('toastError', 'Ocurrió un error al asignar los importes.');
                    location.reload();
                });
        });

        // Mostrar mensajes almacenados en localStorage después del refresco
        window.addEventListener('DOMContentLoaded', (event) => {
            // Verificar si hay un mensaje en localStorage
            const toastMessage = localStorage.getItem('toastMessage');
            if (toastMessage) {
                // Mostrar el mensaje
                mostrarExito(toastMessage);
                // Eliminar el mensaje de localStorage
                localStorage.removeItem('toastMessage');
            }
            const toastError = localStorage.getItem('toastError');
            if (toastError) {
                // Mostrar el mensaje de error
                mostrarError(toastError);
                // Eliminar el mensaje de localStorage
                localStorage.removeItem('toastError');
            }
        });
    </script>

</body>

</html>