-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-08-2023 a las 15:44:16
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `kallpaq`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generar_frecuencias` (IN `p_indicador_id` INT, IN `p_periodo_actual` YEAR)   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_indicador_id INT;
    DECLARE v_frecuencia VARCHAR(255);
    DECLARE v_fecha_actual DATE;
    DECLARE v_contador INT;
    DECLARE v_intervalo INT;
    DEClARE v_repeat INT; 

    DECLARE cur CURSOR FOR
        SELECT id, frecuencia FROM indicadores WHERE id = p_indicador_id AND estado = 2;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_indicador_id, v_frecuencia;

        IF done THEN
            LEAVE read_loop;
        END IF;

        SET v_fecha_actual =LAST_DAY( DATE_FORMAT(CONCAT(p_periodo_actual-1, '-12-30'), '%Y-%m-%d'));
        SET v_contador = 1;

        CASE v_frecuencia
            WHEN 'mensual' THEN SET v_intervalo = 1; SET v_repeat = 12;
            WHEN 'trimestral' THEN SET v_intervalo = 3; SET v_repeat = 4;
            WHEN 'semestral' THEN SET v_intervalo = 6; SET v_repeat = 2;
            ELSE SET v_intervalo = 0; SET v_repeat = 0;
        END CASE;

        WHILE v_contador <= v_repeat DO
  SET v_fecha_actual = LAST_DAY(DATE_ADD(v_fecha_actual, INTERVAL v_intervalo MONTH));
             REPLACE INTO indicadores_seguimiento (indicador_id, fecha)
    VALUES (v_indicador_id, v_fecha_actual);

          
            SET v_contador = v_contador + 1;
        END WHILE;
UPDATE indicadores SET estado = 3 WHERE id = v_indicador_id;
    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE `auditorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `auditoria_cod` varchar(255) NOT NULL,
  `objetivo` varchar(255) NOT NULL,
  `criterios_auditoria` varchar(255) NOT NULL,
  `alcance_auditoria` varchar(255) NOT NULL,
  `tipo_auditoria` enum('INT','EXT') NOT NULL,
  `sistema_iso` varchar(255) NOT NULL,
  `costo_programado` double NOT NULL,
  `costo_ejecutado` double NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `programa_auditoria_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_equipo`
--

