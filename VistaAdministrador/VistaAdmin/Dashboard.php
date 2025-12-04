<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Meta y Título -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="../../src/LOGO ESQUINA WEB ICONO.png">
    <!-- Bootstrap desde CDN -->
    <link rel="stylesheet" href="https://bootswatch.com/4/lumen/bootstrap.min.css">
    <!-- Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- ECharts desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
</head>

<body>
    <?php
    include("../../Conexion/conexion.php");
    include("../../Controlador/ControladorAdmin/Controlador_Usuario/ControladorDashboard.php");
    include "../../VistaSecciones/Topbar.php";
    ?>
    <div class="container mt-3 mb-5">
<div class="row">
    <!-- Total Asignación -->
    <div class="col-md-4 mb-1">
        <a href="../VistaAdminAsignacion/Historial.php" class="text-decoration-none">
            <div class="card text-center text-white" style="background-color: rgba(74, 211, 72, 0.8);">
                <div class="card-body">
                    <i class="fas fa-wallet fa-3x"></i>
                    <h5 class="card-title mt-2">Total Abonos</h5>
                    <p class="card-text display-4">Bs. <?php echo number_format($totalAsignacion, 2); ?></p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Total Gastos -->
    <div class="col-md-4 mb-1">
        <a href="../VistaAdminAsignacion/Historial.php" class="text-decoration-none">
            <div class="card text-center text-white" style="background-color: rgba(252, 75, 75, 0.8);">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x"></i>
                    <h5 class="card-title mt-2">Total Gastos</h5>
                    <p class="card-text display-4">Bs. <?php echo number_format($totalGastos, 2); ?></p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Diferencia -->
    <div class="col-md-4 mb-1">
        <a href="../VistaAdminAsignacion/Historial.php" class="text-decoration-none">
            <div class="card text-center text-white" style="background-color: rgba(114, 157, 255, 0.8);">
                <div class="card-body">
                    <i class="fas fa-calculator fa-3x"></i>
                    <h5 class="card-title mt-2">Diferencia</h5>
                    <p class="card-text display-4">Bs. <?php echo number_format($diferencia, 2); ?></p>
                </div>
            </div>
        </a>
    </div>
</div>
        <!-- Gráficos de Barras y Circular -->
        <div class="row mt-4">
            <div class="col-12 col-md-7">
                <h5 class="text-center">Abonos y Gastos por Empresa (<?php echo $currentMonthName . ' ' . $currentYear; ?>)</h5>
                <div id="barChart" style="width: 100%; height: 400px;"></div>
            </div>
            <div class="col-12 col-md-5">
                <h5 class="text-center">Porcentaje de Abonos y Gastos (<?php echo $currentMonthName . ' ' . $currentYear; ?>)</h5>
                <div id="pieChart" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
        
        <!-- Gráfico de Líneas -->
        <div class="row mt-4">
            <div class="col-12 col-md-12">
                <h5 class="text-center">Abonos y Gastos Mensuales (Año <?php echo $currentYear; ?>)</h5>
                <div id="lineChart" style="width: 100%; height: 400px;"></div>
            </div>
        </div>

        <!-- Footer fijo con iconos -->
        <footer class="fixed-bottom border-top border-gray py-2" style="background-color: #f0f0f0; border-radius: 15px 15px 0 0;">
            <div class="container d-flex justify-content-between">
                <!-- Icono de casa con margen a la izquierda -->
                <a href="Dashboard.php" class="text-dark" style="margin-left: 10%;">
                    <i class="bi bi-house-door fa-2x"></i>
                </a>
                <!-- Espacio flexible para centrar el icono del QR -->
                <div class="flex-grow-1 text-center"></div>
                <!-- Icono de usuario con margen a la derecha -->
                <a href="Gestion.php" class="text-dark" style="margin-right: 10%;">
                    <i class="bi bi-nut fa-2x"></i>
                </a>
            </div>
            <!-- Botón flotante grande y centrado con icono de QR en la parte inferior -->
            <a href="../VistaAdminAsignacion/Asignacion.php" class="btn btn-primary btn-lg rounded-circle position-fixed" 
               style="bottom: 25px; left: 50%; transform: translateX(-50%); width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-cash-coin fa-2x" style="margin: 0; width: 45px; height: 45px;"></i>
            </a>
        </footer>
    </div>

    <!-- Scripts de Bootstrap y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para los Gráficos -->
    <script>
        // Gráfico de Líneas
        var lineChartDom = document.getElementById('lineChart');
        var lineChart = echarts.init(lineChartDom);
        var lineOption;

        lineOption = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['Abonos', 'Gastos']
            },
            xAxis: {
                type: 'category',
                data: <?php echo json_encode($labels); ?>
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    formatter: 'Bs. {value}'
                }
            },
            series: [
                {
                    name: 'Abonos',
                    type: 'line',
                    data: <?php echo json_encode($importesData); ?>,
                    smooth: true,
                    itemStyle: {
                        color: 'rgba(40, 167, 69, 1)'
                    }
                },
                {
                    name: 'Gastos',
                    type: 'line',
                    data: <?php echo json_encode($gastosData); ?>,
                    smooth: true,
                    itemStyle: {
                        color: 'rgba(220, 53, 69, 1)'
                    }
                }
            ]
        };

        lineChart.setOption(lineOption);

        // Gráfico de Barras
        var barChartDom = document.getElementById('barChart');
        var barChart = echarts.init(barChartDom);
        var barOption;

        barOption = {
            tooltip: {
                trigger: 'axis',
                axisPointer: { 
                    type: 'shadow' 
                }
            },
            legend: {
                data: ['Abonos', 'Gastos']
            },
            xAxis: {
                type: 'category',
                data: <?php echo json_encode($labelsBarChart); ?>
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    formatter: 'Bs. {value}'
                }
            },
            series: [
                {
                    name: 'Abonos',
                    type: 'bar',
                    data: <?php echo json_encode($importesBarData); ?>,
                    itemStyle: {
                        color: 'rgba(40, 167, 69, 0.8)'
                    }
                },
                {
                    name: 'Gastos',
                    type: 'bar',
                    data: <?php echo json_encode($gastosBarData); ?>,
                    itemStyle: {
                        color: 'rgba(220, 53, 69, 0.8)'
                    }
                }
            ]
        };

        barChart.setOption(barOption);

        // Gráfico Circular (Pie)
        var pieChartDom = document.getElementById('pieChart');
        var pieChart = echarts.init(pieChartDom);
        var pieOption;

        pieOption = {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {d}%'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['Abonos', 'Gastos']
            },
            series: [
                {
                    name: 'Abonos vs Gastos',
                    type: 'pie',
                    radius: '50%',
                    data: [
                        {value: <?php echo $totalAsignacion; ?>, name: 'Abonos'},
                        {value: <?php echo $totalGastos; ?>, name: 'Gastos'}
                    ],
                    label: {
                        formatter: '{b}: {d}%'
                    },
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        pieChart.setOption(pieOption);
        
        // Inicializar Toastify (No es necesario una instancia)
        // Toastify no requiere una inicialización previa como Notyf

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
</body>

</html>