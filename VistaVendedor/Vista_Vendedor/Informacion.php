<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informaci車n del Usuario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="icon" href="../../public/img/icono.jpg">
</head>
<body>
    <?php include "../../Conexion/conexion.php"; ?>
    <?php include "../../Controlador/ControladorVendedor/ControladorInformacion.php"; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                <?php if (isset($usuario)) { ?>
                    <h1><?php echo htmlspecialchars($usuario['Nombre_Completo']); ?></h1>
                    <h2>CI: <?php echo htmlspecialchars($usuario['CI']); ?></h2>
                <?php } ?>
            </div>
        </div>

        <!-- Formulario para ingresar un gasto a restar -->
        <div class="mt-4">
            <form id="gasto-form" action="" method="POST" class="text-center">
                <div class="form-group">
                    <input type="number" class="form-control" placeholder="Ingrese el Monto" name="gasto" step="0.01" min="0" required style="height: 50px; font-size: 1.7em;">
                </div>

                
                <button type="button" class="form-control btn btn-secondary" onclick="mostrarPin()" style="height: 50px; font-size: 1.7em;">Confirmar</button>

                <!-- Campo del PIN (inicialmente oculto) -->
                <div id="pin-container" class="mt-3" style="display:none;">
                    <div class="form-group">
                        <input type="number" class="form-control" placeholder="Ingrese el PIN" name="pin" style="height: 50px; font-size: 1.7em;" required>
                    </div>
                        <button type="submit" name="aceptar" class="btn btn-primary rounded-circle position-fixed"
                                id="submit-button" style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px;
                                display: none; display: flex; align-items: center; justify-content: center; z-index: 9999;">
                            <i class="bi bi-check-lg fa-3x" style="line-height: 70px; margin: 0;"></i>
                        </button>
                </div>
            </form>

            <?php if (isset($mensaje)) { echo $mensaje; } ?>
        </div>
        <!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0; z-index: 2000;">
    <div class="container d-flex justify-content-between">
        <a href="DashboardV.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>

</footer>
    </div>

    <script>
        function mostrarPin() {
            document.getElementById('pin-container').style.display = 'block';
            document.getElementById('submit-button').style.display = 'inline';
        }
    </script>
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

        // Mostrar notificaciones basadas en las variables de sesión
        <?php
            if (isset($_SESSION['success'])) {
                echo "mostrarExito('" . addslashes($_SESSION['success']) . "');";
                unset($_SESSION['success']);
            }

            if (isset($_SESSION['error'])) {
                echo "mostrarError('" . addslashes($_SESSION['error']) . "');";
                unset($_SESSION['error']);
            }
        ?>
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>