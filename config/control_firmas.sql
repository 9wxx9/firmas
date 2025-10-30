-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 21:34:22
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `control_firmas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_libros`
--

CREATE TABLE `auditoria_libros` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `firmantes`
--

CREATE TABLE `firmantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `firmantes`
--

INSERT INTO `firmantes` (`id`, `nombre`, `cargo`, `departamento`, `email`, `telefono`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Mario Ariza', 'Tesorero', 'Asurcol', 'tesorero@asurcol.org', NULL, 1, '2025-09-11 20:56:51', '2025-10-29 21:22:25'),
(2, 'Willard Cano', 'Presidente', 'Presidente Asurcol', 'presidente@asurcol.org', NULL, 1, '2025-09-11 20:56:51', '2025-10-29 21:23:13'),
(3, 'Rodrigo Peña', 'secretario general', 'Asurcol', 'secretario@asurcol.org', NULL, 1, '2025-09-11 20:56:51', '2025-10-29 21:20:29'),
(4, 'Sandra Vera', 'Caja', 'Contabilidad', 'caja@asurcol.org', NULL, 1, '2025-09-11 20:56:51', '2025-10-29 21:21:01'),
(5, 'pastores', 'Pastores distritos', 'evangelización', 'pastores@asurcol.org', NULL, 1, '2025-09-11 20:56:51', '2025-10-29 21:21:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `firmas`
--

CREATE TABLE `firmas` (
  `id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `firmante_id` int(11) NOT NULL,
  `estado` enum('pendiente','firmado','rechazado','exento') NOT NULL DEFAULT 'pendiente',
  `orden` int(11) DEFAULT 1,
  `fecha_firma` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `firmado_por` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `firmas`
--