CREATE TABLE `auditoria_equipo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `auditoria_id` bigint(20) UNSIGNED NOT NULL,
  `personal_id` bigint(20) UNSIGNED NOT NULL,
  `rol` varchar(255) NOT NULL,
  `equipo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_procesos`
--

CREATE TABLE `auditoria_procesos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `auditoria_id` bigint(20) UNSIGNED NOT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
(1, 'periodo_actual', '2023', '2023-08-19 02:27:59', NULL),
(2, 'fecha_inicio_bloqueo', '01/01/2023', '2023-08-19 02:28:31', NULL),
(3, 'fecha_fin_bloqueo', '30/06/2023', '2023-08-19 02:27:59', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cod_documento` varchar(255) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `proceso_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_documento_id` bigint(20) UNSIGNED DEFAULT NULL,
  `documento_referencia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vigencia_at` date DEFAULT NULL,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factores`
--

CREATE TABLE `factores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `valor` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `factores`
--

INSERT INTO `factores` (`id`, `nombre`, `valor`, `estado`, `created_at`, `updated_at`, `inactivate_at`, `deleted_at`) VALUES
(1, 'Estratégico', 3, 1, NULL, NULL, NULL, NULL),
(2, 'Operacional', 2, 1, NULL, NULL, NULL, NULL),
(3, 'Corrupción', 4, 1, NULL, NULL, NULL, NULL),
(4, 'Cumplimiento', 3, 1, NULL, NULL, NULL, NULL),
(5, 'Reputacional', 3, 1, NULL, NULL, NULL, NULL),
(6, 'Ambiental', 2, 1, NULL, NULL, NULL, NULL),
(7, 'Seguridad', 4, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hallazgos`
--

CREATE TABLE `hallazgos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `informe_id` bigint(20) UNSIGNED NOT NULL,
  `proceso_cod` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `evidencia` varchar(255) NOT NULL,
  `criterio` varchar(255) NOT NULL,
  `clasificacion` enum('NC mayor','NC menor','Observacion','Odm') NOT NULL,
  `fecha_identificacion` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores`
--

CREATE TABLE `indicadores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indicador_id` text NOT NULL,
  `tipo_indicador` enum('Producto','Servicio','Resultado','Calidad') NOT NULL,
  `proceso_cod` bigint(20) UNSIGNED NOT NULL,
  `producto` text NOT NULL,
  `cliente` text NOT NULL,
  `objSIG` varchar(255) NOT NULL,
  `objSIG_estado` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `formula` varchar(255) NOT NULL,
  `frecuencia` enum('mensual','trimestral','semestral','anual') NOT NULL,
  `meta` double(8,2) NOT NULL,
  `tipo_meta` enum('Acumulada','Parcial') NOT NULL,
  `unidad_medida` enum('ratio','porcentaje','numero','indice','otros') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicadores`
--

INSERT INTO `indicadores` (`id`, `indicador_id`, `tipo_indicador`, `proceso_cod`, `producto`, `cliente`, `objSIG`, `objSIG_estado`, `estado`, `nombre`, `descripcion`, `formula`, `frecuencia`, `meta`, `tipo_meta`, `unidad_medida`, `created_at`, `updated_at`) VALUES
(1, '1', 'Producto', 35, 'Texto Unico de Procedimientos Administrativos - TUPA', 'Órganos y unidades orgánicas que tienen a cargo algun procedimiento administrativo consignado en el TUPA, Ciudadanía, Entidades', '2', 1, 3, 'Porcentaje de Procedimientos del TUPA actualizados', 'Medir la actualización deel TUPA de la CGR', 'Número de Procedimientos del TUPA actualizados (V1)/ Total de Procedimientos consignados en el TUPA (V2)', 'mensual', 0.78, 'Acumulada', 'porcentaje', '2023-05-26 23:01:48', '2023-08-23 01:28:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores_historico`
--

CREATE TABLE `indicadores_historico` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indicador_id` bigint(20) UNSIGNED NOT NULL,
  `año` year(4) NOT NULL,
  `meta` double(8,2) NOT NULL,
  `valor` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicadores_historico`
--

INSERT INTO `indicadores_historico` (`id`, `indicador_id`, `año`, `meta`, `valor`, `created_at`, `updated_at`) VALUES
(1, 1, 2021, 0.75, 1.00, '2023-05-26 23:01:48', NULL),
(2, 1, 2022, 0.75, 0.75, '2023-05-26 23:01:48', NULL),
(3, 1, 2023, 0.75, 0.67, '2023-05-26 23:01:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores_seguimiento`
--

CREATE TABLE `indicadores_seguimiento` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indicador_id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `meta` double(8,2) DEFAULT 0.00,
  `valor` double(8,2) DEFAULT 0.00,
  `var1` double(8,2) DEFAULT 0.00,
  `var2` double(8,2) DEFAULT 0.00,
  `var3` double(8,2) DEFAULT 0.00,
  `var4` double(8,2) DEFAULT 0.00,
  `var5` double(8,2) DEFAULT 0.00,
  `var6` double(8,2) DEFAULT 0.00,
  `accion` text DEFAULT '',
  `responsable` text DEFAULT '',
  `plazo` date DEFAULT NULL,
  `plazo_repr` date DEFAULT NULL,
  `accion_estado` enum('cerrada','pendiente','implementada','cancelada') DEFAULT NULL,
  `evidencias` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicadores_seguimiento`
--

INSERT INTO `indicadores_seguimiento` (`id`, `indicador_id`, `fecha`, `meta`, `valor`, `var1`, `var2`, `var3`, `var4`, `var5`, `var6`, `accion`, `responsable`, `plazo`, `plazo_repr`, `accion_estado`, `evidencias`, `created_at`, `updated_at`) VALUES
(116, 1, '2023-01-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(117, 1, '2023-02-28', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(118, 1, '2023-03-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(119, 1, '2023-04-30', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(120, 1, '2023-05-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(121, 1, '2023-06-30', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(122, 1, '2023-07-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(123, 1, '2023-08-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(124, 1, '2023-09-30', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(125, 1, '2023-10-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(126, 1, '2023-11-30', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL),
(127, 1, '2023-12-31', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_auditoria`
--

CREATE TABLE `informe_auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `auditoria_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_emision` date NOT NULL,
  `informe_pdf` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(45, '2014_10_12_000000_create_users_table', 1),
(46, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(47, '2019_08_19_000000_create_failed_jobs_table', 1),
(48, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(49, '2023_04_18_161252_create_tipos_documentos_tables', 1),
(50, '2023_04_18_163750_create_procesos_table', 1),
(51, '2023_04_18_221818_create_documento_table', 1),
(52, '2023_05_09_151326_create_riesgo__factor', 1),
(53, '2023_05_09_154008_create_riesgo', 1),
(54, '2023_05_09_190650_create_riesgo__acciones', 1),
(55, '2023_05_09_195539_create_riesgo__controles', 1),
(56, '2023_05_09_215714_create_programa_auditoria', 1),
(57, '2023_05_09_220452_create_auditoria', 1),
(58, '2023_05_09_221339_create_auditoria__procesos', 1),
(59, '2023_05_09_223614_create_personal', 1),
(60, '2023_05_09_223702_create_auditoria_equipo', 1),
(61, '2023_05_09_232311_create_informe_auditoria', 2),
(63, '2023_05_09_232514_create_hallazgos', 3),
(64, '2023_06_01_231039_create_propietarios_table', 4),
(66, '2023_08_09_144635_create_indicadores', 5),
(68, '2023_08_09_153856_create_indicadores__seguimiento', 6),
(69, '2023_08_09_162426_create_indicadores__historico', 6),
(71, '2023_08_18_232019_create_planificacion_sig', 7),
(73, '2023_08_22_205219_create_configuracion_table', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `personal_cod` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `profesion` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planificacion_sig`
--

CREATE TABLE `planificacion_sig` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `objetivo` varchar(255) NOT NULL,
  `sistema` enum('SGC','SGAS') NOT NULL,
  `nombre_objetivo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `planificacion_sig`
--

INSERT INTO `planificacion_sig` (`id`, `objetivo`, `sistema`, `nombre_objetivo`, `created_at`, `updated_at`) VALUES
(1, 'OSGC1', 'SGC', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2023-08-19 02:27:59', NULL),
(2, 'OSGC2', 'SGC', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población', '2023-08-19 02:27:59', NULL),
(3, 'OSGC3', 'SGC', 'Promover la participación ciudadana en el control social.', '2023-08-19 02:28:31', NULL),
(4, 'OSGC4', 'SGC', 'Fortalecer la gestión del Sistema Nacional de Control.', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesos`
--

CREATE TABLE `procesos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cod_proceso` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo_proceso` enum('Misional','Estratégico','Apoyo') NOT NULL,
  `cod_proceso_padre` varchar(20) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `procesos`
--

INSERT INTO `procesos` (`id`, `cod_proceso`, `nombre`, `tipo_proceso`, `cod_proceso_padre`, `estado`, `inactivate_at`, `created_at`, `updated_at`) VALUES
(1, 'PE01', 'Planeamiento Estratégico', 'Misional', NULL, 1, NULL, '2023-05-26 23:01:48', '2023-06-02 04:00:48'),
(2, 'PE02', 'Desarrollo Institucional', 'Estratégico', NULL, 1, NULL, NULL, NULL),
(3, 'PE03', 'Comunicación y Relaciones Interinstitucionales', 'Estratégico', NULL, 1, NULL, NULL, NULL),
(4, 'PM01', 'Prevención y Detección de la Corrupción', 'Misional', NULL, 1, NULL, NULL, NULL),
(5, 'PM02', 'Atención a las Entidades y Partes Interesadas', 'Misional', NULL, 1, NULL, NULL, NULL),
(6, 'PM03', 'Realización de los Servicios de Control Simultáneo, de Control Posterior y Relacionados', 'Misional', NULL, 1, NULL, NULL, NULL),
(7, 'PM04', 'Gestión de Sanciones y Procesos Judiciales Resultantes de los Servicios de Control', 'Misional', NULL, 1, NULL, NULL, NULL),
(8, 'PM05', 'Gestión de los Resultados del Control para la Mejora de las Entidades Públicas', 'Misional', NULL, 1, NULL, NULL, NULL),
(9, 'PA01', 'Gestión del Capital Humano', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(10, 'PA02', 'Gestión de Activos Documentarios', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(11, 'PA03', 'Gestión de Abastecimiento', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(12, 'PA04', 'Gestión Financiera', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(13, 'PA05', 'Gestión de Tecnologías de la Información y Comunicaciones', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(14, 'PA06', 'Gestión Jurídico Legal', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(15, 'PA07', 'Gestión de la Seguridad', 'Apoyo', NULL, 1, NULL, NULL, NULL),
(30, 'PE01.01', 'Planeamiento Estratégico', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(31, 'PE01.02', 'Gestión de Entidades Sujetad a Control', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(32, 'PE01.03', 'Planeamiento Operativo', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(33, 'PE01.04', 'Control Institucional', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(34, 'PE02.01', 'Diseño Organizacional', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(35, 'PE02.02', 'Gestión de la Modernización', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(36, 'PE02.03', 'Gestión Normativa', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(37, 'PE02.04', 'Gestión de la Inversión', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(38, 'PE02.05', 'Gestión del Conocimiento', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(39, 'PE02.06', 'Gestión de la Continuidad del Negocio', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(40, 'PE02.07', 'Gestión de la Integridad Institucional', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(41, 'PE03.01', 'Gestión de la Comunicación Institucional', 'Estratégico', 'PE03', 1, NULL, '2023-08-09 17:21:22', NULL),
(42, 'PE03.02', 'Gestión de las Relaciones Interinstitucionales', 'Estratégico', 'PE03', 1, NULL, '2023-08-09 17:21:22', NULL),
(56, 'PM01.01', 'Gestión de mecanismos de prevención y detección de la corrupción', 'Misional', 'PM01', 1, NULL, '2023-08-09 17:28:23', NULL),
(57, 'PM01.02', 'Participación ciudadana', 'Misional', 'PM01', 1, NULL, '2023-08-09 17:28:23', NULL),
(58, 'PM02.01', 'Atención de la demanda imprevisible de control', 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(59, 'PM02.02', 'Atención de pedidos de información y solicitudes de opinión', 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(60, 'PM02.03', 'Atención de quejas y reclamos', 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(61, 'PM03.01', 'Programación de los servicios de control y de fiscalización', 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(62, 'PM03.02', 'Realización de los servicios de control simultáneo', 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(63, 'PM03.03', 'Realización de los servicios de control posterior', 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(64, 'PM03.04', 'Realización de los servicios relacionados', 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(65, 'PM03.05', 'Supervisión técnica y revisión de oficio de los servicios de control', 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(66, 'PM04.01', 'Gestión de sanciones administrativas', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(67, 'PM04.02', 'Gestión del procedimiento sancionador por infracción al ejercicio del control gubernamental', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(68, 'PM04.03', 'Gestión de los procesos judiciales resultantes de los servicios de control', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(69, 'PM05.01', 'Seguimiento y evaluación a la implementación de las recomendaciones, acciones y pronunciamientos, resultados de los servicios de control', 'Misional', 'PM05', 1, NULL, '2023-08-09 17:28:23', NULL),
(70, 'PM05.02', 'Desarrollo de buenas prácticas y propuestas de mejora para la gestión de las entidades', 'Misional', 'PM05', 1, NULL, '2023-08-09 17:28:23', NULL),
(71, 'PA01.01', 'Planificación del capital humano', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(72, 'PA01.02', 'Incorporación del capital humano', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(73, 'PA01.03', 'Desarrollo del capital humano', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(74, 'PA01.04', 'Administración del capital humano', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(75, 'PA01.05', 'Gestión del bienestar del capital humano', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(76, 'PA01.06', 'Gestión del jefe y personal del OCI', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(77, 'PA02.01', 'Planificación del activo documentario', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(78, 'PA02.02', 'Recepción de documentos', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(79, 'PA02.03', 'Clasificación, reclasificación y desclasificación de documentos secretos y reservados', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(80, 'PA02.04', 'Distribución de documentos y valijas', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(81, 'PA02.05', 'Archivo, custodia y conservación de documentos', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(82, 'PA02.06', 'Autenticación de firmas y certificación de documentos', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(83, 'PA03.01', 'Elaboración del plan anual de contrataciones', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(84, 'PA03.02', 'Contratación de bienes y servicios', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(85, 'PA03.03', 'Gestión de bienes patrimoniales', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(86, 'PA03.04', 'Gestión de almacén', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(87, 'PA03.05', 'Administración de servicios generales', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(88, 'PA03.06', 'Gestión de sociedades de auditoria', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(89, 'PA04.01', 'Programación multianual, formulación y aprobación del presupuesto', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(90, 'PA04.02', 'Ejecución presupuestal', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(91, 'PA04.03', 'Evaluación presupuestal', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(92, 'PA04.04', 'Gestión contable', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(93, 'PA05.01', 'Planificación de tecnologías de la información y comunicaciones', 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(94, 'PA05.02', 'Implementación de tecnologías de la información y comunicaciones', 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(95, 'PA05.03', 'Operación de tecnologías de la información y comunicaciones', 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(96, 'PA06.01', 'Gestión y difusión de productos de interés legal', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(97, 'PA06.02', 'Gestión de los procesos judiciales de la CGR', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(98, 'PA06.03', 'Gestión de los procesos arbitrales de la CGR', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(99, 'PA06.04', 'Defensa legal de los colaboradores y ex colaboradores', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(100, 'PA06.05', 'Absolución de consultas internas de carácter jurídico', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(101, 'PA07.01', 'Gestión de prevención de riesgos de desastres', 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL),
(102, 'PA07.02', 'Operación de la gestión de la seguridad', 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL),
(103, 'PA07.03', 'Fomento de una cultura de seguridad', 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_auditoria`
--

CREATE TABLE `programa_auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `observacion` varchar(255) NOT NULL,
  `avance` varchar(255) NOT NULL,
  `version` int(11) NOT NULL,
  `presupuesto_total` double NOT NULL,
  `fecha_aprobacion` date NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

CREATE TABLE `propietarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `UOU` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `riesgos`
--

CREATE TABLE `riesgos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `riesgo_cod` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `proceso_cod` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_riesgo` enum('Riesgo','Oportunidad') NOT NULL,
  `factor_cod` bigint(20) UNSIGNED NOT NULL,
  `probabilidad` int(11) NOT NULL,
  `impacto` int(11) NOT NULL,
  `valoracion_riesgo` int(11) NOT NULL,
  `tratamiento_riesgo` enum('Reducir','Aceptar','Trasladar') NOT NULL,
  `estado` enum('Abierto','Cerrado') NOT NULL,
  `fecha_valoracion_rr` date DEFAULT NULL,
  `probabilidad_rr` int(11) DEFAULT NULL,
  `impacto_rr` int(11) DEFAULT NULL,
  `evaluacion_rr` int(11) DEFAULT NULL,
  `estado_riesgo_rr` enum('Con Eficacia','Sin eficacia') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `riesgo_acciones`
--

CREATE TABLE `riesgo_acciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `riesgo_cod` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_prog_inicio` date DEFAULT NULL,
  `fecha_prog_fin` date DEFAULT NULL,
  `fecha_implementacion` date DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `estado` enum('En Implementación','Pendiente','Implementado','Cancelado') NOT NULL,
  `comentario` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `riesgo_controles`
--

CREATE TABLE `riesgo_controles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `control_cod` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` enum('Manual','Automatico','Mixto') NOT NULL,
  `frecuencia` varchar(255) NOT NULL,
  `responsable` varchar(255) NOT NULL,
  `fecha_implementacion` date DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `evaluación` enum('implementado','parcialmente','no implementado','inadecuado') NOT NULL,
  `fecha_revaluacion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `riesgo_cod` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documentos`
--

CREATE TABLE `tipos_documentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sigla` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_documentos`
--

INSERT INTO `tipos_documentos` (`id`, `sigla`, `nombre`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
(1, 'MG', 'Manual de Sistema de Gestión', 1, NULL, NULL, NULL),
(2, 'MN', 'Manual de aplicativos informáticos', 1, NULL, NULL, NULL),
(3, 'PC', 'Plan de la Calidad', 1, NULL, NULL, NULL),
(4, 'PR', 'Procedimiento', 1, NULL, NULL, NULL),
(5, 'GU', 'Guía', 1, NULL, NULL, NULL),
(6, 'IT', 'Instructivo', 1, NULL, NULL, NULL),
(7, 'DI', 'Directriz', 1, NULL, NULL, NULL),
(8, 'PT', 'Protocolo', 1, NULL, NULL, NULL),
(9, 'F', 'Formato', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditorias_programa_auditoria_id_foreign` (`programa_auditoria_id`);

--
-- Indices de la tabla `auditoria_equipo`
--
ALTER TABLE `auditoria_equipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditoria_equipo_auditoria_id_foreign` (`auditoria_id`),
  ADD KEY `auditoria_equipo_personal_id_foreign` (`personal_id`);

--
-- Indices de la tabla `auditoria_procesos`
--
ALTER TABLE `auditoria_procesos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auditoria_procesos_auditoria_id_foreign` (`auditoria_id`),
  ADD KEY `auditoria_procesos_proceso_id_foreign` (`proceso_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `configuracion_clave_unique` (`clave`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documentos_cod_documento_unique` (`cod_documento`),
  ADD KEY `documentos_proceso_id_foreign` (`proceso_id`),
  ADD KEY `documentos_tipo_documento_id_foreign` (`tipo_documento_id`),
  ADD KEY `documentos_documento_referencia_id_foreign` (`documento_referencia_id`);

--
-- Indices de la tabla `factores`
--
ALTER TABLE `factores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `hallazgos`
--
ALTER TABLE `hallazgos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hallazgos_informe_id_foreign` (`informe_id`),
  ADD KEY `hallazgos_proceso_cod_foreign` (`proceso_cod`);

--
-- Indices de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicadores_proceso_cod_foreign` (`proceso_cod`);

--
-- Indices de la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicadores_historico_indicador_id_foreign` (`indicador_id`);

--
-- Indices de la tabla `indicadores_seguimiento`
--
ALTER TABLE `indicadores_seguimiento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indicador_id` (`indicador_id`,`fecha`),
  ADD KEY `indicadores_seguimiento_indicador_id_foreign` (`indicador_id`);

--
-- Indices de la tabla `informe_auditoria`
--
ALTER TABLE `informe_auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `informe_auditoria_auditoria_id_foreign` (`auditoria_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_personal_cod_unique` (`personal_cod`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `planificacion_sig`
--
ALTER TABLE `planificacion_sig`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `procesos`
--
ALTER TABLE `procesos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cod_proceso` (`cod_proceso`);

--
-- Indices de la tabla `programa_auditoria`
--
ALTER TABLE `programa_auditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `riesgos`
--
ALTER TABLE `riesgos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `riesgos_riesgo_cod_unique` (`riesgo_cod`),
  ADD KEY `riesgos_proceso_cod_foreign` (`proceso_cod`),
  ADD KEY `riesgos_factor_cod_foreign` (`factor_cod`);

--
-- Indices de la tabla `riesgo_acciones`
--
ALTER TABLE `riesgo_acciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `riesgo_acciones_riesgo_cod_foreign` (`riesgo_cod`);

--
-- Indices de la tabla `riesgo_controles`
--
ALTER TABLE `riesgo_controles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `riesgo_controles_riesgo_cod_foreign` (`riesgo_cod`);

--
-- Indices de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipos_documentos_sigla_unique` (`sigla`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditorias`
--
ALTER TABLE `auditorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria_equipo`
--
ALTER TABLE `auditoria_equipo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `auditoria_procesos`
--
ALTER TABLE `auditoria_procesos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factores`
--
ALTER TABLE `factores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hallazgos`
--
ALTER TABLE `hallazgos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `indicadores_seguimiento`
--
ALTER TABLE `indicadores_seguimiento`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT de la tabla `informe_auditoria`
--
ALTER TABLE `informe_auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planificacion_sig`
--
ALTER TABLE `planificacion_sig`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `procesos`
--
ALTER TABLE `procesos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `programa_auditoria`
--
ALTER TABLE `programa_auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `riesgos`
--
ALTER TABLE `riesgos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `riesgo_acciones`
--
ALTER TABLE `riesgo_acciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `riesgo_controles`
--
ALTER TABLE `riesgo_controles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_programa_auditoria_id_foreign` FOREIGN KEY (`programa_auditoria_id`) REFERENCES `programa_auditoria` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria_equipo`
--
ALTER TABLE `auditoria_equipo`
  ADD CONSTRAINT `auditoria_equipo_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auditoria_equipo_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `auditoria_procesos`
--
ALTER TABLE `auditoria_procesos`
  ADD CONSTRAINT `auditoria_procesos_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auditoria_procesos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_documento_referencia_id_foreign` FOREIGN KEY (`documento_referencia_id`) REFERENCES `documentos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documentos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documentos_tipo_documento_id_foreign` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipos_documentos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `hallazgos`
--
ALTER TABLE `hallazgos`
  ADD CONSTRAINT `hallazgos_informe_id_foreign` FOREIGN KEY (`informe_id`) REFERENCES `informe_auditoria` (`id`),
  ADD CONSTRAINT `hallazgos_proceso_cod_foreign` FOREIGN KEY (`proceso_cod`) REFERENCES `procesos` (`id`);

--
-- Filtros para la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD CONSTRAINT `indicadores_proceso_cod_foreign` FOREIGN KEY (`proceso_cod`) REFERENCES `procesos` (`id`);

--
-- Filtros para la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  ADD CONSTRAINT `indicadores_historico_indicador_id_foreign` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`);

--
-- Filtros para la tabla `indicadores_seguimiento`
--
ALTER TABLE `indicadores_seguimiento`
  ADD CONSTRAINT `indicadores_seguimiento_indicador_id_foreign` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`);

--
-- Filtros para la tabla `informe_auditoria`
--
ALTER TABLE `informe_auditoria`
  ADD CONSTRAINT `informe_auditoria_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`);

--
-- Filtros para la tabla `riesgos`
--
ALTER TABLE `riesgos`
  ADD CONSTRAINT `riesgos_factor_cod_foreign` FOREIGN KEY (`factor_cod`) REFERENCES `factores` (`id`),
  ADD CONSTRAINT `riesgos_proceso_cod_foreign` FOREIGN KEY (`proceso_cod`) REFERENCES `procesos` (`id`);

--
-- Filtros para la tabla `riesgo_acciones`
--
ALTER TABLE `riesgo_acciones`
  ADD CONSTRAINT `riesgo_acciones_riesgo_cod_foreign` FOREIGN KEY (`riesgo_cod`) REFERENCES `riesgos` (`id`);

--
-- Filtros para la tabla `riesgo_controles`
--
ALTER TABLE `riesgo_controles`
  ADD CONSTRAINT `riesgo_controles_riesgo_cod_foreign` FOREIGN KEY (`riesgo_cod`) REFERENCES `riesgos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
