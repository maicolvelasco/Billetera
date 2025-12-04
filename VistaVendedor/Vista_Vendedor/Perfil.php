<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Meta y enlaces CSS (mantienen los mismos) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de la Empresa</title>
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Enlaces adicionales -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> 
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
</head>

<body>
    <?php include "../../Conexion/conexion.php"; ?>
    <?php include "../../Controlador/ControladorVendedor/ControladorPerfil.php"; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

    <div class="container mt-2">
        <div class="card shadow-sm">
            <div class="card-header text-center bg-info text-white">
                <h2>Perfil de la Empresa</h2>
            </div>

            <div class="card-body text-center">
                <div class="mb-4">
                    <?php
                    // Definir la ruta de la foto
                    if (!empty($fotoActual) && $fotoActual !== 'sinfoto.png') {
                        $rutaFoto = "/uploads/" . htmlspecialchars($fotoActual) . "?t=" . time();
                    } else {
                        $rutaFoto = "../../src/sinfoto.png"; // Ruta a la imagen por defecto
                    }
                    ?>
                    <img src="<?= $rutaFoto ?>" id="profile-pic" alt="Foto de perfil" class="rounded-circle" width="150" height="150">
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3 text-center">
                                <label for="usuarioEmpresa">Usuario</label>
                                <input type="text" class="form-control text-center" id="usuarioEmpresa" value="<?= htmlspecialchars($usuarioEmpresa) ?>" readonly>
                            </div>
                            
                            <!-- Campo para modificar la contrase�0�9a con ��cono de ojo -->
                            <div class="form-group mb-3 text-center">
                                <label for="passwordEmpresa">Contrasena</label>
                                <div class="input-group">
                                    <input type="password" class="form-control text-center" id="passwordEmpresa" name="passwordEmpresa" value="<?= htmlspecialchars($passwordEmpresa) ?>" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom file input con Bootstrap -->
                            <div class="form-group mb-4">
                                <label for="profile-upload" class="form-label">Seleccionar imagen</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="profile-upload" name="profile-upload" accept="image/*">
                                    <label class="custom-file-label" for="profile-upload">Elige un archivo...</label>
                                </div>
                            </div>

                            <!-- Bot��n flotante -->
                            <button type="submit" class="btn btn-primary btn-lg rounded-circle position-fixed" 
                                style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; z-index: 2000;">
                                <i class="fa-regular fa-floppy-disk fa-2x" style="font-size: 1.5em;"></i>
                            </button>

                            <!-- Footer fijo con z-index menor -->
                            <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0; z-index: 1000;">
                                <div class="container d-flex justify-content-between">
                                    <!-- Icono de casa con margen a la izquierda -->
                                    <a href="DashboardV.php" class="text-dark" style="margin-left: 10%;">
                                        <i class="bi bi-house-door fa-2x"></i>
                                    </a>
                                    <!-- Espacio flexible para centrar el icono del perfil -->
                                    <div class="flex-grow-1 text-center"></div>
                                    <!-- Icono de usuario con margen a la derecha -->
                                    <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
                                        <i class="bi bi-person fa-2x"></i>
                                    </a>
                                </div>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        // Mostrar el nombre del archivo seleccionado
        document.getElementById('profile-upload').addEventListener('change', function(event) {
            var input = event.target;
            var fileName = input.files[0].name;
            var label = input.nextElementSibling; // El label que sigue al input
            label.textContent = fileName;
        });

        // Previsualizaci��n de la imagen seleccionada
        document.getElementById("profile-upload").onchange = function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById("profile-pic");
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
    <script>
        // Funci��n para mostrar notificaciones de ��xito
        function mostrarExito(mensaje) {
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top", // 'top' o 'bottom'
                position: "right", // 'left', 'center' o 'right'
                backgroundColor: "#4caf50", // Verde para ��xito
                close: true
            }).showToast();
        }

        // Funci��n para mostrar notificaciones de error
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

        // Mostrar notificaciones basadas en las variables de sesi��n
        <?php if (isset($_SESSION['success'])): ?>
            mostrarExito("<?php echo addslashes($_SESSION['success']); ?>");
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            mostrarError("<?php echo addslashes($_SESSION['error']); ?>");
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
    
    <!-- Script para toggle de contrase�0�9a -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('passwordEmpresa');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordInput.readOnly = false;
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordInput.readOnly = true;
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>