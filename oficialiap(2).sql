-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-09-2025 a las 23:57:38
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
-- Base de datos: `oficialiap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id`, `nombre`, `descripcion`, `fecha_creacion`, `activo`) VALUES
(1, 'Administracion', 'Altas y bajas de areas y usuarios', '2025-09-05 21:03:00', 1),
(7, 'Tecnologias', 'Apoyo y mantenimiento', '2025-09-19 15:27:30', 1),
(13, 'Caseta', 'Registro', '2025-09-24 19:21:29', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_derivaciones`
--

CREATE TABLE `historial_derivaciones` (
  `id` int(11) NOT NULL,
  `oficio_id` int(11) NOT NULL,
  `area_origen_id` int(11) NOT NULL,
  `usuario_origen_id` int(11) NOT NULL,
  `area_destino_id` int(11) DEFAULT NULL,
  `usuario_destino_id` int(11) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `fecha_derivacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_derivaciones`
--

INSERT INTO `historial_derivaciones` (`id`, `oficio_id`, `area_origen_id`, `usuario_origen_id`, `area_destino_id`, `usuario_destino_id`, `respuesta`, `estado`, `fecha_derivacion`, `observaciones`) VALUES
(1, 15, 1, 1, 7, 18, 'ya quedo', 'completado', '2025-09-22 18:26:55', NULL),
(2, 25, 1, 1, 7, 20, 'este tambien', 'tramite', '2025-09-23 15:51:36', NULL),
(3, 26, 1, 1, 7, 18, 'ya esta', 'completado', '2025-09-23 15:58:31', NULL),
(4, 25, 1, 1, 7, 18, 'pasalo', 'tramite', '2025-09-23 16:29:15', NULL),
(5, 26, 1, 1, NULL, NULL, 'no', 'denegado', '2025-09-24 17:14:13', 'RESPUESTA FINAL'),
(7, 26, 1, 1, 7, 20, 'no', 'denegado', '2025-09-24 19:06:14', NULL),
(8, 25, 1, 1, 1, 1, 'pasalo', 'tramite', '2025-09-24 19:06:45', NULL),
(9, 26, 1, 1, 7, 20, 'no', 'tramite', '2025-09-24 19:19:19', NULL),
(10, 15, 1, 1, 7, 20, 'ya quedo', 'completado', '2025-09-24 19:19:28', NULL),
(11, 15, 1, 1, NULL, NULL, 'no se puede', 'denegado', '2025-09-25 16:29:32', 'RESPUESTA FINAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_usuario` varchar(20) NOT NULL DEFAULT 'usuario',
  `area_id` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id`, `usuario`, `password`, `nombre`, `tipo_usuario`, `area_id`, `email`) VALUES
(1, 'admin', '$2y$10$.c/NDeZxiIj4b64rIRqePu4fIwDE4FbbAYiZU9xaLvUfqHIK1evbq', 'Administrador', 'Administrador', 1, 'admin@gmail.com'),
(18, 'jorge', '$2y$10$V1hqiU7F1YQ.JGMI9jYhV.YCD.WIcSWvrJlONgzZMFFHAidZxxSbW', 'Jorge Julian Rodriguez Lopez', 'Usuario', 7, 'jorge@gmail.com'),
(20, 'julian', '$2y$10$gOVDij6o2eyPAhvZXbitPOOeE.dSS4Su5WKCU2Wq3hrrBwzmVNVLW', 'Jorge Julian Rodriguez Lopez', 'Usuario', 7, 'julian@gmail.com'),
(23, 'juan', '$2y$10$IsUoQDY8JxMaJgc6XDHOuuWkhEeVEblNzKcoWAov1Uu/41YAgfJCG', 'Juan Perez', 'Guardia', 13, 'juan@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficios`
--

CREATE TABLE `oficios` (
  `id` int(11) NOT NULL,
  `remitente` varchar(255) NOT NULL,
  `tipo_persona` enum('natural','juridica') NOT NULL,
  `tipo_documento` enum('carta','ruc_dni') NOT NULL,
  `numero_documento` varchar(20) DEFAULT NULL,
  `folios` int(11) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `asunto` varchar(500) NOT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_ruta` varchar(500) DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `area_derivada_id` int(11) DEFAULT NULL,
  `usuario_derivado_id` int(11) DEFAULT NULL,
  `fecha_derivacion` timestamp NULL DEFAULT NULL,
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `area_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','tramite','completado','denegado') DEFAULT 'pendiente',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `oficios`
--

INSERT INTO `oficios` (`id`, `remitente`, `tipo_persona`, `tipo_documento`, `numero_documento`, `folios`, `correo`, `telefono`, `asunto`, `archivo_nombre`, `archivo_ruta`, `respuesta`, `area_derivada_id`, `usuario_derivado_id`, `fecha_derivacion`, `fecha_respuesta`, `area_id`, `usuario_id`, `fecha_registro`, `estado`, `activo`) VALUES
(15, 'Cristiano', 'natural', 'carta', '123456', 5, 'jorge@gmail.com', '4622457138', 'delito armado', NULL, NULL, 'no se puede', 7, 20, '2025-09-24 19:19:29', '2025-09-25 16:29:32', 1, 1, '2025-09-22 18:23:03', 'denegado', 1),
(25, 'jorge', 'juridica', 'carta', '1379', 6, 'jorge@jorge.com', '4623465798', 'camaras accidente', NULL, NULL, 'pasalo', 1, 1, '2025-09-24 19:06:45', NULL, 1, 1, '2025-09-22 22:08:37', 'tramite', 1),
(26, 'jorge', 'juridica', 'carta', '1379', 2, 'jorge@gmail.com', '4622457138', 'videos', NULL, NULL, 'no', 7, 20, '2025-09-24 19:19:19', '2025-09-24 17:14:13', 1, 1, '2025-09-22 22:08:37', 'tramite', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `historial_derivaciones`
--
ALTER TABLE `historial_derivaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area_origen_id` (`area_origen_id`),
  ADD KEY `usuario_origen_id` (`usuario_origen_id`),
  ADD KEY `area_destino_id` (`area_destino_id`),
  ADD KEY `usuario_destino_id` (`usuario_destino_id`),
  ADD KEY `idx_oficio_id` (`oficio_id`),
  ADD KEY `idx_fecha_derivacion` (`fecha_derivacion`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_area` (`area_id`);

--
-- Indices de la tabla `oficios`
--
ALTER TABLE `oficios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `area_derivada_id` (`area_derivada_id`),
  ADD KEY `usuario_derivado_id` (`usuario_derivado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `historial_derivaciones`
--
ALTER TABLE `historial_derivaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `oficios`
--
ALTER TABLE `oficios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_derivaciones`
--
ALTER TABLE `historial_derivaciones`
  ADD CONSTRAINT `historial_derivaciones_ibfk_1` FOREIGN KEY (`oficio_id`) REFERENCES `oficios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_derivaciones_ibfk_2` FOREIGN KEY (`area_origen_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `historial_derivaciones_ibfk_3` FOREIGN KEY (`usuario_origen_id`) REFERENCES `login` (`id`),
  ADD CONSTRAINT `historial_derivaciones_ibfk_4` FOREIGN KEY (`area_destino_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `historial_derivaciones_ibfk_5` FOREIGN KEY (`usuario_destino_id`) REFERENCES `login` (`id`);

--
-- Filtros para la tabla `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `fk_login_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `oficios`
--
ALTER TABLE `oficios`
  ADD CONSTRAINT `oficios_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `oficios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `login` (`id`),
  ADD CONSTRAINT `oficios_ibfk_3` FOREIGN KEY (`area_derivada_id`) REFERENCES `areas` (`id`),
  ADD CONSTRAINT `oficios_ibfk_4` FOREIGN KEY (`usuario_derivado_id`) REFERENCES `login` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
