<?php
session_start();

if (!empty($_POST["btningresar"])) {
    
    if (!empty($_POST["Codigo_empleado"]) && !empty($_POST["Password"])) {

        $codigo = $_POST["Codigo_empleado"];
        $password = $_POST["Password"];

        // Consulta SQL para verificar si es un usuario regular y su estado
        $sql_usuario = $conexion->prepare("SELECT u.Nombre_Completo, u.Id_Usuario, u.Estado, r.Id_Rol FROM usuario u INNER JOIN roles r ON u.Rol = r.Id_Rol WHERE Codigo_empleado = ? AND Password = ?");
        $sql_usuario->bind_param("ss", $codigo, $password);
        $sql_usuario->execute();
        $sql_usuario->store_result();
        $sql_usuario->bind_result($nombre_completo, $id_usuario, $estado_usuario, $rol_id);
        
        // Verificar si se encontró un usuario
        if ($sql_usuario->fetch()) {
            // Verificar si el usuario está activo
            if ($estado_usuario == 1) {  // Usuario activo
                $_SESSION["Nombre_Completo"] = $nombre_completo;
                $_SESSION["Id_Usuario"] = $id_usuario;

                // Acceso según el rol del usuario
                switch ($rol_id) {
                    case 1:  // Admin
                        $dashboard_url = './VistaAdministrador/VistaAdmin/Dashboard.php';
                        break;
                    case 2:  // Usuario
                        $dashboard_url = './VistaUsuario/Vista_Usuario/DashboardU.php';
                        break;
                    default:
                        echo "<div class='alert alert-danger'>Rol no válido</div>";
                        exit;
                }

                // Redirigir al dashboard correspondiente
                $sql_usuario->close();
                header("location: $dashboard_url");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Su cuenta está inactiva. Contacte al administrador.</div>";
            }

        } else {
            // Si no se encontró un usuario, verificar credenciales de la empresa
            $sql_empresa = $conexion->prepare("SELECT Id_Empresa, Nombre FROM empresa WHERE Usuario = ? AND Password = ?");
            $sql_empresa->bind_param("ss", $codigo, $password);
            $sql_empresa->execute();
            $sql_empresa->store_result();
            $sql_empresa->bind_result($id_empresa, $nombre_empresa);

            // Verificar si se encontró una empresa
            if ($sql_empresa->fetch()) {
                $_SESSION["Nombre_Empresa"] = $nombre_empresa;
                $_SESSION["Id_Empresa"] = $id_empresa;

                // Redirigir a un dashboard específico para la empresa
                $dashboard_url_empresa = './VistaVendedor/Vista_Vendedor/DashboardV.php';
                $sql_empresa->close();
                header("location: $dashboard_url_empresa");
                exit();
            } else {
                $sql_empresa->close();
                echo "<div class='alert alert-danger'>El usuario o la contraseña de la empresa son incorrectos.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Debe completar ambos campos.</div>";
    }
}
?>