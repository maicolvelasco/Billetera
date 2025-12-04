-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-12-2024 a las 08:15:39
-- Versión del servidor: 10.6.19-MariaDB-log
-- Versión de PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `duralitc_billetera`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area_trabajo`
--

CREATE TABLE `area_trabajo` (
  `Id_Area` int(11) NOT NULL,
  `Nombres` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `area_trabajo`
--

INSERT INTO `area_trabajo` (`Id_Area`, `Nombres`) VALUES
(1, 'Produccion'),
(2, 'Despacho'),
(3, 'Carpinteria'),
(4, 'Mantenimiento'),
(5, 'Almacenes'),
(6, 'Contabilidad'),
(7, 'Compras'),
(8, 'Recursos Humanos'),
(9, 'Atencion al cliente'),
(10, 'Comercial'),
(11, 'Facturacion'),
(12, 'Informatica'),
(24, 'Syso'),
(25, 'Moldeos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo`
--

CREATE TABLE `consumo` (
  `Id_Consumo` int(11) NOT NULL,
  `Id_Importe` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Importe_Total` decimal(10,2) DEFAULT 0.00,
  `Gastos` decimal(10,2) DEFAULT 0.00,
  `Ultimo_Agregado` datetime DEFAULT NULL,
  `Id_Empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consumo`
--

INSERT INTO `consumo` (`Id_Consumo`, `Id_Importe`, `Id_Usuario`, `Importe_Total`, `Gastos`, `Ultimo_Agregado`, `Id_Empresa`) VALUES
(47, 346, 26, 0.00, 60.00, '2024-11-13 14:24:59', 12),
(48, 347, 24, 39.00, 21.00, '2024-11-18 08:24:28', 12),
(49, 348, 26, 0.00, 60.00, '2024-11-25 18:32:36', 12),
(50, 355, 27, 50.00, 200.00, '2024-12-06 12:48:03', 12),
(51, 353, 26, 0.00, 130.00, '2024-12-07 07:55:42', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_importe`
--

CREATE TABLE `empleado_importe` (
  `Id_Importe` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `Codigo_empleado` varchar(255) NOT NULL,
  `Nombre_Completo` varchar(255) NOT NULL,
  `Importes` decimal(10,2) DEFAULT 0.00,
  `Importe_total` decimal(10,2) DEFAULT 0.00,
  `Ultimo_agregado` datetime DEFAULT NULL,
  `Id_Empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado_importe`
--

INSERT INTO `empleado_importe` (`Id_Importe`, `Id_Usuario`, `Codigo_empleado`, `Nombre_Completo`, `Importes`, `Importe_total`, `Ultimo_agregado`, `Id_Empresa`) VALUES
(346, 26, '972824', 'Jhonny Padilla Montaño', 60.00, 0.00, '2024-11-12 13:36:27', 12),
(347, 24, '972820', 'Carlos Enriquez Zabala', 60.00, 39.00, '2024-11-17 10:44:06', 12),
(348, 26, '972824', 'Jhonny Padilla Montaño', 60.00, 0.00, '2024-11-21 14:46:41', 12),
(349, 24, '972820', 'Carlos Enriquez Zabala', 40.00, 79.00, '2024-11-22 14:37:30', 12),
(350, 26, '972824', 'Jhonny Padilla Montaño', 60.00, 60.00, '2024-11-29 09:46:08', 12),
(351, 24, '972820', 'Carlos Enriquez Zabala', 60.00, 139.00, '2024-11-29 13:29:51', 12),
(352, 25, '972828', 'Ernesto Ramirez Piza', 120.00, 120.00, '2024-12-03 11:26:54', 12),
(353, 26, '972824', 'Jhonny Padilla Montaño', 70.00, 0.00, '2024-12-03 15:41:03', 12),
(354, 28, '972885', 'Eddy Noe Yubanure', 250.00, 250.00, '2024-12-04 07:10:42', 12),
(355, 27, '972876', 'Ricardo Corrales', 250.00, 50.00, '2024-12-04 07:10:42', 12),
(356, 24, '972820', 'Carlos Enriquez Zabala', 60.00, 199.00, '2024-12-09 15:14:21', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `Id_Empresa` int(11) NOT NULL,
  `Nombre` varchar(250) NOT NULL,
  `Usuario` varchar(250) NOT NULL,
  `Password` varchar(250) NOT NULL,
  `Foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`Id_Empresa`, `Nombre`, `Usuario`, `Password`, `Foto`) VALUES
(12, 'Agencia Pil', 'agenciapil', '1234567', '../../uploads/agenciapil.png'),
(13, 'Bata', 'Bata', '1234567', '../../uploads/Bata.png'),
(14, 'Libreria Maratel', 'Maratel', '1234567', '../../uploads/Libreria.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puesto`
--

CREATE TABLE `puesto` (
  `Id_Puesto` int(11) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puesto`
--

INSERT INTO `puesto` (`Id_Puesto`, `Nombre`) VALUES
(1, 'Mecanico'),
(2, 'Electrico'),
(3, 'Operario'),
(4, 'Administrativo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `Id_Rol` int(11) NOT NULL,
  `Nombres` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`Id_Rol`, `Nombres`) VALUES
(1, 'Administrador'),
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencias_enviadas`
--

CREATE TABLE `transferencias_enviadas` (
  `Id_Transferencia` int(11) NOT NULL,
  `Id_Usuario_Emisor` int(11) NOT NULL,
  `Id_Usuario_Receptor` int(11) NOT NULL,
  `Id_Empresa` int(11) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transferencias_enviadas`
--

INSERT INTO `transferencias_enviadas` (`Id_Transferencia`, `Id_Usuario_Emisor`, `Id_Usuario_Receptor`, `Id_Empresa`, `Monto`, `Fecha`) VALUES
(0, 22, 20, 12, 10.00, '2024-10-25 15:05:12'),
(0, 22, 21, 12, 10.00, '2024-10-25 15:05:23'),
(0, 21, 22, 12, 10.00, '2024-10-26 19:42:57'),
(0, 20, 21, 12, 15.00, '2024-10-27 13:30:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencias_recibidas`
--

CREATE TABLE `transferencias_recibidas` (
  `Id_Transferencia` int(11) NOT NULL,
  `Id_Usuario_Receptor` int(11) NOT NULL,
  `Id_Usuario_Emisor` int(11) NOT NULL,
  `Id_Empresa` int(11) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transferencias_recibidas`
--

INSERT INTO `transferencias_recibidas` (`Id_Transferencia`, `Id_Usuario_Receptor`, `Id_Usuario_Emisor`, `Id_Empresa`, `Monto`, `Fecha`) VALUES
(0, 20, 22, 12, 10.00, '2024-10-25 15:05:12'),
(0, 21, 22, 12, 10.00, '2024-10-25 15:05:23'),
(0, 22, 21, 12, 10.00, '2024-10-26 19:42:57'),
(0, 21, 20, 12, 15.00, '2024-10-27 13:30:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_pins`
--

CREATE TABLE `user_pins` (
  `Id_PIN` int(11) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `PIN` varchar(6) NOT NULL,
  `Fecha_Creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_pins`
--

INSERT INTO `user_pins` (`Id_PIN`, `Id_Usuario`, `PIN`, `Fecha_Creacion`) VALUES
(19, 1, '388649', '2024-10-09 14:26:22'),
(349, 11, '021926', '2024-10-21 14:09:12'),
(462, 24, '958144', '2024-11-20 11:32:32'),
(469, 25, '952084', '2024-12-05 15:33:47'),
(472, 27, '731623', '2024-12-06 17:47:43'),
(473, 26, '724767', '2024-12-07 12:55:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `Id_Usuario` int(11) NOT NULL,
  `Rol` int(11) DEFAULT NULL,
  `Area` int(11) DEFAULT NULL,
  `Puesto` int(11) DEFAULT NULL,
  `Codigo_empleado` varchar(255) NOT NULL,
  `CI` int(11) NOT NULL,
  `Nombre_Completo` varchar(100) NOT NULL,
  `Estado` tinyint(1) NOT NULL,
  `Fecha_Nacimiento` date DEFAULT NULL,
  `Correo_Electronico` varchar(255) DEFAULT NULL,
  `Telefono` int(11) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`Id_Usuario`, `Rol`, `Area`, `Puesto`, `Codigo_empleado`, `CI`, `Nombre_Completo`, `Estado`, `Fecha_Nacimiento`, `Correo_Electronico`, `Telefono`, `Password`, `Foto`) VALUES
(1, 1, 1, 1, '9373386', 9373386, 'Maicol William Arratia Velasco', 1, '2003-04-17', 'maicolarratia4@gmail.com', 60776373, '9373386', 'foto_67165ee6350f45.38892608.jpg'),
(11, 1, 12, 4, '983498', 4385485, 'Andres Merwin Condoretty Zelada', 1, '2000-02-10', 'acondorettyz@elementia.com', 79794880, 'V1d452003', '../../uploads/perfil.png'),
(23, 1, 8, 4, '972790', 6475612, 'Mariel Vargas Villarreal', 1, '2000-04-04', 'mcvargas@elementia.com', 70300411, '972790', '../../uploads/FotoMariel.jpg'),
(24, 2, 24, 3, '972820', 4460795, 'Carlos Enriquez Zabala', 1, '0074-08-26', 'cenriqvez76@gmail.com', 76450500, 'Carlos2820', '../../uploads/perfil_generico.png'),
(25, 2, 24, 3, '972828', 5937311, 'Ernesto Ramirez Piza', 1, '0000-00-00', '', 79980706, '972828', '../../uploads/perfil_generico.png'),
(26, 2, 4, 1, '972824', 4494625, 'Jhonny Padilla Montaño', 1, '0000-00-00', '', 76479057, '972824', '../../uploads/perfil_generico.png'),
(27, 2, 1, 3, '972876', 6452375, 'Ricardo Corrales', 1, '0000-00-00', '', 0, '972876', '../../uploads/perfil_generico.png'),
(28, 2, 1, 3, '972885', 8849798, 'Eddy Noe Yubanure', 1, '0000-00-00', '', 0, '972885', '../../uploads/perfil_generico.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area_trabajo`
--
ALTER TABLE `area_trabajo`
  ADD PRIMARY KEY (`Id_Area`);

--
-- Indices de la tabla `consumo`
--
ALTER TABLE `consumo`
  ADD PRIMARY KEY (`Id_Consumo`),
  ADD KEY `fk_user_c` (`Id_Usuario`),
  ADD KEY `fk_import_c` (`Id_Importe`),
  ADD KEY `fk_empre` (`Id_Empresa`);

--
-- Indices de la tabla `empleado_importe`
--
ALTER TABLE `empleado_importe`
  ADD PRIMARY KEY (`Id_Importe`),
  ADD KEY `fk_user` (`Id_Usuario`),
  ADD KEY `fk_empresa` (`Id_Empresa`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`Id_Empresa`);

--
-- Indices de la tabla `puesto`
--
ALTER TABLE `puesto`
  ADD PRIMARY KEY (`Id_Puesto`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Id_Rol`);

--
-- Indices de la tabla `user_pins`
--
ALTER TABLE `user_pins`
  ADD PRIMARY KEY (`Id_PIN`),
  ADD KEY `Id_Usuario` (`Id_Usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`Id_Usuario`),
  ADD KEY `fk_roles` (`Rol`),
  ADD KEY `fk_areas` (`Area`),
  ADD KEY `fk_puestos` (`Puesto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area_trabajo`
--
ALTER TABLE `area_trabajo`
  MODIFY `Id_Area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `consumo`
--
ALTER TABLE `consumo`
  MODIFY `Id_Consumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `empleado_importe`
--
ALTER TABLE `empleado_importe`
  MODIFY `Id_Importe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=357;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `Id_Empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `puesto`
--
ALTER TABLE `puesto`
  MODIFY `Id_Puesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `Id_Rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `user_pins`
--
ALTER TABLE `user_pins`
  MODIFY `Id_PIN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=474;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `Id_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `consumo`
--
ALTER TABLE `consumo`
  ADD CONSTRAINT `fk_empre` FOREIGN KEY (`Id_Empresa`) REFERENCES `empresa` (`Id_Empresa`),
  ADD CONSTRAINT `fk_import_c` FOREIGN KEY (`Id_Importe`) REFERENCES `empleado_importe` (`Id_Importe`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_c` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleado_importe`
--
ALTER TABLE `empleado_importe`
  ADD CONSTRAINT `fk_empresa` FOREIGN KEY (`Id_Empresa`) REFERENCES `empresa` (`Id_Empresa`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_pins`
--
ALTER TABLE `user_pins`
  ADD CONSTRAINT `user_pins_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuario` (`Id_Usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_areas` FOREIGN KEY (`Area`) REFERENCES `area_trabajo` (`Id_Area`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_puestos` FOREIGN KEY (`Puesto`) REFERENCES `puesto` (`Id_Puesto`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_roles` FOREIGN KEY (`Rol`) REFERENCES `roles` (`Id_Rol`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;