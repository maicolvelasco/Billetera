# Billetera Empresarial

**Billetera Empresarial** es una aplicación web de gestión de billetera digital y transferencias de dinero diseñada específicamente para empresas. Permite a empleados gestionar sus cuentas, realizar transferencias, consultar extractos de movimientos y más, mientras que los administradores pueden gestionar usuarios, empresas, puestos y áreas de trabajo.

## 📋 Características Principales

### Para Usuarios (Empleados)
- 🔐 **Autenticación segura** con login y logout
- 💰 **Dashboard personalizado** con resumen de cuenta
- 💳 **Gestión de cuentas** - Ver saldos y detalles
- 📤 **Transferencias** - Enviar dinero a otros empleados
- 🛍️ **Compras** - Registro de compras realizadas
- 📊 **Extracto de movimientos** - Historial detallado con descarga en PDF
- 👤 **Perfil** - Ver y editar información personal

### Para Vendedores
- 📱 **Captura de consumos** por cámara/QR
- 💾 **Registro de ventas** con generación de PDFs
- 📈 **Dashboard de ventas**
- 📋 **Extracto de consumos**
- ℹ️ **Información de cuenta**

### Para Administradores
- 👥 **Gestión de usuarios** - Crear, editar y eliminar empleados
- 🏢 **Gestión de empresas** - Administrar múltiples empresas
- 🏭 **Gestión de áreas** - Crear áreas de trabajo
- 📋 **Gestión de puestos** - Definir puestos laborales
- 📊 **Gestión de asignaciones** - Asignar recursos a empleados
- 🔐 **Seguridad** - Gestión de permisos y roles
- 💾 **Backup** - Respaldar datos de la base de datos

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8.1+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **Base de datos**: MySQL/MariaDB 10.6+
- **Generación de PDFs**: FPDF
- **Códigos QR**: phpqrcode
- **PWA**: Manifest.json y Service Worker para instalación como app

## 📁 Estructura del Proyecto

```
Billetera/
├── index.php                    # Página de inicio
├── manifest.json                # Configuración PWA
├── service-worker.js            # Service worker para offline
├── Schema.sql                   # Estructura de base de datos
├── error_log                    # Log de errores
│
├── Conexion/
│   └── conexion.php            # Configuración de conexión MySQL
│
├── Controlador/                # Controladores (Lógica de negocio)
│   ├── login.php               # Autenticación de usuarios
│   ├── ControladorAdmin/       # Controladores administrativos
│   │   ├── Controlador_Usuario/
│   │   ├── Controlador_Cuenta/
│   │   ├── Controlador_PA/     # Puestos y Áreas
│   │   └── Controlador_Asignacion/
│   ├── ControladorSeccion/
│   │   └── ControladorTopBar.php
│   ├── ControladorUsuario/     # Acciones de empleados
│   │   ├── ControladorCompras.php
│   │   ├── ControladorCuentas.php
│   │   ├── ControladorDashboard.php
│   │   ├── ControladorExtracto.php
│   │   ├── ControladorTransferencia.php
│   │   └── generate_pdf_movimientos.php
│   └── ControladorVendedor/    # Acciones de vendedores
│       ├── ControladorDashboard.php
│       ├── ControladorExtracto.php
│       └── generate_consumos_pdf.php
│
├── VistaAdministrador/         # Vistas administrativas
│   ├── VistaAdmin/
│   ├── VistaAdminUsuario/
│   ├── VistaAdminEmpresa/
│   ├── VistaAdminPA/           # Puestos y Áreas
│   └── VistaAdminAsignacion/
│
├── VistaUsuario/               # Vistas para empleados
│   ├── Vista_Usuario/
│   │   ├── DashboardU.php
│   │   ├── Cuentas.php
│   │   ├── Compras.php
│   │   ├── Transferencia.php
│   │   ├── Extracto.php
│   │   └── Perfil.php
│   └── qr/                     # Códigos QR generados
│
├── VistaVendedor/              # Vistas para vendedores
│   ├── Vista_Vendedor/
│   │   ├── DashboardV.php
│   │   ├── Camara.php
│   │   ├── Extracto.php
│   │   └── Informacion.php
│
├── VistaSecciones/             # Componentes compartidos
│   ├── Topbar.php
│   └── logout.php
│
├── libs/
│   └── fpdf/                   # Librería FPDF para generar PDFs
│
├── phpqrcode/                  # Librería para generar códigos QR
│
├── uploads/                    # Directorio para archivos subidos
│
└── src/                        # Recursos (imágenes, logos, etc.)
```

