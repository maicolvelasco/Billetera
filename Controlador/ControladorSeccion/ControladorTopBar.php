<?php
// Incluir la conexión a la base de datos
include '../../Conexion/conexion.php';

// Inicialización de variables
$tipoLogin = null;
$rutaFoto = '../../src/sinfoto.png'; // Imagen predeterminada
$menu = '';

// Verificar si un usuario está logueado
if (isset($_SESSION['Nombre_Completo'])) {
    $tipoLogin = 'usuario';
    $nombreUsuario = $_SESSION['Nombre_Completo'];

    // Consulta SQL para obtener la foto del usuario
    $sql = "SELECT Rol, Foto FROM usuario WHERE Nombre_Completo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $stmt->bind_result($rolUsuario, $fotoUsuario);

    // Si el usuario existe, obtener los datos
    if ($stmt->fetch()) {
        // Verificar si la foto está disponible y asignarla
        if (!empty($fotoUsuario)) {
            if (strpos($fotoUsuario, 'uploads/') === false) {
                $rutaFoto = '/uploads/' . $fotoUsuario; // Ajustar ruta base si es necesario
            } else {
                $rutaFoto = $fotoUsuario; // Si ya tiene la ruta completa
            }
        }
        
        // Generar el menú según el rol del usuario
        if ($rolUsuario == 1) { // Usuario administrador
            $menu = '
                <a class="dropdown-item" href="../../VistaAdministrador/VistaAdmin/Seguridad.php">Ver Perfil</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../../VistaSecciones/logout.php">Cerrar Sesión</a>
            ';
        } elseif ($rolUsuario == 2) { // Usuario normal
            $menu = '
                <a class="dropdown-item" href="../../VistaUsuario/Vista_Usuario/Perfil.php">Ver Perfil</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../../VistaSecciones/logout.php">Cerrar Sesión</a>
            ';
        }
    }

    $stmt->close();
} elseif (isset($_SESSION['Nombre_Empresa'])) { // Verificar si una empresa está logueada
    $tipoLogin = 'empresa';
    $nombreEmpresa = $_SESSION['Nombre_Empresa'];

    // Consulta SQL para obtener la foto de la empresa
    $sql = "SELECT Foto FROM empresa WHERE Nombre = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nombreEmpresa);
    $stmt->execute();
    $stmt->bind_result($fotoEmpresa);

    // Si la empresa existe, obtener los datos
    if ($stmt->fetch()) {
        if (!empty($fotoEmpresa)) {
            if (strpos($fotoEmpresa, 'uploads/') === false) {
                $rutaFoto = '/uploads/' . $fotoEmpresa; // Ajustar ruta base si es necesario
            } else {
                $rutaFoto = $fotoEmpresa; // Si ya tiene la ruta completa
            }
        }

        // Generar el menú para la empresa
        $menu = '
            <a class="dropdown-item" href="../../VistaSecciones/logout.php">Cerrar Sesión</a>
        ';
    }

    $stmt->close();
} else {
    // Si no hay nadie logueado, puedes definir un menú genérico o dejarlo vacío
    $menu = '
        <a class="dropdown-item" href="../../VistaSecciones/login.php">Iniciar Sesión</a>
    ';
}

// Enviar los datos a la vista
return compact('rutaFoto', 'menu');