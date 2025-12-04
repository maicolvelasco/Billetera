<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billetera Duralit</title>
    <link rel="icon" type="image/png" href="./src/LOGO ESQUINA WEB ICONO.png">
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <style>
        .logo {
            width: 150px;
            height: auto;
            cursor: pointer;
        }
        .modal-custom {
            background: linear-gradient(135deg, #f6f8f9 0%, #e5ebee 100%);
        }
        .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .contact-info i {
            margin-right: 10px;
            color: #007bff;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .profile-img {
            width: 80px;  /* Reducido de 100px a 80px */
            height: 80px; /* Reducido de 100px a 80px */
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .contact-link {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .contact-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container d-flex flex-column align-items-center vh-100 mt-5">
        
        <!-- Logo centrado con tamaño ajustado -->
        <img id="logoImage" src="./src/LOGO ESQUINA WEB.png" alt="Logo" class="mb-4 logo">

        <!-- Contenedor del formulario de login -->
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
            <form action="" method="post">
                <h1 class="text-center mb-4">Ingresar</h1>

                <?php
                if (file_exists("Conexion/conexion.php")) {
                    include "Conexion/conexion.php";
                } else {
                    die("No se encontró el archivo de conexión.");
                }

                if (file_exists("Controlador/login.php")) {
                    include "Controlador/login.php";
                } else {
                    die("No se encontró el archivo del controlador de login.");
                }
                ?>

                <!-- Input de usuario sin label -->
                <div class="mb-3">
                    <input type="text" id="Codigo_empleado" name="Codigo_empleado" class="form-control" placeholder="Usuario" required>
                </div>

                <!-- Input de contraseña sin label -->
                <div class="mb-3">
                    <input type="password" id="Password" name="Password" class="form-control" placeholder="Contraseña" required>
                </div>

                <!-- Botón de ingresar -->
                <div class="d-grid">
                    <input name="btningresar" class="btn btn-primary" type="submit" value="Ingresar">
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Profesional -->
    <div class="modal fade" id="logoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-custom shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title w-100 text-center" style="color: #333;">
                        <strong>Billetera Digital</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <!-- Foto de perfil o logo de la empresa -->
                            <img src="./src/LOGO ESQUINA WEB ICONO.png" alt="Perfil" class="profile-img mb-3">
                        </div>
                        <div class="col-md-8 text-start">
                            <div class="contact-info">
                                <i class="fas fa-building"></i>
                                <strong>Empresa:</strong> Duralit
                            </div>
                            <div class="contact-info">
                                <i class="fas fa-user-tie"></i>
                                <strong>Ingeniero:</strong> Andres Condorety
                            </div>
                            <div class="contact-info">
                                <i class="fas fa-code"></i>
                                <strong>Desarrollador:</strong> Maicol William Arratia Velasco
                            </div>
                            <div class="contact-info">
                                <i class="fas fa-phone"></i>
                                <strong>Número:</strong> 
                                <a href="https://wa.me/+59160776373" target="_blank" class="contact-link ms-2">
                                    +591 60776373
                                </a>
                            </div>
                            <div class="contact-info">
                                <i class="fas fa-envelope"></i>
                                <strong>Correo:</strong> 
                                <a href="mailto:maicolarratia4@gmail.com" class="contact-link ms-2">
                                    maicolarratia4@gmail.com
                                </a>
                            </div>
                            <div class="contact-info">
                                <i class="fab fa-github"></i>
                                <strong>GitHub:</strong> 
                                <a href="https://github.com/Maik1704" target="_blank" class="contact-link ms-2">
                                    Maicol-Arratia
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <a href="https://docs.google.com/document/d/1fCE--ORv-4rmD7LPfqOQf0Tm5TMtKH4l/export?format=pdf" 
                       class="btn btn-custom" 
                       download="Hoja_de_Vida_Maicol.pdf">
                        <i class="fas fa-file-download me-2"></i>Descargar Hoja de Vida
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlace a Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration);
                    })
                    .catch(error => {
                        console.log('Error en el registro del ServiceWorker:', error);
                    });
            });
        }

        // Lógica para mostrar el modal al hacer clic 5 veces en la imagen
        const logoImage = document.getElementById('logoImage');
        let clickCount = 0;
        let lastClickTime = 0;

        logoImage.addEventListener('click', (event) => {
            const currentTime = new Date().getTime();
            
            // Resetear el conteo si han pasado más de 2 segundos desde el último clic
            if (currentTime - lastClickTime > 2000) {
                clickCount = 0;
            }

            clickCount++;
            lastClickTime = currentTime;

            if (clickCount === 5) {
                const logoModal = new bootstrap.Modal(document.getElementById('logoModal'));
                logoModal.show();
                clickCount = 0; // Resetear el conteo
            }
        });
    </script>
</body>

</html>