## 📊 Base de Datos

El proyecto utiliza las siguientes tablas principales:

- **usuarios** - Información de empleados
- **empresas** - Datos de las empresas
- **area_trabajo** - Áreas de la empresa
- **puesto_trabajo** - Puestos laborales
- **cuentas** - Cuentas bancarias/billetera de usuarios
- **empleado_importe** - Montos asignados a empleados
- **consumo** - Registro de consumos/compras
- **transferencias** - Historial de transferencias
- **movimientos** - Transacciones y movimientos

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP 8.1** o superior
- **MySQL 10.6** o **MariaDB** compatible
- **Xampp** o **Laragon**
- **Navegador moderno** (Chrome, Firefox, Edge)

### Paso 1: Descargar e Instalar Xampp o Laragon

**Con Xampp:**
1. Descarga desde: https://www.apachefriends.org/
2. Instala siguiendo las instrucciones
3. Inicia Apache y MySQL desde el panel de control

**Con Laragon:**
1. Descarga desde: https://laragon.org/
2. Instala (proceso más rápido que Xampp)
3. La interfaz iniciará automáticamente Apache y MySQL

### Paso 2: Clonar el Proyecto

```bash
# Con Xampp (en C:\xampp\htdocs):
cd C:\xampp\htdocs
git clone <tu-repositorio> Billetera
# O descargar el ZIP y extraer como carpeta "Billetera"

# Con Laragon (en C:\laragon\www):
cd C:\laragon\www
git clone <tu-repositorio> Billetera
```

### Paso 3: Crear la Base de Datos

**Opción A: Usando phpMyAdmin**

1. Abre phpMyAdmin:
   - **Xampp**: http://localhost/phpmyadmin
   - **Laragon**: http://laragon.test/phpmyadmin (ajusta el puerto si es necesario)

2. Crea una nueva base de datos:
   - Nombre: `tubasededatos` (o el que prefieras)
   - Cotejamiento: `utf8mb4_general_ci`

3. Importa el archivo `Schema.sql`:
   - Ve a "Importar"
   - Selecciona el archivo `Schema.sql` del proyecto
   - Haz clic en "Importar"

**Opción B: Usando línea de comandos**

```bash
# Xampp
cd C:\xampp\mysql\bin
mysql -u root < C:\xampp\htdocs\Billetera\Schema.sql

# Laragon
cd C:\laragon\bin\mysql\mysql-8.0-winx64\bin
mysql -u root < C:\laragon\www\Billetera\Schema.sql
```

### Paso 4: Configurar la Conexión a Base de Datos

Edita el archivo `Conexion/conexion.php`:

```php
<?php
$host = 'localhost';
$username = 'root';           // Usuario MySQL (generalmente 'root')
$password = '';               // Contraseña MySQL (vacía por defecto)
$database = 'tubasededatos';  // Nombre de tu base de datos
$port = 3306;                 // Puerto MySQL (3306 es el default)

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $username, $password, $database, $port);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Error en la conexión: " . $e->getMessage());
    die("Conexión fallida. Inténtalo más tarde.");
}
?>
```

### Paso 5: Acceder a la Aplicación

**Con Xampp:**
```
http://localhost/Billetera
```

**Con Laragon:**
```
http://billetera.test
# O si lo nombraste diferente, http://nombre-carpeta.test
```

### Paso 6: Crear Usuario Administrador (Primera vez)

1. Accede a phpMyAdmin
2. Abre la tabla `usuarios`
3. Crea un registro con:
   - Email y contraseña de administrador
   - Rol: `admin`
   - Estado: `activo`

**O usando SQL directo:**

```sql
INSERT INTO usuarios (email, contraseña, nombre, rol, estado) 
VALUES ('admin@billetera.com', SHA2('admin123', 256), 'Administrador', 'admin', 'activo');
```

