<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf/notyf.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/notyf/notyf.min.js"></script>
    <title>TopBar Usuario/Empresa</title>
</head>

<body>

<?php
// Incluir el controlador que maneja la lógica de la barra superior para usuario y empresa
$data = include '../../Controlador/ControladorSeccion/ControladorTopBar.php';
// Extraer variables para usarlas en la vista
$rutaFoto = $data['rutaFoto'];
$menu = $data['menu'];
?>

<!-- Barra de navegación superior con Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../../src/LOGO ESQUINA WEB.png" alt="Logo Empresa" width="100" height="auto">
        </a>

        <div class="ms-auto">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= htmlspecialchars($rutaFoto) ?>" alt="Usuario/Empresa" class="rounded-circle" width="40" height="40">
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <?= $menu ?>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Espaciado para que el contenido no se superponga con la barra superior -->
<div class="container mt-4 pt-4">
</div>

<!-- Forzar despliegue con jQuery -->
<script>
    $(document).ready(function() {
        $('#dropdownMenuLink').dropdown();
    });
</script>

</body>

</html>