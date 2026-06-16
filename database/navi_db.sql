-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-06-2026 a las 09:59:33
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `navi_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_zonas`
--

CREATE TABLE `asignacion_zonas` (
  `id` bigint UNSIGNED NOT NULL,
  `segmentacion_zona_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignacion_zonas`
--

INSERT INTO `asignacion_zonas` (`id`, `segmentacion_zona_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-05-19 14:11:04', '2026-05-19 14:11:04'),
(2, 2, 4, '2026-05-19 14:14:31', '2026-05-19 14:14:31'),
(3, 1, 5, '2026-05-19 14:17:03', '2026-05-19 14:17:03'),
(5, 2, 6, '2026-05-19 14:22:08', '2026-05-19 14:22:08'),
(6, 3, 3, '2026-06-03 19:34:40', '2026-06-03 19:34:40'),
(7, 3, 8, '2026-06-03 19:37:27', '2026-06-03 19:37:27'),
(8, 3, 5, '2026-06-09 08:47:22', '2026-06-09 08:47:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_productos`
--

CREATE TABLE `categoria_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria_productos`
--

INSERT INTO `categoria_productos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'CATEGORIA 1', '2026-05-19 14:30:20', '2026-05-19 14:30:20'),
(2, 'CATEGORIA 2', '2026-05-19 14:30:24', '2026-05-19 14:30:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudads`
--

CREATE TABLE `ciudads` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ciudads`
--

INSERT INTO `ciudads` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Ciudad 1', NULL, NULL),
(2, 'Ciudad 2', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nit_ci` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dir` varchar(900) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitud` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitud` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_negocio_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `segmentacion_zona_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `fono`, `razon_social`, `nit_ci`, `dir`, `latitud`, `longitud`, `tipo_negocio_id`, `user_id`, `segmentacion_zona_id`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'mario gonzales', '67676767', 'gonzales', '564564564', 'los pedregales #w323', '-16.4666816266489', '-68.15597157691236', 2, 5, 1, '2026-05-20', 1, '2026-05-20 15:56:03', '2026-06-03 15:43:58'),
(2, 'juan mamani', '67676767', NULL, NULL, 'los pedregales #111', '-16.464543790825925', '-68.1563555938417', 1, 5, 1, '2026-05-20', 1, '2026-05-20 19:55:22', '2026-06-03 15:43:53'),
(3, 'maria gonzales', '676767676', NULL, NULL, 'los pedregales', '-16.46639354031556', '-68.15444854486383', 1, 5, 1, '2026-05-20', 1, '2026-05-20 19:55:36', '2026-06-03 15:41:35'),
(4, 'felix contreras', '67676767', NULL, NULL, 'los olivos 3223', '-16.51077436050994', '-68.15931904933588', 2, 6, 2, '2026-05-20', 1, '2026-05-20 20:05:12', '2026-06-03 15:41:27'),
(5, 'francisca choque', '6767676767', NULL, NULL, 'los pedregales', '-16.510646101336924', '-68.15821393431824', 2, 6, 2, '2026-05-20', 1, '2026-05-20 20:05:35', '2026-06-03 15:41:23'),
(6, 'elias', '77575383', NULL, NULL, 'los pedregales', '-16.466163649686624', '-68.15611723176876', 1, 1, 1, '2026-05-23', 1, '2026-05-23 20:55:57', '2026-06-03 15:41:17'),
(7, 'juan quispe', '45546456456', NULL, NULL, 'pedregales', '-16.49283557291197', '-68.14310976545919', 1, 3, 3, '2026-06-03', 1, '2026-06-03 19:36:17', '2026-06-03 19:36:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisions`
--

CREATE TABLE `comisions` (
  `id` bigint UNSIGNED NOT NULL,
  `distribuidor_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comisions`
--

INSERT INTO `comisions` (`id`, `distribuidor_id`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(2, 3, 1, '2026-05-26', '11:35:44', '2026-05-26 15:35:44', '2026-05-26 15:35:44'),
(3, 3, 1, '2026-05-26', '12:42:22', '2026-05-26 16:42:22', '2026-05-26 16:42:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comision_detalles`
--

CREATE TABLE `comision_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `comision_id` bigint UNSIGNED NOT NULL,
  `despacho_id` bigint UNSIGNED NOT NULL,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `comision_distribuidor` decimal(24,2) NOT NULL,
  `comision_vendedor` decimal(24,2) NOT NULL,
  `entrega_distribuidor` decimal(24,2) NOT NULL,
  `entrega_vendedor` decimal(24,2) NOT NULL,
  `detalle_presentacion` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comision_detalles`
--

INSERT INTO `comision_detalles` (`id`, `comision_id`, `despacho_id`, `categoria_producto_id`, `producto_id`, `cantidad`, `total`, `comision_distribuidor`, `comision_vendedor`, `entrega_distribuidor`, `entrega_vendedor`, `detalle_presentacion`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 1, 1, 12, 200.00, 20.00, 10.00, 20.00, 10.00, '[{\"id\": 8, \"ver\": false, \"total\": 200, \"nombre\": \"media caja\", \"precio\": \"200.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:50:47.000000Z\", \"p_vendedor\": \"5.00\", \"updated_at\": \"2026-05-19T19:50:47.000000Z\", \"producto_id\": 1, \"comi_vendedor\": \"5.00\", \"p_distribuidor\": \"10.00\", \"total_cantidad\": 1, \"comi_distribuidor\": \"10.00\", \"comision_vendedor\": 10, \"cantidad_presentacion\": 1, \"comision_distribuidor\": 20}]', '2026-05-26 15:35:44', '2026-05-26 15:35:44'),
(2, 2, 3, 1, 3, 36, 195.00, 9.75, 4.88, 9.75, 4.88, '[{\"id\": 2, \"ver\": false, \"total\": 195, \"nombre\": \"media caja\", \"precio\": \"65.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:41:19.000000Z\", \"p_vendedor\": \"2.50\", \"updated_at\": \"2026-05-19T19:41:19.000000Z\", \"producto_id\": 3, \"comi_vendedor\": \"2.50\", \"p_distribuidor\": \"5.00\", \"total_cantidad\": 3, \"comi_distribuidor\": \"5.00\", \"comision_vendedor\": 4.88, \"cantidad_presentacion\": 3, \"comision_distribuidor\": 9.75}]', '2026-05-26 15:35:44', '2026-05-26 15:35:44'),
(3, 2, 3, 2, 2, 36, 330.00, 16.50, 8.25, 16.50, 8.25, '[{\"id\": 5, \"ver\": false, \"total\": 330, \"nombre\": \"media caja\", \"precio\": \"110.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:48:16.000000Z\", \"p_vendedor\": \"2.50\", \"updated_at\": \"2026-05-19T19:48:16.000000Z\", \"producto_id\": 2, \"comi_vendedor\": \"2.50\", \"p_distribuidor\": \"5.00\", \"total_cantidad\": 3, \"comi_distribuidor\": \"5.00\", \"comision_vendedor\": 8.25, \"cantidad_presentacion\": 3, \"comision_distribuidor\": 16.5}]', '2026-05-26 15:35:44', '2026-05-26 15:35:44'),
(4, 3, 4, 1, 4, 5, 150.00, 4.50, 1.50, 4.50, 1.50, '[{\"id\": 12, \"ver\": false, \"total\": 150, \"nombre\": \"unidad\", \"precio\": \"30.00\", \"equivale\": 1, \"created_at\": \"2026-05-26T16:40:24.000000Z\", \"p_vendedor\": \"1.00\", \"updated_at\": \"2026-05-26T16:40:24.000000Z\", \"producto_id\": 4, \"comi_vendedor\": \"1.00\", \"p_distribuidor\": \"3.00\", \"total_cantidad\": 5, \"comi_distribuidor\": \"3.00\", \"comision_vendedor\": 1.5, \"cantidad_presentacion\": 5, \"comision_distribuidor\": 4.5}]', '2026-05-26 16:42:22', '2026-05-26 16:42:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` bigint UNSIGNED NOT NULL,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `precio_compra` decimal(24,2) NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `categoria_producto_id`, `producto_id`, `cantidad`, `precio_compra`, `total`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100, 40.00, 4000.00, '2026-05-26', '11:10:41', '2026-05-26 15:10:41', '2026-05-26 15:10:41'),
(2, 2, 2, 100, 30.00, 3000.00, '2026-05-26', '11:10:46', '2026-05-26 15:10:46', '2026-05-26 15:10:46'),
(3, 1, 3, 100, 35.00, 3500.00, '2026-05-26', '11:10:52', '2026-05-26 15:10:52', '2026-05-26 15:10:52'),
(4, 1, 4, 100, 36.00, 3600.00, '2026-05-26', '11:10:57', '2026-05-26 15:10:57', '2026-05-26 15:10:57'),
(5, 1, 1, 100, 40.00, 4000.00, '2026-05-26', '11:33:06', '2026-05-26 15:33:06', '2026-05-26 15:33:06'),
(6, 2, 2, 100, 35.00, 3500.00, '2026-05-26', '11:33:13', '2026-05-26 15:33:13', '2026-05-26 15:33:13'),
(7, 1, 3, 100, 37.00, 3700.00, '2026-05-26', '11:33:18', '2026-05-26 15:33:18', '2026-05-26 15:33:18'),
(8, 1, 4, 100, 45.00, 4500.00, '2026-05-26', '11:33:23', '2026-05-26 15:33:23', '2026-05-26 15:33:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracions`
--

CREATE TABLE `configuracions` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre_sistema` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actividad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_hora_inicio_admin` time DEFAULT NULL,
  `b_hora_fin_admin` time DEFAULT NULL,
  `b_hora_inicio_dist` time DEFAULT NULL,
  `b_hora_fin_dist` time DEFAULT NULL,
  `b_hora_inicio_ven` time DEFAULT NULL,
  `b_hora_fin_ven` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `logo`, `actividad`, `b_hora_inicio_admin`, `b_hora_fin_admin`, `b_hora_inicio_dist`, `b_hora_fin_dist`, `b_hora_inicio_ven`, `b_hora_fin_ven`, `created_at`, `updated_at`) VALUES
(1, 'NAVI', 'NAVI', 'logo11779111091.png', 'ACTIVIDAD NAVI', '08:00:00', '18:30:00', '08:00:00', '18:30:00', '08:00:00', '18:30:00', '2026-05-18 13:31:09', '2026-06-03 14:23:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consolidados`
--

CREATE TABLE `consolidados` (
  `id` bigint UNSIGNED NOT NULL,
  `distribuidor_id` bigint UNSIGNED NOT NULL,
  `despacho_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `consolidados`
--

INSERT INTO `consolidados` (`id`, `distribuidor_id`, `despacho_id`, `user_id`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 3, 3, 1, '2026-05-26', '11:34:36', '2026-05-26 15:34:36', '2026-05-26 15:34:36'),
(2, 3, 4, 1, '2026-05-26', '12:41:31', '2026-05-26 16:41:31', '2026-05-26 16:41:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'La Paz', NULL, NULL),
(2, 'Cochabamba', NULL, NULL),
(3, 'Santa Cruz', NULL, NULL),
(4, 'Oruro', NULL, NULL),
(5, 'Potosi', NULL, NULL),
(6, 'Chuquisaca', NULL, NULL),
(7, 'Tarija', NULL, NULL),
(8, 'Pando', NULL, NULL),
(9, 'Beni', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `despachos`
--

CREATE TABLE `despachos` (
  `id` bigint UNSIGNED NOT NULL,
  `distribuidor_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SIN CONSOLIDAR',
  `comision` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `despachos`
--

INSERT INTO `despachos` (`id`, `distribuidor_id`, `user_id`, `observacion`, `fecha`, `hora`, `estado`, `comision`, `created_at`, `updated_at`) VALUES
(3, 3, 1, NULL, '2026-05-26', '11:33:54', 'CONSOLIDADO', 1, '2026-05-26 15:33:54', '2026-05-26 15:35:44'),
(4, 3, 1, NULL, '2026-05-26', '12:40:58', 'CONSOLIDADO', 1, '2026-05-26 16:40:58', '2026-05-26 16:42:22'),
(5, 3, 1, NULL, '2026-06-03', '10:35:24', 'SIN CONSOLIDAR', 0, '2026-06-03 14:35:24', '2026-06-03 14:35:24'),
(6, 3, 1, NULL, '2026-06-03', '16:03:29', 'SIN CONSOLIDAR', 0, '2026-06-03 20:03:29', '2026-06-03 20:03:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_accions`
--

CREATE TABLE `historial_accions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `accion` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_original` json DEFAULT NULL,
  `datos_nuevo` json DEFAULT NULL,
  `modulo` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_accions`
--

INSERT INTO `historial_accions` (`id`, `user_id`, `accion`, `descripcion`, `datos_original`, `datos_nuevo`, `modulo`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 5, 'CREACIÓN', 'EL USUARIO jmamani REGISTRO UN PEDIDO', '{\"id\": 1, \"hora\": \"11:32:04\", \"fecha\": \"2026-05-26\", \"total\": \"310.00\", \"user_id\": 5, \"subtotal\": \"310.00\", \"descuento\": \"0\", \"cliente_id\": \"1\", \"created_at\": \"2026-05-26T15:32:04.000000Z\", \"updated_at\": \"2026-05-26T15:32:04.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 1, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:32:04.000000Z\", \"updated_at\": \"2026-05-26T15:32:04.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}, {\"id\": 2, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"110.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:32:04.000000Z\", \"updated_at\": \"2026-05-26T15:32:04.000000Z\", \"producto_id\": 2, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-26', '11:32:04', '2026-05-26 15:32:04', '2026-05-26 15:32:04'),
(2, 5, 'CREACIÓN', 'EL USUARIO jmamani REGISTRO UN PEDIDO', '{\"id\": 2, \"hora\": \"11:32:17\", \"fecha\": \"2026-05-26\", \"total\": \"340.00\", \"user_id\": 5, \"subtotal\": \"340.00\", \"descuento\": \"0\", \"cliente_id\": \"2\", \"created_at\": \"2026-05-26T15:32:17.000000Z\", \"updated_at\": \"2026-05-26T15:32:17.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 3, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 2, \"subtotal\": \"220.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:32:17.000000Z\", \"updated_at\": \"2026-05-26T15:32:17.000000Z\", \"producto_id\": 2, \"cantidad_total\": 24, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}, {\"id\": 4, \"precio\": \"120.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"120.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:32:17.000000Z\", \"updated_at\": \"2026-05-26T15:32:17.000000Z\", \"producto_id\": 3, \"cantidad_total\": 24, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 1}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-26', '11:32:17', '2026-05-26 15:32:17', '2026-05-26 15:32:17'),
(3, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMPRA', '{\"id\": 5, \"hora\": \"11:33:06\", \"fecha\": \"2026-05-26\", \"total\": 4000, \"cantidad\": 100, \"created_at\": \"2026-05-26T15:33:06.000000Z\", \"updated_at\": \"2026-05-26T15:33:06.000000Z\", \"producto_id\": 1, \"precio_compra\": 40, \"categoria_producto_id\": 1}', NULL, 'COMPRAS', '2026-05-26', '11:33:06', '2026-05-26 15:33:06', '2026-05-26 15:33:06'),
(4, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMPRA', '{\"id\": 6, \"hora\": \"11:33:13\", \"fecha\": \"2026-05-26\", \"total\": 3500, \"cantidad\": 100, \"created_at\": \"2026-05-26T15:33:13.000000Z\", \"updated_at\": \"2026-05-26T15:33:13.000000Z\", \"producto_id\": 2, \"precio_compra\": 35, \"categoria_producto_id\": 2}', NULL, 'COMPRAS', '2026-05-26', '11:33:13', '2026-05-26 15:33:13', '2026-05-26 15:33:13'),
(5, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMPRA', '{\"id\": 7, \"hora\": \"11:33:18\", \"fecha\": \"2026-05-26\", \"total\": 3700, \"cantidad\": 100, \"created_at\": \"2026-05-26T15:33:18.000000Z\", \"updated_at\": \"2026-05-26T15:33:18.000000Z\", \"producto_id\": 3, \"precio_compra\": 37, \"categoria_producto_id\": 1}', NULL, 'COMPRAS', '2026-05-26', '11:33:18', '2026-05-26 15:33:18', '2026-05-26 15:33:18'),
(6, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMPRA', '{\"id\": 8, \"hora\": \"11:33:23\", \"fecha\": \"2026-05-26\", \"total\": 4500, \"cantidad\": 100, \"created_at\": \"2026-05-26T15:33:23.000000Z\", \"updated_at\": \"2026-05-26T15:33:23.000000Z\", \"producto_id\": 4, \"precio_compra\": 45, \"categoria_producto_id\": 1}', NULL, 'COMPRAS', '2026-05-26', '11:33:23', '2026-05-26 15:33:23', '2026-05-26 15:33:23'),
(7, 5, 'CREACIÓN', 'EL USUARIO jmamani REGISTRO UN PEDIDO', '{\"id\": 1, \"hora\": \"11:33:35\", \"fecha\": \"2026-05-26\", \"total\": \"420.00\", \"user_id\": 5, \"subtotal\": \"420.00\", \"descuento\": \"0\", \"cliente_id\": \"1\", \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:35.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 1, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:35.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}, {\"id\": 2, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 2, \"subtotal\": \"220.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:35.000000Z\", \"producto_id\": 2, \"cantidad_total\": 24, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-26', '11:33:35', '2026-05-26 15:33:35', '2026-05-26 15:33:35'),
(8, 5, 'CREACIÓN', 'EL USUARIO jmamani REGISTRO UN PEDIDO', '{\"id\": 2, \"hora\": \"11:33:46\", \"fecha\": \"2026-05-26\", \"total\": \"305.00\", \"user_id\": 5, \"subtotal\": \"305.00\", \"descuento\": \"0\", \"cliente_id\": \"2\", \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:46.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 3, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"110.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:46.000000Z\", \"producto_id\": 2, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}, {\"id\": 4, \"precio\": \"65.00\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"195.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:46.000000Z\", \"producto_id\": 3, \"cantidad_total\": 36, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 2}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-26', '11:33:46', '2026-05-26 15:33:46', '2026-05-26 15:33:46'),
(9, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN DESPACHO', '{\"id\": 3, \"hora\": \"11:33:54\", \"fecha\": \"2026-05-26\", \"pedidos\": [{\"id\": 1, \"hora\": \"11:33:35\", \"fecha\": \"2026-05-26\", \"total\": \"420.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"26/05/2026\", \"user_id\": 5, \"subtotal\": \"420.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}, {\"id\": 2, \"hora\": \"11:33:46\", \"fecha\": \"2026-05-26\", \"total\": \"305.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"26/05/2026\", \"user_id\": 5, \"subtotal\": \"305.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}], \"user_id\": 1, \"created_at\": \"2026-05-26T15:33:54.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"observacion\": null, \"distribuidor_id\": 3}', NULL, 'DESPACHOS', '2026-05-26', '11:33:54', '2026-05-26 15:33:54', '2026-05-26 15:33:54'),
(10, 5, 'MODIFICACIÓN', 'EL USUARIO jmamani ENTREGÓ UN PEDIDO', '{\"id\": 1, \"hora\": \"11:33:35\", \"fecha\": \"2026-05-26\", \"total\": \"420.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"420.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"pedido_detalles\": [{\"id\": 1, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 12, \"cantidad_entregado\": 12, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}, {\"id\": 2, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 2, \"subtotal\": \"220.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 2, \"cantidad_total\": 24, \"cantidad_despacho\": 24, \"cantidad_entregado\": 24, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}', '{\"id\": 1, \"hora\": \"11:33:35\", \"fecha\": \"2026-05-26\", \"total\": \"420.00\", \"estado\": \"ENTREGADO\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"420.00\", \"descuento\": \"0.00\", \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:34:16.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"pedido_detalles\": [{\"id\": 1, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 12, \"cantidad_entregado\": 12, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}, {\"id\": 2, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 2, \"subtotal\": \"220.00\", \"pedido_id\": 1, \"created_at\": \"2026-05-26T15:33:35.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 2, \"cantidad_total\": 24, \"cantidad_despacho\": 24, \"cantidad_entregado\": 24, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": 5}', 'PEDIDOS', '2026-05-26', '11:34:16', '2026-05-26 15:34:16', '2026-05-26 15:34:16'),
(11, 5, 'MODIFICACIÓN', 'EL USUARIO jmamani ENTREGÓ UN PEDIDO', '{\"id\": 2, \"hora\": \"11:33:46\", \"fecha\": \"2026-05-26\", \"total\": \"305.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"305.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"pedido_detalles\": [{\"id\": 3, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"110.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 2, \"cantidad_total\": 12, \"cantidad_despacho\": 12, \"cantidad_entregado\": 12, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}, {\"id\": 4, \"precio\": \"65.00\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"195.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 3, \"cantidad_total\": 36, \"cantidad_despacho\": 36, \"cantidad_entregado\": 36, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 2}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}', '{\"id\": 2, \"hora\": \"11:33:46\", \"fecha\": \"2026-05-26\", \"total\": \"305.00\", \"estado\": \"ENTREGADO\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"305.00\", \"descuento\": \"0.00\", \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:34:27.000000Z\", \"despacho_id\": 3, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"pedido_detalles\": [{\"id\": 3, \"precio\": \"110.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"110.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 2, \"cantidad_total\": 12, \"cantidad_despacho\": 12, \"cantidad_entregado\": 12, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 5}, {\"id\": 4, \"precio\": \"65.00\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"195.00\", \"pedido_id\": 2, \"created_at\": \"2026-05-26T15:33:46.000000Z\", \"updated_at\": \"2026-05-26T15:33:54.000000Z\", \"producto_id\": 3, \"cantidad_total\": 36, \"cantidad_despacho\": 36, \"cantidad_entregado\": 36, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 2}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": 5}', 'PEDIDOS', '2026-05-26', '11:34:27', '2026-05-26 15:34:27', '2026-05-26 15:34:27'),
(12, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN CONSOLIDADO', '{\"id\": 1, \"hora\": \"11:34:36\", \"fecha\": \"2026-05-26\", \"pedidos\": [], \"user_id\": 1, \"created_at\": \"2026-05-26T15:34:36.000000Z\", \"updated_at\": \"2026-05-26T15:34:36.000000Z\", \"despacho_id\": 3, \"distribuidor_id\": 3}', NULL, 'CONSOLIDADOS', '2026-05-26', '11:34:36', '2026-05-26 15:34:36', '2026-05-26 15:34:36'),
(13, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMISIÓN', '{\"id\": 2, \"hora\": \"11:35:44\", \"fecha\": \"2026-05-26\", \"user_id\": 1, \"created_at\": \"2026-05-26T15:35:44.000000Z\", \"updated_at\": \"2026-05-26T15:35:44.000000Z\", \"distribuidor_id\": 3, \"comision_detalles\": [{\"id\": 1, \"total\": \"200.00\", \"cantidad\": 12, \"created_at\": \"2026-05-26T15:35:44.000000Z\", \"updated_at\": \"2026-05-26T15:35:44.000000Z\", \"comision_id\": 2, \"despacho_id\": 3, \"producto_id\": 1, \"entrega_vendedor\": \"10.00\", \"comision_vendedor\": \"10.00\", \"detalle_presentacion\": [{\"id\": 8, \"ver\": false, \"total\": 200, \"nombre\": \"media caja\", \"precio\": \"200.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:50:47.000000Z\", \"p_vendedor\": \"5.00\", \"updated_at\": \"2026-05-19T19:50:47.000000Z\", \"producto_id\": 1, \"comi_vendedor\": \"5.00\", \"p_distribuidor\": \"10.00\", \"total_cantidad\": 1, \"comi_distribuidor\": \"10.00\", \"comision_vendedor\": 10, \"cantidad_presentacion\": 1, \"comision_distribuidor\": 20}], \"entrega_distribuidor\": \"20.00\", \"categoria_producto_id\": 1, \"comision_distribuidor\": \"20.00\"}, {\"id\": 2, \"total\": \"195.00\", \"cantidad\": 36, \"created_at\": \"2026-05-26T15:35:44.000000Z\", \"updated_at\": \"2026-05-26T15:35:44.000000Z\", \"comision_id\": 2, \"despacho_id\": 3, \"producto_id\": 3, \"entrega_vendedor\": \"4.88\", \"comision_vendedor\": \"4.88\", \"detalle_presentacion\": [{\"id\": 2, \"ver\": false, \"total\": 195, \"nombre\": \"media caja\", \"precio\": \"65.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:41:19.000000Z\", \"p_vendedor\": \"2.50\", \"updated_at\": \"2026-05-19T19:41:19.000000Z\", \"producto_id\": 3, \"comi_vendedor\": \"2.50\", \"p_distribuidor\": \"5.00\", \"total_cantidad\": 3, \"comi_distribuidor\": \"5.00\", \"comision_vendedor\": 4.88, \"cantidad_presentacion\": 3, \"comision_distribuidor\": 9.75}], \"entrega_distribuidor\": \"9.75\", \"categoria_producto_id\": 1, \"comision_distribuidor\": \"9.75\"}, {\"id\": 3, \"total\": \"330.00\", \"cantidad\": 36, \"created_at\": \"2026-05-26T15:35:44.000000Z\", \"updated_at\": \"2026-05-26T15:35:44.000000Z\", \"comision_id\": 2, \"despacho_id\": 3, \"producto_id\": 2, \"entrega_vendedor\": \"8.25\", \"comision_vendedor\": \"8.25\", \"detalle_presentacion\": [{\"id\": 5, \"ver\": false, \"total\": 330, \"nombre\": \"media caja\", \"precio\": \"110.00\", \"equivale\": 12, \"created_at\": \"2026-05-19T19:48:16.000000Z\", \"p_vendedor\": \"2.50\", \"updated_at\": \"2026-05-19T19:48:16.000000Z\", \"producto_id\": 2, \"comi_vendedor\": \"2.50\", \"p_distribuidor\": \"5.00\", \"total_cantidad\": 3, \"comi_distribuidor\": \"5.00\", \"comision_vendedor\": 8.25, \"cantidad_presentacion\": 3, \"comision_distribuidor\": 16.5}], \"entrega_distribuidor\": \"16.50\", \"categoria_producto_id\": 2, \"comision_distribuidor\": \"16.50\"}]}', NULL, 'COMISIONES', '2026-05-26', '11:35:44', '2026-05-26 15:35:44', '2026-05-26 15:35:44'),
(14, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA PRESENTACIÓN', '{\"id\": 1, \"nombre\": \"unidad\", \"created_at\": \"2026-05-26T16:35:05.000000Z\", \"updated_at\": \"2026-05-26T16:35:05.000000Z\"}', NULL, 'PRESENTACIÓN DE PRODUCTOS', '2026-05-26', '12:35:05', '2026-05-26 16:35:05', '2026-05-26 16:35:05'),
(15, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA PRESENTACIÓN', '{\"id\": 2, \"nombre\": \"media caja\", \"created_at\": \"2026-05-26T16:35:10.000000Z\", \"updated_at\": \"2026-05-26T16:35:10.000000Z\"}', NULL, 'PRESENTACIÓN DE PRODUCTOS', '2026-05-26', '12:35:10', '2026-05-26 16:35:10', '2026-05-26 16:35:10'),
(16, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA PRESENTACIÓN', '{\"id\": 3, \"nombre\": \"caja\", \"created_at\": \"2026-05-26T16:35:14.000000Z\", \"updated_at\": \"2026-05-26T16:35:14.000000Z\"}', NULL, 'PRESENTACIÓN DE PRODUCTOS', '2026-05-26', '12:35:14', '2026-05-26 16:35:14', '2026-05-26 16:35:14'),
(17, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA PRESENTACIÓN', '{\"id\": 3, \"nombre\": \"caja\", \"created_at\": \"2026-05-26T16:35:14.000000Z\", \"updated_at\": \"2026-05-26T16:35:14.000000Z\"}', '{\"id\": 3, \"nombre\": \"caja asdas\", \"created_at\": \"2026-05-26T16:35:14.000000Z\", \"updated_at\": \"2026-05-26T16:35:18.000000Z\"}', 'PRESENTACIÓN DE PRODUCTOS', '2026-05-26', '12:35:18', '2026-05-26 16:35:18', '2026-05-26 16:35:18'),
(18, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA PRESENTACIÓN DE PRODUCTO', '{\"id\": 12, \"nombre\": \"unidad\", \"precio\": 30, \"equivale\": 1, \"created_at\": \"2026-05-26T16:40:24.000000Z\", \"updated_at\": \"2026-05-26T16:40:24.000000Z\", \"producto_id\": 4, \"comi_vendedor\": 1, \"comi_distribuidor\": 3}', NULL, 'PRESENTACIÓN DE PRODUCTOS', '2026-05-26', '12:40:24', '2026-05-26 16:40:24', '2026-05-26 16:40:24'),
(19, 5, 'CREACIÓN', 'EL USUARIO jmamani REGISTRO UN PEDIDO', '{\"id\": 3, \"hora\": \"12:40:50\", \"fecha\": \"2026-05-26\", \"total\": \"150.00\", \"user_id\": 5, \"subtotal\": \"150.00\", \"descuento\": \"0\", \"cliente_id\": \"3\", \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:50.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 5, \"precio\": \"30.00\", \"status\": 1, \"cantidad\": 5, \"subtotal\": \"150.00\", \"pedido_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:50.000000Z\", \"producto_id\": 4, \"cantidad_total\": 5, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 12}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-26', '12:40:50', '2026-05-26 16:40:50', '2026-05-26 16:40:50'),
(20, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN DESPACHO', '{\"id\": 4, \"hora\": \"12:40:58\", \"fecha\": \"2026-05-26\", \"pedidos\": [{\"id\": 3, \"hora\": \"12:40:50\", \"fecha\": \"2026-05-26\", \"total\": \"150.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"26/05/2026\", \"user_id\": 5, \"subtotal\": \"150.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:58.000000Z\", \"despacho_id\": 4, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}], \"user_id\": 1, \"created_at\": \"2026-05-26T16:40:58.000000Z\", \"updated_at\": \"2026-05-26T16:40:58.000000Z\", \"observacion\": null, \"distribuidor_id\": 3}', NULL, 'DESPACHOS', '2026-05-26', '12:40:58', '2026-05-26 16:40:58', '2026-05-26 16:40:58'),
(21, 5, 'MODIFICACIÓN', 'EL USUARIO jmamani ENTREGÓ UN PEDIDO', '{\"id\": 3, \"hora\": \"12:40:50\", \"fecha\": \"2026-05-26\", \"total\": \"150.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"150.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:58.000000Z\", \"despacho_id\": 4, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"pedido_detalles\": [{\"id\": 5, \"precio\": \"30.00\", \"status\": 1, \"cantidad\": 5, \"subtotal\": \"150.00\", \"pedido_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:58.000000Z\", \"producto_id\": 4, \"cantidad_total\": 5, \"cantidad_despacho\": 5, \"cantidad_entregado\": 5, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 12}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}', '{\"id\": 3, \"hora\": \"12:40:50\", \"fecha\": \"2026-05-26\", \"total\": \"150.00\", \"estado\": \"ENTREGADO\", \"status\": 1, \"user_id\": 5, \"subtotal\": \"150.00\", \"descuento\": \"0.00\", \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:41:15.000000Z\", \"despacho_id\": 4, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"pedido_detalles\": [{\"id\": 5, \"precio\": \"30.00\", \"status\": 1, \"cantidad\": 5, \"subtotal\": \"150.00\", \"pedido_id\": 3, \"created_at\": \"2026-05-26T16:40:50.000000Z\", \"updated_at\": \"2026-05-26T16:40:58.000000Z\", \"producto_id\": 4, \"cantidad_total\": 5, \"cantidad_despacho\": 5, \"cantidad_entregado\": 5, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 12}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": 5}', 'PEDIDOS', '2026-05-26', '12:41:15', '2026-05-26 16:41:15', '2026-05-26 16:41:15'),
(22, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN CONSOLIDADO', '{\"id\": 2, \"hora\": \"12:41:31\", \"fecha\": \"2026-05-26\", \"pedidos\": [], \"user_id\": 1, \"created_at\": \"2026-05-26T16:41:31.000000Z\", \"updated_at\": \"2026-05-26T16:41:31.000000Z\", \"despacho_id\": 4, \"distribuidor_id\": 3}', NULL, 'CONSOLIDADOS', '2026-05-26', '12:41:31', '2026-05-26 16:41:31', '2026-05-26 16:41:31'),
(23, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA COMISIÓN', '{\"id\": 3, \"hora\": \"12:42:22\", \"fecha\": \"2026-05-26\", \"user_id\": 1, \"created_at\": \"2026-05-26T16:42:22.000000Z\", \"updated_at\": \"2026-05-26T16:42:22.000000Z\", \"distribuidor_id\": 3, \"comision_detalles\": [{\"id\": 4, \"total\": \"150.00\", \"cantidad\": 5, \"created_at\": \"2026-05-26T16:42:22.000000Z\", \"updated_at\": \"2026-05-26T16:42:22.000000Z\", \"comision_id\": 3, \"despacho_id\": 4, \"producto_id\": 4, \"entrega_vendedor\": \"1.50\", \"comision_vendedor\": \"1.50\", \"detalle_presentacion\": [{\"id\": 12, \"ver\": false, \"total\": 150, \"nombre\": \"unidad\", \"precio\": \"30.00\", \"equivale\": 1, \"created_at\": \"2026-05-26T16:40:24.000000Z\", \"p_vendedor\": \"1.00\", \"updated_at\": \"2026-05-26T16:40:24.000000Z\", \"producto_id\": 4, \"comi_vendedor\": \"1.00\", \"p_distribuidor\": \"3.00\", \"total_cantidad\": 5, \"comi_distribuidor\": \"3.00\", \"comision_vendedor\": 1.5, \"cantidad_presentacion\": 5, \"comision_distribuidor\": 4.5}], \"entrega_distribuidor\": \"4.50\", \"categoria_producto_id\": 1, \"comision_distribuidor\": \"4.50\"}]}', NULL, 'COMISIONES', '2026-05-26', '12:42:22', '2026-05-26 16:42:22', '2026-05-26 16:42:22'),
(24, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PEDIDO', '{\"id\": 4, \"hora\": \"09:40:26\", \"fecha\": \"2026-05-30\", \"total\": \"7.00\", \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0\", \"cliente_id\": \"1\", \"created_at\": \"2026-05-30T13:40:26.000000Z\", \"updated_at\": \"2026-05-30T13:40:26.000000Z\", \"observacion\": null, \"pedido_detalles\": [{\"id\": 6, \"precio\": \"7.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"7.00\", \"pedido_id\": 4, \"created_at\": \"2026-05-30T13:40:26.000000Z\", \"updated_at\": \"2026-05-30T13:40:26.000000Z\", \"producto_id\": 2, \"cantidad_total\": 1, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 6}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-05-30', '09:40:26', '2026-05-30 13:40:26', '2026-05-30 13:40:26'),
(25, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PEDIDO', '{\"id\": 5, \"hora\": \"09:42:16\", \"fecha\": \"2026-05-30\", \"total\": \"7.00\", \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0\", \"cliente_id\": \"4\", \"created_at\": \"2026-05-30T13:42:16.000000Z\", \"updated_at\": \"2026-05-30T13:42:16.000000Z\", \"observacion\": null, \"distribuidor_id\": \"4\", \"pedido_detalles\": [{\"id\": 7, \"precio\": \"7.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"7.00\", \"pedido_id\": 5, \"created_at\": \"2026-05-30T13:42:16.000000Z\", \"updated_at\": \"2026-05-30T13:42:16.000000Z\", \"producto_id\": 2, \"cantidad_total\": 1, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 6}], \"segmentacion_zona_id\": 2}', NULL, 'PEDIDOS', '2026-05-30', '09:42:16', '2026-05-30 13:42:16', '2026-05-30 13:42:16'),
(26, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"id\": 2, \"foto\": null, \"tipo\": \"ADMINISTRADOR\", \"acceso\": 1, \"nombre\": \"JUAN PERES\", \"status\": 1, \"bloqueo\": 1, \"usuario\": \"jperes\", \"created_at\": \"2026-05-19T13:02:59.000000Z\", \"updated_at\": \"2026-05-19T13:02:59.000000Z\", \"fecha_registro\": \"2026-05-19\"}', '{\"id\": 2, \"foto\": null, \"tipo\": \"ADMINISTRADOR\", \"acceso\": \"1\", \"nombre\": \"JUAN PERES\", \"status\": 1, \"bloqueo\": \"0\", \"usuario\": \"jperes\", \"created_at\": \"2026-05-19T13:02:59.000000Z\", \"updated_at\": \"2026-05-30T13:50:36.000000Z\", \"fecha_registro\": \"2026-05-19\"}', 'USUARIOS', '2026-05-30', '09:50:36', '2026-05-30 13:50:36', '2026-05-30 13:50:36'),
(27, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"id\": 2, \"foto\": null, \"tipo\": \"ADMINISTRADOR\", \"acceso\": 1, \"nombre\": \"JUAN PERES\", \"status\": 1, \"bloqueo\": 0, \"usuario\": \"jperes\", \"created_at\": \"2026-05-19T13:02:59.000000Z\", \"updated_at\": \"2026-05-30T13:50:36.000000Z\", \"fecha_registro\": \"2026-05-19\"}', '{\"id\": 2, \"foto\": null, \"tipo\": \"ADMINISTRADOR\", \"acceso\": \"1\", \"nombre\": \"JUAN PERES\", \"status\": 1, \"bloqueo\": \"1\", \"usuario\": \"jperes\", \"created_at\": \"2026-05-19T13:02:59.000000Z\", \"updated_at\": \"2026-05-30T13:50:43.000000Z\", \"fecha_registro\": \"2026-05-19\"}', 'USUARIOS', '2026-05-30', '09:50:43', '2026-05-30 13:50:43', '2026-05-30 13:50:43'),
(28, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PEDIDO', '{\"id\": 6, \"hora\": \"10:34:56\", \"fecha\": \"2026-06-03\", \"total\": \"200.00\", \"user_id\": 1, \"subtotal\": \"200.00\", \"descuento\": \"0\", \"cliente_id\": \"2\", \"created_at\": \"2026-06-03T14:34:56.000000Z\", \"updated_at\": \"2026-06-03T14:34:56.000000Z\", \"observacion\": null, \"distribuidor_id\": \"3\", \"pedido_detalles\": [{\"id\": 8, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 6, \"created_at\": \"2026-06-03T14:34:56.000000Z\", \"updated_at\": \"2026-06-03T14:34:56.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-06-03', '10:34:56', '2026-06-03 14:34:56', '2026-06-03 14:34:56'),
(29, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PEDIDO', '{\"id\": 7, \"hora\": \"10:35:07\", \"fecha\": \"2026-06-03\", \"total\": \"7.00\", \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0\", \"cliente_id\": \"2\", \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T14:35:07.000000Z\", \"observacion\": null, \"distribuidor_id\": \"3\", \"pedido_detalles\": [{\"id\": 9, \"precio\": \"7.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"7.00\", \"pedido_id\": 7, \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T14:35:07.000000Z\", \"producto_id\": 2, \"cantidad_total\": 1, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 6}], \"segmentacion_zona_id\": 1}', NULL, 'PEDIDOS', '2026-06-03', '10:35:07', '2026-06-03 14:35:07', '2026-06-03 14:35:07'),
(30, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN DESPACHO', '{\"id\": 5, \"hora\": \"10:35:24\", \"fecha\": \"2026-06-03\", \"pedidos\": [{\"id\": 4, \"hora\": \"09:40:26\", \"fecha\": \"2026-05-30\", \"total\": \"7.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"30/05/2026\", \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 1, \"created_at\": \"2026-05-30T13:40:26.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"despacho_id\": 5, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}, {\"id\": 6, \"hora\": \"10:34:56\", \"fecha\": \"2026-06-03\", \"total\": \"200.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"03/06/2026\", \"user_id\": 1, \"subtotal\": \"200.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-06-03T14:34:56.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"despacho_id\": 5, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}, {\"id\": 7, \"hora\": \"10:35:07\", \"fecha\": \"2026-06-03\", \"total\": \"7.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"03/06/2026\", \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"despacho_id\": 5, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}], \"user_id\": 1, \"created_at\": \"2026-06-03T14:35:24.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"observacion\": null, \"distribuidor_id\": 3}', NULL, 'DESPACHOS', '2026-06-03', '10:35:24', '2026-06-03 14:35:24', '2026-06-03 14:35:24'),
(31, 3, 'MODIFICACIÓN', 'EL USUARIO fcortez ANULÓ UN PEDIDO', '{\"id\": 7, \"hora\": \"10:35:07\", \"fecha\": \"2026-06-03\", \"total\": \"7.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"despacho_id\": 5, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}', '{\"id\": 7, \"hora\": \"10:35:07\", \"fecha\": \"2026-06-03\", \"total\": \"7.00\", \"estado\": \"ANULADO\", \"status\": 0, \"user_id\": 1, \"subtotal\": \"7.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 2, \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T15:08:00.000000Z\", \"despacho_id\": 5, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": 3, \"pedido_detalles\": [{\"id\": 9, \"precio\": \"7.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"7.00\", \"pedido_id\": 7, \"created_at\": \"2026-06-03T14:35:07.000000Z\", \"updated_at\": \"2026-06-03T14:35:24.000000Z\", \"producto_id\": 2, \"cantidad_total\": 1, \"cantidad_despacho\": 1, \"cantidad_entregado\": 1, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 6}], \"segmentacion_zona_id\": 1, \"user_distribucion_id\": null}', 'PEDIDOS', '2026-06-03', '11:08:00', '2026-06-03 15:08:00', '2026-06-03 15:08:00'),
(32, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 6, \"dir\": \"los pedregales\", \"fono\": \"77575383\", \"nit_ci\": null, \"nombre\": \"elias\", \"status\": 1, \"latitud\": \"-16.466163649686624\", \"user_id\": 1, \"longitud\": \"-68.15611723176876\", \"created_at\": \"2026-05-23T20:55:57.000000Z\", \"updated_at\": \"2026-05-23T20:58:36.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-23\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 1}', '{\"id\": 6, \"dir\": \"los pedregales\", \"fono\": \"77575383\", \"nit_ci\": null, \"nombre\": \"elias\", \"status\": 1, \"latitud\": \"-16.466163649686624\", \"user_id\": 1, \"longitud\": \"-68.15611723176876\", \"created_at\": \"2026-05-23T20:55:57.000000Z\", \"updated_at\": \"2026-06-03T15:41:17.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-23\", \"tipo_negocio_id\": \"1\", \"segmentacion_zona_id\": \"1\"}', 'CLIENTES', '2026-06-03', '11:41:17', '2026-06-03 15:41:17', '2026-06-03 15:41:17'),
(33, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 5, \"dir\": \"los pedregales\", \"fono\": \"6767676767\", \"nit_ci\": null, \"nombre\": \"francisca choque\", \"status\": 1, \"latitud\": \"-16.510646101336924\", \"user_id\": 6, \"longitud\": \"-68.15821393431824\", \"created_at\": \"2026-05-20T20:05:35.000000Z\", \"updated_at\": \"2026-05-20T20:05:35.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 2}', '{\"id\": 5, \"dir\": \"los pedregales\", \"fono\": \"6767676767\", \"nit_ci\": null, \"nombre\": \"francisca choque\", \"status\": 1, \"latitud\": \"-16.510646101336924\", \"user_id\": 6, \"longitud\": \"-68.15821393431824\", \"created_at\": \"2026-05-20T20:05:35.000000Z\", \"updated_at\": \"2026-06-03T15:41:23.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": \"2\", \"segmentacion_zona_id\": \"2\"}', 'CLIENTES', '2026-06-03', '11:41:23', '2026-06-03 15:41:23', '2026-06-03 15:41:23'),
(34, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 4, \"dir\": \"los olivos 3223\", \"fono\": \"67676767\", \"nit_ci\": null, \"nombre\": \"felix contreras\", \"status\": 1, \"latitud\": \"-16.51077436050994\", \"user_id\": 6, \"longitud\": \"-68.15931904933588\", \"created_at\": \"2026-05-20T20:05:12.000000Z\", \"updated_at\": \"2026-05-20T20:05:12.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 2}', '{\"id\": 4, \"dir\": \"los olivos 3223\", \"fono\": \"67676767\", \"nit_ci\": null, \"nombre\": \"felix contreras\", \"status\": 1, \"latitud\": \"-16.51077436050994\", \"user_id\": 6, \"longitud\": \"-68.15931904933588\", \"created_at\": \"2026-05-20T20:05:12.000000Z\", \"updated_at\": \"2026-06-03T15:41:27.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": \"2\", \"segmentacion_zona_id\": \"2\"}', 'CLIENTES', '2026-06-03', '11:41:27', '2026-06-03 15:41:27', '2026-06-03 15:41:27'),
(35, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 3, \"dir\": \"los pedregales\", \"fono\": \"676767676\", \"nit_ci\": null, \"nombre\": \"maria gonzales\", \"status\": 1, \"latitud\": \"-16.46639354031556\", \"user_id\": 5, \"longitud\": \"-68.15444854486383\", \"created_at\": \"2026-05-20T19:55:36.000000Z\", \"updated_at\": \"2026-05-20T19:55:36.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 1}', '{\"id\": 3, \"dir\": \"los pedregales\", \"fono\": \"676767676\", \"nit_ci\": null, \"nombre\": \"maria gonzales\", \"status\": 1, \"latitud\": \"-16.46639354031556\", \"user_id\": 5, \"longitud\": \"-68.15444854486383\", \"created_at\": \"2026-05-20T19:55:36.000000Z\", \"updated_at\": \"2026-06-03T15:41:35.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": \"1\", \"segmentacion_zona_id\": \"1\"}', 'CLIENTES', '2026-06-03', '11:41:35', '2026-06-03 15:41:35', '2026-06-03 15:41:35'),
(36, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 2, \"dir\": \"los pedregales #111\", \"fono\": \"67676767\", \"nit_ci\": null, \"nombre\": \"juan mamani\", \"status\": 1, \"latitud\": \"-16.464543790825925\", \"user_id\": 5, \"longitud\": \"-68.1563555938417\", \"created_at\": \"2026-05-20T19:55:22.000000Z\", \"updated_at\": \"2026-05-20T19:55:22.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 1}', '{\"id\": 2, \"dir\": \"los pedregales #111\", \"fono\": \"67676767\", \"nit_ci\": null, \"nombre\": \"juan mamani\", \"status\": 1, \"latitud\": \"-16.464543790825925\", \"user_id\": 5, \"longitud\": \"-68.1563555938417\", \"created_at\": \"2026-05-20T19:55:22.000000Z\", \"updated_at\": \"2026-06-03T15:43:53.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": \"1\", \"segmentacion_zona_id\": \"1\"}', 'CLIENTES', '2026-06-03', '11:43:53', '2026-06-03 15:43:53', '2026-06-03 15:43:53'),
(37, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"id\": 1, \"dir\": \"los pedregales #w323\", \"fono\": \"67676767\", \"nit_ci\": \"564564564\", \"nombre\": \"mario gonzales\", \"status\": 1, \"latitud\": \"-16.4666816266489\", \"user_id\": 5, \"longitud\": \"-68.15597157691236\", \"created_at\": \"2026-05-20T15:56:03.000000Z\", \"updated_at\": \"2026-05-20T15:56:03.000000Z\", \"razon_social\": \"gonzales\", \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": 0, \"segmentacion_zona_id\": 1}', '{\"id\": 1, \"dir\": \"los pedregales #w323\", \"fono\": \"67676767\", \"nit_ci\": \"564564564\", \"nombre\": \"mario gonzales\", \"status\": 1, \"latitud\": \"-16.4666816266489\", \"user_id\": 5, \"longitud\": \"-68.15597157691236\", \"created_at\": \"2026-05-20T15:56:03.000000Z\", \"updated_at\": \"2026-06-03T15:43:58.000000Z\", \"razon_social\": \"gonzales\", \"fecha_registro\": \"2026-05-20\", \"tipo_negocio_id\": \"2\", \"segmentacion_zona_id\": \"1\"}', 'CLIENTES', '2026-06-03', '11:43:58', '2026-06-03 15:43:58', '2026-06-03 15:43:58'),
(38, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA SEGMENTACIÓN DE ZONA', '{\"id\": 3, \"zona\": \"zona 3\", \"color\": \"#5dbeee\", \"ciudad_id\": \"1\", \"created_at\": \"2026-06-03T19:34:27.000000Z\", \"updated_at\": \"2026-06-03T19:34:27.000000Z\", \"provincia_id\": \"1\", \"segmentacion\": [{\"color\": \"#5dbeee\", \"coordenadas\": [{\"lat\": \"-16.4952048753785\", \"lng\": \"-68.14450223718754\"}, {\"lat\": \"-16.49230384215826\", \"lng\": \"-68.14737745996058\"}, {\"lat\": \"-16.49055336390828\", \"lng\": \"-68.14213183487387\"}, {\"lat\": \"-16.49152038856092\", \"lng\": \"-68.13957874040993\"}]}], \"departamento_id\": \"1\"}', NULL, 'SEGMENTACIÓN DE ZONAS', '2026-06-03', '15:34:27', '2026-06-03 19:34:27', '2026-06-03 19:34:27'),
(39, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ASIGNACIÓN DE ZONA', '{\"id\": 6, \"user_id\": 3, \"created_at\": \"2026-06-03T19:34:40.000000Z\", \"updated_at\": \"2026-06-03T19:34:40.000000Z\", \"segmentacion_zona_id\": 3}', NULL, 'ASIGNACIÓN DE ZONAS', '2026-06-03', '15:34:40', '2026-06-03 19:34:40', '2026-06-03 19:34:40'),
(40, 3, 'CREACIÓN', 'EL USUARIO fcortez REGISTRO UN CLIENTE', '{\"id\": 7, \"dir\": \"pedregales\", \"fono\": \"45546456456\", \"nit_ci\": null, \"nombre\": \"juan quispe\", \"latitud\": \"-16.49283557291197\", \"user_id\": 3, \"longitud\": \"-68.14310976545919\", \"created_at\": \"2026-06-03T19:36:17.000000Z\", \"updated_at\": \"2026-06-03T19:36:17.000000Z\", \"razon_social\": null, \"fecha_registro\": \"2026-06-03\", \"tipo_negocio_id\": \"1\", \"segmentacion_zona_id\": \"3\"}', NULL, 'CLIENTES', '2026-06-03', '15:36:17', '2026-06-03 19:36:17', '2026-06-03 19:36:17'),
(41, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"id\": 8, \"tipo\": \"VENDEDOR\", \"acceso\": \"1\", \"nombre\": \"alex choque\", \"bloqueo\": \"1\", \"usuario\": \"achoque\", \"created_at\": \"2026-06-03T19:36:43.000000Z\", \"updated_at\": \"2026-06-03T19:36:43.000000Z\", \"fecha_registro\": \"2026-06-03\"}', NULL, 'USUARIOS', '2026-06-03', '15:36:43', '2026-06-03 19:36:43', '2026-06-03 19:36:43'),
(42, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ASIGNACIÓN DE ZONA', '{\"id\": 7, \"user_id\": 8, \"created_at\": \"2026-06-03T19:37:27.000000Z\", \"updated_at\": \"2026-06-03T19:37:27.000000Z\", \"segmentacion_zona_id\": 3}', NULL, 'ASIGNACIÓN DE ZONAS', '2026-06-03', '15:37:27', '2026-06-03 19:37:27', '2026-06-03 19:37:27'),
(43, 8, 'CREACIÓN', 'EL USUARIO achoque REGISTRO UN PEDIDO', '{\"id\": 8, \"hora\": \"15:38:17\", \"fecha\": \"2026-06-03\", \"total\": \"200.00\", \"user_id\": 8, \"subtotal\": \"200.00\", \"descuento\": \"0\", \"cliente_id\": \"7\", \"created_at\": \"2026-06-03T19:38:17.000000Z\", \"updated_at\": \"2026-06-03T19:38:17.000000Z\", \"observacion\": null, \"distribuidor_id\": null, \"pedido_detalles\": [{\"id\": 10, \"precio\": \"200.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"200.00\", \"pedido_id\": 8, \"created_at\": \"2026-06-03T19:38:17.000000Z\", \"updated_at\": \"2026-06-03T19:38:17.000000Z\", \"producto_id\": 1, \"cantidad_total\": 12, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 8}], \"segmentacion_zona_id\": 3}', NULL, 'PEDIDOS', '2026-06-03', '15:38:17', '2026-06-03 19:38:17', '2026-06-03 19:38:17'),
(44, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN DESPACHO', '{\"id\": 6, \"hora\": \"16:03:29\", \"fecha\": \"2026-06-03\", \"pedidos\": [{\"id\": 8, \"hora\": \"15:38:17\", \"fecha\": \"2026-06-03\", \"total\": \"200.00\", \"estado\": \"PENDIENTE\", \"status\": 1, \"fecha_t\": \"03/06/2026\", \"user_id\": 8, \"subtotal\": \"200.00\", \"descuento\": \"0.00\", \"tipo_pago\": null, \"cliente_id\": 7, \"created_at\": \"2026-06-03T19:38:17.000000Z\", \"updated_at\": \"2026-06-03T20:03:29.000000Z\", \"despacho_id\": 6, \"observacion\": null, \"consolidado_id\": null, \"distribuidor_id\": null, \"segmentacion_zona_id\": 3, \"user_distribucion_id\": null}], \"user_id\": 1, \"created_at\": \"2026-06-03T20:03:29.000000Z\", \"updated_at\": \"2026-06-03T20:03:29.000000Z\", \"observacion\": null, \"distribuidor_id\": 3}', NULL, 'DESPACHOS', '2026-06-03', '16:03:29', '2026-06-03 20:03:29', '2026-06-03 20:03:29'),
(45, 8, 'CREACIÓN', 'EL USUARIO achoque REGISTRO UN PEDIDO', '{\"id\": 9, \"hora\": \"16:04:30\", \"fecha\": \"2026-06-03\", \"total\": \"6.00\", \"user_id\": 8, \"subtotal\": \"6.00\", \"descuento\": \"0\", \"cliente_id\": \"7\", \"created_at\": \"2026-06-03T20:04:30.000000Z\", \"updated_at\": \"2026-06-03T20:04:30.000000Z\", \"observacion\": null, \"distribuidor_id\": null, \"pedido_detalles\": [{\"id\": 11, \"precio\": \"6.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"6.00\", \"pedido_id\": 9, \"created_at\": \"2026-06-03T20:04:30.000000Z\", \"updated_at\": \"2026-06-03T20:04:30.000000Z\", \"producto_id\": 3, \"cantidad_total\": 1, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 1, \"presentacion_producto_id\": 3}], \"segmentacion_zona_id\": 3}', NULL, 'PEDIDOS', '2026-06-03', '16:04:30', '2026-06-03 20:04:30', '2026-06-03 20:04:30'),
(46, 8, 'CREACIÓN', 'EL USUARIO achoque REGISTRO UN PEDIDO', '{\"id\": 10, \"hora\": \"16:10:58\", \"fecha\": \"2026-06-03\", \"total\": \"7.00\", \"user_id\": 8, \"subtotal\": \"7.00\", \"descuento\": \"0\", \"cliente_id\": \"7\", \"created_at\": \"2026-06-03T20:10:58.000000Z\", \"updated_at\": \"2026-06-03T20:10:58.000000Z\", \"observacion\": null, \"distribuidor_id\": 3, \"pedido_detalles\": [{\"id\": 12, \"precio\": \"7.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"7.00\", \"pedido_id\": 10, \"created_at\": \"2026-06-03T20:10:58.000000Z\", \"updated_at\": \"2026-06-03T20:10:58.000000Z\", \"producto_id\": 2, \"cantidad_total\": 1, \"cantidad_despacho\": 0, \"cantidad_entregado\": 0, \"cantidad_devolucion\": 0, \"categoria_producto_id\": 2, \"presentacion_producto_id\": 6}], \"segmentacion_zona_id\": 3}', NULL, 'PEDIDOS', '2026-06-03', '16:10:58', '2026-06-03 20:10:58', '2026-06-03 20:10:58'),
(47, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"id\": 3, \"foto\": null, \"tipo\": \"DISTRIBUIDOR\", \"acceso\": 1, \"nombre\": \"FELIX CORTEZ\", \"status\": 1, \"bloqueo\": 1, \"usuario\": \"fcortez\", \"created_at\": \"2026-05-19T13:03:19.000000Z\", \"updated_at\": \"2026-05-19T13:03:19.000000Z\", \"fecha_registro\": \"2026-05-19\"}', '{\"id\": 3, \"foto\": null, \"tipo\": \"DISTRIBUIDOR\", \"acceso\": \"1\", \"nombre\": \"FELIX CORTEZ\", \"status\": 1, \"bloqueo\": \"0\", \"usuario\": \"fcortez\", \"created_at\": \"2026-05-19T13:03:19.000000Z\", \"updated_at\": \"2026-06-09T08:38:31.000000Z\", \"fecha_registro\": \"2026-05-19\"}', 'USUARIOS', '2026-06-09', '04:38:31', '2026-06-09 08:38:31', '2026-06-09 08:38:31'),
(48, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ASIGNACIÓN DE ZONA', '{\"id\": 8, \"user_id\": 5, \"created_at\": \"2026-06-09T08:47:22.000000Z\", \"updated_at\": \"2026-06-09T08:47:22.000000Z\", \"segmentacion_zona_id\": 3}', NULL, 'ASIGNACIÓN DE ZONAS', '2026-06-09', '04:47:22', '2026-06-09 08:47:22', '2026-06-09 08:47:22'),
(49, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"id\": 6, \"foto\": \"61779392180.jpg\", \"tipo\": \"VENDEDOR\", \"acceso\": 1, \"nombre\": \"MARIA MAMANI\", \"status\": 1, \"bloqueo\": 1, \"usuario\": \"mmamani\", \"created_at\": \"2026-05-19T13:04:11.000000Z\", \"updated_at\": \"2026-05-21T19:36:20.000000Z\", \"fecha_registro\": \"2026-05-19\"}', '{\"id\": 6, \"foto\": \"61779392180.jpg\", \"tipo\": \"VENDEDOR\", \"acceso\": \"1\", \"nombre\": \"MARIA MAMANI\", \"status\": 1, \"bloqueo\": \"0\", \"usuario\": \"mmamani\", \"created_at\": \"2026-05-19T13:04:11.000000Z\", \"updated_at\": \"2026-06-09T09:35:56.000000Z\", \"fecha_registro\": \"2026-05-19\"}', 'USUARIOS', '2026-06-09', '05:35:56', '2026-06-09 09:35:56', '2026-06-09 09:35:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_31_165641_create_configuracions_table', 1),
(2, '2024_11_02_153317_create_users_table', 1),
(3, '2024_11_02_153318_create_historial_accions_table', 1),
(4, '2026_05_18_082418_create_departamentos_table', 1),
(5, '2026_05_18_082424_create_provincias_table', 1),
(6, '2026_05_18_082428_create_ciudads_table', 1),
(7, '2026_05_18_082438_create_segmentacion_zonas_table', 1),
(8, '2026_05_18_082525_create_asignacion_zonas_table', 1),
(9, '2026_05_18_082538_create_categoria_productos_table', 1),
(10, '2026_05_18_082547_create_productos_table', 1),
(11, '2026_05_18_082559_create_presentacion_productos_table', 1),
(12, '2026_05_18_082648_create_compras_table', 1),
(13, '2026_05_18_082710_create_clientes_table', 1),
(14, '2026_05_18_082718_create_pedidos_table', 1),
(15, '2026_05_18_082903_create_despachos_table', 1),
(16, '2026_05_18_082918_create_ventas_table', 1),
(17, '2026_05_18_083043_create_consolidados_table', 1),
(18, '2026_05_18_083057_create_comisions_table', 1),
(19, '2026_05_18_084327_create_pedido_detalles_table', 1),
(21, '2026_05_20_085305_create_movimiento_inventarios_table', 2),
(22, '2026_05_21_112811_create_comision_detalles_table', 3),
(23, '2026_05_26_121604_create_presentacions_table', 4),
(24, '2026_06_03_113144_create_tipo_negocios_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_inventarios`
--

CREATE TABLE `movimiento_inventarios` (
  `id` bigint UNSIGNED NOT NULL,
  `tipo_registro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` bigint UNSIGNED DEFAULT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `presentacion_producto_id` bigint UNSIGNED DEFAULT NULL,
  `presentacion_id` bigint UNSIGNED DEFAULT NULL,
  `detalle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) DEFAULT NULL,
  `tipo_is` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_ingreso` double DEFAULT NULL,
  `cantidad_salida` double DEFAULT NULL,
  `cantidad_saldo` double NOT NULL,
  `cu` decimal(24,2) NOT NULL,
  `monto_ingreso` decimal(24,2) DEFAULT NULL,
  `monto_salida` decimal(24,2) DEFAULT NULL,
  `monto_saldo` decimal(24,2) NOT NULL,
  `fecha` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimiento_inventarios`
--

INSERT INTO `movimiento_inventarios` (`id`, `tipo_registro`, `registro_id`, `modulo`, `producto_id`, `presentacion_producto_id`, `presentacion_id`, `detalle`, `precio`, `tipo_is`, `cantidad_ingreso`, `cantidad_salida`, `cantidad_saldo`, `cu`, `monto_ingreso`, `monto_salida`, `monto_saldo`, `fecha`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Compra', 5, 'Compra', 1, NULL, NULL, 'COMPRA DE PRODUCTO', 40.00, 'INGRESO', 100, NULL, 100, 40.00, 4000.00, NULL, 4000.00, '2026-05-26', 1, '2026-05-26 15:33:06', '2026-05-26 15:33:06'),
(2, 'Compra', 6, 'Compra', 2, NULL, NULL, 'COMPRA DE PRODUCTO', 35.00, 'INGRESO', 100, NULL, 100, 35.00, 3500.00, NULL, 3500.00, '2026-05-26', 1, '2026-05-26 15:33:13', '2026-05-26 15:33:13'),
(3, 'Compra', 7, 'Compra', 3, NULL, NULL, 'COMPRA DE PRODUCTO', 37.00, 'INGRESO', 100, NULL, 100, 37.00, 3700.00, NULL, 3700.00, '2026-05-26', 1, '2026-05-26 15:33:18', '2026-05-26 15:33:18'),
(4, 'Compra', 8, 'Compra', 4, NULL, NULL, 'COMPRA DE PRODUCTO', 45.00, 'INGRESO', 100, NULL, 100, 45.00, 4500.00, NULL, 4500.00, '2026-05-26', 1, '2026-05-26 15:33:23', '2026-05-26 15:33:23'),
(5, 'Despacho', 1, 'PedidoDetalle', 1, NULL, NULL, 'Despacho de producto', 200.00, 'EGRESO', NULL, 12, 88, 200.00, NULL, 2400.00, 1600.00, '2026-05-26', 1, '2026-05-26 15:33:54', '2026-05-26 15:33:54'),
(6, 'Despacho', 4, 'PedidoDetalle', 3, NULL, NULL, 'Despacho de producto', 65.00, 'EGRESO', NULL, 36, 64, 65.00, NULL, 2340.00, 1360.00, '2026-05-26', 1, '2026-05-26 15:33:54', '2026-05-26 15:33:54'),
(7, 'Despacho', 2, 'PedidoDetalle', 2, NULL, NULL, 'Despacho de producto', 110.00, 'EGRESO', NULL, 24, 76, 110.00, NULL, 2640.00, 860.00, '2026-05-26', 1, '2026-05-26 15:33:54', '2026-05-26 15:33:54'),
(8, 'Despacho', 3, 'PedidoDetalle', 2, NULL, NULL, 'Despacho de producto', 110.00, 'EGRESO', NULL, 12, 64, 110.00, NULL, 1320.00, -460.00, '2026-05-26', 1, '2026-05-26 15:33:54', '2026-05-26 15:33:54'),
(9, 'Despacho', 5, 'PedidoDetalle', 4, NULL, NULL, 'Despacho de producto', 30.00, 'EGRESO', NULL, 5, 95, 30.00, NULL, 150.00, 4350.00, '2026-05-26', 1, '2026-05-26 16:40:58', '2026-05-26 16:40:58'),
(10, 'Despacho', 8, 'PedidoDetalle', 1, NULL, NULL, 'Despacho de producto', 200.00, 'EGRESO', NULL, 12, 76, 200.00, NULL, 2400.00, -800.00, '2026-06-03', 1, '2026-06-03 14:35:24', '2026-06-03 14:35:24'),
(11, 'Despacho', 6, 'PedidoDetalle', 2, NULL, NULL, 'Despacho de producto', 7.00, 'EGRESO', NULL, 1, 63, 7.00, NULL, 7.00, -467.00, '2026-06-03', 1, '2026-06-03 14:35:24', '2026-06-03 14:35:24'),
(12, 'Despacho', 9, 'PedidoDetalle', 2, NULL, NULL, 'Despacho de producto', 7.00, 'EGRESO', NULL, 1, 62, 7.00, NULL, 7.00, -474.00, '2026-06-03', 1, '2026-06-03 14:35:24', '2026-06-03 14:35:24'),
(13, 'PedidoDetalle', 9, 'PedidoDetalle', 2, NULL, NULL, 'Por anulación de pedido', 7.00, 'INGRESO', 1, NULL, 63, 7.00, 7.00, NULL, -467.00, '2026-06-03', 1, '2026-06-03 15:08:00', '2026-06-03 15:08:00'),
(14, 'Despacho', 10, 'PedidoDetalle', 1, NULL, NULL, 'Despacho de producto', 200.00, 'EGRESO', NULL, 12, 64, 200.00, NULL, 2400.00, -3200.00, '2026-06-03', 1, '2026-06-03 20:03:29', '2026-06-03 20:03:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `user_distribucion_id` bigint UNSIGNED DEFAULT NULL,
  `distribuidor_id` bigint UNSIGNED DEFAULT NULL,
  `segmentacion_zona_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `despacho_id` bigint UNSIGNED DEFAULT NULL,
  `consolidado_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `descuento` decimal(24,2) NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `tipo_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `fecha_salida` date DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `user_id`, `user_distribucion_id`, `distribuidor_id`, `segmentacion_zona_id`, `cliente_id`, `despacho_id`, `consolidado_id`, `subtotal`, `descuento`, `total`, `tipo_pago`, `fecha`, `hora`, `fecha_salida`, `hora_salida`, `observacion`, `estado`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 5, 3, 1, 1, 3, 1, 420.00, 0.00, 420.00, 'EFECTIVO', '2026-05-26', '11:33:35', NULL, NULL, NULL, 'ENTREGADO', 1, '2026-05-26 15:33:35', '2026-05-26 15:34:36'),
(2, 5, 5, 3, 1, 2, 3, 1, 305.00, 0.00, 305.00, 'EFECTIVO', '2026-05-26', '11:33:46', NULL, NULL, NULL, 'ENTREGADO', 1, '2026-05-26 15:33:46', '2026-05-26 15:34:36'),
(3, 5, 5, 3, 1, 3, 4, 2, 150.00, 0.00, 150.00, 'EFECTIVO', '2026-05-26', '12:40:50', NULL, NULL, NULL, 'ENTREGADO', 1, '2026-05-26 16:40:50', '2026-05-26 16:41:31'),
(4, 1, 3, 3, 1, 1, 5, NULL, 7.00, 0.00, 7.00, NULL, '2026-05-30', '09:40:26', '2026-06-09', '05:24:42', NULL, 'PENDIENTE', 1, '2026-05-30 13:40:26', '2026-06-09 09:24:42'),
(5, 1, NULL, 4, 2, 4, NULL, NULL, 7.00, 0.00, 7.00, NULL, '2026-05-30', '09:42:16', NULL, NULL, NULL, 'PENDIENTE', 1, '2026-05-30 13:42:16', '2026-05-30 13:42:16'),
(6, 1, NULL, 3, 1, 2, 5, NULL, 200.00, 0.00, 200.00, NULL, '2026-06-03', '10:34:56', NULL, NULL, NULL, 'PENDIENTE', 1, '2026-06-03 14:34:56', '2026-06-03 14:35:24'),
(7, 1, NULL, 3, 1, 2, 5, NULL, 7.00, 0.00, 7.00, NULL, '2026-06-03', '10:35:07', NULL, NULL, NULL, 'ANULADO', 0, '2026-06-03 14:35:07', '2026-06-03 15:08:00'),
(8, 8, NULL, 3, 3, 7, 6, NULL, 200.00, 0.00, 200.00, NULL, '2026-06-03', '15:38:17', NULL, NULL, NULL, 'PENDIENTE', 1, '2026-06-03 19:38:17', '2026-06-03 20:03:29'),
(9, 8, NULL, 3, 3, 7, NULL, NULL, 6.00, 0.00, 6.00, NULL, '2026-06-03', '16:04:30', NULL, NULL, NULL, 'PENDIENTE', 1, '2026-06-03 20:04:30', '2026-06-03 20:04:30'),
(10, 8, NULL, 3, 3, 7, NULL, NULL, 7.00, 0.00, 7.00, NULL, '2026-06-03', '16:10:58', NULL, NULL, NULL, 'PENDIENTE', 1, '2026-06-03 20:10:58', '2026-06-03 20:10:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `pedido_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `presentacion_producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `cantidad_total` double(8,2) NOT NULL,
  `cantidad_despacho` double(8,2) NOT NULL,
  `cantidad_entregado` double(8,2) NOT NULL,
  `cantidad_devolucion` double(8,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `pedido_id`, `producto_id`, `categoria_producto_id`, `presentacion_producto_id`, `cantidad`, `cantidad_total`, `cantidad_despacho`, `cantidad_entregado`, `cantidad_devolucion`, `precio`, `subtotal`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 8, 1, 12.00, 12.00, 12.00, 0.00, 200.00, 200.00, 1, '2026-05-26 15:33:35', '2026-05-26 15:33:54'),
(2, 1, 2, 2, 5, 2, 24.00, 24.00, 24.00, 0.00, 110.00, 220.00, 1, '2026-05-26 15:33:35', '2026-05-26 15:33:54'),
(3, 2, 2, 2, 5, 1, 12.00, 12.00, 12.00, 0.00, 110.00, 110.00, 1, '2026-05-26 15:33:46', '2026-05-26 15:33:54'),
(4, 2, 3, 1, 2, 3, 36.00, 36.00, 36.00, 0.00, 65.00, 195.00, 1, '2026-05-26 15:33:46', '2026-05-26 15:33:54'),
(5, 3, 4, 1, 12, 5, 5.00, 5.00, 5.00, 0.00, 30.00, 150.00, 1, '2026-05-26 16:40:50', '2026-05-26 16:40:58'),
(6, 4, 2, 2, 6, 1, 1.00, 1.00, 1.00, 0.00, 7.00, 7.00, 1, '2026-05-30 13:40:26', '2026-06-03 14:35:24'),
(7, 5, 2, 2, 6, 1, 1.00, 0.00, 0.00, 0.00, 7.00, 7.00, 1, '2026-05-30 13:42:16', '2026-05-30 13:42:16'),
(8, 6, 1, 1, 8, 1, 12.00, 12.00, 12.00, 0.00, 200.00, 200.00, 1, '2026-06-03 14:34:56', '2026-06-03 14:35:24'),
(9, 7, 2, 2, 6, 1, 1.00, 1.00, 1.00, 0.00, 7.00, 7.00, 1, '2026-06-03 14:35:07', '2026-06-03 14:35:24'),
(10, 8, 1, 1, 8, 1, 12.00, 12.00, 12.00, 0.00, 200.00, 200.00, 1, '2026-06-03 19:38:17', '2026-06-03 20:03:29'),
(11, 9, 3, 1, 3, 1, 1.00, 0.00, 0.00, 0.00, 6.00, 6.00, 1, '2026-06-03 20:04:30', '2026-06-03 20:04:30'),
(12, 10, 2, 2, 6, 1, 1.00, 0.00, 0.00, 0.00, 7.00, 7.00, 1, '2026-06-03 20:10:58', '2026-06-03 20:10:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion_productos`
--

CREATE TABLE `presentacion_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `equivale` int NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `comi_distribuidor` decimal(24,2) NOT NULL,
  `comi_vendedor` decimal(24,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `presentacion_productos`
--

INSERT INTO `presentacion_productos` (`id`, `producto_id`, `nombre`, `equivale`, `precio`, `comi_distribuidor`, `comi_vendedor`, `created_at`, `updated_at`) VALUES
(1, 3, 'caja', 24, 120.00, 10.00, 5.00, '2026-05-19 19:39:50', '2026-05-19 19:39:50'),
(2, 3, 'media caja', 12, 65.00, 5.00, 2.50, '2026-05-19 19:41:19', '2026-05-19 19:41:19'),
(3, 3, 'unidad', 1, 6.00, 1.50, 0.50, '2026-05-19 19:43:34', '2026-05-26 14:24:10'),
(4, 2, 'caja', 24, 200.00, 10.00, 5.00, '2026-05-19 19:46:57', '2026-05-19 19:46:57'),
(5, 2, 'media caja', 12, 110.00, 5.00, 2.50, '2026-05-19 19:48:16', '2026-05-19 19:48:16'),
(6, 2, 'unidad', 1, 7.00, 2.50, 0.50, '2026-05-19 19:48:37', '2026-05-19 19:48:37'),
(7, 1, 'caja', 24, 400.00, 20.00, 10.00, '2026-05-19 19:49:05', '2026-05-19 19:49:05'),
(8, 1, 'media caja', 12, 200.00, 10.00, 5.00, '2026-05-19 19:50:47', '2026-05-19 19:50:47'),
(12, 4, 'unidad', 1, 30.00, 3.00, 1.00, '2026-05-26 16:40:24', '2026-05-26 16:40:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_producto_id` bigint UNSIGNED NOT NULL,
  `stock_min` double NOT NULL,
  `stock_actual` double NOT NULL DEFAULT '0',
  `estado` int NOT NULL DEFAULT '1',
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `categoria_producto_id`, `stock_min`, `stock_actual`, `estado`, `imagen`, `created_at`, `updated_at`) VALUES
(1, 'producto 1', 'desc prod 1', 1, 5, 64, 1, '11779216124.png', '2026-05-19 18:42:04', '2026-06-03 20:03:29'),
(2, 'producto 2', NULL, 2, 4, 63, 1, '21779216384.png', '2026-05-19 18:46:24', '2026-06-03 15:08:00'),
(3, 'producto 3', NULL, 1, 4, 64, 1, NULL, '2026-05-19 18:46:36', '2026-05-26 15:33:54'),
(4, 'producto 4', 'desc', 1, 5, 95, 1, '41779484536.jpeg', '2026-05-22 21:14:31', '2026-05-26 16:40:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

CREATE TABLE `provincias` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Provincia 1', NULL, NULL),
(2, 'Provincia 2', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `segmentacion_zonas`
--

CREATE TABLE `segmentacion_zonas` (
  `id` bigint UNSIGNED NOT NULL,
  `departamento_id` bigint UNSIGNED NOT NULL,
  `provincia_id` bigint UNSIGNED NOT NULL,
  `ciudad_id` bigint UNSIGNED NOT NULL,
  `zona` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `segmentacion` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `segmentacion_zonas`
--

INSERT INTO `segmentacion_zonas` (`id`, `departamento_id`, `provincia_id`, `ciudad_id`, `zona`, `color`, `segmentacion`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'zona 1', '#ee4949', '[{\"color\": \"#ee4949\", \"coordenadas\": [{\"lat\": \"-16.465594227108642\", \"lng\": \"-68.15925558050728\"}, {\"lat\": \"-16.46471099182779\", \"lng\": \"-68.15804239371619\"}, {\"lat\": \"-16.46411423502453\", \"lng\": \"-68.15699100494386\"}, {\"lat\": \"-16.46359978803017\", \"lng\": \"-68.15621848539249\"}, {\"lat\": \"-16.463929034263817\", \"lng\": \"-68.15405163831707\"}, {\"lat\": \"-16.4648550362982\", \"lng\": \"-68.15287133628524\"}, {\"lat\": \"-16.4669539578672\", \"lng\": \"-68.15450170749297\"}, {\"lat\": \"-16.467981225075743\", \"lng\": \"-68.15530659842725\"}]}]', '2026-05-18 21:15:38', '2026-05-20 15:25:24'),
(2, 1, 1, 1, 'zona 2', '#2ab760', '[{\"color\": \"#2ab760\", \"coordenadas\": [{\"lat\": \"-16.511951190703318\", \"lng\": \"-68.15705537796022\"}, {\"lat\": \"-16.508906397080768\", \"lng\": \"-68.15606832504274\"}, {\"lat\": \"-16.509070981745964\", \"lng\": \"-68.16160440444948\"}, {\"lat\": \"-16.512198063759822\", \"lng\": \"-68.16164731979372\"}]}]', '2026-05-18 21:32:09', '2026-05-18 21:44:08'),
(3, 1, 1, 1, 'zona 3', '#5dbeee', '[{\"color\": \"#5dbeee\", \"coordenadas\": [{\"lat\": \"-16.4952048753785\", \"lng\": \"-68.14450223718754\"}, {\"lat\": \"-16.49230384215826\", \"lng\": \"-68.14737745996058\"}, {\"lat\": \"-16.49055336390828\", \"lng\": \"-68.14213183487387\"}, {\"lat\": \"-16.49152038856092\", \"lng\": \"-68.13957874040993\"}]}]', '2026-06-03 19:34:27', '2026-06-03 19:34:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_negocios`
--

CREATE TABLE `tipo_negocios` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_negocios`
--

INSERT INTO `tipo_negocios` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Tipo Negocio 1', NULL, NULL),
(2, 'Tipo Negocio 2', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acceso` int NOT NULL DEFAULT '1',
  `bloqueo` int NOT NULL DEFAULT '1',
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `nombre`, `password`, `foto`, `acceso`, `bloqueo`, `tipo`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '$2y$12$M2b1lEQrbaI17YIiht9xAuk2FCenB023wiuQVI63qmFsgogMzdXFe', NULL, 1, 0, 'ADMINISTRADOR', '2026-05-18', 1, '2026-05-18 13:31:09', '2026-05-18 13:31:09'),
(2, 'jperes', 'JUAN PERES', '$2y$12$LQY1VK7rzrSVgLmxmMYRyeh.JeSIG2Hn9SavUruOtwuFgwktPTa6W', NULL, 1, 1, 'ADMINISTRADOR', '2026-05-19', 1, '2026-05-19 13:02:59', '2026-05-30 13:50:43'),
(3, 'fcortez', 'FELIX CORTEZ', '$2y$12$1m21Dvtn6UxFGOUABnzwZep515WuVPuiKuDYjbgCoOxzBTqU378/O', NULL, 1, 0, 'DISTRIBUIDOR', '2026-05-19', 1, '2026-05-19 13:03:19', '2026-06-09 08:38:31'),
(4, 'csanz', 'CARLOS SANZ', '$2y$12$PPb6IyMpNpo1KTqPhYF./eTkJr8ildoMDAtEkXbto3Fxn26QTZ2ua', NULL, 1, 1, 'DISTRIBUIDOR', '2026-05-19', 1, '2026-05-19 13:03:33', '2026-05-19 13:03:33'),
(5, 'jmamani', 'JOSE MAMANI', '$2y$12$rMLj2OuFwzN/uTtmuJ2G0eFjE0NPp/GxoIJB5Z4QUBLaTdOzUSMlq', NULL, 1, 1, 'VENDEDOR', '2026-05-19', 1, '2026-05-19 13:03:57', '2026-05-19 13:03:57'),
(6, 'mmamani', 'MARIA MAMANI', '$2y$12$SCXrQpZOHpAhBcRhYYr2oOcFE0ti9fX8oqMNAZ.q0eD1dtd7j0g0a', '61779392180.jpg', 1, 0, 'VENDEDOR', '2026-05-19', 1, '2026-05-19 13:04:11', '2026-06-09 09:35:56'),
(7, 'jgonzales', 'jose gonzales', '$2y$12$e9x.kHTvJCbWBkd5OpUmOeDCSuT4zBYg0mWqF4NOl859GPtjHFFSq', NULL, 1, 1, 'DISTRIBUIDOR', '2026-05-22', 1, '2026-05-22 20:06:14', '2026-05-22 20:06:14'),
(8, 'achoque', 'alex choque', '$2y$12$EOlegQUVVKGeo0cmU66FSOXOACun00K36xbTnTik7kvkle5J9F5Fy', NULL, 1, 1, 'VENDEDOR', '2026-06-03', 1, '2026-06-03 19:36:43', '2026-06-03 19:36:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacion_zonas`
--
ALTER TABLE `asignacion_zonas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asignacion_zonas_segmentacion_zona_id_foreign` (`segmentacion_zona_id`),
  ADD KEY `asignacion_zonas_user_id_foreign` (`user_id`) USING BTREE;

--
-- Indices de la tabla `categoria_productos`
--
ALTER TABLE `categoria_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ciudads`
--
ALTER TABLE `ciudads`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clientes_user_id_fk` (`user_id`),
  ADD KEY `clientes_segmentacion_zona_id_fk` (`segmentacion_zona_id`);

--
-- Indices de la tabla `comisions`
--
ALTER TABLE `comisions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comision_detalles`
--
ALTER TABLE `comision_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comision_detalles_comision_id_foreign` (`comision_id`),
  ADD KEY `comision_detalles_despacho_id_foreign` (`despacho_id`),
  ADD KEY `comision_detalles_categoria_producto_id_foreign` (`categoria_producto_id`),
  ADD KEY `comision_detalles_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compras_categoria_producto_id_foreign` (`categoria_producto_id`),
  ADD KEY `compras_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `consolidados`
--
ALTER TABLE `consolidados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consolidados_distribuidor_id_foreign` (`distribuidor_id`),
  ADD KEY `consolidados_despacho_id_foreign` (`despacho_id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `despachos`
--
ALTER TABLE `despachos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `despachos_distribuidor_id_foreign` (`distribuidor_id`);

--
-- Indices de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_accions_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimiento_inventarios`
--
ALTER TABLE `movimiento_inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimiento_inventarios_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedidos_user_id_foreign` (`user_id`),
  ADD KEY `pedidos_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_detalles_pedido_id_foreign` (`pedido_id`),
  ADD KEY `pedido_detalles_producto_id_foreign` (`producto_id`),
  ADD KEY `pedido_detalles_presentacion_producto_id_foreign` (`presentacion_producto_id`);

--
-- Indices de la tabla `presentacion_productos`
--
ALTER TABLE `presentacion_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presentacion_productos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productos_categoria_producto_id_foreign` (`categoria_producto_id`);

--
-- Indices de la tabla `provincias`
--
ALTER TABLE `provincias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `segmentacion_zonas`
--
ALTER TABLE `segmentacion_zonas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `segmentacion_zonas_departamento_id_foreign` (`departamento_id`),
  ADD KEY `segmentacion_zonas_provincia_id_foreign` (`provincia_id`),
  ADD KEY `segmentacion_zonas_ciudad_id_foreign` (`ciudad_id`);

--
-- Indices de la tabla `tipo_negocios`
--
ALTER TABLE `tipo_negocios`
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
-- AUTO_INCREMENT de la tabla `asignacion_zonas`
--
ALTER TABLE `asignacion_zonas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `categoria_productos`
--
ALTER TABLE `categoria_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ciudads`
--
ALTER TABLE `ciudads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `comisions`
--
ALTER TABLE `comisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comision_detalles`
--
ALTER TABLE `comision_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `consolidados`
--
ALTER TABLE `consolidados`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `despachos`
--
ALTER TABLE `despachos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `movimiento_inventarios`
--
ALTER TABLE `movimiento_inventarios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `presentacion_productos`
--
ALTER TABLE `presentacion_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `provincias`
--
ALTER TABLE `provincias`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `segmentacion_zonas`
--
ALTER TABLE `segmentacion_zonas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_negocios`
--
ALTER TABLE `tipo_negocios`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_zonas`
--
ALTER TABLE `asignacion_zonas`
  ADD CONSTRAINT `asignacion_zonas_distribuidor_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `asignacion_zonas_segmentacion_zona_id_foreign` FOREIGN KEY (`segmentacion_zona_id`) REFERENCES `segmentacion_zonas` (`id`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_segmentacion_zona_id_fk` FOREIGN KEY (`segmentacion_zona_id`) REFERENCES `segmentacion_zonas` (`id`),
  ADD CONSTRAINT `clientes_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `comision_detalles`
--
ALTER TABLE `comision_detalles`
  ADD CONSTRAINT `comision_detalles_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`),
  ADD CONSTRAINT `comision_detalles_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisions` (`id`),
  ADD CONSTRAINT `comision_detalles_despacho_id_foreign` FOREIGN KEY (`despacho_id`) REFERENCES `despachos` (`id`),
  ADD CONSTRAINT `comision_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`),
  ADD CONSTRAINT `compras_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `consolidados`
--
ALTER TABLE `consolidados`
  ADD CONSTRAINT `consolidados_despacho_id_foreign` FOREIGN KEY (`despacho_id`) REFERENCES `despachos` (`id`),
  ADD CONSTRAINT `consolidados_distribuidor_id_foreign` FOREIGN KEY (`distribuidor_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `despachos`
--
ALTER TABLE `despachos`
  ADD CONSTRAINT `despachos_distribuidor_id_foreign` FOREIGN KEY (`distribuidor_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD CONSTRAINT `historial_accions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `movimiento_inventarios`
--
ALTER TABLE `movimiento_inventarios`
  ADD CONSTRAINT `movimiento_inventarios_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `pedidos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `pedido_detalles_presentacion_producto_id_foreign` FOREIGN KEY (`presentacion_producto_id`) REFERENCES `presentacion_productos` (`id`),
  ADD CONSTRAINT `pedido_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `presentacion_productos`
--
ALTER TABLE `presentacion_productos`
  ADD CONSTRAINT `presentacion_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_categoria_producto_id_foreign` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categoria_productos` (`id`);

--
-- Filtros para la tabla `segmentacion_zonas`
--
ALTER TABLE `segmentacion_zonas`
  ADD CONSTRAINT `segmentacion_zonas_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudads` (`id`),
  ADD CONSTRAINT `segmentacion_zonas_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`),
  ADD CONSTRAINT `segmentacion_zonas_provincia_id_foreign` FOREIGN KEY (`provincia_id`) REFERENCES `provincias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