## 🔐 Roles y Permisos

### 1. **Administrador**
- Acceso completo a todas las funciones
- Gestión de usuarios y empresas
- Creación de áreas y puestos
- Generación de reportes
- Respaldo de base de datos

### 2. **Usuario (Empleado)**
- Ver su billetera/saldo
- Realizar transferencias a otros empleados
- Ver historial de transacciones
- Editar su perfil
- Descargar extractos en PDF

### 3. **Vendedor**
- Registrar consumos/ventas
- Capturar datos por cámara
- Ver extracto de ventas
- Generar reportes de consumos

## 💻 Uso de la Aplicación

### Login
1. Ve a la página principal
2. Ingresa email y contraseña
3. Se redirigirá al dashboard según tu rol

### Dashboard Usuario
- **Resumen**: Saldo actual y últimos movimientos
- **Mi Billetera**: Consultar cuentas disponibles
- **Transferencias**: Enviar dinero a compañeros
- **Compras**: Registro de gastos realizados
- **Extracto**: Descargar PDF con movimientos
- **Perfil**: Actualizar información personal

### Panel Administrador
- Acceso completo a todas las secciones
- Gestión integral de usuarios y datos
- Reportes y análisis

## 🐛 Solución de Problemas

### Error: "Conexión fallida"
- Verifica que MySQL/MariaDB esté corriendo
- Confirma que la base de datos existe
- Revisa las credenciales en `Conexion/conexion.php`

### Error 404 al acceder
- Verifica que la carpeta esté en el directorio correcto (htdocs o www)
- Reinicia Apache desde el panel de Xampp/Laragon

### PDFs no se generan
- Verifica que la carpeta `uploads/` tenga permisos de escritura (755)
- Comprueba que la librería FPDF esté correctamente cargada

### Problemas de caracteres especiales
- Asegúrate que MySQL esté configurado con `utf8mb4`
- Verifica la línea `$conexion->set_charset("utf8mb4");` en `conexion.php`

## 📱 Funcionamiento como PWA (Progressive Web App)

La aplicación puede instalarse como una aplicación móvil:

1. Abre la aplicación en el navegador
2. Busca la opción "Instalar" en el navegador
3. Se agregará a tu pantalla de inicio
4. Funciona offline con el Service Worker

**Configuración en `manifest.json`:**
- Nombre: Billetera Duralit
- Tema: Azul (#007bff)
- Iconos: 192x192 y 512x512

## 📝 Archivos Importantes

| Archivo | Descripción |
|---------|------------|
| `index.php` | Página de inicio y landing |
| `Schema.sql` | Script SQL para crear la BD |
| `manifest.json` | Configuración PWA |
| `service-worker.js` | Para funcionamiento offline |
| `Conexion/conexion.php` | Configuración de base de datos |

## 🔧 Configuración Avanzada

### Cambiar Puerto MySQL (si es necesario)

En `Conexion/conexion.php`:
```php
$port = 3307;  // O el puerto que uses
```

### Configurar Zona Horaria

En PHP, agrega al inicio de archivos que lo requieran:
```php
date_default_timezone_set('America/Bogota'); // Cambia según tu zona
```

### Habilitar CORS (si necesitas API externa)

En el header de controladores que sirvan como API:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');
```

## 📚 Librerias Incluidas

1. **Bootstrap 5.3** - Framework CSS para diseño responsivo
2. **FPDF** - Generación de archivos PDF
3. **phpqrcode** - Generación de códigos QR
4. **jQuery** - (si está incluido) Manipulación del DOM

## 🚀 Mejoras Futuras

- [ ] Autenticación de dos factores (2FA)
- [ ] Notificaciones en tiempo real
- [ ] API REST completa
- [ ] Dashboard analytics avanzado
- [ ] Soporte multi-idioma
- [ ] Integración con pasarelas de pago

## 📞 Soporte

Si encuentras problemas:

1. Revisa los logs en `error_log`
2. Consulta la sección "Solución de Problemas"
3. Verifica que toda la configuración sea correcta

## 📄 Licencia

Este proyecto es de uso interno para Duralit.

---

**Versión**: 1.0  
**Última actualización**: Diciembre 2024  
**Desarrollado para**: Duralit
