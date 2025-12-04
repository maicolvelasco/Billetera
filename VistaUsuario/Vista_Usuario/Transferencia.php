<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Transferencia</title>
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <style>
        body {
            padding-bottom: 100px; /* Espacio para el footer */
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f0f0f0;
            border-radius: 15px 15px 0 0;
        }
        /* Estilo para asegurar que Select2 se adapte bien */
        .select2-container--default .select2-selection--single {
            height: 38px; /* Ajustar altura para coincidir con otros inputs */
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding: 6px 12px;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 10px;
        }
    </style>
</head>
<body>

<?php
include "../../Conexion/conexion.php";
include "../../Controlador/ControladorUsuario/ControladorTransferencia.php";
include "../../VistaSecciones/Topbar.php";
?>

<div class="container mt-4">
    <h3 class="text-center">Realizar Transferencia</h3>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="Transferencia.php">
        <div class="form-group">
            <label for="id_usuario_receptor">Nombre del usuario:</label>
            <select class="form-control select2" id="id_usuario_receptor" name="id_usuario_receptor" required>
                <option value="">Seleccione un usuario</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo $usuario['Id_Usuario']; ?>"><?php echo htmlspecialchars($usuario['Nombre_Completo']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_empresa">Empresa:</label>
            <select class="form-control select2" id="id_empresa" name="id_empresa" required>
                <option value="">Seleccione una empresa</option>
                <?php foreach ($empresas_usuario as $empresa): ?>
                    <option value="<?php echo $empresa['Id_Empresa']; ?>"><?php echo ($empresa['Nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="monto">Monto a transferir:</label>
            <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Enviar Monto</button>
    </form>
</div>

<!-- Footer fijo con iconos -->
<footer class="fixed-bottom border-top border-gray py-2">
    <div class="container d-flex justify-content-between">
        <a href="DashboardU.php" class="text-dark" style="margin-left: 10%;">
            <i class="bi bi-house-door fa-2x"></i>
        </a>
        <div class="flex-grow-1 text-center"></div>
        <a href="Perfil.php" class="text-dark" style="margin-right: 10%;">
            <i class="bi bi-person fa-2x"></i>
        </a>
    </div>
    <a href="Compras.php" class="btn btn-secondary btn-lg rounded-circle position-fixed" 
       style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-arrow-left fa-2x" style="margin: 0;"></i>
    </a>
</footer>

<!-- Enlaces a jQuery (si no lo tienes ya) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Enlaces a JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#id_usuario_receptor').select2({
        placeholder: 'Seleccione un usuario',
        allowClear: true,
        width: '100%' // Asegurar que se ajuste al contenedor
    });
    $('#id_empresa').select2({
        placeholder: 'Seleccione una empresa',
        allowClear: true,
        width: '100%' // Asegurar que se ajuste al contenedor
    });
});
</script>

</body>
</html>