INSERT INTO `firmas` (`id`, `libro_id`, `firmante_id`, `estado`, `orden`, `fecha_firma`, `observaciones`, `firmado_por`, `created_at`, `updated_at`) VALUES
(17, 30, 3, 'firmado', 1, '2025-10-06 23:08:54', NULL, NULL, '2025-10-06 21:05:07', '2025-10-06 21:08:54'),
(18, 29, 1, 'firmado', 1, '2025-10-06 23:10:03', NULL, NULL, '2025-10-06 21:09:35', '2025-10-06 21:10:03'),
(19, 29, 5, 'firmado', 2, '2025-10-28 22:24:15', NULL, NULL, '2025-10-06 21:09:35', '2025-10-28 21:24:15'),
(20, 29, 2, 'firmado', 3, '2025-10-28 22:24:22', NULL, NULL, '2025-10-06 21:09:35', '2025-10-28 21:24:22'),
(21, 28, 2, 'pendiente', 1, NULL, NULL, NULL, '2025-10-07 20:47:39', '2025-10-07 20:47:39'),
(26, 33, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-08 16:56:07', '2025-10-08 16:56:07'),
(27, 33, 4, 'pendiente', 2, NULL, NULL, NULL, '2025-10-08 16:56:07', '2025-10-08 16:56:07'),
(28, 33, 2, 'pendiente', 3, NULL, NULL, NULL, '2025-10-08 16:56:07', '2025-10-08 16:56:07'),
(29, 26, 5, 'firmado', 1, '2025-10-08 18:58:26', NULL, NULL, '2025-10-08 16:58:15', '2025-10-08 16:58:26'),
(30, 27, 5, 'pendiente', 1, NULL, NULL, NULL, '2025-10-15 14:11:55', '2025-10-15 14:11:55'),
(31, 35, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-28 21:20:48', '2025-10-28 21:20:48'),
(32, 35, 2, 'pendiente', 2, NULL, NULL, NULL, '2025-10-28 21:20:48', '2025-10-28 21:20:48'),
(33, 36, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-28 21:21:36', '2025-10-28 21:21:36'),
(34, 37, 1, 'firmado', 1, '2025-10-29 17:48:48', NULL, NULL, '2025-10-28 21:22:09', '2025-10-29 16:48:48'),
(35, 37, 4, 'firmado', 2, '2025-10-29 17:48:52', NULL, NULL, '2025-10-28 21:22:09', '2025-10-29 16:48:52'),
(36, 37, 2, 'firmado', 3, '2025-10-29 17:48:56', NULL, NULL, '2025-10-28 21:22:09', '2025-10-29 16:48:56'),
(37, 38, 1, 'firmado', 1, '2025-10-29 17:48:05', NULL, NULL, '2025-10-29 16:38:18', '2025-10-29 16:48:05'),
(38, 39, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-29 17:05:02', '2025-10-29 17:05:02'),
(39, 40, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-29 19:55:11', '2025-10-29 19:55:11'),
(40, 40, 4, 'pendiente', 2, NULL, NULL, NULL, '2025-10-29 19:55:11', '2025-10-29 19:55:11'),
(41, 41, 1, 'pendiente', 1, NULL, NULL, NULL, '2025-10-29 21:06:26', '2025-10-29 21:06:26'),
(42, 41, 2, 'pendiente', 2, NULL, NULL, NULL, '2025-10-29 21:06:26', '2025-10-29 21:06:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_firmas`
--

CREATE TABLE `historial_firmas` (
  `id` int(11) NOT NULL,
  `firma_id` int(11) NOT NULL,
  `estado_anterior` enum('pendiente','firmado','rechazado','exento') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','firmado','rechazado','exento') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `numero_referencia` varchar(50) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `escaneado` tinyint(1) DEFAULT 0,
  `mes` varchar(20) NOT NULL,
  `año` int(11) NOT NULL,
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('activo','cerrado','archivado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dia` int(50) DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `numero_referencia`, `titulo`, `descripcion`, `escaneado`, `mes`, `año`, `fecha_limite`, `estado`, `created_at`, `updated_at`, `dia`, `created_by`) VALUES
(2, 'JOURNAL-2002', 'DIARIO REMESAS', 'Registro remesas febrero', 0, '02', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 12, NULL),
(3, 'JOURNAL-2003', 'DIARIO CAJA', 'Registro caja marzo', 0, '03', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 20, NULL),
(4, 'JOURNAL-2004', 'DIARIO PAGOS', 'Registro pagos abril', 0, '04', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 8, NULL),
(5, 'JOURNAL-2005', 'DIARIO NÓMINA', 'Registro nómina mayo', 0, '05', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 17, NULL),
(6, 'JOURNAL-2006', 'DIARIO REMESAS', 'Registro remesas junio', 0, '06', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 25, NULL),
(7, 'JOURNAL-2007', 'DIARIO CAJA', 'Registro caja julio', 0, '07', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 3, NULL),
(8, 'JOURNAL-2008', 'DIARIO PAGOS', 'Registro pagos agosto', 0, '08', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 14, NULL),
(9, 'JOURNAL-2009', 'DIARIO NÓMINA', 'Registro nómina septiembre', 0, '09', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 29, NULL),
(10, 'JOURNAL-2010', 'DIARIO REMESAS', 'Registro remesas octubre', 0, '10', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 6, NULL),
(11, 'JOURNAL-2011', 'DIARIO CAJA', 'Registro caja noviembre', 0, '11', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 18, NULL),
(12, 'JOURNAL-2012', 'DIARIO PAGOS', 'Registro pagos diciembre', 0, '12', 2024, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 23, NULL),
(13, 'JOURNAL-2013', 'DIARIO NÓMINA', 'Registro prueba nómina', 0, '01', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 11, NULL),
(14, 'JOURNAL-2014', 'DIARIO REMESAS', 'Registro prueba remesas', 0, '02', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 21, NULL),
(15, 'JOURNAL-2015', 'DIARIO CAJA', 'Registro prueba caja', 0, '03', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 7, NULL),
(16, 'JOURNAL-2016', 'DIARIO PAGOS', 'Registro prueba pagos', 0, '04', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 16, NULL),
(17, 'JOURNAL-2017', 'DIARIO NÓMINA', 'Registro nómina ejemplo', 0, '05', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 30, NULL),
(18, 'JOURNAL-2018', 'DIARIO REMESAS', 'Registro remesas ejemplo', 0, '06', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 13, NULL),
(19, 'JOURNAL-2019', 'DIARIO CAJA', 'Registro caja ejemplo', 0, '07', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 4, NULL),
(20, 'JOURNAL-2020', 'DIARIO PAGOS', 'Registro pagos ejemplo', 0, '08', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 28, NULL),
(21, 'JOURNAL-2021', 'DIARIO NÓMINA', 'Entrada prueba nómina', 0, '09', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 9, NULL),
(23, 'JOURNAL-2023', 'DIARIO CAJA', 'Entrada prueba caja', 0, '11', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 22, NULL),
(24, 'JOURNAL-2024', 'DIARIO PAGOS', 'Entrada prueba pagos', 0, '12', 2025, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 27, NULL),
(25, 'JOURNAL-2025', 'DIARIO NÓMINA', 'Generado automáticamente', 0, '01', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 2, NULL),
(26, 'JOURNAL-2026', 'DIARIO REMESAS', 'Generado automáticamente', 0, '02', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 10, NULL),
(27, 'JOURNAL-2027', 'DIARIO CAJA', 'Generado automáticamente', 0, '03', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 19, NULL),
(28, 'JOURNAL-2028', 'DIARIO PAGOS', 'Generado automáticamente', 0, '04', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 26, NULL),
(29, 'JOURNAL-2029', 'DIARIO NÓMINA', 'Generado automáticamente', 0, '05', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 1, NULL),
(30, 'JOURNAL-2030', 'DIARIO REMESAS', 'Generado automáticamente', 0, '06', 2026, NULL, 'activo', '2025-09-30 23:55:03', '2025-09-30 23:55:03', 24, NULL),
(33, 'LIB-2025-1008-1155', 'JV-COMPROBANTE CONTABLE', 'mario paginas 1,2,3 y willard pagina 1', 0, '10', 2025, NULL, 'activo', '2025-10-08 16:56:07', '2025-10-08 16:56:07', 8, '1'),
(35, 'JOURNAL-2543', 'NOMINA OCTUBRE ', 'Paginas 4,3,8', 0, '10', 2025, NULL, 'activo', '2025-10-28 21:19:31', '2025-10-28 21:19:31', 28, '6'),
(36, 'KK-2121', 'RETORNO ', '', 0, '11', 2025, NULL, 'activo', '2025-10-28 21:21:36', '2025-10-28 21:21:36', 16, '6'),
(37, 'KKKKK-323', 'DEVOLUCION 2020', 'AA', 0, '03', 2025, NULL, 'activo', '2025-10-28 21:22:09', '2025-10-28 21:22:09', 28, '6'),
(38, 'JOURNAL-8245', 'PAYMENT', 'pagina 3 falta por firma ', 0, '10', 2025, NULL, 'activo', '2025-10-29 16:38:18', '2025-10-29 16:38:18', 29, '6'),
(39, '8293', 'REM', 'Remesa', 0, '10', 2025, NULL, 'activo', '2025-10-29 17:05:02', '2025-10-29 19:51:32', NULL, '6'),
(40, '8265', 'REM', '', 0, '10', 2025, NULL, 'activo', '2025-10-29 19:55:11', '2025-10-29 19:55:11', 29, '6'),
(41, '8085', 'CONTADOR', '', 0, '10', 2025, NULL, 'activo', '2025-10-29 21:06:26', '2025-10-29 21:06:26', 29, '7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre`, `email`, `telefono`, `direccion`, `avatar`, `rol`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$vDuQFuH3aQ3eYL8ccpUsDeYVwbvoUz.matfoWcZYQevY2DJi186su', 'Mario Ariza', 'admin@example.com', '3115016507', 'Manzana f Cs 14 barrio ambikaima salado', 'avatar_1_1758575655.png', 'admin', NULL, '2025-09-11 20:56:51', '2025-10-09 21:40:41'),
(2, 'usuario', '$2y$10$AFNGKLpbpE83jirQ4CDjLu1vxtobOrjGVDaiKRoWIySfzGe9eDsai', 'Usuario Normal', 'usuario@example.com', NULL, NULL, NULL, 'usuario', NULL, '2025-09-11 20:56:51', '2025-09-11 21:32:08'),
(4, 'gabriela', '$2y$10$PqkXHkl4cXqjR9o9j1PPwODhOrnJf39dVMJfYL8KXAx.6styoYTDC', '', 'kevinavenda99@gmail.com', '1212121212', NULL, NULL, '', NULL, '2025-10-08 21:51:07', '2025-10-08 21:51:07'),
(5, 'bibiRivera', '$2y$10$0yNZDf3ffX8jrtMgj5uleOuK3pZSs3wB7qjfpTGrWmqk2h4Qn6bdi', 'bibiana rivera', 'contadora@asurcol.org', '3111111111111', '', NULL, 'admin', NULL, '2025-10-14 15:55:45', '2025-10-14 15:58:04'),
(6, 'mario ariza', '$2y$10$z3MgujlrVwnddrxXUws/NOxxg9ilixOd8pB/SW6UNYrKTPAD0kAf2', 'Mario Ariza', 'tesorero@asurcol.org', '321212121', '', NULL, 'usuario', NULL, '2025-10-28 20:01:10', '2025-10-29 21:24:07'),
(7, 'mariana vargas', '$2y$10$LXV8JRLAMNhps6tGfYZuC.fGQS185ZV7b1Ugo3TaRa8Vry7HMikhy', 'Mariana Vargas Erazo', 'mv66@gmail.com', '3108454964', '', 'avatar_7_1761769736.png', 'admin', NULL, '2025-10-29 20:22:47', '2025-10-29 20:28:56'),
(8, 'willard cano', '$2y$10$gDSgbzTQrbIKLRJrXPW.T.eQmi85JP0zh31CfKxGdpAK7/5uJeG8i', '', 'presidente@asurcol.org', '3225488857', NULL, NULL, 'usuario', NULL, '2025-10-29 21:08:29', '2025-10-29 21:24:34');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria_libros`
--
ALTER TABLE `auditoria_libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_fecha` (`user_id`,`created_at`),
  ADD KEY `idx_libro` (`libro_id`),
  ADD KEY `idx_accion` (`accion`);

--
-- Indices de la tabla `firmantes`
--
ALTER TABLE `firmantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `firmas`
--
ALTER TABLE `firmas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_libro_firmante` (`libro_id`,`firmante_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_firma` (`fecha_firma`),
  ADD KEY `idx_libro_estado` (`libro_id`,`estado`),
  ADD KEY `idx_libro_id` (`libro_id`),
  ADD KEY `idx_firmante_id` (`firmante_id`),
  ADD KEY `idx_firmado_por` (`firmado_por`);

--
-- Indices de la tabla `historial_firmas`
--
ALTER TABLE `historial_firmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_firma_id` (`firma_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_referencia` (`numero_referencia`),
  ADD KEY `idx_numero_referencia` (`numero_referencia`),
  ADD KEY `idx_mes_año` (`mes`,`año`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_libros`
--
ALTER TABLE `auditoria_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `firmantes`
--
ALTER TABLE `firmantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `firmas`
--
ALTER TABLE `firmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `historial_firmas`
--
ALTER TABLE `historial_firmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria_libros`
--
ALTER TABLE `auditoria_libros`
  ADD CONSTRAINT `auditoria_libros_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auditoria_libros_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `firmas`
--
ALTER TABLE `firmas`
  ADD CONSTRAINT `fk_firmas_firmante` FOREIGN KEY (`firmante_id`) REFERENCES `firmantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_firmas_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_firmas_usuario` FOREIGN KEY (`firmado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `historial_firmas`
--
ALTER TABLE `historial_firmas`
  ADD CONSTRAINT `fk_historial_firma` FOREIGN KEY (`firma_id`) REFERENCES `firmas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
