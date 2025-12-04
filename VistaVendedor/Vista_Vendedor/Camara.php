<?php
session_start();
if (!isset($_SESSION["Nombre_Empresa"])) {
    header("location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LECQR</title>
  <link rel="icon" href="../../public/img/icono.jpg">
  <!-- Bootstrap CDN -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  
</head>

<body class="bg-light d-flex flex-column vh-100 mt-3">

    <?php include "../../Conexion/conexion.php"; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

  <div class="container text-center">
    <h1 class="display-4 text-secondary mb-4">Lector QR</h1>

    <!-- Video para la cámara con Bootstrap, diseño alto para móviles -->
    <div class="embed-responsive embed-responsive-1by1 mb-4" style="max-height: 80vh;">
      <video id="video" class="embed-responsive-item border border-secondary rounded" playsinline autoplay></video>
    </div>

    <!-- Botón para volver atrás -->
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
      <i class="fas fa-arrow-left"></i> Volver Atrás
    </a>
  </div>

  <!-- JavaScript de Bootstrap y dependencias -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.0.0/dist/jsQR.js"></script>

  <script>
    let scanning = true; // Variable para controlar el escaneo
    let scanTimeout; // Variable para controlar el tiempo de escaneo

    // Verificar si la API getUserMedia es compatible con el navegador
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert("La API de acceso a la cámara no está soportada por este navegador.");
    } else {
      // Enumerar dispositivos para verificar si existen cámaras
      navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
          const videoDevices = devices.filter(device => device.kind === "videoinput");
          if (videoDevices.length === 0) {
            alert("No se encontraron cámaras en este dispositivo.");
          } else {
            // Iniciar la cámara y el escaneo
            startCamera("environment");

            // Iniciar un temporizador de 1 minuto para detener el escaneo
            scanTimeout = setTimeout(stopScanning, 60000); // 60 segundos
          }
        })
        .catch(function(err) {
          alert(`Error al enumerar dispositivos: ${err.message}`);
        });
    }

    // Función para iniciar la cámara
    function startCamera(facingMode) {
      navigator.mediaDevices.getUserMedia({
          video: { facingMode: facingMode }
        })
        .then(function(stream) {
          const video = document.getElementById("video");
          video.srcObject = stream;
          video.play();

          const canvasElement = document.createElement("canvas");
          const canvas = canvasElement.getContext("2d");
          scanQRCode();

          function scanQRCode() {
            if (scanning && video.readyState === video.HAVE_ENOUGH_DATA) {
              canvasElement.width = video.videoWidth;
              canvasElement.height = video.videoHeight;
              canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
              const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
              const code = jsQR(imageData.data, imageData.width, imageData.height);

              if (code) {
                clearTimeout(scanTimeout); // Detener el temporizador si encuentra un código
                scanning = false; // Detener el escaneo
                window.location.href = `Informacion.php?ci=${encodeURIComponent(code.data)}`;
              }
            }
            if (scanning) {
              requestAnimationFrame(scanQRCode);
            }
          }
        })
        .catch(function(err) {
          console.error(`Error al acceder a la cámara (${facingMode}):`, err);
          if (err.name === "NotAllowedError") {
            alert("Permisos para acceder a la cámara denegados.");
          } else if (err.name === "NotFoundError") {
            alert("No se encontró ninguna cámara en el dispositivo.");
          } else if (facingMode === "environment") {
            startCamera("user");
          } else {
            alert(`Error al acceder a la cámara: ${err.message}`);
          }
        });
    }

    // Función para detener el escaneo después de 1 minuto si no se encuentra ningún código QR
    function stopScanning() {
      scanning = false;
      alert("No se encontró ningún código QR. Por favor, intente de nuevo.");
    }
  </script>
</body>

</html>