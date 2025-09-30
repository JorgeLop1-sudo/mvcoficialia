-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2025 a las 00:00:32
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
(12, 27, 1, 23, 1, 1, 'hola', 'pendiente', '2025-09-29 18:27:08', NULL);

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
(1, 'admin', '$2y$10$leoiQsy7CpYfEIB.TpPbAOo00GT0Gx.Pngy5s9jSk4jQfy6jgG7zy', 'Administrador', 'Administrador', 1, 'admin@gmail.com'),
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
(27, 'messi', 'natural', 'ruc_dni', '987654321', 5, 'jorge@gmail.com', '4622457138', 'camaras', 'oficialiap(1).sql', '../../../uploads/1758911321_oficialiap(1).sql', 'hola', 1, 1, '2025-09-29 18:27:08', NULL, 1, 23, '2025-09-26 18:28:41', 'tramite', 1),
(28, 'messi', 'natural', 'carta', '', 4, 'jorge@gmail.com', '4622457138', 'camaras buenas', '05-01-24 TBN.pdf', '../../../uploads/1759259536_05-01-24 TBN.pdf', NULL, NULL, NULL, NULL, NULL, 1, 23, '2025-09-30 19:12:16', 'pendiente', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `oficios`
--
ALTER TABLE `oficios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
