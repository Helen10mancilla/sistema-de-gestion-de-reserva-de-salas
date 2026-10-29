-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2026 a las 20:09:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `programacion_salas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assembly_hall`
--

CREATE TABLE `assembly_hall` (
  `id` int(30) NOT NULL,
  `room_name` varchar(250) NOT NULL,
  `location` text NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `assembly_hall`
--

INSERT INTO `assembly_hall` (`id`, `room_name`, `location`, `description`, `capacity`, `status`, `date_created`, `date_updated`) VALUES
(1, 'SALA MULTIMEDIA NORTE', 'Biblioteca Norte', 'Capacidad 10 personas', NULL, 0, '2021-08-06 09:26:17', '2026-10-13 14:40:00'),
(2, 'SALA AUXILIAR SUR', 'Biblioteca Sur', 'Para docentes, funcionarios y estudiantes. Capacidad m&aacute;xima 14 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 14, 1, '2021-08-06 09:31:14', '2026-10-15 10:35:12'),
(3, 'SALA DE JUNTAS SUR', 'Biblioteca sur ', 'Para docentes, funcionarios y estudiantes. Cantidad m&aacute;xima 14 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 14, 1, '2024-11-06 12:23:04', '2026-10-15 10:34:50'),
(4, 'COWORKING 4 SUR', 'Biblioteca sur', 'Espacio para docentes, funcionarios y estudiantes. Cantidad m&aacute;xima 8 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 8, 1, '2024-11-06 12:24:11', '2026-10-13 15:08:42'),
(5, 'COWORKING 3 SUR', 'Biblioteca sur', 'Espacio para docentes, funcionarios y estudiantes. Cantidad m&aacute;xima 5 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 5, 1, '2024-11-06 12:25:00', '2026-10-13 15:09:22'),
(6, 'COWORKING 2 SUR', 'Biblioteca sur', 'Espacio para docentes, funcionarios y estudiantes. Capacidad m&aacute;xima 5 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 5, 1, '2024-11-06 12:25:46', '2026-10-15 10:32:36'),
(7, 'COWORKING 1 SUR', 'Biblioteca sur', 'Espacio para docentes, funcionarios y estudiantes. Capacidad m&aacute;xima 5 personas. Reserva por franja de 2 horas. En caso de requerir m&aacute;s tiempo en este espacio, por favor, realice una nueva reserva para el horario siguiente.', 5, 1, '2024-11-06 12:26:30', '2026-10-13 15:08:57'),
(8, 'CABINA INSONORIZADA 3 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 17:38:42', '2026-10-15 10:33:06'),
(9, 'CABINA INSONORIZADA 4 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 17:40:45', '2026-10-15 10:33:15'),
(10, 'CABINA INSONORIZADA 2 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 18:20:30', '2026-10-15 10:32:57'),
(11, 'CABINA INSONORIZADA 1 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 18:20:38', '2026-10-15 10:32:49'),
(12, 'COWORKING 5 SUR', 'Biblioteca sur', '', NULL, 1, '2026-10-12 18:21:52', NULL),
(13, 'COWORKING 6 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 18:22:11', '2026-10-15 10:33:42'),
(14, 'COWORKING 7 SUR', 'Biblioteca sur', '', 0, 0, '2026-10-12 18:22:29', '2026-10-19 21:03:58'),
(15, 'SALA DE REUNION ', 'Biblioteca Norte', '', NULL, 0, '2026-10-12 18:23:36', '2026-10-13 14:40:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_users`
--

CREATE TABLE `portal_users` (
  `id` int(11) NOT NULL,
  `institutional_email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(250) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `role` enum('student','faculty') NOT NULL,
  `coa_ref_id` varchar(100) DEFAULT NULL COMMENT 'ID/código de la persona en la BD coa, si aplica',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_users`
--

INSERT INTO `portal_users` (`id`, `institutional_email`, `password`, `fullname`, `cedula`, `role`, `coa_ref_id`, `status`, `date_added`, `reset_token`, `reset_expires`) VALUES
(1, 'helenorejuela@estudiante.uniajc.edu.co', '$2y$10$fzWDQaKGkngPh1XfrveBBOPO66b3CjFJK.ZGJeOZEq2KFfrMZUK5O', 'Helen Orejuela mancilla', NULL, 'student', NULL, 1, '2026-09-12 17:42:10', NULL, NULL),
(2, 'jose@profesores.uniajc.edu.co', '$2y$10$jEEiMow4J.9ND8oySCy78OnlQIF6nrr9YTjTb0761LVkC3YirujLG', 'jose alvarez', NULL, 'faculty', NULL, 1, '2026-09-12 17:45:25', NULL, NULL),
(3, 'sandrajaramillo@profesores.uniajc.edu.co', '$2y$10$RsEBlfzBteboW7W8x0uTcODBo3SzrnqOk5ldM5py07BOb.LgIds2S', 'Sandra jaramillo', NULL, 'faculty', NULL, 1, '2026-09-15 17:40:05', NULL, NULL),
(4, 'danielagmez@estudiante.uniajc.edu.co', '$2y$10$FSQpdPg/3h8oaKSJSSa2UOkFWJrtO46kmuYjh72bsbvVpiqrvaNDu', 'Daniela Gomez', NULL, 'student', NULL, 1, '2026-09-15 20:29:57', NULL, NULL),
(7, 'helen@estudiante.uniajc.edu.co', '$2y$10$Od5j6WIGVgaPJ5wNe9vO.OizGqlF7HEThZeWUEWNXX94aaFjE9vfy', 'Helen mancilla', NULL, 'student', NULL, 1, '2026-10-15 14:26:34', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `schedule_list`
--

CREATE TABLE `schedule_list` (
  `id` int(30) NOT NULL,
  `portal_user_id` int(11) DEFAULT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `cantidad_personas` int(11) DEFAULT NULL,
  `assembly_hall_id` int(30) NOT NULL,
  `reserved_by` text NOT NULL,
  `schedule_remarks` text NOT NULL,
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `schedule_list`
--

INSERT INTO `schedule_list` (`id`, `portal_user_id`, `cedula`, `cantidad_personas`, `assembly_hall_id`, `reserved_by`, `schedule_remarks`, `datetime_start`, `datetime_end`, `status`, `date_created`, `date_updated`) VALUES
(5, NULL, NULL, NULL, 1, 'Adriana Florez', 'Se aparta sala para reunión de compra de aplicación de sistema.', '2021-09-20 08:29:00', '2021-09-20 10:00:00', 0, '2021-09-19 21:47:51', NULL),
(10, NULL, NULL, NULL, 1, 'Juan Mejia', '', '2024-11-26 08:00:00', '2024-11-26 10:00:00', 0, '2024-11-29 08:11:40', '2024-11-29 08:12:10'),
(11, NULL, NULL, NULL, 1, 'Sandra', '', '2024-11-29 14:00:00', '2024-11-29 16:16:00', 0, '2024-11-29 08:16:54', NULL),
(12, NULL, NULL, NULL, 1, 'Juan Mejia', '', '2024-11-29 09:17:00', '2024-11-29 10:17:00', 0, '2024-11-29 08:18:07', NULL),
(13, NULL, NULL, NULL, 6, 'Angello', '', '2024-11-29 08:22:00', '2024-11-29 10:22:00', 0, '2024-11-29 08:22:11', NULL),
(36, 7, '080808', NULL, 5, 'Helen mancilla', '', '2026-10-15 14:40:00', '2026-10-15 16:05:00', 0, '2026-10-15 14:31:40', '2026-10-15 14:32:42'),
(40, NULL, '100000000', NULL, 4, 'admin', '', '2026-10-20 09:30:00', '2026-10-20 10:30:00', 0, '2026-10-16 19:35:09', NULL),
(41, 1, '1061429262', 5, 6, 'Helen Orejuela mancilla', '', '2026-10-19 09:30:00', '2026-10-19 10:30:00', 0, '2026-10-16 19:38:22', NULL),
(43, 3, '2555555', 4, 5, 'Sandra jaramillo', 'se reserva espacio para evaluacion con 3 estudiantes', '2026-10-21 09:30:00', '2026-10-21 11:00:00', 0, '2026-10-19 09:43:27', '2026-10-19 21:03:36'),
(44, 1, '1061429262', 5, 6, 'Helen Orejuela mancilla', '', '2026-10-20 14:00:00', '2026-10-20 15:30:00', 0, '2026-10-19 20:59:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Sistema De Gestion De Reservas'),
(6, 'short_name', 'SGR'),
(11, 'logo', 'uploads/1730914380_images.jpg'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/1628211420_1.jpg'),
(15, 'anuncio', ''),
(16, 'hora_apertura', '08:00'),
(17, 'hora_cierre', '18:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'JUAN', 'MEJIA', 'juanmejia', '25d55ad283aa400af464c76d713c07ad', 'uploads/1632106140_avatar.gif', NULL, 1, '2024-11-05 14:02:37', '2025-11-05 21:49:35'),
(2, 'Helen', 'orejuela', 'helenmancilla', '5f501483fa790c630aeafb3f90fbfd83', NULL, NULL, 1, '2026-08-25 16:42:56', '2026-08-25 16:50:35'),
(6, 'Admin', 'Local', 'admin', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, 0, '2026-08-25 16:57:52', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `assembly_hall`
--
ALTER TABLE `assembly_hall`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `portal_users`
--
ALTER TABLE `portal_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institutional_email` (`institutional_email`);

--
-- Indices de la tabla `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_schedule_portal_user` (`portal_user_id`);

--
-- Indices de la tabla `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `assembly_hall`
--
ALTER TABLE `assembly_hall`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `portal_users`
--
ALTER TABLE `portal_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `schedule_list`
--
ALTER TABLE `schedule_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD CONSTRAINT `fk_schedule_portal_user` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
