<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Importes</title>
    <!-- Bootstrap desde CDN -->
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
</head>

<body>
    <?php include '../../Controlador/ControladorAdmin/Controlador_Asignacion/ControladorHistorial.php'; ?>
    <?php include "../../VistaSecciones/Topbar.php"; ?>

    <div id="contenido" class="container mt-3" style="margin-bottom: 70px;">
        <h2 class="text-center text-secondary">Historial</h2>

        <!-- Formulario de selección de empresa, fechas y búsqueda por nombre -->
        <form id="formSeleccion" method="POST" action="">
            <div class="form-row">
                <!-- Selección de Empresa -->
                <div class="form-group col-6">
                    <select name="id_empresa" id="id_empresa" class="form-control" onchange="this.form.submit()">
                        <option value="">Empresa</option>
                        <?php
                        // Volver a ejecutar la consulta para obtener todas las empresas
                        $result_empresas = $conexion->query($sql_empresas);
                        while ($empresa = $result_empresas->fetch_assoc()):
                        ?>
                            <option value="<?php echo $empresa['Id_Empresa']; ?>" <?php echo $empresa['Id_Empresa'] == $id_empresa ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($empresa['Nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Campo de búsqueda de nombre y código -->
                <div class="form-group col-6 d-flex align-items-end">
                    <div class="w-100">
                        <div class="input-group">
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                                placeholder="Nombre o Codigo"
                                value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fechas y filtros -->
            <div class="form-row">
                <!-- Fecha Inicio -->
                <div class="form-group col-6">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                        value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                </div>
                <!-- Fecha Final -->
                <div class="form-group col-6">
                    <label for="fecha_final">Fecha Final:</label>
                    <input type="date" class="form-control" id="fecha_final" name="fecha_final"
                        value="<?php echo htmlspecialchars($fecha_final); ?>">
                </div>
            </div>

            <!-- Botones de Abono y Consumos -->
            <div class="row mb-3">
                <div class="col-6">
                    <button type="submit" name="accion" value="abono" class="btn btn-info btn-block">Abonos</button>
                </div>
                <div class="col-6">
                    <button type="submit" name="accion" value="consumo" class="btn btn-danger btn-block">Consumo</button>
                </div>
            </div>
        </form>

        <!-- Sección de resultados -->
        <div id="resultados">
            <!-- Aquí se cargarán los resultados después de la búsqueda -->
            <?php if ($mostrar_abono || $mostrar_consumo): ?>
                <?php if ($mostrar_abono): ?>
                    <h5 class="text-center text-info">Abonos</h5>

                    <!-- Títulos de la lista de empleados -->
                    <div class="row mb-2 text-center">
                        <div class="col-3 col-md-2 col-lg-1">
                            <strong>Fecha</strong>
                        </div>
                        <div class="col-6 col-md-7 col-lg-9">
                            <strong>Nombre Completo</strong>
                        </div>
                        <div class="col-2 col-md-3 col-lg-1">
                            <strong>Importe</strong>
                        </div>
                    </div>

                    <div style="max-height: 220px; overflow-y: auto;">                    
                    <!-- Lista de empleados -->
                        <ul class="list-group">
                            <?php if (!empty($lista_abono)): ?>
                                <?php foreach ($lista_abono as $abono): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="text-left">
                                            <?php echo date('d-m-Y', strtotime($abono['Ultimo_agregado'])); ?>
                                        </div>
                                        <div class="col-7 text-center text-truncate">
                                            <strong><?php echo htmlspecialchars($abono['Nombre_Completo']); ?></strong>
                                        </div>
                                        <div class="col-2 text-center">
                                            <?php echo number_format($abono['Importes'], 2); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No se encontraron registros de abono.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                        <!-- Suma total de importes -->
                        <div class="text-right mt-3">
                            <strong>Suma Total de Abonos:</strong> <?php echo number_format($suma_importes, 2); ?>
                        </div>
                <?php elseif ($mostrar_consumo): ?>
                    <h5 class="text-center text-danger">Consumos</h5>

                    <!-- Títulos de la lista de consumos -->
                    <div class="row mb-2 text-center">
                        <div class="col-3 col-md-2 col-lg-1">
                            <strong>Fecha</strong>
                        </div>
                        <div class="col-6 col-md-7 col-lg-9">
                            <strong>Nombre Completo</strong>
                        </div>
                        <div class="col-2 col-md-3 col-lg-1">
                            <strong>Gastos</strong>
                        </div>
                    </div>

                <div style="max-height: 220px; overflow-y: auto;"> 
                    <!-- Lista de consumos -->
                    <ul class="list-group">
                        <?php if (!empty($lista_consumo)): ?>
                            <?php foreach ($lista_consumo as $consumo): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="text-left">
                                        <?php echo date('d-m-Y', strtotime($consumo['Ultimo_agregado'])); ?>
                                    </div>
                                    <div class="col-7 text-center text-truncate">
                                        <strong><?php echo htmlspecialchars($consumo['Nombre_Completo']); ?></strong>
                                    </div>
                                    <div class="col-2 text-center">
                                        <?php echo number_format($consumo['Gastos'], 2); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No se encontraron registros de consumos.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                    <!-- Suma total de gastos -->
                    <div class="text-right mt-3">
                        <strong>Suma Total de Consumos:</strong> <?php echo number_format($suma_gastos, 2); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    <!-- Modal para los botones de descarga -->
    <div class="modal fade" id="descargaModal" tabindex="-1" role="dialog" aria-labelledby="descargaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descargaModalLabel">Selecciona el tipo de Descarga</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Botones de descarga -->
                    <div class="row">
                        <div class="col-12 col-md-6 mb-2">
                            <form method="POST" action="../../Controlador/ControladorAdmin/Controlador_Asignacion/generate_pdf.php" target="_blank">
                                <input type="hidden" name="id_empresa" value="<?php echo htmlspecialchars($id_empresa); ?>">
                                <input type="hidden" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                                <input type="hidden" name="fecha_final" value="<?php echo htmlspecialchars($fecha_final); ?>">
                                <input type="hidden" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                                <input type="hidden" name="accion" value="<?php echo htmlspecialchars($accion); ?>">
                                <button type="submit" class="btn btn-danger w-100">Descargar PDF <i class="fas fa-file-pdf"></i></button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <form method="POST" action="../../Controlador/ControladorAdmin/Controlador_Asignacion/generate_excel.php" target="_blank">
                                <input type="hidden" name="id_empresa" value="<?php echo htmlspecialchars($id_empresa); ?>">
                                <input type="hidden" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                                <input type="hidden" name="fecha_final" value="<?php echo htmlspecialchars($fecha_final); ?>">
                                <input type="hidden" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                                <input type="hidden" name="accion" value="<?php echo htmlspecialchars($accion); ?>">
                                <button type="submit" class="btn btn-success w-100">Descargar Excel <i class="fas fa-file-excel"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
        <div class="container d-flex justify-content-between">
            <!-- Icono de casa con margen a la izquierda -->
            <a href="../VistaAdmin/Dashboard.php" class="text-dark" style="margin-left: 10%;">
                <i class="bi bi-house-door fa-2x"></i>
            </a>
            <!-- Espacio flexible para centrar el icono del QR -->
            <div class="flex-grow-1 text-center"></div>
            <!-- Icono de usuario con margen a la derecha -->
            <a href="Asignacion.php" class="text-dark" style="margin-right: 10%;">
                <i class="bi bi-backspace fa-2x"></i>
            </a>
        </div>
        <!-- Botón flotante grande y centrado con icono de QR en la parte inferior -->
        <button class="btn btn-primary btn-lg rounded-circle position-fixed" data-toggle="modal" data-target="#descargaModal"
            style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-file-earmark-plus fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
        </button>
    </footer>

    </div>

    <!-- JS de Bootstrap y Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>