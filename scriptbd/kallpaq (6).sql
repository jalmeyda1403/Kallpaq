-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-01-2025 a las 01:29:09
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
    DECLARE v_meta DECIMAL(10,5);
    DECLARE v_tipo_agregacion 
  VARCHAR(255);
  DECLARE v_meta_por_intervalo 
  DECIMAL(10,5);
  DECLARE v_meta_acumulada 
  DECIMAL(10,5);

    DECLARE cur CURSOR FOR
        SELECT id, frecuencia, meta, tipo_agregacion FROM indicadores WHERE id = p_indicador_id AND estado = 2;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_indicador_id, v_frecuencia, v_meta, v_tipo_agregacion;


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
DELETE from indicadores_seguimiento  WHERE indicador_id = p_indicador_id AND year(fecha) = p_periodo_actual;

IF v_tipo_agregacion = 'acumulada' THEN
      SET v_meta_por_intervalo = v_meta / v_repeat;
      SET v_meta_acumulada =  v_meta / v_repeat;

      WHILE v_contador <= v_repeat DO
        SET v_fecha_actual = LAST_DAY(DATE_ADD(v_fecha_actual, INTERVAL v_intervalo MONTH));
        REPLACE INTO indicadores_seguimiento (indicador_id, fecha, meta)
        VALUES (v_indicador_id, v_fecha_actual, v_meta_acumulada);

        SET v_meta_acumulada = v_meta_acumulada + v_meta_por_intervalo;
        SET v_contador = v_contador + 1;
      END WHILE;
    ELSE
      WHILE v_contador <= v_repeat DO
        SET v_fecha_actual = LAST_DAY(DATE_ADD(v_fecha_actual, INTERVAL v_intervalo MONTH));
        REPLACE INTO indicadores_seguimiento (indicador_id, fecha, meta)
        VALUES (v_indicador_id, v_fecha_actual, v_meta);

        SET v_contador = v_contador + 1;
      END WHILE;
    END IF;

    UPDATE indicadores SET estado = 2 WHERE id = v_indicador_id;
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
(2, 'fecha_inicio_bloqueo', '01/02/2023', '2023-08-19 02:28:31', NULL),
(3, 'fecha_fin_bloqueo', '31/07/2023', '2023-08-19 02:27:59', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contexto_analisis`
--

CREATE TABLE `contexto_analisis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contexto_determinacion_id` bigint(20) UNSIGNED NOT NULL,
  `internal_context_id` bigint(20) UNSIGNED NOT NULL,
  `external_context_id` bigint(20) UNSIGNED NOT NULL,
  `analisis` text NOT NULL,
  `nivel` enum('Muy Alto','Alto','Medio','Bajo') NOT NULL,
  `valoracion` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contexto_determinacion`
--

CREATE TABLE `contexto_determinacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contexto_externo`
--

CREATE TABLE `contexto_externo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contexto_determinacion_id` bigint(20) UNSIGNED NOT NULL,
  `perspective_type` enum('legal','politico','institucional','tecnologia','social','economico') NOT NULL,
  `amenazas` text NOT NULL,
  `oportunidades` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contexto_interno`
--

CREATE TABLE `contexto_interno` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contexto_determinacion_id` bigint(20) UNSIGNED NOT NULL,
  `perspective_type` enum('normativa','infraestructura','tecnologia','organizacion','personal','cultura_organizacional') NOT NULL,
  `fortalezas` text NOT NULL,
  `debilidades` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `especialistas`
--

CREATE TABLE `especialistas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellido_paterno` varchar(255) NOT NULL,
  `apellido_materno` varchar(255) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `especialistas`
--

INSERT INTO `especialistas` (`id`, `user_id`, `nombres`, `apellido_paterno`, `apellido_materno`, `cargo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Juan Manuel', 'Almeyda', 'Requejo', 'Especialista SIG', '2024-06-05 19:38:32', NULL),
(2, 2, 'Manuel Wilson', 'Perez', 'Efus', 'Especialista TUPA', '2024-06-05 19:38:32', NULL),
(3, 3, 'Angel Arturo', 'Bendezú', 'Cardenas', 'Especialista Riesgos', '2024-06-05 19:38:32', NULL),
(4, 4, 'Maria Isabel', 'Hiyo', 'Huapaya', 'Especialista SIG', '2024-06-05 19:38:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialista_hallazgo`
--

CREATE TABLE `especialista_hallazgo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `especialista_id` bigint(20) UNSIGNED NOT NULL,
  `hallazgo_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_asignacion` timestamp NULL DEFAULT NULL,
  `motivo_asignacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
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
  `smp_cod` varchar(18) NOT NULL,
  `informe_id` varchar(350) DEFAULT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `resumen` varchar(300) NOT NULL,
  `descripcion` text NOT NULL,
  `evidencia` text DEFAULT NULL,
  `criterio` text DEFAULT NULL,
  `clasificacion` enum('NCM','Ncme','Obs','Odm') NOT NULL,
  `origen` enum('IN','EX','SN','RI','GR','CL','HA','ACAL','HOF','OT') NOT NULL,
  `estado` enum('Abierto','Aprobado','En implementación','Pendiente','Cerrado') NOT NULL,
  `sig` enum('SGC','SGAS','SGCM','SGSI') NOT NULL,
  `auditor` varchar(250) NOT NULL,
  `auditor_tipo` enum('auditor interno','auditor externo','colaborador') NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_aprobacion` date DEFAULT NULL,
  `fecha_cierre_acciones` date DEFAULT NULL,
  `avance` decimal(10,2) DEFAULT NULL,
  `fecha_planificacion_evaluacion` date DEFAULT NULL,
  `evaluacion` varchar(255) DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `fecha_cierre_hallazgo` date DEFAULT NULL,
  `estado_final` enum('Sin Efacia','Con Eficacia') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hallazgos`
--

INSERT INTO `hallazgos` (`id`, `smp_cod`, `informe_id`, `proceso_id`, `resumen`, `descripcion`, `evidencia`, `criterio`, `clasificacion`, `origen`, `estado`, `sig`, `auditor`, `auditor_tipo`, `fecha_solicitud`, `fecha_aprobacion`, `fecha_cierre_acciones`, `avance`, `fecha_planificacion_evaluacion`, `evaluacion`, `fecha_evaluacion`, `fecha_cierre_hallazgo`, `estado_final`, `created_at`, `updated_at`) VALUES
(1, 'SMP-RH-IN-0044', '03-2024(I)', 196, 'No se ha designado al Oficial de Compliance del SGCM, por lo que no pudo evidenciarse su legajo.', 'Conforme indica la Norma ISO 37301:2021 (Requisito 7.2.1),  la organización debe asegurarse de que las personas sean competentes sobre la base de una educación, formación o experiencia adecuadas; asimismo, la información documentada apropiada debe estar disponible como evidencia de competencia.\r\n\r\nSegún lo revisado durante el proceso de auditoría, las competencias de compliance para el puesto de Oficial de Compliance se encuentran descritas en el documento \"MATRIZ DE COMPETENCIAS PARA EL SISTEMA DE GESTIÓN COMPLIANCE Función de compliance: Oficial de Compliance”, que cuenta con los V°B° de la Subgerente de Modernización y Subgerente de Políticas y Desarrollo Humano con fecha 17/04/24. Se solicita, la información que sustente lo indicado en la Matriz de Competencias, sin embargo, no fue posible evidenciar el legajo del puesto de Oficial de Compliance. Conforme indica personal de Capital Humano debido a que aún no se ha designado este puesto dentro del SGCM.', 'Según lo revisado durante el proceso de auditoría, las competencias de compliance para el puesto de Oficial de Compliance se encuentran descritas en el documento \"MATRIZ DE COMPETENCIAS PARA EL SISTEMA DE GESTIÓN COMPLIANCE Función de compliance: Oficial de Compliance”, que cuenta con los V°B° de la Subgerente de Modernización y Subgerente de Políticas y Desarrollo Humano con fecha 17/04/24. Se solicita, la información que sustente lo indicado en la Matriz de Competencias, sin embargo, no fue posible evidenciar el legajo del puesto de Oficial de Compliance. Conforme indica personal de Capital Humano debido a que aún no se ha designado este puesto dentro del SGCM.', 'Norma ISO 37301, requisitos: 7.2 Competencia, 7.2.1. Generalidades.', 'Ncme', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-07-01', '2024-06-25', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 15:43:44', '2024-07-04 01:04:14'),
(2, 'SMP-MODER-IN-0015', '03-2024(I)', 107, 'Se identificaron desviaciones en la identificación de riesgos de compliance.', 'La norma ISO 37301:2021, en su requisito 6.1, establece que la organización debe considerar los problemas a los que se hace referencia en 4.1 y los requisitos mencionados en 4.2 y determinar los riesgos y  oportunidades que deben abordarse. (Requisito 6.1 Acciones para abordar riesgos y oportunidades), sin embargo, se identificaron algunas desviaciones en la identificación de riesgos.', 'Durante el desarrollo de la auditoría, se identificó las siguientes desviaciones en los procesos evaluados:\r\nProcesos Administración de información del personal:\r\nSe evidenció en la auditoria, en la revisión documentaria que el proceso en mención, no contaba con algún riesgo del \"Contexto de la organización\" F01(PR-MODER-04)02, como, por ejemplo: \r\nA1 Falta de interiorización por parte de los colaboradores de la obligatoriedad del cumplimiento del procedimiento.\r\nGestión de Capital Humano - Entrega de Puesto del Colaborador:\r\nRiesgo MO-CHP-001 y MO-CHP-002 no están relacionados con los factores externos e internos.\r\nGestión de las Comunicaciones (Diseño del plan de comunicación corporativa, Gestión de la comunicación interna y Gestión de la publicación institucional):\r\nEl riesgo MO-COM-007 no está relacionados con los factores externos e internos.\r\nPlaneamiento Estratégico:\r\nEl riesgo MO-PEI-001 \"No contar con la documentación administrativa que sustente la elaboración, seguimiento y evaluación del PEI (hoja informativa, reportes, anexos de la Guía CEPLAN, debido a la omisión por parte del personal\" no están relacionado con los factores externos e internos. Asimismo, no se evidencia la evaluación del riesgo MO-PEI-002 \"No cumplir con el plazo establecido para el seguimiento del PEI, de acuerdo a los \r\nestablecido en la Guía CEPLAN debido a la falta de información necesaria para el análisis y evaluación del PEI, que debe ser remitida por los OUO responsables de indicadores\".\r\nGestión de Activos Documentarios (Archivo, custodia y conservación de Documentos):\r\nEl riesgo MO-ARCH-004 Solicitudes de eliminación rechazadas debido a desconocimiento del procedimiento vigente, no están relacionado con los factores externos e internos.\r\nEvaluación de Prestaciones Adicionales de Obra - 1era instancia y Evaluación de Prestaciones Adicionales de Supervisión de Obra - 1era instancia:\r\nEl riesgo MO-CPRE-003 “Posible aprobación del PO o PASO a causa de inobservar el plazo que establece la Ley de Contrataciones del Estado y su Reglamento” no están relacionado con los factores externos e internos.\r\nAuditoría de la Cuenta General de la República:\r\nEl riesgo MO-ACGR-001 \"Presentación inconsistente del Informe de la Auditoría de la Cuenta General de la República, a causa de que los informes de la auditoría de las entidades (materia de insumo del informe de la \r\nauditoría de la cuenta general de la República)\" no están relacionado con los factores externos e internos.\r\nOperativo de Control Simultáneo:\r\nEl riesgo MO-GSCS-CS-001 \"Probabilidad de presentación del Informe de Operativo de Control Simultáneo incompleto (no incluye el total de resultados esperados, los cuales se encuentran contenidos en el Plan Operativo), debido a la falta de priorización de las visitas de control por parte de las unidades orgánicas participantes\" no están relacionado con los factores externos e internos.\r\nAuditoría de Cumplimiento:\r\nEl riesgo MO-SCP-AC-001 \"Que el planeamiento de la auditoría de cumplimiento (carpeta de servicio) se efectúe sin cumplir con lo dispuesto en la normativa y lineamientos aplicables (*), a causa de la limitada \r\ncompetencia del personal” no están relacionado con los factores externos e internos.\r\nGestión de Capital Humano - Encargo de Jefatura de Órgano o Unidad Orgánica:\r\nEl Riesgo MO-EFJUO-001 \"Incumplir la normativa dejando sin encargar de funciones a la unidad orgánica por mala comunicación no están relacionados con los factores externos e internos”.\r\nGestión de Abastecimiento - Gestión de Bienes Patrimoniales:\r\nPara la Oportunidad (O1, F1), se ha establecido como plan de tratamiento \"Realizar capacitaciones, al personal de Patrimonio y a los usuarios”, sin embargo, no se indica en qué temas se darán las capacitaciones ni el mecanismo para asegurar que la acción sea permanente. \r\nPara el Riesgo (MO-GBPAT-005), se ha establecido como plan de tratamiento \" Emitir por correo electrónico el enlace a los procedimientos internos del proceso de Gestión de Bienes Patrimoniales”, sin embargo, no se indica el mecanismo para garantizar que la acción de envío de correos electrónicos sea de manera permanente para mitigar el riesgo.\r\nProceso Gestión Antisoborno:\r\nEl proceso ha identificado el riesgo (D1, A4) el cual está registrado en la Matriz integral de riesgos y oportunidades F02(PR-MODER-04)04, Vs.04 aprobado el 18/04/2024, sin embargo, el factor \"A4\" de la \"Determinación del contexto\" del proceso, no es correspondiente con el riesgo en mención.', 'Norma ISO 37301, requisitos: 6.1 Acciones para abordar los riesgos y oportunidades.', 'Ncme', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 16:47:15', '2024-06-26 18:55:54'),
(3, 'SMP-GCM-IN-001', '03-2024(I)', 114, 'No se evidencia la designación de la función de compliance de la CGR', 'No se evidencia que se haya designado la función de compliance en la CGR, ni que se implementen los principios de acceso directo, independencia, autoridad y competencia adecuada de la función de compliance.', 'Durante la auditoría a los procesos Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance, en la cual se entrevistó a la Subgerenta de Modernización , Supervisora General de Modernización y la Supervisora del SIG, se declara que a la fecha no se cuenta con el nombramiento de la función de cumplimiento.', 'Norma ISO 37301, 5.1.1 Órgano y  Alta Dirección.', 'NCM', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-06-26', '2024-06-25', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 21:53:09', '2024-06-27 15:51:49'),
(4, 'SMP-GCM-IN-002', '03-2024(I)', 114, 'No se cuenta con Política de Compliance', 'No se evidencia que se haya aprobado, implementado, comunicado la Política de Compliance y que esté disponible para las partes interesadadas, según corresponda.', 'Al respecto, la Política de Compliance presentada en la auditoría de los procesos de \r\n administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance, no se encuentra aprobada, implementada, comunicada dentro de la organización ni está disponible para las partes interesadas.', 'Norma ISO 37301, requisito: 5.2 Política de Compliance.', 'NCM', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-07-01', '2024-07-30', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 22:16:38', '2024-07-02 01:49:44'),
(5, 'SMP-GCM-IN-003', '03-2024(I)', 114, 'No se cuenta con objetivos para el Sistema de Gestión de Compliance', 'No se evidencia que se haya aprobado y comunicado los Objetivos de Compliance en las funciones y niveles relevantes', 'Durante la auditoría a los procesos de \"Administración de los Sistemas de Gestión\", \"Gestión de Riesgos\", \"Gestión por Procesos\" y \"Gestión Compliance\" se presentó el documento \"Planificación de los objetivos del Sistema Integrado de Gestión\", indicando que que los Objetivos de Compliance se  encuentran en proceso de \"revisión\", en consecuencia no están implementados, ni comunicados en la entidad.', 'Norma ISO 37301, requisito:  6.2. Objetivos de cumplimiento y planificación para lograrlos.', 'NCM', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-06-27', '2024-06-26', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 22:40:54', '2024-06-27 14:56:45'),
(6, 'SMP-GCM-IN-004', '03-2024(I)', 114, 'No se evidencia cumplimiento de requisitos en el proceso  Recepción y Evaluación de Denuncias', 'La Norma ISO 37301:2021 indica que la organización debe establecer, implementar, mantener y mejorar continuamente un sistema de gestión del cumplimiento,\r\nincluidos los procesos necesarios y sus interacciones, de acuerdo con los requisitos de este documento. Al respecto , durante la auditoría al proceso de Recepción y Evaluación de Denuncias el personal entrevistado: -Subgerente de Participación Ciudadana y Control Social -Especialista en Procesos -Analista - Coordinador del Área de Asistencia Técnica-Analista de Denuncias Declaran no ejecutar el proceso de Recepción y Evaluación de Denuncias dado que actualmente está bajo su control el proceso de Asistencia Técnica y Capacitación, no pudiendo verificar de manera adecuada los requisitos de la norma en\r\nmención para el proceso, según lo planificado para la auditoría interna (Recepción y Evaluación de Denuncias.', 'Durante la auditoría al proceso de Recepción y Evaluación de Denuncias el personal entrevistado: -Subgerente de Participación Ciudadana y Control Social -Especialista en Procesos -Analista - Coordinador del Área de Asistencia Técnica-Analista de Denuncias Declaran no ejecutar el proceso de Recepción y Evaluación de Denuncias', 'Norma ISO 37301, requisito: 4.4 Sistema de gestión compliance', 'NCM', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-07-01', '2024-06-28', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-27 15:08:08', '2024-07-02 01:37:02'),
(7, 'SMP-GCM-IN-005', '03-2024(I)', 114, 'No se presentaron los resultados de la medición de los indicadores relacionados al logro de los objetivos de compliance', 'La organización debe desarrollar, implementar y mantener un conjunto de indicadores apropiados que ayudaran a la organización a evaluar el logro de sus objetivos de compliance y evaluar su desempeño de cumplimiento. Al respecto no se evidenció la implementación y mantenimiento de indicadores que ayuden al logro de los objetivos de compliance. (Requisito 9.1. Seguimiento, medición, análisis y evaluación)', 'Auditoría a los procesos Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance donde no se pudo presentar los resultados de la medición de los indicadores relacionados al logro de los objetivos de compliance', 'Norma ISO 37301, requsito: 9.1. Seguimiento, medición, análisis y evaluación.', 'Ncme', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-07-01', '2024-06-26', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-27 15:33:08', '2024-07-02 01:39:09'),
(8, 'SMP-NORM-IN-003', '03-2024(I)', 117, 'La organización debe asegurar que la información documentada sea la apropiada.', 'La Norma ISO 37301:2021 indica en su requisito 7.5 que, al crear y actualizar información documentada, la organización debe asegurarse de que sea apropiado: Durante el desarrollo de las auditorías se obtuvieron las siguientes desviaciones en relación con este requisito: Respaldo y restauración de la información, administración y Verificación de Rendición de Cuentas de los Titulares, Administración y Verificación de Rendición de Cuentas de los Titulares, Gestión del Capital Humano - Administración de Información del Personal, Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance y Administración y evaluación de la implementación del control interno en las Entidades Públicas.', 'Respaldo y restauración de la información: Los siguientes documentos no se encuentran aprobados para este proceso: Determinación de Contexto de los procesos, F01(PR-MODER-04)02, Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18)00, Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04 y Matriz de Caracterización del Proceso F09(PR-NORM-06)02.\r\n\r\nAdministración y Verificación de Rendición de Cuentas de los Titulares: Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance (fecha de actualización del 12.04.24), sin codificación, ni aprobación. \r\n\r\nGestión del Capital Humano - Reclutamiento y Selección: Se puede evidenciar que el documento \"Determinación de Contexto F01(PR-MODER-04)02”, aún no se encuentra aprobado. Se verificó que el área de Modernización solicitó la aprobación del documento en mención según Memorando N°000189-2024-CG/MODER de fecha 09/04/2024, sin embargo, aún se encuentra pendiente por el Propietario de Proceso. \r\n\r\nGestión del Capital Humano - Administración de Información del Personal: Al revisar la documentación del proceso se evidenció que la \"Matriz de caracterización\" F09(PR-NORM-06) Ve.01 del proceso no se encontraba con el formato actualizado de acuerdo con el Procedimiento de alcance del SIG, siendo su  última fecha de actualización el 27/05/2022. Asimismo, el Procedimiento de \"Administración de información del personal\", adecuado al proceso de Gestión de Compliance no se encuentra aprobado. \r\n\r\nGestión del Capital Humano - Proceso Vinculación del personal: De acuerdo con el proceso de auditoría, al revisar la documentación del proceso se evidenció que la \"Matriz de Caracterización\" F09(PR-NORM-06) VE.01 no se encuentra aprobado, siendo su última fecha de aprobación en agosto del 2023. \r\n\r\nAdministración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance: Durante la auditoría al proceso Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance se observa que los siguientes documentos están en proceso de revisión: Manual del Sistema \r\nIntegrado de Gestión (MG-MODER-02), Política del SGCM, Objetivos del SGCM \r\n\r\nAdministración y evaluación de la implementación del control interno en las Entidades Públicas: En los siguientes casos no tienen la versión vigente: • F01(PR-MODER-04)01 se muestra la versión 01 siendo la versión vigente 02• F02(PR-MODER-04)03 se muestra en versión 03 siendo la versión vigente 04.', 'Norma ISO 37301, requisito: 7.5 Información documentada.', 'Ncme', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-07-01', '2024-06-10', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-27 16:03:20', '2024-07-01 17:41:34'),
(9, 'SMP-SCS-IN-001', '03-2024(I)', 125, 'La Gerencia Regional de Control de Ancash no tuvo acceso a la información documentada del proceso de Visita de Control.', 'La información documentada requerida por el Sistema de Gestión de Compliance se debe controlar para asegurarse que: está disponible y es adecuada para su uso, dónde y cuándo se necesite.\r\nNo se evidencia que en el proceso de Visita de Control se asegure que la información documentada requerida por el Sistema de Gestión de Compliance se encuentre disponible y adecuada para su uso, dónde y \r\ncuándo se necesite.', 'La Gerencia Regional de Control de Ancash no tuvo acceso a la información con respecto a:\r\n- Matriz de caracterización del Proceso \r\n- Determinación del Contexto\r\n- Matriz integral de riesgos y oportunidades\r\n- Matriz de principales obligaciones de compliance \r\nDocumentos aprobados el 19 de marzo de 2024 por el propietario del proceso de Visita de Control', 'Norma SO 37301, requisito: 7.5 Información documentada.', 'Ncme', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, '2024-06-14', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-27 16:07:16', '2024-06-27 16:25:44'),
(10, 'Obs-RDGD-IN-001', '03-2024(I)', 78, 'Falta identificar partes interesadas para el proceso de Recepción de Documentos.', 'Si bien algunos de los subprocesos de Gestión de Activos Documentarios han determinado sus partes interesadas pertinentes, falta que se identifique para el subprocesos de Recepción de Documentos. Cabe señalar que, el auditado mencionó algunas partes externas tales como: ciudadanos, personas jurídicas entre otros.', 'Matriz de Caracterización del Proceso.', 'Norma ISO 37301, requisito: 4.2 Comprensión de las necesidades y expectativas de las partes interesadas.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 20:16:55', '2024-06-27 20:16:55'),
(11, 'Obs-ERPS-IN-001', '03-2024(I)', 213, 'Algunos subprocesos de Gestión del Capital Humano no han determinado sus partes interesadas.', 'Si bien algunos de los subprocesos de Gestión del Capital Humano han determinado sus partes interesadas pertinentes, falta que se identifiquen para los subprocesos de Entrega de puestos al colaborador y Encargatura de jefatura de OUO.', 'Matrices de caracterización de los procesos de Entrega de puestos al colaborador y Encargatura de jefatura de OUO.', 'Norma ISO 37301, requisito 4.2. Comprender las necesidades y expectativas de las partes interesadas.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 21:53:56', '2024-06-27 21:54:15'),
(12, 'Obs-GOCI-IN-001', '03-2024(I)', 76, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) con fecha de aprobación 26/03/24, en la que se han identificado 04 obligaciones relacionadas a este proceso; para las cuales en el campo de “Principales Obligaciones/compromisos que contiene una Obligación” se han indicado los objetivos o alcances de dichas obligaciones, mas no los elementos de obligatoriedad.', 'Matriz de Identificación de Principales Obligaciones Compliance del proceso de Gestión del Jefe y personal OCI.', 'Norma ISO 37301, requisito 4.5 Obligaciones de compliance.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:04:49', '2024-06-27 22:04:49'),
(13, 'Obs-GCAP-IN-001', '03-2024(I)', 201, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) con fecha de aprobación 22/04/24, en la que se han identificado 04 obligaciones relacionadas a este proceso; para las cuales en el campo de “Principales Obligaciones/compromisos que contiene una Obligación” se han indicado los objetivos o alcances de dichas obligaciones , mas no los elementos de obligatoriedad , como por ejemplo: en el caso de la Directiva N°141-2026, las  obligaciones de los plazos establecidos para la planificación, ejecución; asi como la emisión de una resolución para aprobar el Plan de Desarrollo de las Personas al Servicio del Estado (PDP).', 'Matriz de Identificación de Principales Obligaciones Compliance del proceso de Gestión de la Capacitación.', 'Norma ISO 37301, requisito 4.5 Obligaciones de Compliance', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:09:50', '2024-06-27 22:39:21'),
(14, 'Obs-AIPE-IN-001', '03-2024(I)', 210, 'Procedimiento de \"Administración de información del personal\" no cita los documentos normativos identificados en la MIPOC.', 'Se evidenció que en el Procedimiento de \"Administración de información del personal\" (punto \"Marco legal\") no se encuentra identificado la principal normativa del proceso como es el caso de la Directiva N° 001-2023-SERVIR-GDSRH “Normas para la Gestión del Proceso de Administración de Legajos”.', 'PR-ACH-06 Procedimiento \"Administración de Información de Personal\"', 'Norma ISO 37301, requsito 8.1. Planificación y control operacional.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:22:33', '2024-06-27 22:28:46'),
(15, 'Obs-DPPC-IN-001', '03-2024(I)', 200, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) con fecha de aprobación 18/04/24, en la que se han identificado 03 obligaciones relacionadas a este proceso; para las cuales se ha registrado como principal obligación el “Verificar el cumplimiento de requisitos y condiciones para acceder al cargo en el proceso de designación o encargo\". Sin embargo, durante las entrevistas al personal pudo identificar que cada una de estas obligaciones (leyes) implican otros aspectos relevantes de cumplimiento para el proceso, por lo que deben quedar claramente registradas en su actual Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18). -Ley N° 28175, Ley Marco del Empleo Público: Establece el % total que se puede tener de puestos de confianza en la entidad. ´-Ley N° 31419, Ley y reglamento de Ley la cual establece las disposiciones para garantizar la idoneidad en el acceso y ejercicio de la función pública de funcionarios y directivos de libre designación y remoción, y otras disposiciones: Determina los requisitos para establecer los puestos de trabajo. Se debe indicar también el reglamento en relación. ´-Ley N° 31676, Ley que modifica el código penal, con la finalidad de reprimir las conductas que afectan los principios de mérito, idoneidad y legalidad para el acceso a la función pública: Establece las sanciones legales tanto para el postulante como para el funcionario en caso de portar información falsa de los puestos de confianza.', 'Matriz de Identificación de Principales Obligaciones Compliance del proceso de Designación de personal en puestos de confianza.', 'Norma ISO 37301, requisito 4.5 Obligaciones de compliance.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:35:58', '2024-06-27 22:39:07'),
(16, 'Obs-VIPE-IN-001', '03-2024(I)', 198, 'Procedimiento de \"Vinculación de Personal\" no cita los documentos normativos identificados en la MIPOC.', 'Se evidenció que en el Procedimiento de \"Vinculación del personal\" no se encuentra identificado uno de sus principales normativas (punto \"Marco\" legal\", como es el caso del Decreto Supremo Nº 075-2008-PCM, que aprueba el Reglamento del Decreto Legislativo Nº 1057 que regula el Régimen Especial de Contratación Administrativa de Servicios. (Requisito 8.1. Planificación y control operacional)', 'PR-ICP-05 Procedimiento \"Vinculación de Personal\"', 'Norma ISO 37301, requsito 8.1. Planificación y control operacional.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:38:31', '2024-06-27 22:38:31'),
(17, 'Obs-REST-IN-001', '03-2024(I)', 118, 'No se han registrado controles para el riesgo MO-REST-0002.', 'Si bien en la Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04, se indica que no se cuentan con controles actuales para el riesgo MO-REST-0002, a la fecha sí se vienen realizando coordinaciones con el proveedor por el aspecto de tiempos, como por ejemplo mantener los tickets abiertos. Este control no está incluido en la MIRO.', 'Matriz Integral de Riesgos y Oportunidades del Proceso de Respaldo de Información', 'Norma ISO 37301, requisito 8.1 Planificación y control operacional', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:54:47', '2024-06-28 00:06:29'),
(18, 'Obs-SGCM-IN-006', '03-2024(I)', 114, 'Manual del Sistema Integrado de Gestión, no contempla organo de gobierno.', 'El Órgano de Gobierno y la Alta Dirección deben demostrar liderazgo y compromiso con respecto al sistema de gestión de cumplimiento.\r\n\r\nAl respecto durante la auditoría a los procesos Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance se revisó el Anexo 2 Estructura del SGCM de la CGR contenido en el Manual del Sistema Integrado de Gestión (MG-MODER-02) conformada por la Alta Dirección CMI (Contralor, Vicecontralor de Integridad y Control, Vicecontralor de Control Sectorial y Territorial y Secretario General), no evidenciando la representación del órgano de gobierno y alta dirección, asimismo en el punto 5 de Manual del Sistema Integrado de Gestión (MG-MODER-02) se declara \"no contar con un órgano de gobierno\".', 'Manual del Sistema Integrado de Gestión.', 'Norma ISO 37301, requisito: 5.1.1 Órgano de gobierno y alta dirección', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 22:59:55', '2024-06-28 00:06:39'),
(19, 'Obs-SGCM-IN-007', '03-2024(I)', 114, 'No se evidencia que se haya asignado los roles y responsabilidades para el Órgano de Gobierno.', 'El órgano de gobierno y la alta dirección se aseguran que las responsabilidades y autoridades para los roles relevantes se asignen y se comuniquen dentro de la organización. El órgano de gobierna deberá: — asegurarse de que la alta dirección se mida en función del logro de los objetivos de cumplimiento; — ejercer la supervisión de la alta dirección con respecto al funcionamiento del sistema de gestión del cumplimiento. La alta dirección deberá: — asignar recursos adecuados y apropiados para establecer, desarrollar, implementar, evaluar, mantener y mejorar el sistema de gestión del cumplimiento; — asegurar que existan sistemas efectivos de informes oportunos sobre el desempeño del cumplimiento; — asegurar la alineación entre los objetivos estratégicos y operativos y las obligaciones de cumplimiento; — establecer y mantener mecanismos de rendición de cuentas, incluidas acciones disciplinarias y consecuencias; — Asegurar la integración del desempeño de cumplimiento en las evaluaciones de desempeño del personal. Al respecto, durante la auditoría a los procesos Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance, se revisó el documento Matriz de Competencia para el Sistema de Gestión Compliance F02(MG-MODER-02) con fecha de aprobación del 17/04/2024, en la cual no se evidencia que se haya asignado los roles y responsabilidades para el Órgano de Gobierno.', 'Matriz de Competencia para el Sistema de Gestión Compliance F02(MG-MODER-02) con fecha de aprobación del 17/04/2024', 'Norma ISO 37301, requisito: 7.2 Competencia.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor interno', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:02:34', '2024-06-28 00:06:50'),
(20, 'Obs-OROF-IN-001', '03-2024(I)', 126, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) con fecha de aprobación 27/03/24, en la que se han identificado 02 obligaciones relacionadas a este proceso; para las cuales en el campo de “Principales obligaciones/compromisos que contiene una Obligación” se han indicado los objetivos o alcances de dichas obligaciones, mas no los elementos de obligatoriedad.', 'Matriz de Identificación de Principales Obligaciones Compliance', 'Norma ISO 37301, requsiito 4.5 Obligaciones de compliance', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:10:51', '2024-06-28 00:07:23'),
(21, 'Obs-FECP-IN-001', '03-2024(I)', 219, 'No se pudo evidenciar la eficacia de controles.', 'Se solicitó una muestra para realizar la trazabilidad al procedimiento \"PR-PROY-09) VE.00 Gestión de Pagos a Consultores Individuales (ve.00) con fecha de aprobación 02.02.2022”,a efectos de verificar los controles;  sin embargo, no se pudo evidenciar documentación ya que la usuaria no estaba disponible.', 'Procedimiento (PR-PROY-09) VE.00 Gestión de Pagos a Consultores Individuales', 'Norma ISO 37301, requisito: 8.2 Establecimiento de controles y procedimientos', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:13:55', '2024-06-28 00:07:06'),
(22, 'Obs-PCMC-IN-001', '03-2024(I)', 283, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'En el proceso de auditoria se evidenció en la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) del proceso, no se encuentra identificada la Directiva N° 006-2024-CG/GPCS aprobada mediante Resolución de Contraloría N° 204-2024-CG.', 'Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18', 'Norma ISO 37301, requisito: 4.5 Obligaciones de compliance', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:16:02', '2024-06-27 23:16:02'),
(23, 'Obs--IN-001', '03-2024(I)', 97, 'No se ha identificado los elementos de obligatoriedad en la Identificación de Obligaciones de Compliance.', 'Se cuenta con la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) con fecha de aprobación 02/04/24, en la que se han identificado 06 obligaciones relacionadas a este proceso; para las cuales en el campo de “Principales Obligaciones/compromisos que contiene una Obligación” se han indicado los objetivos o alcances de dichas obligaciones , mas no los elementos de obligatoriedad como por ejemplo , para el caso del \"DLNº 1326 que reestructura el Sistema Administrativo de Defensa Jurídica del Estado y su reglamento\",  el auditado mencionó  que este decreto  establece las funciones y atribuciones del equipo de defensa de los procuradores , y no se ha indicado en la F4(PR-MODER-18).', 'Matriz de Identificación de Principales Obligaciones Compliance', 'Norma ISO 37301, requisito 4.5 Obligaciones de compliance.', 'Obs', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:17:44', '2024-06-27 23:17:44'),
(24, 'Odm-GOCI-IN-002', '03-2024(I)', 76, 'Evaluar la actualización de la  Matriz Integral de Riesgos y Oportunidades', 'De acuerdo a lo revisado en la Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04 con fecha 26/03/24 , se recomienda :\r\n-Se puedan identificar oportunidades en la Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04. , de acuerdo a los elementos de la \"Determinación de Contexto de los procesos en revisión F01(PR-MODER-04)02´\r\n-Enfatizar el punto de la directiva (numeral 7.7) que incide como control preventivo al riesgo R(D1,A1).\r\n-Replantear el plan de tratamiento ya que la acción que se ha establecido es una acción puntual.', 'Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04 con fecha 26/03/24 .', 'Norma ISO 37301, requisito 6.1. Acciones para abordar riesgos y oportunidades.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:25:16', '2024-06-27 23:42:24'),
(25, 'Odm-DPPC-IN-002', '03-2024(I)', 200, 'Evaluar la actualización de la Determinación del Contexto', 'De acuerdo con lo revisado en el documento \"Determinación de Contexto F01(PR-MODER-04)02 aprobado el 18/04/2024 por la Gerencia de Capital Humano, se sugiere se evalúe lo descrito en la D1 (SERVIR no ha determinado alcances de similares en el cumplimiento de los perfiles) y se reformule como una probable amenaza; o en su defecto, se señale la cuestión interna relacionada a dicha problemática.', 'Determinación de Contexto F01(PR-MODER-04)02 aprobado el 18/04/2024.', 'Norma ISO 37301, requisito 4.1 Comprender la organización y su contexto.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:27:24', '2024-06-28 00:06:03'),
(26, 'Odm-DPPC-IN-003', '03-2024(I)', 200, 'Evaluar la actualización de los controles del Procedimiento de designación o encargo de personal en puestos de confianza', 'Se recomienda que para el control \"Procedimiento de designación o encargo de personal en puestos de confianza (PR-ICP-01) el cual señala las responsabilidades de la GCH, POLDEH, PER, GJNC y AJ.\", sujeto al Riesgo (MO-DPC-0002) se puedan incluir las actividades \"6\" y \"9\" del PR-ICP-01 VE.01 (Procedimiento Designación o Encargo en Puestos de Confianza). \r\nSe recomienda que para el control \"Verificación posterior de legajos de manera aleatoria\", sujeto al Riesgo (MO-DPC-0001), se pueda incluir la actividad \"5\" del PR-ICP-01 VE.01 (Procedimiento Designación o Encargo en Puestos de Confianza). \r\nAsimismo, se debería precisar que se emiten memorandos, cuando se rechazan los expedientes que no tengan información exacta o veraz. (Requisito 6.1. Acciones para abordar riesgos y oportunidades)', 'Procedimiento de designación o encargo de personal en puestos de confianza (PR-ICP-01)', 'Norma ISO 37301, 8.2 Establecimiento de controles y procedimientos', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:29:25', '2024-06-27 23:42:35'),
(27, 'Odm-GPAD-IN-001', '03-2024(I)', 279, 'Evaluar la actualización de la Determinación del Contexto', 'Se cuenta con la Determinación de Contexto de los procesos en revisión F01(PR-MODER-04)02 y fecha de aprobación 16/04/24, en el cual se ha determinado 08 debilidades, 04 amenazas, 08 fortalezas y 03 oportunidades del SGCM. Según lo revisado durante la auditoría, se dan las siguientes recomendaciones:\r\n●	D2: (No se cuenta con procedimiento de gestión que regule la atención de recursos impugnatorios por sanciones disciplinarias). A la fecha, ya se tiene un procedimiento (PR-ACH-04), motivo por el que se sugiere evaluar el retirar dicha D2.\r\n●	A4: Se recomienda precisar que la STPAD podría conocer petitorios ajenos a sus competencias cuando éstos estén con plazo limitado o vencido.', 'Determinación de Contexto  F01(PR-MODER-04)02', 'Norma ISO 37301, requisito 4.1 Comprender la organización y su contexto)', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor interno', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:31:28', '2024-06-27 23:56:06'),
(28, 'Odm-INPE-IN-001', '03-2024(I)', 199, 'Evaluar la actualización de Matriz Integral de Riesgos y Oportunidades', 'Conforme a lo revisado en la Matriz Integral de Riesgos y Oportunidades F02(PR MODER-04)04 con fecha de aprobación 09/04/24, se sugiere que:\r\n-Para el riesgo MO-IND-001 (\"Que el personal incorporado desconozca sobre la entidad y sobre el puesto que va ocupar , debido a que no recibe la información correspondiente en la inducción\") , se sugiere redireccionar el riesgo al proceso de inducción , y que sería el de no ejecutarse dentro del tiempo planificado para asegurar el cumplimiento del objetivo del proceso según MCAR.\r\n-Para el control actual del riesgo MO-IND-001 (Ejecución de las actividades establecidas en el procedimiento - Gestión de Inducción del Personal PR-ICP-02) , se recomienda poder precisar como control el \"cumplimiento del programa de inducción (inducciones masivas) y el correo enviado a las OUO u órganos para ejecutar las inducciones específicas , de tal forma que incida sobre el riesgo.', 'Matriz Integral de Riesgos y Oportunidades F02(PR MODER-04)04 con fecha de aprobación 09/04/2024.', 'Norma ISO 37301, requisito 6.1. Acciones para abordar riesgos y oportunidades.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor interno', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:34:39', '2024-06-28 00:05:46'),
(29, 'Odm-GBPA-IN-001', '03-2024(I)', 85, 'Evaluar la actualización de las partes interesadas.', 'Conforme a lo indicado por los entrevistados, se identificó la relación del proceso Gestión de Bienes Patrimoniales con la empresa que brinda el servicio de corretaje de seguro, por lo que se recomienda se evalúe la conveniencia de incluirla como una parte interesada.', 'Matriz de Caracterización del Proceso', 'Norma ISO 37301, requisito 4.2 Comprender las necesidades y expectativas de las partes interesadas.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:37:02', '2024-06-28 00:11:06'),
(30, 'Odm-SGCM-IN-008', '03-2024(I)', 114, 'Evaluar la enmienda del cambio climático.', 'Se recomienda considerar la inclusión de la enmienda del cambio climático puesto que es una reciente modificación al requisito 4, la cual indica que las organizaciones deberán determinar si el cambio climático es un tema relevante.', 'Norma ISO 37301, enmienda.', 'Norma ISO 37301, requisito 4.1. Comprensión de la organización y su contexto.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor interno', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:39:24', '2024-06-27 23:39:24'),
(31, 'Odm-POI-IN-001', '03-2024(I)', 32, 'Evaluar la actualización de la Determinación del Contexto', 'En el proceso de auditoría se evidencio que el proceso Planeamiento Operativo contaba con la Determinación del contexto F01(PR-MODER-04)02, con fecha de aprobación 08/04/2024, sin embargo se recomienda evaluar los factores de las Fortaleza (legales) y Oportunidades (legales) y que estos sean acorde al proceso en mención, es decir las fortalezas en el aspecto legal deben incluir normativas legales internas que forman parte el proceso y en las oportunidades se deben considerar normativas legales externas que pueden influir en el proceso.', 'Determinación del contexto F01(PR-MODER-04)02, con fecha de aprobación 08/04/24,', 'Norma ISO 37301, requisito 4.1 Comprender la organización y su contexto.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:41:15', '2024-06-27 23:44:32'),
(32, 'Odm-ADES-IN-001', '03-2024(I)', 143, 'Evaluar la actualización de la Determinación del Contexto', 'Se revisó en la Determinación de Contexto de los procesos en revisión F01(PR-MODER-04), la debilidad \"D8”, que indica la limitación en el uso del procedimiento, y el auditado precisó que la debilidad viene porque el proceso aún no tiene un instrumento normativo o lineamiento que ayude al cumplimiento del procedimiento sancionador que fortalezca o contribuya a la evaluación de desempeño, por lo que se sugiere se concrete dicho punto.', 'Determinación de Contexto', 'Norma ISO37301, requisito 4.1 Comprender la organización y su contexto.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:44:10', '2024-06-27 23:44:40'),
(33, 'Odm-ADES-IN-002', '03-2024(I)', 143, 'Evaluar la valoración del riesgo MO-SCP-AD-0002', 'Para el Riesgo (MO-SCP-AD-0002), se ha indicado como consecuencia \"Obtener resultados que no se encuentran alineados con los objetivos de la auditoría de desempeño\". Durante la auditoría, el entrevistado amplió esas posibilidades de consecuencias con aspectos de relevancia para el proceso, motivo por es conveniente se detallen en su matriz MIRO, considerando que dicho riesgo tiene un Impacto de nivel \"ALTO”, (Alta responsabilidad legal para la institución, sus funcionarios o frente a terceros. Grave incumplimiento de las obligaciones).', 'Matria Integral de Riesgos y Oportunidades.', 'Norma ISO 37301, requisito 4.6. Evaluación de riesgos de compliance.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor interno', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-27 23:47:13', '2024-06-27 23:47:13'),
(34, 'Odm-FECP-IN-002', '03-2024(I)', 219, 'Se sugiere adecuar el impacto del Riesgo (D13, D14, A48)', 'Se revisó el Riesgo (D13, D14, A48), relacionado a la afectación del cumplimiento de los objetivos de proyectos para el cual se estableció como consecuencia \"No cumplir con los tiempos de ejecución de los proyectos\". Por lo expuesto y comentado por el auditado, se sugiere adicionar las consecuencias de mayor impacto como por ejemplo el incumplimiento del cronograma del proyecto, o las limitaciones con los términos contractuales con el BID, con fines de establecer controles específicos a estas posibles situaciones.', 'Matriz integral de riesgos y oportunidades', 'Norma ISO 37301, requisito 6.1. Acciones para abordar riesgos y oportunidades', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-28 00:00:26', '2024-06-28 00:05:29'),
(35, 'Odm-PCAP-IN-001', '03-2024(I)', 284, 'Se sugiere actualizar la Matriz de Identificación de Principales Obligaciones Compliance', 'Se evidenció en la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18) del proceso, no se encuentra identificada la Directiva N° 006-2024-CG/GPCS aprobada mediante Resolución de Contraloría N° 204-2024-CG', 'Matriz de Identificación de Principales Obligaciones Compliance', 'Norma ISO 37301, requisito 4.5. Obligaciones de compliance.', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-28 00:02:13', '2024-06-28 00:02:13'),
(36, 'Odm-GPRJ-IN-002', '03-2024(I)', 97, 'Evaluar la incorporación de controles en la Matriz Integral de Riesgos y Oportunidades.', 'De acuerdo a lo revisado en la Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04 con fecha 02/04/2024 , se recomienda considerar elementos con los que ya cuenta el proceso auditado y que no se mencionan en su F02(PR-MODER-04)04 , según se cita:\r\n-Incluir como control actual el Sistema de Gestión de la Procuraduría (SGP ) , en el cual se puede ver el estatus de los casos llevados por la Procuraduría.\r\n- Incluir como control actual el uso de Memorandos para anticipar o comunicar los plazos de presentación de escritos.\r\n-Hacer referencia a los procedimientos recientemente implementados , como parte de los controles actuales ; como por ejemplo el PR-GP-JUD-01 VE.00 Gestión de los Procesos Civiles Resultantes de los Servicios de Control (Aprobación 22/04) , y el PR-GP-JUD-02 Gestión de los Procesos Penales Resultantes de los Servicios de Control', 'Matriz Integral de Riesgos y Oportunidades F02(PR-MODER-04)04 con fecha 02/04/2024', 'Norma ISO 37301, requisito 6.1. Acciones para abordar riesgos y oportunidades)', 'Odm', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-28 00:05:03', '2024-06-28 00:05:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hallazgos_acciones`
--

CREATE TABLE `hallazgos_acciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hallazgo_id` bigint(20) UNSIGNED NOT NULL,
  `accion_cod` varchar(20) NOT NULL,
  `accion` text NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `responsable_id` varchar(255) DEFAULT NULL,
  `responsable_correo` varchar(255) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_fin_reprogramada` date DEFAULT NULL,
  `fecha_cancelada` date DEFAULT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `ruta_evidencia` text DEFAULT NULL,
  `estado` enum('Programada','Pendiente','En implementación','Cancelada','Completada','Cerrada') NOT NULL DEFAULT 'Programada',
  `es_correctiva` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hallazgos_acciones`
--

INSERT INTO `hallazgos_acciones` (`id`, `hallazgo_id`, `accion_cod`, `accion`, `fecha_inicio`, `fecha_fin`, `responsable_id`, `responsable_correo`, `comentario`, `fecha_fin_reprogramada`, `fecha_cancelada`, `fecha_fin_real`, `ruta_evidencia`, `estado`, `es_correctiva`, `created_at`, `updated_at`) VALUES
(12, 1, 'SMP-RH-IN-0044-001', 'Verificación del perfil del Oficial de Compliance.', '2024-06-13', '2024-06-17', 'Daniel Sedan Villacorta', 'dsedan@contraloria.gob.pe', 'Nuevo registro', NULL, NULL, NULL, NULL, 'Completada', 0, '2024-06-26 20:57:03', '2024-07-04 01:04:10'),
(13, 1, 'SMP-RH-IN-0044-002', 'Resguardo de legajo, con los documentos presentados por el Oficial de Compliance.', '2024-06-13', '2024-06-19', 'Daniel Sedan Villacorta', 'dsedan@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-26 20:58:31', '2024-07-02 01:42:58'),
(14, 1, 'SMP-RH-IN-0044-003', 'Designar la función de compliance en la CGR .', '2024-06-13', '2024-06-21', 'Luis Miguel Iglesias León', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-26 21:00:26', '2024-07-02 01:42:58'),
(15, 1, 'SMP-RH-IN-0044-004', 'Difundir dicha designacion a todo el personal en CGR.', '2024-06-13', '2024-06-25', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-26 21:01:39', '2024-07-02 01:42:58'),
(16, 3, 'SMP-GCM-IN-001-001', 'Designar la función de compliance en la CGR', '2024-06-06', '2024-06-21', 'Luis Miguel Iglesias León (Ata Dirección)', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-26 22:09:17', '2024-07-02 01:42:58'),
(17, 3, 'SMP-GCM-IN-001-002', 'Difundir dicha designacion a todo el personal en la CGR', '2024-06-06', '2024-06-25', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-26 22:10:03', '2024-07-02 01:42:58'),
(18, 4, 'SMP-GCM-IN-002-001', 'Presentar la propuesta de la Política de Compliance para la evaluación de las unidades orgánicas que conforman el \r\nproceso de revisión establecido dentro de la CGR.', '2024-06-06', '2024-07-30', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'En implementación', 0, '2024-06-26 22:31:53', '2024-07-02 01:51:58'),
(19, 4, 'SMP-GCM-IN-002-002', 'Aprobar la Política Compliance', '2024-06-06', '2024-06-21', 'Luis Miguel Iglesias León (Ata Dirección)', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Pendiente', 0, '2024-06-26 22:32:58', '2024-07-02 01:48:06'),
(20, 4, 'SMP-GCM-IN-002-003', 'Comunicar la Política Compliance', '2024-06-06', '2024-06-26', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Pendiente', 0, '2024-06-26 22:33:30', '2024-07-02 01:48:06'),
(21, 4, 'SMP-GCM-IN-002-004', 'Poner a disposicion de las partes internas la Política Compliance', '2024-06-06', '2024-06-26', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Pendiente', 0, '2024-06-26 22:34:05', '2024-07-02 01:48:06'),
(22, 5, 'SMP-GCM-IN-003-001', 'Presentar la propuesta de los Objetivos de Compliance para la \r\nevaluación de las unidades orgánicas que conforman el proceso de revisión establecido dentro de la CGR.', '2024-06-06', '2024-05-31', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 14:54:26', '2024-07-02 01:42:58'),
(23, 5, 'SMP-GCM-IN-003-002', 'Aprobar Objetivos Compliance', '2024-06-06', '2024-06-21', 'Luis Miguel Iglesias León (Ata Dirección)', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 14:55:10', '2024-07-02 01:42:58'),
(24, 5, 'SMP-GCM-IN-003-003', 'Comunicar los Objetivos Compliance', '2024-06-06', '2024-06-26', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 14:55:58', '2024-07-02 01:42:58'),
(25, 6, 'SMP-GCM-IN-004-001', 'Revisión y elaboración de informe complementario que sustente el cambio en el alcance del SGCM', '2024-06-06', '2024-06-10', 'Ana Elsa Gonzales Napaico', 'agonzalesn@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:21:25', '2024-07-02 01:42:58'),
(26, 6, 'SMP-GCM-IN-004-002', 'Aprobación de informe complementario que sustente el\r\ncambio en el alcance del SGCM.', '2024-06-06', '2024-06-21', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:22:16', '2024-07-02 01:42:58'),
(27, 6, 'SMP-GCM-IN-004-003', 'Actualización de Manual del Sistema Integrado de Gestión', '2024-06-06', '2024-06-21', 'Ana Elsa Gonzales Napaico', 'agonzalesn@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:22:52', '2024-07-02 01:42:58'),
(28, 6, 'SMP-GCM-IN-004-004', 'Presentación de alcance del SGCM al Comité de Modernización\r\nInstutuciona', '2024-06-06', '2024-06-28', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:23:33', '2024-07-02 01:42:58'),
(29, 7, 'SMP-GCM-IN-005-001', 'Aprobar Política y Objetivos de compliance.', '2024-06-06', '2024-06-21', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:44:08', '2024-07-02 01:42:58'),
(30, 7, 'SMP-GCM-IN-005-002', 'evisar y adecuar los indicadores de la Planificación del SGCM en los procesos de la CGR.(Matriz cliente proveedor)', '2024-06-06', '2024-06-25', 'Johnny Elmo Ponce Cajas', 'jponcec@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:45:08', '2024-07-02 01:42:58'),
(31, 7, 'SMP-GCM-IN-005-003', 'Medir los indicadores', '2024-06-06', '2024-06-26', 'Johnny Elmo Ponce Cajas', 'jponcec@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 15:46:06', '2024-07-02 01:42:58'),
(32, 9, 'SMP-SCS-IN-001-001', 'Emitir un memorando circular para:\r\n1. Remitir documentos aprobados del SGCM: \r\nDeterminación de contexto y MIRO.\r\n2. Informar que los documentos: MCAR y la MIPOC se \r\nencuentran disponibles en la Intranet de la CGR.', '2024-05-30', '2024-05-31', 'Luis Carlos Echeverria Tamayo', 'lecheverria@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 1, '2024-06-27 16:18:42', '2024-07-02 01:42:58'),
(33, 9, 'SMP-SCS-IN-001-002', 'Sensibilización del Sistema de Gestión de Compliance.', '2024-05-30', '2024-06-14', 'Luis Carlos Echeverria Tamayo', 'lecheverria@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 16:21:37', '2024-07-02 01:42:58'),
(34, 9, 'SMP-SCS-IN-001-003', 'Capacitación sobre los accesos a los documentos de gestión, relacionados al proceso de Visita de Control.', '2024-06-14', '2024-06-14', 'Ana Patricia Alvarez Giraldo', 'aalvarez@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 16:23:19', '2024-07-02 01:42:58'),
(35, 9, 'SMP-SCS-IN-001-004', 'Charla de orientación para afrontar auditorias.', '2024-06-14', '2024-06-14', 'Luis Carlos Echeverria Tamayo', 'lecheverria@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 16:24:09', '2024-07-02 01:42:58'),
(36, 9, 'SMP-SCS-IN-001-005', 'Definir los roles de quienes participan en el proceso de \r\nauditoria, a través de un correo electrónico de los \r\nacuerdos', '2024-05-30', '2024-05-31', 'Ana Patricia Alvarez Giraldo', 'aalvarez@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 16:24:58', '2024-07-02 01:42:58'),
(37, 9, 'SMP-SCS-IN-001-006', 'Difundir la política y los objetivos del SGCM.', '2024-06-14', '2024-06-14', 'Luis Carlos Echeverria Tamayo', 'lecheverria@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-06-27 16:25:44', '2024-07-02 01:42:58'),
(38, 8, 'SMP-NORM-IN-003-001', 'Respaldo y restauración de la información´-Aprobar el documento Determinación de Contexto de los procesos, F01(PR-MODER-04)02,´-Aprobar la Matriz de Identificación de Principales Obligaciones Compliance F4(PR-MODER-18)00,', '2024-06-06', '2024-06-10', 'Juan Manuel Almeyda Requejo', 'jalmeyda@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '', 1, '2024-07-01 17:17:44', '2024-07-02 01:42:58'),
(39, 8, 'SMP-NORM-IN-003-002', 'Difusión de la cápsula del conocimiento del procedimiento \"Gestión de documentos normativos en el alcance del SIG\", así como la aplicación del procedimiento del procedimiento \"Gestión de documentos normativos en el alcance del SIG\" (PR-NORM-06).', '2024-06-06', '2024-06-06', 'Juan Manuel Almeyda Requejo', 'jalmeyda1403@gmail.com', NULL, NULL, NULL, NULL, NULL, '', 0, '2024-07-01 17:20:09', '2024-07-02 01:42:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hallazgos_causas`
--

CREATE TABLE `hallazgos_causas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hallazgo_id` bigint(20) UNSIGNED NOT NULL,
  `metodo` enum('ishikawa','cinco_porques') NOT NULL,
  `por_que_1` text DEFAULT NULL,
  `por_que_2` text DEFAULT NULL,
  `por_que_3` text DEFAULT NULL,
  `por_que_4` text DEFAULT NULL,
  `por_que_5` text DEFAULT NULL,
  `mano_obra` text DEFAULT NULL,
  `metodologias` text DEFAULT NULL,
  `materiales` text DEFAULT NULL,
  `maquinas` text DEFAULT NULL,
  `medicion` text DEFAULT NULL,
  `medio_ambiente` text DEFAULT NULL,
  `resultado` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hallazgos_causas`
--

INSERT INTO `hallazgos_causas` (`id`, `hallazgo_id`, `metodo`, `por_que_1`, `por_que_2`, `por_que_3`, `por_que_4`, `por_que_5`, `mano_obra`, `metodologias`, `materiales`, `maquinas`, `medicion`, `medio_ambiente`, `resultado`, `created_at`, `updated_at`) VALUES
(6, 1, 'ishikawa', '¿Porque no se evidenció el legajo del puesto del Oficial de Compliance?\r\nNo se tiene designado al profesional que realizará las labores de la función de compliance de la CGR.', NULL, NULL, NULL, NULL, 'Falta de designación de personal para la función de compliance de la CGR.', NULL, NULL, NULL, NULL, NULL, 'No se tiene designado al profesional que realizará las labores de la función de compliance de la CGR.', '2024-06-26 20:55:18', '2024-06-26 21:34:42'),
(7, 3, 'cinco_porques', '¿Por qué no se evidencia que se haya designado la función de compliance en la CGR ni que se implementen los principios de acceso directo, independencia, autoridad y competencia adecuada de la función de compliance?\r\nNo se ha designado la función de compliance en la CGR ni implementado los principios de acceso directo, independencia, autoridad y competencia adecuada de la función de compliance porque no se  tenia identificado al profesional que cumpla con las competencias establecidas para el Oficial Compliance. Durante la ejecucion de la auditoría interna se estaba evaluando al profesional idóneo.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No se tenía identificado al profesional que cumpla con las competencias establecidas para el Oficial Compliance.', '2024-06-26 22:03:35', '2024-06-26 22:10:27'),
(8, 4, 'cinco_porques', '¿Por qué no se evidencia que se haya aprobado, implementado, comunicado la Política de Compliance y que esté disponible para las partes interesadadas?\r\nPorque al momento de la auditoria interna se encontraba en proceso de revisión para aprobación.', '¿Por qué al momento de la auditoria interna la Política de Compliance se encontraba en proceso de revisión?\r\nDada la relevancia y el alcance previsto para el Sistema de Gestión de Compliance (SGCM), para la evaluación de la Politica del SGCM se solicitó opinión a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Se solicitó opinión a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR.', '2024-06-26 22:26:50', '2024-06-26 22:30:01'),
(9, 5, 'cinco_porques', '¿Por qué no se evidencia que se haya aprobado y comunicado los Objetivos de Compliance en las funciones y niveles relevantes?\r\nPorque al momento de la auditoria interna se encontraba en proceso de revision para aprobación', 'Por qué al momento de la auditoria interna los Objetivos de Compliance se encontraban en proceso de revisión?\r\nDada la relavancia y el alcance previsto para el Sistema de Gestión de Compliance (SGCM), para la evaluación de los Objetivos de Copmpliance se solicitó opinion a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Se solicitó opinion a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR.', '2024-06-26 22:41:25', '2024-06-26 22:41:56'),
(10, 6, 'cinco_porques', 'Por qué no se encontraba adecudamente implementando el proceso Recepción y Evaluación de Denuncias?\r\nC1: El personal entrevistado actualmente se encarga de brindar asistencia técnica y capacitación al proceso de recepción y evaluación de denuncia', 'Por qué el personal ya no esta a cargo del proceso de recepciòn y evaluaciòn de denuncias?\r\nC2: Debido a cambios en la estructura de la organización los cuales quedan reflejados en el documento Reglamento de Oganización y Funciones vigente desde el 3 de enero 2024 y que no se tomaron acciones ante estos cambios.', 'Por qué no se tomaron acciones antes los cambios de la estructura de la organización con respecto al proceso de recepciòn y evaluaciòn de denuncias?\r\nC3: Debido a que se encontraba en implementación de actividades para la adecuación de la norma ISO 37301 y se priorizó estas actividades.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'En el transcurso de las actividades de implementación hubieron cambios en la estructura de la organización (ROF) que no fueron contemplados en el Alcance del SGCM.', '2024-06-27 15:20:21', '2024-06-27 15:20:21'),
(11, 7, 'cinco_porques', '¿Por qué no se evidenció los resultados de la medición de los indicadores relacionados al logro de los Objetivos de Compliance?\r\nC1: Durante el proceso de auditoría, la Política y Objetivos de compliance se encontraban en proceso de revisión.', '¿Se cuenta con indicadores para medir estos Objetivos de Compliance?\r\nC2: Si bien se cuenta con indicadores para la planificación del objetivos del SGCM, estos aún no están incoporados en los procesos.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Los indicadores para la planificación del objetivos del SGCM, aún no están incoporados en los procesos.', '2024-06-27 15:34:43', '2024-06-27 15:34:43'),
(12, 9, 'cinco_porques', '¿Por qué el personal de la Gerencia Regional de Control de Ancash, en el desarrollo de la auditoría no logró acceder a los documentos del SGCM Determinación del contexto, MIRO, MCAR y MIPOC)?\r\nC1: Porque no fueron enviados los documentos aprobados (Determinación del contexto y la MIRO) al personal de Visita de Control.\r\nC2: Porque el personal no tuvo la orientación adecuada para afrontar la auditoría.\r\nC3: Por falta de coordinación para la realización de la auditoría interna.\r\nC4: Porque el personal desconocía las políticas y objetivos del SGCM.\r\nC5: Porque el personal no tuvo clara la ruta de acceso a los documentos publicados en la intranet (MCAR y MIPOC)', '¿Por qué no fueron enviados los documentos (Determinación del contexto y MIRO) al personal de visita de control?\r\nC1.2: Por que la Vicecontraloría de Control Sectorial y Territorial estuvo a cargo de 9 procesos que simultáneamente se estaba implementando para el Sistema de Gestión Compliance, en el cual se brindó prioridad a la aprobación de los documentos correspondientes y no al envío de la información al personal.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Se brindó prioridad a la aprobación de los documentos correspondientes y no al envío de la información al personal.', '2024-06-27 16:20:26', '2024-06-27 16:20:26'),
(13, 8, 'cinco_porques', '¿Por qué no se tienen aprobados, codificados , migrado al formato vigente y usado los documentos vigentes en los diferentes casos?\r\nDesconocimiento por parte de las Unidades Orgánicas de la aplicación del procedimiento PR-MODER-06 \"Gestión de Documentos del SIG\", que contempla el control documental de los documentos del SIG de la CGR.', '¿Por qué hay desconocimiento por parte de las Unidades Orgánicas de la aplicación del procedimiento PR-MODER-06 \"Gestión de Documentos del SIG\"?\r\nFalta de capacitación a los facilitadores de las UO en relación a la aplicación del del procedimiento PR-MODER-06 \"Gestión de Documentos del SIG.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Desconocimiento y limitada capacitación en  la aplicación del procedimiento PR-MODER-06 \"Gestión de Documentos del SIG\",', '2024-07-01 17:19:12', '2024-07-01 17:19:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores`
--

CREATE TABLE `indicadores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_indicador` enum('Producto','Servicio','Resultado','Calidad') NOT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `producto` text NOT NULL,
  `cliente` text NOT NULL,
  `planificacion_sig_id` bigint(255) NOT NULL,
  `planificacion_sig_estado` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `formula` varchar(255) NOT NULL,
  `frecuencia` enum('mensual','trimestral','semestral','anual') NOT NULL,
  `meta` double(8,2) NOT NULL,
  `tipo_agregacion` enum('acumulada','no acumulada') NOT NULL,
  `parametro_medida` enum('ratio','porcentaje','numero','indice','tasa','promedio') NOT NULL,
  `sentido` enum('ascendente','lineal','descendente') NOT NULL,
  `var1` varchar(255) DEFAULT NULL,
  `var2` varchar(255) DEFAULT NULL,
  `var3` varchar(255) DEFAULT NULL,
  `var4` varchar(255) DEFAULT NULL,
  `var5` varchar(255) DEFAULT NULL,
  `var6` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicadores`
--

INSERT INTO `indicadores` (`id`, `tipo_indicador`, `proceso_id`, `producto`, `cliente`, `planificacion_sig_id`, `planificacion_sig_estado`, `estado`, `nombre`, `descripcion`, `formula`, `frecuencia`, `meta`, `tipo_agregacion`, `parametro_medida`, `sentido`, `var1`, `var2`, `var3`, `var4`, `var5`, `var6`, `created_at`, `updated_at`) VALUES
(1, 'Producto', 35, 'Texto Único de Procedimientos Administrativos - TUPA', 'Órganos y unidades orgánicas que tienen a cargo algún procedimiento administrativo consignado en el TUPA, Ciudadanía, Entidades', 2, 1, 2, 'Porcentaje de Procedimientos del TUPA actualizados', 'Medir la actualización deel TUPA de la CGR', 'var1/var2', 'mensual', 0.78, 'no acumulada', 'porcentaje', 'ascendente', 'Cantidad de  Procedimientos Administrativos (PA) actualizados', 'Total de PA', NULL, NULL, NULL, NULL, '2023-05-26 23:01:48', '2024-06-04 04:59:22'),
(2, 'Producto', 35, 'Nuevo', 'Manuelito', 2, 0, 2, 'Indicador prueba', 'Prueba', 'var1/var2', 'trimestral', 0.90, 'acumulada', 'tasa', 'ascendente', 'var1', 'var2', NULL, NULL, NULL, NULL, '2024-06-04 21:06:47', '2024-06-06 03:55:02'),
(3, 'Producto', 8, 'Ejempñpo', 'Ejemplo', 3, 0, 2, 'Ejemploi', 'Ejempó', 'var1+var2+var3', 'mensual', 120.00, 'no acumulada', 'ratio', 'ascendente', 'v1', 'v2', 'v3', NULL, NULL, NULL, '2024-06-05 05:02:47', '2024-06-05 05:02:47');

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
-- Estructura de tabla para la tabla `indicadores_proceso_ouo`
--

CREATE TABLE `indicadores_proceso_ouo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_proceso_ouo` bigint(20) UNSIGNED NOT NULL,
  `id_indicador` bigint(20) UNSIGNED NOT NULL,
  `meta` double(8,2) NOT NULL,
  `year` char(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores_seguimiento`
--

CREATE TABLE `indicadores_seguimiento` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `indicador_id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `meta` double(8,5) DEFAULT 0.00000,
  `valor` double(8,2) DEFAULT 0.00,
  `estado` varchar(100) NOT NULL,
  `var1` double(8,2) DEFAULT 0.00,
  `var2` double(8,2) DEFAULT 0.00,
  `var3` double(8,2) DEFAULT 0.00,
  `var4` double(8,2) DEFAULT 0.00,
  `var5` double(8,2) DEFAULT 0.00,
  `var6` double(8,2) DEFAULT 0.00,
  `evidencias` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicadores_seguimiento`
--

INSERT INTO `indicadores_seguimiento` (`id`, `indicador_id`, `fecha`, `meta`, `valor`, `estado`, `var1`, `var2`, `var3`, `var4`, `var5`, `var6`, `evidencias`, `created_at`, `updated_at`) VALUES
(266, 1, '2023-01-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(267, 1, '2023-02-28', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(268, 1, '2023-03-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(269, 1, '2023-04-30', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(270, 1, '2023-05-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(271, 1, '2023-06-30', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(272, 1, '2023-07-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(273, 1, '2023-08-31', 0.78000, 1.00, 'bueno', 3.00, 3.00, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-05 04:34:26'),
(274, 1, '2023-09-30', 0.78000, 1.00, 'bueno', 12.00, 12.00, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-05 04:34:36'),
(275, 1, '2023-10-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(276, 1, '2023-11-30', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(277, 1, '2023-12-31', 0.78000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(282, 3, '2023-01-31', 120.00000, 39.00, 'malo', 12.00, 13.00, 14.00, NULL, NULL, NULL, NULL, NULL, '2024-06-05 21:07:47'),
(283, 3, '2023-02-28', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(284, 3, '2023-03-31', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(285, 3, '2023-04-30', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(286, 3, '2023-05-31', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(287, 3, '2023-06-30', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(288, 3, '2023-07-31', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(289, 3, '2023-08-31', 120.00000, 42.00, 'malo', 12.00, 14.00, 16.00, NULL, NULL, NULL, NULL, NULL, '2024-06-05 21:08:13'),
(290, 3, '2023-09-30', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(291, 3, '2023-10-31', 120.00000, 360.00, 'bueno', 120.00, 120.00, 120.00, NULL, NULL, NULL, NULL, NULL, '2024-06-05 21:08:31'),
(292, 3, '2023-11-30', 120.00000, 135.00, 'bueno', 45.00, 45.00, 45.00, NULL, NULL, NULL, NULL, NULL, '2024-06-05 21:19:27'),
(293, 3, '2023-12-31', 120.00000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(294, 2, '2023-03-31', 0.22500, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(295, 2, '2023-06-30', 0.45000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(296, 2, '2023-09-30', 0.67500, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL),
(297, 2, '2023-12-31', 0.90000, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, NULL);

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
-- Estructura de tabla para la tabla `inventario_procesos`
--

CREATE TABLE `inventario_procesos` (
  `id_inventario` bigint(20) UNSIGNED NOT NULL,
  `id_proceso` bigint(20) UNSIGNED NOT NULL,
  `id_ouo_responsable` bigint(20) UNSIGNED NOT NULL,
  `id_ouo_delegada` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
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
(64, '2023_06_01_231039_create_propietarios_table', 4),
(66, '2023_08_09_144635_create_indicadores', 5),
(68, '2023_08_09_153856_create_indicadores__seguimiento', 6),
(69, '2023_08_09_162426_create_indicadores__historico', 6),
(71, '2023_08_18_232019_create_planificacion_sig', 7),
(73, '2023_08_22_205219_create_configuracion_table', 8),
(74, '2023_08_25_150243_create_permission_tables', 9),
(75, '2023_08_25_154137_create_user_proceso_table', 10),
(76, '2014_10_12_100000_create_password_resets_table', 11),
(78, '2023_05_09_232514_create_hallazgos', 12),
(79, '2023_09_19_230946_create_hallazgos_causas', 13),
(80, '2023_09_19_233028_create_hallazgos_acciones', 14),
(81, '2019_12_14_000001_create_personal_access_tokens_table', 15),
(82, '2024_05_06_194734_create_requerimiento', 15),
(86, '2024_05_06_205459_create_requerimiento_movimientos', 16),
(87, '2024_05_06_211747_create_requerimiento_necesidad', 17),
(88, '2024_06_10_190503_create_especialistas_table', 17),
(89, '2024_06_10_191830_create_especialista_hallazgo_table', 18),
(90, '2024_06_11_142926_add_cinco_porques_to_analisis_causa_raiz_table', 19),
(91, '2024_08_27_112811_create_determinacion_contexto', 20),
(94, '2024_08_27_115127_create_contexto_interno', 21),
(95, '2024_08_27_115136_create_contexto_externo', 21),
(96, '2024_08_27_120745_create_contexto_analisis', 22),
(97, '2025_01_16_113704_create_ouo', 23),
(98, '2025_01_16_173807_create_inventario_procesos', 24),
(99, '2025_01_16_175307_create_procesos_ouo', 25),
(100, '2025_01_16_182503_create_indicadores_proceso_ouo', 26);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`model_id`, `model_type`, `role_id`) VALUES
(2, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ouo`
--

CREATE TABLE `ouo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `ouo_padre` bigint(20) UNSIGNED DEFAULT NULL,
  `subgerente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nivel_jerarquico` int(11) NOT NULL,
  `fecha_vigencia_inicio` date NOT NULL,
  `fecha_vigencia_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ouo`
--

INSERT INTO `ouo` (`id`, `nombre`, `codigo`, `ouo_padre`, `subgerente_id`, `nivel_jerarquico`, `fecha_vigencia_inicio`, `fecha_vigencia_fin`, `created_at`, `updated_at`) VALUES
(1, 'Despacho del Contralor', 'D100', NULL, 9, 1, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(2, 'Órgano de Auditoría Interna', 'D200', 1, 10, 2, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(3, 'Procuraduría Pública', 'D900', 1, 11, 2, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(4, 'Oficina de Gestión de la Potestad Administrativa', 'E200', 1, 12, 2, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(5, 'Oficina de Integridad Institucional', 'A260', 1, 13, 2, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(6, 'Tribunal Superior de Responsabilidades Administrativas', 'E300', 1, 14, 2, '2024-11-08', NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
(7, 'Vicecontraloría de Gestión Estratégica, Integridad y Control', 'L110', 1, 15, 2, '2024-11-08', NULL, '2025-01-16 20:27:32', '2025-01-16 20:27:32'),
(8, 'Gerencia de Prevención y Control Social', 'C601', 7, 16, 3, '2024-11-08', NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
(9, 'Gerencia de Análisis de Información para el Control', 'C120', 7, 17, 3, '2024-11-08', NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
(10, 'Gerencia de Recursos Estratégicos', 'D500', 7, 18, 3, '2024-11-08', NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
(11, 'Escuela Nacional de Control', 'D400', 7, 19, 3, '2024-11-08', NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
(12, 'Subgerencia de Prevención e Integridad', 'C370', 8, 20, 4, '2024-11-08', NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
(13, 'Subgerencia de Auditoría de Desempeño', 'L200', 8, 21, 4, '2024-11-08', NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
(14, 'Subgerencia de Participación Ciudadana y Control Social', 'C600', 8, 22, 4, '2024-11-08', NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
(15, 'Subgerencia del Observatorio Anticorrupción', 'C602', 9, 23, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(16, 'Subgerencia de Gestión de Declaraciones Juradas', 'C122', 9, 24, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(17, 'Subgerencia de Fiscalización', 'L1540', 9, 25, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(18, 'Subgerencia de Contrataciones Estratégicas', 'D501', 10, 26, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(19, 'Subgerencia de Gestión de Inversiones', 'C322', 10, 27, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(20, 'Subdirección Académica', 'D401', 11, 19, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(21, 'Subdirección de Posgrado', 'D403', 11, 28, 4, '2024-11-08', NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
(22, 'Vicecontraloría de Control Sectorial y Territorial', 'L100', 1, 30, 2, '2024-11-08', NULL, '2025-01-16 20:41:44', '2025-01-16 20:41:44'),
(23, 'Gerencia de Control Político, Institucional y Económico', 'L301', 22, 31, 3, '2024-11-08', NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
(24, 'Gerencia de Control de Servicios Públicos Básicos', 'L303', 22, 32, 3, '2024-11-08', NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
(25, 'Gerencia de Control de Megaproyectos', 'L304', 22, 33, 3, '2024-11-08', NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
(26, 'Subgerencia de Control del Sector Seguridad Interna y Externa', 'L340', 23, 34, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(27, 'Subgerencia de Control del Sector Justicia, Político y Electoral', 'L352', 23, 35, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(28, 'Subgerencia de Control del Sector Social y Cultura', 'L315', 23, 36, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(29, 'Subgerencia de Control del Sector Económico y Financiero', 'L320', 23, 37, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(30, 'Subgerencia de Control del Sector Productivo y Trabajo', 'L330', 23, 38, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(31, 'Subgerencia de Control del Sector Transportes y Comunicaciones', 'L331', 24, 39, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(32, 'Subgerencia de Control del Sector Vivienda, Construcción y Saneamiento', 'L336', 24, 40, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(33, 'Subgerencia de Control del Sector Agricultura y Ambiente', 'L332', 24, 32, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(34, 'Subgerencia de Control del Sector Educación', 'L351', 24, 41, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(35, 'Subgerencia de Control del Sector Salud', 'L316', 24, 42, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(36, 'Subgerencia de Control de Universidades', 'L353', 24, 43, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(37, 'Subgerencia de Control de Megaproyectos', 'L334', 25, 184, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(38, 'Subgerencia de Control de Asociaciones Público Privadas y Obras por Impuestos', 'C920', 25, 44, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(39, 'Subgerencia de Control Previo de Adicionales de Obra', 'L556', 25, 33, 4, '2024-11-08', NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
(40, 'Secretaría General', 'D300', 1, 46, 2, '2024-11-08', NULL, '2025-01-16 21:01:05', '2025-01-16 21:01:05'),
(41, 'Oficina de Seguridad y Defensa Nacional', 'D531', 40, 47, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(42, 'Gerencia de Administración', 'C200', 40, 48, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(43, 'Gerencia de Capital Humano', 'D550', 40, 50, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(44, 'Gerencia de Tecnologías de la Información', 'D600', 40, 53, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(45, 'Gerencia de Comunicación Corporativa', 'C401', 40, 57, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(46, 'Gerencia de Asesoría Jurídica y Normatividad en Control Gubernamental', 'D700', 40, 60, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(47, 'Gerencia de Modernización y Planeamiento', 'L527', 40, 63, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(48, 'Gerencia de Relaciones Institucionales', 'C381', 40, 67, 3, '2024-11-08', NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
(69, 'Subgerencia de Abastecimiento', 'D530', 42, NULL, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(70, 'Subgerencia de Gestión Documentaria', 'D320', 42, 49, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(71, 'Subgerencia de Políticas y Desarrollo Humano', 'D517', 43, 50, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(72, 'Subgerencia de Personal y Compensaciones', 'D510', 43, 51, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(73, 'Subgerencia de Bienestar y Relaciones Laborales', 'D511', 43, 52, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(74, 'Subgerencia de Sistemas de Información', 'D610', 44, 54, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(75, 'Subgerencia de Operaciones y Plataforma Tecnológica', 'D602', 44, 55, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(76, 'Subgerencia de Gobierno Digital', 'D603', 44, 56, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(77, 'Subgerencia de Prensa', 'C360', 45, 58, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(78, 'Subgerencia de Imagen y Relaciones Corporativas', 'D310', 45, 59, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(79, 'Subgerencia de Comunicación y Medios Digitales', 'C402', 45, 58, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(80, 'Subgerencia de Asesoría Jurídica', 'D710', 46, 60, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(81, 'Subgerencia de Normatividad en Control Gubernamental', 'C312', 46, 61, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(82, 'Subgerencia de Aseguramiento de la Calidad', 'L157', 46, 62, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(83, 'Subgerencia de Planeamiento, Presupuesto y Programación de Inversiones', 'L520', 47, 64, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(84, 'Subgerencia de Seguimiento y Evaluación del SNC', 'L590', 47, 65, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(85, 'Subgerencia de Modernización', 'C321', 47, 66, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(86, 'Subgerencia de Coordinación Parlamentaria', 'C380', 48, 67, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(87, 'Subgerencia de Coordinación Institucional Nacional', 'C382', 48, 68, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
(88, 'Subgerencia de Cooperación y Asuntos Internacionales', 'D800', 48, 69, 4, '2024-11-08', NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Módulo Indicadores', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(2, 'Modulo Procesos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(3, 'Modulo Hallazgos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(4, 'Modulo Riesgos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30');

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
  `sigla` varchar(6) DEFAULT NULL,
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

INSERT INTO `procesos` (`id`, `cod_proceso`, `nombre`, `sigla`, `tipo_proceso`, `cod_proceso_padre`, `estado`, `inactivate_at`, `created_at`, `updated_at`) VALUES
(1, 'PE01', 'Gestión Estratégica', NULL, 'Estratégico', NULL, 1, NULL, '2023-05-26 23:01:48', '2023-06-02 04:00:48'),
(2, 'PE02', 'Desarrollo Institucional', NULL, 'Estratégico', NULL, 1, NULL, NULL, NULL),
(3, 'PE03', 'Comunicación y Relaciones Interinstitucionales', NULL, 'Estratégico', NULL, 1, NULL, NULL, NULL),
(4, 'PM01', 'Prevención y Detección de la Corrupción', NULL, 'Misional', NULL, 1, NULL, NULL, NULL),
(5, 'PM02', 'Atención a las Entidades y Partes Interesadas', NULL, 'Misional', NULL, 1, NULL, NULL, NULL),
(6, 'PM03', 'Realización de los Servicios de Control Simultáneo, Posterior y Relacionados', NULL, 'Misional', NULL, 1, NULL, NULL, NULL),
(7, 'PM04', 'Gestión de Sanciones y Procesos Judiciales', NULL, 'Misional', NULL, 1, NULL, NULL, NULL),
(8, 'PM05', 'Gestión de los Resultados del Control', NULL, 'Misional', NULL, 1, NULL, NULL, NULL),
(9, 'PA01', 'Gestión del Capital Humano', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(10, 'PA02', 'Gestión de Activos Documentarios', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(11, 'PA03', 'Gestión de Abastecimiento', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(12, 'PA04', 'Gestión Financiera', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(13, 'PA05', 'Gestión de Tecnologías de la Información y Comunicaciones', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(14, 'PA06', 'Gestión Jurídico Legal', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(15, 'PA07', 'Gestión de la Seguridad', NULL, 'Apoyo', NULL, 1, NULL, NULL, NULL),
(30, 'PE01.01', 'Planeamiento Estratégico', 'PEI', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(31, 'PE01.02', 'Gestión de Entidades Sujetad a Control', 'GESC', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(32, 'PE01.03', 'Planeamiento Operativo', 'POI', 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(33, 'PE01.04', 'Control Institucional', NULL, 'Estratégico', 'PE01', 1, NULL, '2023-08-09 17:21:22', NULL),
(34, 'PE02.01', 'Diseño Organizacional', 'DEO', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(35, 'PE02.02', 'Gestión de la Modernización', NULL, 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(36, 'PE02.03', 'Gestión Normativa', 'GNOR', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(37, 'PE02.04', 'Gestión de la Inversión', 'PROY', 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(38, 'PE02.05', 'Gestión del Conocimiento', NULL, 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(39, 'PE02.06', 'Gestión de la Continuidad del Negocio', NULL, 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(40, 'PE02.07', 'Gestión de la Integridad Institucional', NULL, 'Estratégico', 'PE02', 1, NULL, '2023-08-09 17:21:22', NULL),
(41, 'PE03.01', 'Gestión de la Comunicación Institucional', NULL, 'Estratégico', 'PE03', 1, NULL, '2023-08-09 17:21:22', NULL),
(42, 'PE03.02', 'Gestión de las Relaciones Interinstitucionales', NULL, 'Estratégico', 'PE03', 1, NULL, '2023-08-09 17:21:22', NULL),
(56, 'PM01.01', 'Gestión de mecanismos de prevención y detección de la corrupción', NULL, 'Misional', 'PM01', 1, NULL, '2023-08-09 17:28:23', NULL),
(57, 'PM01.02', 'Participación ciudadana', NULL, 'Misional', 'PM01', 1, NULL, '2023-08-09 17:28:23', NULL),
(58, 'PM02.01', 'Atención de la demanda imprevisible de control', NULL, 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(59, 'PM02.02', 'Atención de pedidos de información y solicitudes de opinión', NULL, 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(60, 'PM02.03', 'Atención de quejas y reclamos', NULL, 'Misional', 'PM02', 1, NULL, '2023-08-09 17:28:23', NULL),
(61, 'PM03.01', 'Programación de los servicios de control y de fiscalización', NULL, 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(62, 'PM03.02', 'Realización de los servicios de control simultáneo', NULL, 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(63, 'PM03.03', 'Realización de los servicios de control posterior', NULL, 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(64, 'PM03.04', 'Realización de los servicios relacionados', NULL, 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(65, 'PM03.05', 'Supervisión técnica y revisión de oficio de los servicios de control', NULL, 'Misional', 'PM03', 1, NULL, '2023-08-09 17:28:23', NULL),
(66, 'PM04.01', 'Gestión de sanciones administrativas', 'GSAD', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(67, 'PM04.02', 'Gestión del procedimiento sancionador por infracción al ejercicio del control gubernamental', 'GPSA', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(68, 'PM04.03', 'Gestión de los procesos judiciales resultantes de los servicios de control', '', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
(69, 'PM05.01', 'Seguimiento y evaluación a la implementación de las recomendaciones, acciones y pronunciamientos, resultados de los servicios de control', 'SEIR', 'Misional', 'PM05', 1, NULL, '2023-08-09 17:28:23', NULL),
(70, 'PM05.02', 'Desarrollo de buenas prácticas y propuestas de mejora para la gestión de las entidades', 'DBPM', 'Misional', 'PM05', 1, NULL, '2023-08-09 17:28:23', NULL),
(71, 'PA01.01', 'Planificación del capital humano', 'PLCH', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(72, 'PA01.02', 'Incorporación del capital humano', 'INCH', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(73, 'PA01.03', 'Desarrollo del capital humano', 'DECH', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(74, 'PA01.04', 'Administración del capital humano', 'ADCH', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(75, 'PA01.05', 'Gestión del bienestar del capital humano', 'GBCH', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(76, 'PA01.06', 'Gestión del jefe y personal del OCI', 'GOCI', 'Apoyo', 'PA01', 1, NULL, '2023-08-09 18:30:44', NULL),
(77, 'PA02.01', 'Planificación del activo documentario', 'PDAD', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(78, 'PA02.02', 'Recepción de documentos', 'RDGD', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(79, 'PA02.03', 'Clasificación, reclasificación y desclasificación de documentos secretos y reservados', 'CRDD', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(80, 'PA02.04', 'Distribución de documentos y valijas', 'MSJ', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(81, 'PA02.05', 'Archivo, custodia y conservación de documentos', 'ARCH', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(82, 'PA02.06', 'Autenticación de firmas y certificación de documentos', 'AFCD', 'Apoyo', 'PA02', 1, NULL, '2023-08-09 18:30:44', NULL),
(83, 'PA03.01', 'Elaboración del plan anual de contrataciones', 'PNCO', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(84, 'PA03.02', 'Contratación de bienes y servicios', 'ACBS', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(85, 'PA03.03', 'Gestión de bienes patrimoniales', 'GBPA', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(86, 'PA03.04', 'Gestión de almacén', 'GALM', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(87, 'PA03.05', 'Administración de servicios generales', 'ADSG', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(88, 'PA03.06', 'Gestión de sociedades de auditoria', 'GSOA', 'Apoyo', 'PA03', 1, NULL, '2023-08-09 18:30:44', NULL),
(89, 'PA04.01', 'Programación multianual, formulación y aprobación del presupuesto', NULL, 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(90, 'PA04.02', 'Ejecución presupuestal', 'EJPR', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(91, 'PA04.03', 'Evaluación presupuestal', 'EVPR', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(92, 'PA04.04', 'Gestión contable', 'CONT', 'Apoyo', 'PA04', 1, NULL, '2023-08-09 18:30:44', NULL),
(93, 'PA05.01', 'Planificación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(94, 'PA05.02', 'Implementación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(95, 'PA05.03', 'Operación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 'PA05', 1, NULL, '2023-08-09 18:30:44', NULL),
(96, 'PA06.01', 'Gestión y difusión de productos de interés legal', NULL, 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(97, 'PA06.02', 'Gestión de los procesos judiciales de la CGR', 'GPRJ', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(98, 'PA06.03', 'Gestión de los procesos arbitrales de la CGR', NULL, 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(99, 'PA06.04', 'Defensa legal de los colaboradores y ex colaboradores', NULL, 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(100, 'PA06.05', 'Absolución de consultas internas de carácter jurídico', 'ACCJ', 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
(101, 'PA07.01', 'Gestión de prevención de riesgos de desastres', NULL, 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL),
(102, 'PA07.02', 'Operación de la gestión de la seguridad', NULL, 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL),
(103, 'PA07.03', 'Fomento de una cultura de seguridad', NULL, 'Apoyo', 'PA07', 1, NULL, '2023-08-09 18:30:44', NULL),
(104, 'PM06', 'Gestión Educativa', NULL, 'Misional', NULL, 1, NULL, '2023-09-27 15:13:56', NULL),
(105, 'PE02.02.02', 'Administración de los Sistemas de Gestión', 'MODER', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:38:59', NULL),
(106, 'PE02.02.03', 'Gestión de la Calidad', 'SGC', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:45:13', NULL),
(107, 'PE02.02.04', 'Gestión de Riesgos', 'SGR', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:45:13', NULL),
(108, 'PE02.02.05', 'Gestión del Control Interno', NULL, 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:47:23', NULL),
(109, 'PE02.02.06', 'Gestión Antisoborno', 'SGAS', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:47:23', NULL),
(110, 'PE02.02.07', 'Gestión de la Simplificación Administrativa', 'SIMP', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:51:57', NULL),
(111, 'PE02.02.08', 'Aseguramiento de la Calidad', 'ACAL', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:51:57', NULL),
(112, 'PE02.02.09', 'Gestión de la Seguridad de la Información', 'SGSI', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:51:57', NULL),
(113, 'PE02.02.01', 'Gestión por Procesos', 'PROC', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:53:40', NULL),
(114, 'PE02.02.10', 'Gestión de Compliance', 'SGCM', 'Estratégico', 'PE02.02', 1, NULL, '2024-06-19 14:53:40', NULL),
(115, 'PE02.03.01', 'Gestión de Inciativas Legislativas', 'GNIL', 'Estratégico', 'PE02.03', 1, NULL, '2024-06-19 15:11:26', NULL),
(116, 'PE02.03.02', 'Gestión de Documentos Normativos', 'GNDN', 'Estratégico', 'PE02.03', 1, NULL, '2024-06-19 15:11:26', NULL),
(117, 'PE02.03.03', 'Gestión de documentos en el Alcance del SIG', 'NORM', 'Estratégico', 'PE02.03', 1, NULL, '2024-06-19 15:11:26', NULL),
(118, 'PA05.03.01', 'Respaldo de información', 'REST', 'Apoyo', 'PA05.03', 1, NULL, '2024-06-25 17:18:37', NULL),
(119, 'PA05.03.02', 'Atención de requeremientos de recursos informáticos', 'MDA', 'Apoyo', 'PA05.03', 1, NULL, '2024-06-25 17:34:32', NULL),
(120, 'PA05.03.03', 'Seguimiento y control de los servicios de tecnologías de información y comunicaciones', 'SCST', 'Apoyo', 'PA05.03', 1, NULL, '2024-06-25 17:37:25', NULL),
(121, 'PA05.03.04', 'Mantenimiento preventivo y correctivo de activos informáticos y de comunicaciones', 'MTNE', 'Apoyo', 'PA05.03', 1, NULL, '2024-06-25 17:38:13', NULL),
(122, 'PA05.02.01', 'Desarrollo de arquitectura informática y de comunicaciones', 'DACO', 'Apoyo', 'PA05.02', 1, NULL, '2024-06-25 17:42:35', NULL),
(123, 'PA05.02.02', 'Desarrollo de soluciones', 'DSO', 'Apoyo', 'PA05.02', 1, NULL, '2024-06-25 17:43:08', NULL),
(125, 'PM03.02.01', 'Visita de Control', 'VICO', 'Misional', 'PM03.02', 1, NULL, '2024-06-25 18:43:12', NULL),
(126, 'PM03.02.02', 'Orientación de oficio', 'OROF', 'Misional', 'PM03.02', 1, NULL, '2024-06-25 18:43:12', NULL),
(127, 'PM03.02.03', 'Control Concurrente', 'COCO', 'Misional', 'PM03.02', 1, NULL, '2024-06-25 18:45:44', NULL),
(128, 'PM03.02.04', 'Operativo de Control Simultaneo', 'OCOS', 'Misional', 'PM03.02', 1, NULL, '2024-06-25 18:45:44', NULL),
(129, 'PM02.01.01', 'Realización de los servicios de control previo', NULL, 'Misional', 'PM02.01', 1, NULL, '2024-06-25 18:49:07', NULL),
(130, 'PM02.01.01.01', 'Evaluación de prestaciones de adicionales de obra', 'EAOB', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 18:52:50', NULL),
(131, 'PM02.01.01.02', 'Evaluación de recursos de apelación de prestaciones adicionales de obra', 'APAO', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(132, 'PM02.01.01.03', 'Evaluación de prestaciones adicionales de supervisión de obra', 'EPAS', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(133, 'PM02.01.01.04', 'Evaluación de recursos de apelación de prestaciones adicionales de supervisión de obra', 'APAS', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(134, 'PM02.01.01.05', 'Evaluación de solicitudes de emisión de informe previo a las operaciones de asociaciones público privadas y obras por impuestos', 'ESIP', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(135, 'PM02.01.01.06', 'Evaluación de solicitudes de emisión de informe previo a las operaciones de endeudamiento público interno y externo', 'ESIE', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(136, 'PM02.01.01.07', 'Emisión de opinión previa a las compras con carácter de secreto militar o de orden interno', 'EOPM', 'Misional', 'PM02.01.01', 1, NULL, '2024-06-25 19:02:26', NULL),
(137, 'PM02.02.01.01', 'Atención de solicitudes de acceso a la información pública', 'SAIP', 'Misional', 'PM02.02.01', 1, NULL, '2024-06-25 19:08:40', NULL),
(138, 'PM02.02.01.02', 'Atención de requerimientos de información del congreso', 'ARIC', 'Misional', 'PM02.02.01', 1, NULL, '2024-06-25 19:08:40', NULL),
(139, 'PM02.02.01.03', 'Atención de requerimientos de información de entidades', 'ARIE', 'Misional', 'PM02.02.01', 1, NULL, '2024-06-25 19:08:40', NULL),
(140, 'PM02.02.02.01', 'Atención de consulta legal externa respecto a la interpretación y alcance de la normativa de servicios de control o servicios relacionados', 'ACLE', 'Misional', 'PM02.02.02', 1, NULL, '2024-06-25 19:08:40', NULL),
(141, 'PM02.02.02.02', 'Atención de solicitudes de opinión sobre proyectos de ley y otras normas con rango de ley', 'ASOL', 'Misional', 'PM02.02.02', 1, NULL, '2024-06-25 19:08:40', NULL),
(142, 'PM03.03.01', 'Auditoría de cumplimiento', 'ACUM', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(143, 'PM03.03.02', 'Auditoría de desempeño', 'ADES', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(144, 'PM03.03.03', 'Auditoría financiera', 'AFIN', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(145, 'PM03.03.04', 'Auditoría de la Cuenta General de la República', 'ACGR', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(146, 'PM03.03.05', 'Servicio de control específico a hechos con presunta irregularidad', 'SCEH', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(147, 'PM03.03.06', 'Acción de oficio posterior', 'AOPO', 'Misional', 'PM03.03', 1, NULL, '2024-06-25 19:12:46', NULL),
(148, 'PE03.01.01', 'Diseño del plan de comunicación corporativa', 'CODP', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(149, 'PE03.01.02', 'Gestión de la comunicación interna', 'COGI', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(150, 'PE03.01.03', 'Organización y ejecución de eventos para la promoción de la imagen y desarrollo institucional', 'COEI', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(151, 'PE03.01.04', 'Gestión de la publicación institucional', 'COGP', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(152, 'PE03.01.05', 'Actualización de contenidos del portal de transparencia estándar de la contraloría general de la república', 'COPT', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(153, 'PE03.01.06', 'Gestión de prensa', 'COPR', 'Estratégico', 'PE03.01', 1, NULL, '2024-06-25 19:25:53', NULL),
(154, 'PE03.02.01', 'Diseño de la estrategia de relacionamiento interinstitucional', 'GRDE', 'Estratégico', 'PE03.02', 1, NULL, '2024-06-25 19:25:53', NULL),
(155, 'PE03.02.02', 'Atención de necesidades interinstitucionales de representación de autoridades y funcionarios de la cgr', 'GRRE', 'Estratégico', 'PE03.02', 1, NULL, '2024-06-25 19:25:53', NULL),
(156, 'PE03.02.03', 'Gestión de la representación institucional en eventos internacionales', 'GRRI', 'Estratégico', 'PE03.02', 1, NULL, '2024-06-25 19:25:53', NULL),
(157, 'PE03.02.04', 'Gestión de las necesidades institucionales de cooperación técnica y financiera', 'GRCT', 'Estratégico', 'PE03.02', 1, NULL, '2024-06-25 19:25:53', NULL),
(158, 'PE03.02.05', 'Gestión de instrumentos de cooperación', 'GICO', 'Estratégico', 'PE03.02', 1, NULL, '2024-06-25 19:25:53', NULL),
(159, 'PA04.02.01', 'Control de la disponibilidad de los créditos presupuestarios', 'PRCP', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(160, 'PA04.02.02', 'Gestión de la modificación presupuestal a nivel institucional', 'PRMP', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(161, 'PA04.02.03', 'Modificación presupuestal a nivel funcional programático', 'PRFP', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(162, 'PA04.02.04', 'Ejecución de ingresos', 'EDIN', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(163, 'PA04.02.05', 'Ejecución del gasto', 'EDGE', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(164, 'PA04.02.06', 'Gestión de viáticos', 'GVIA', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(165, 'PA04.02.07', 'Gestión del fondo de caja chica', 'GFCC', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(166, 'PA04.02.08', 'Gestión de anticipos', 'GANT', 'Apoyo', 'PA04.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(167, 'PA03.02.01', 'Formulación del requerimiento para la contratación de bienes y servicios', 'BSRC', 'Apoyo', 'PA03.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(168, 'PA03.02.02', 'Procesos de selección', 'BSPS', 'Apoyo', 'PA03.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(169, 'PA03.02.03', 'Contrataciones de bienes y servicios excluidas de la norma', 'BSEX', 'Apoyo', 'PA03.02', 1, NULL, '2024-06-25 19:39:57', NULL),
(194, 'PA01.01.01', 'Diseño de estrategias, políticas y herramientas para la gestión del capital humano', 'DEPH', 'Apoyo', 'PA01.01', 1, NULL, '2024-06-25 20:04:19', NULL),
(195, 'PA01.01.02', 'Planificación de recursos humanos', 'PLRH', 'Apoyo', 'PA01.01', 1, NULL, '2024-06-25 20:04:19', NULL),
(196, 'PA01.01.03', 'Administración de puestos y perfiles', 'APPE', 'Apoyo', 'PA01.01', 1, NULL, '2024-06-25 20:04:19', NULL),
(197, 'PA01.02.01', 'Reclutamiento y selección', 'REYS', 'Apoyo', 'PA01.02', 1, NULL, '2024-06-25 20:04:19', NULL),
(198, 'PA01.02.02', 'Vinculación de personal', 'VIPE', 'Apoyo', 'PA01.02', 1, NULL, '2024-06-25 20:04:19', NULL),
(199, 'PA01.02.03', 'Inducción de personal', 'INPE', 'Apoyo', 'PA01.02', 1, NULL, '2024-06-25 20:04:19', NULL),
(200, 'PA01.02.04', 'Designación de personal en puestos de confianza', 'DPPC', 'Apoyo', 'PA01.02', 1, NULL, '2024-06-25 20:04:19', NULL),
(201, 'PA01.03.01', 'Gestión de la capacitación', 'GCAP', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(202, 'PA01.03.02', 'Gestión del rendimiento', 'GREN', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(203, 'PA01.03.03', 'Gestión de incentivos', 'GINC', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(204, 'PA01.03.04', 'Progresión de la carrera', 'PCPE', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(205, 'PA01.03.05', 'Convocatoria interna', 'COIN', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(206, 'PA01.03.06', 'Traslado y encargo del personal', 'TREP', 'Apoyo', 'PA01.03', 1, NULL, '2024-06-25 20:04:19', NULL),
(207, 'PA01.04.01', 'Gestión de las compensaciones', 'GCOM', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(208, 'PA01.04.02', 'Atención de solicitudes de personal', 'ASPE', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(209, 'PA01.04.03', 'Gestión de seguros', 'GSEG', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(210, 'PA01.04.04', 'Administración de información de personal', 'AIPE', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(211, 'PA01.04.05', 'Proceso disciplinario de personal', 'PADP', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(212, 'PA01.04.06', 'Desvinculación de personal', 'DEPE', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(213, 'PA01.04.07', 'Entrega y recepción de puesto de los servidores', 'ERPS', 'Apoyo', 'PA01.04', 1, NULL, '2024-06-25 20:04:19', NULL),
(214, 'PA01.05.01', 'Seguridad y salud en el trabajo', 'SYST', 'Apoyo', 'PA01.05', 1, NULL, '2024-06-25 20:04:19', NULL),
(215, 'PA01.05.02', 'Relaciones labores individuales y colectivas', 'RLIC', 'Apoyo', 'PA01.05', 1, NULL, '2024-06-25 20:04:19', NULL),
(216, 'PA01.05.03', 'Cultura y clima organizacional', 'CCOR', 'Apoyo', 'PA01.05', 1, NULL, '2024-06-25 20:04:19', NULL),
(217, 'PA01.05.04', 'Bienestar social', 'BSOC', 'Apoyo', 'PA01.05', 1, NULL, '2024-06-25 20:04:19', NULL),
(218, 'PE02.04.01', 'Programación de las inversiones', 'PRIN', 'Estratégico', 'PE02.04', 1, NULL, '2024-06-25 20:09:53', NULL),
(219, 'PE02.04.02', 'Formulación, evaluación, ejecución y cierre de proyectos', 'FECP', 'Estratégico', 'PE02.04', 1, NULL, '2024-06-25 20:09:53', NULL),
(220, 'PE02.04.03', 'Elaboración, aprobación, registro, ejecución física y cierre de las IOARR', 'EARC', 'Estratégico', 'PE02.04', 1, NULL, '2024-06-25 20:09:53', NULL),
(221, 'PE02.04.04', 'Gestión del seguimiento de las inversiones', 'GSI', 'Estratégico', 'PE02.04', 1, NULL, '2024-06-25 20:09:53', NULL),
(222, 'PM02.03.01', 'Atención de reclamos del libro de reclamaciones', 'ARECL', 'Misional', 'PM02.03', 1, NULL, '2024-06-25 20:17:04', NULL),
(223, 'PM02.03.02', 'Atención de quejas por defecto de tramitación', 'AQDT', 'Misional', 'PM02.03', 1, NULL, '2024-06-25 20:17:04', NULL),
(224, 'PM04.01.01', 'Determinación de la existencia de infracción', 'DEIF', 'Misional', 'PM04.01', 1, NULL, '2024-06-25 21:03:13', NULL),
(225, 'PM04.01.02', 'Determinación de la sanción', 'DESA', 'Misional', 'PM04.01', 1, NULL, '2024-06-25 21:03:13', NULL),
(226, 'PM04.01.03', 'Gestión para el cumplimiento de sanciones', 'GCSA', 'Misional', 'PM04.01', 1, NULL, '2024-06-25 21:03:13', NULL),
(227, 'PM04.03.01', 'Gestión a los procesos civiles resultantes de los servicios de control', 'GCSC', 'Misional', 'PM04.03', 1, NULL, '2024-06-25 21:03:13', NULL),
(228, 'PM04.03.02', 'Gestión de procesos penales resultantes de los servicios de control', 'GPSC', 'Misional', 'PM04.03', 1, NULL, '2024-06-25 21:03:13', NULL),
(229, 'PM05.01.01', 'Seguimiento y evaluación a la implementación de las recomendaciones de control posterior', 'SRCP', 'Misional', 'PM05.01', 1, NULL, '2024-06-25 21:10:34', NULL),
(230, 'PM05.01.02', 'Seguimiento y evaluación a la implementación de acciones respecto a los resultados de los informes de control simultáneo', 'SRCS', 'Misional', 'PM05.01', 1, NULL, '2024-06-25 21:10:34', NULL),
(231, 'PM05.01.03', 'Seguimiento y evaluación a la implementación de los pronunciamientos de control previo', 'SPCP', 'Misional', 'PM05.01', 1, NULL, '2024-06-25 21:10:34', NULL),
(232, 'PM01.01.01.01', 'Gestión eventos de prevención de la corrupción', 'GEPC', 'Misional', 'PM01.01.01', 1, NULL, '2024-06-25 21:23:10', NULL),
(233, 'PM01.01.01.02', 'Capacitación en temas de ética, integridad pública y lucha contra la corrupción', 'CEIN', 'Misional', 'PM01.01.01', 1, NULL, '2024-06-25 21:23:10', NULL),
(234, 'PM01.01.01.03', 'Difusión de contenidos para la prevención y lucha contra la corrupción e inconducta funcional', 'DCPR', 'Misional', 'PM01.01.01', 1, NULL, '2024-06-25 21:23:10', NULL),
(235, 'PM01.01.02.01', 'Gestión del registro de avance de obras públicas', 'GROP', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(236, 'PM01.01.02.02', 'Administración y verificación de las transferencias de gestión', 'AVTG', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(237, 'PM01.01.02.03', 'Administración y verificación de rendición de cuentas de titulares', 'ARCT', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(238, 'PM01.01.02.04', 'Recepción y verificación de declaraciones juradas', 'RVDJ', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(239, 'PM01.01.02.05', 'Verificación de la rendición de cuenta del programa de vaso de leche', 'VRVL', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(240, 'PM01.01.02.06', 'Recopilación de información', 'RINF', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(241, 'PM01.01.02.07', 'Gestión de la información de las donaciones de bienes provenientes del exterior', 'GDBE', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(242, 'PM01.01.02.08', 'Gestión del registro de información de funcionarios y servidores públicos que administren y manejen fondos públicos', 'GRFP', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(243, 'PM01.01.02.09', 'Gestión del registro para el control de contratos de consultoría en el estado', 'GRCE', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(244, 'PM01.01.02.10', 'Gestión para la presentación del balance semestral de los regidores municipales y los consejeros regionales sobre la utilización del monto destinado al fortalecimiento de la función de fiscalización', 'GPBS', 'Misional', 'PM01.01.02', 1, NULL, '2024-06-25 21:23:10', NULL),
(245, 'PM01.01.04', 'Gestión del observatorio anticorrupción', 'GOAC', 'Misional', 'PM01.01', 1, NULL, '2024-06-25 21:26:59', NULL),
(246, 'PM01.01.05', 'Administración y evaluación de la implementación del control interno en las entidades públicas', 'AECI', 'Misional', 'PM01.01', 1, NULL, '2024-06-25 21:26:59', NULL),
(247, 'PM01.01.02', 'Aprovisionamiento de información específica de operaciones relacionadas a la gestión de recursos públicos', 'AIEG', 'Misional', 'PM01.01', 1, NULL, '2024-06-25 22:00:53', NULL),
(248, 'PM01.01.03', 'Aprovisionamiento de información masiva de operaciones relacionadas a la gestión de recursos públicos', 'AIMG', 'Misional', 'PM01.01', 1, NULL, '2024-06-25 22:00:53', NULL),
(249, 'PM03.04.01', 'Fiscalización de los funcionarios y servidores públicos', 'FIFP', 'Misional', 'PM03.04', 1, NULL, '2024-06-25 22:09:26', NULL),
(250, 'PM03.04.02', 'Análisis y evaluación de la ejecución del gasto del programa vaso de leche', 'APVL', 'Misional', 'PM03.04', 1, NULL, '2024-06-25 22:09:26', NULL),
(251, 'PM03.05.01', 'Supervisión técnica de los servicios de control', 'STSC', 'Misional', 'PM03.05', 1, NULL, '2024-06-25 22:11:46', NULL),
(252, 'PM03.05.02', 'Revisión de oficio de informes de control', 'ROFI', 'Misional', 'PM03.05', 1, NULL, '2024-06-25 22:11:46', NULL),
(253, 'PM03.05.03', 'Reformulación de informes de control', 'REIC', 'Misional', 'PM03.05', 1, NULL, '2024-06-25 22:11:46', NULL),
(254, 'PA01.03.05.01', 'Recategorización de personal', 'RCPR', 'Apoyo', 'PA01.03.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(255, 'PA01.03.05.02', 'Convocatoria interna', 'CINT', 'Apoyo', 'PA01.03.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(256, 'PA01.03.06.01', 'Traslados del personal (rotación)', 'TPER', 'Apoyo', 'PA01.03.06', 1, NULL, '2024-06-25 22:20:25', NULL),
(257, 'PA01.03.06.02', 'Encargo de jefatura del órgano o unidad órganica', 'ECPR', 'Apoyo', 'PA01.03.06', 1, NULL, '2024-06-25 22:20:25', NULL),
(259, 'PA01.04.01.01', 'Control de asistencia del personal', 'CAPR', 'Apoyo', 'PA01.04.01', 1, NULL, '2024-06-25 22:20:25', NULL),
(260, 'PA01.04.01.02', 'Control de vacaciones del personal', 'CVPR', 'Apoyo', 'PA01.04.01', 1, NULL, '2024-06-25 22:20:25', NULL),
(261, 'PA01.04.01.03', 'Administración de remuneración del personal', 'ARPR', 'Apoyo', 'PA01.04.01', 1, NULL, '2024-06-25 22:20:25', NULL),
(262, 'PA01.04.01.04', 'Administración de pensiones', 'ADPN', 'Apoyo', 'PA01.04.01', 1, NULL, '2024-06-25 22:20:25', NULL),
(263, 'PA01.04.01.05', 'Evaluación de solicitudes de pensiones (de cesantía)', 'ESPC', 'Apoyo', 'PA01.04.01', 1, NULL, '2024-06-25 22:20:25', NULL),
(264, 'PA01.04.02.01', 'Evaluación de licencias del personal', 'ELPR', 'Apoyo', 'PA01.04.02', 1, NULL, '2024-06-25 22:20:25', NULL),
(265, 'PA01.04.02.02', 'Evaluación de horarios especiales del personal', 'EHPR', 'Apoyo', 'PA01.04.02', 1, NULL, '2024-06-25 22:20:25', NULL),
(266, 'PA01.04.02.03', 'Emisión de certificados y constancias de trabajo del personal', 'ECTP', 'Apoyo', 'PA01.04.02', 1, NULL, '2024-06-25 22:20:25', NULL),
(267, 'PA01.04.02.04', 'Emisión de cartas de presentación del personal', 'ECPP', 'Apoyo', 'PA01.04.02', 1, NULL, '2024-06-25 22:20:25', NULL),
(268, 'PA01.04.03.01', 'Afiliación a seguros EPS', 'ASEP', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(269, 'PA01.04.03.02', 'Afiliación a seguros Es Salud', 'ASES', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(270, 'PA01.04.03.03', 'Desafiliación a seguros EPS', 'DSEP', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(271, 'PA01.04.03.04', 'Desafiliación a seguros Es Salud', 'DSES', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(272, 'PA01.04.03.05', 'Reembolso de seguros EPS', 'RSEP', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(273, 'PA01.04.03.06', 'Atención de solicitudes de subsidios (incluye canje CITT)', 'ASSC', 'Apoyo', 'PA01.04.03', 1, NULL, '2024-06-25 22:20:25', NULL),
(274, 'PA01.04.04.01', 'Administración de legajos', 'ADLG', 'Apoyo', 'PA01.04.04', 1, NULL, '2024-06-25 22:20:25', NULL),
(275, 'PA01.04.04.02', 'Verificación de autenticidad de documentos', 'VADN', 'Apoyo', 'PA01.04.04', 1, NULL, '2024-06-25 22:20:25', NULL),
(276, 'PA01.04.05.01', 'Evaluación de denuncias de corrupción contra el personal de la CGR', 'DCGR', 'Apoyo', 'PA01.04.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(277, 'PA01.04.05.02', 'Evaluación de denuncias contra el gerente y personal del órgano de auditoría interna de la CGR', 'DOAI', 'Apoyo', 'PA01.04.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(278, 'PA01.04.05.03', 'Evaluación de denuncias contra los jefes y personal del OCI', 'DOCI', 'Apoyo', 'PA01.04.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(279, 'PA01.04.05.04', 'Gestión del procedimiento administrativo disciplinario', 'GPAD', 'Apoyo', 'PA01.04.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(280, 'PA01.04.06.01', 'Tramite documental para el cese de personal', 'TDCP', 'Apoyo', 'PA01.04.06', 1, NULL, '2024-06-25 22:20:25', NULL),
(281, 'PA01.04.06.02', 'Generación y pago de la liquidación de beneficios sociales', 'GPLB', 'Apoyo', 'PA01.04.06', 1, NULL, '2024-06-25 22:20:25', NULL),
(282, 'PM01.02.01', 'Participación ciudadana en el control social a través de auditores juveniles', 'PCAJ', 'Misional', 'PM01.02', 1, NULL, '2024-06-25 22:26:03', NULL),
(283, 'PM01.02.02', 'Participación ciudadana en el control social a través de monitores ciudadanos de control', 'PCMC', 'Misional', 'PM01.02', 1, NULL, '2024-06-25 22:26:03', NULL),
(284, 'PM01.02.03', 'Participación ciudadana en el control social a través de audiencias públicas', 'PCAP', 'Misional', 'PM01.02', 1, NULL, '2024-06-25 22:26:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesos_ouo`
--

CREATE TABLE `procesos_ouo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_proceso` bigint(20) UNSIGNED NOT NULL,
  `id_ouo` bigint(20) UNSIGNED NOT NULL,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proceso_user`
--

CREATE TABLE `proceso_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proceso_user`
--

INSERT INTO `proceso_user` (`id`, `user_id`, `proceso_id`, `created_at`, `updated_at`) VALUES
(9, 1, 8, NULL, NULL),
(12, 1, 9, NULL, NULL),
(14, 1, 11, NULL, NULL),
(26, 1, 35, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_auditorias`
--

CREATE TABLE `programa_auditorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `observacion` varchar(255) NOT NULL,
  `avance` decimal(10,2) NOT NULL,
  `version` int(11) NOT NULL,
  `periodo` varchar(200) NOT NULL,
  `presupuesto` double NOT NULL,
  `fecha_aprobacion` date NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_auditorias`
--

INSERT INTO `programa_auditorias` (`id`, `observacion`, `avance`, `version`, `periodo`, `presupuesto`, `fecha_aprobacion`, `fecha_publicacion`, `archivo_pdf`, `created_at`, `updated_at`) VALUES
(1, 'Nuevo Programa 2024', '0.00', 0, '2024', 130000, '2024-06-05', '2024-06-05', '', '2024-06-05 19:38:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requerimientos`
--

CREATE TABLE `requerimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proceso_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `facilitador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descripcion` text NOT NULL,
  `justificacion` text NOT NULL,
  `estado` enum('creado','aprobado','asignado','atendido','desestimado') NOT NULL,
  `prioridad` enum('baja','media','alta','muy alta') NOT NULL,
  `complejidad` enum('baja','media','alta','muy alta') NOT NULL,
  `ruta_archivo_desistimacion` varchar(255) DEFAULT NULL,
  `ruta_archivo_entregable` varchar(255) DEFAULT NULL,
  `fecha_limite` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `requerimientos`
--

INSERT INTO `requerimientos` (`id`, `proceso_id`, `user_id`, `facilitador_id`, `descripcion`, `justificacion`, `estado`, `prioridad`, `complejidad`, `ruta_archivo_desistimacion`, `ruta_archivo_entregable`, `fecha_limite`, `updated_at`, `created_at`) VALUES
(1, 2, 1, NULL, 'dadad', 'dadad', 'creado', 'media', 'alta', NULL, NULL, NULL, '2024-05-08 02:54:07', '2024-05-08 02:54:07'),
(2, 6, 1, NULL, 'dadd', 'dad', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-08 22:12:01', '2024-05-08 22:12:01'),
(3, 3, 1, NULL, 'sasas', 'xasas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-08 22:34:40', '2024-05-08 22:34:40'),
(4, 6, 1, NULL, 'addda', 'dad', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-08 22:42:55', '2024-05-08 22:42:55'),
(5, 3, 1, NULL, 'sassa', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:12:38', '2024-05-09 00:12:38'),
(6, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:55', '2024-05-09 00:54:55'),
(7, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:56', '2024-05-09 00:54:56'),
(8, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:57', '2024-05-09 00:54:57'),
(9, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:57', '2024-05-09 00:54:57'),
(10, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:58', '2024-05-09 00:54:58'),
(11, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:58', '2024-05-09 00:54:58'),
(12, 3, 1, NULL, 'sas', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:54:58', '2024-05-09 00:54:58'),
(13, 4, 1, NULL, 'sass', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:56:13', '2024-05-09 00:56:13'),
(14, 4, 1, NULL, 'sass', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:56:46', '2024-05-09 00:56:46'),
(15, 4, 1, NULL, 'sass', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:56:48', '2024-05-09 00:56:48'),
(16, 4, 1, NULL, 'sass', 'sas', 'creado', 'baja', 'baja', NULL, NULL, NULL, '2024-05-09 00:56:50', '2024-05-09 00:56:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requerimiento_movimientos`
--

CREATE TABLE `requerimiento_movimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requerimiento_id` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('creado','aprobado','derivado','atendido','desestimado','cerrado') NOT NULL,
  `comentario` text DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requerimiento_tipo_documento`
--

CREATE TABLE `requerimiento_tipo_documento` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requerimiento_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_documento_id` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('crear','actualizar','eliminar') NOT NULL,
  `nombre_documento` text DEFAULT NULL,
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
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `descripcion`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administración de Tablas Maestras y configuraciones del Sistema.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(2, 'especialista_sig', 'Especialista del SIG, puede tener permisos de los modulos de Indicadores, Riesgos, Hallazgos o Procesos.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(3, 'facilitador', 'Tiene acceso a la vista de facilitador del proceso y se puede habilitar los diferentes módulos del SIG.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(4, 'especialista', 'Tienes acceso de sólo lectura a los reportes y dashboards del SIG de acuerdo a los procesos de su propiedad.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
(5, 'propietario', 'Tienes acceso de sólo lectura a los reportes y dashboards del SIG de acuerdo a los procesos de su propiedad.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1);

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
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Juan Almeyda Requejo', 'jalmeyda@contraloria.gob.pe', '2023-08-30 15:54:20', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', '7enzMOmElIxFlw4IOcON2GRTjN983HKPg4ms8i2YPjae60wK7AU3kqDyUTOU', '2023-05-26 23:01:48', '2023-08-29 04:37:54'),
(2, 'Manuel Perez Effus', 'manuelperez@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(3, 'Angel Arturo Bendezu Cardenas\r\n', 'abendezuc@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(4, 'Maria Isabel Hiyo Huapaya\r\n', 'mhiyo@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(5, 'Ana Elsa Gonzales Napaico\r\n', 'agonzalesn@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(6, 'Gatsby Loayza Parraga\r\n', 'gloayza@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(7, 'Gustavo Adolfo Villanueva Salvador\r\n', 'gvillanuevas@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(8, 'Elias Martin Tresierra Paz\r\n', 'etresierra@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(9, 'César Aguilar Surichaqui', 'despachocontralorcgr@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(10, 'Giovanna Muñoz Silva (e)', 'gmunoz@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(11, 'Amado Enco Tirado', 'aenco@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(12, 'Hilda Jaramillo Velarde (e)', 'hjaramillo@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(13, 'Ester Díaz Segura', 'ediazse@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(14, 'Richard León Vargas', 'rleonva@contraloria.gob.pe', NULL, '', NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
(15, 'Luigino Pilotto Carreño', 'lpilotto@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(16, 'Víctor Mejía Zuloeta', 'emejia@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(17, 'Luis Ramírez Moscoso', 'luis.ramirez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(18, 'Giancarlo Ugaz Figueroa', 'giancarlo.ugaz@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(19, 'Magda Salgado Rubianes', 'magda.salgado@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(20, 'Solange Pérez Montero', 'solange.perez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(21, 'Carlos Jaime Rivero Morales', 'crivero@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(22, 'Michell Sifuentes Sifuentes', 'michell.sifuentes@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(23, 'Frank Mauricio Morales', 'frank.morales@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(24, 'Karla Pérez Guzmán', 'karla.perez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(25, 'Vanessa Walde Ortega', 'vanessa.walde@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(26, 'María Luna Torres', 'maria.luna@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(27, 'Ganimedes Rosales Reyes', 'ganimedes.rosales@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(28, 'Areli Valencia Vargas', 'areli.valencia@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(29, 'Patricia Salazar Velarde', 'patricia.salazar@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(30, 'Marco Argandoña Dueñas', 'margandona@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(31, 'Janes Rodríguez López', 'janes.rodriguez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(32, 'Luis Castillo Torrealva', 'luis.castillo@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(33, 'Edwars Cotrina Chávez', 'edwars.cotrina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(34, 'Johnny Rubina Meza', 'johnny.rubina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(35, 'Felix Li Padilla', 'felix.li@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(36, 'Juana Llacsahuanga Chávez', 'juana.llacsahuanga@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(37, 'Jorge Llamoctanta Trejo', 'jorge.llamoctanta@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(38, 'Moisés Vera Rodríguez', 'moises.vera@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(39, 'Tito Medina Sánchez', 'tito.medina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(40, 'Fidel Hernández Vega', 'fidel.hernandez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(41, 'Flabio García Esquivel', 'flabio.garcia@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(42, 'Dante Yorges Avalos', 'dante.yorges@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(43, 'Francisco Ochoa Uriarte', 'francisco.ochoa@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(44, 'Iván Cieza Yaipén', 'ivan.cieza@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(45, 'Paco Toledo Yallico', 'paco.toledo@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
(46, 'Gonzalo Pérez Wicht', 'gonzalo.perez@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(47, 'Luis Peralta Guzmán', 'luis.peralta@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(48, 'Oscar Mestanza Malaspina', 'oscar.mestanza@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(49, 'Eduardo Dionisio Astuhuamán', 'eduardo.dionisio@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(50, 'Alan Saldaña Bustamante', 'alan.saldana@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(51, 'Daniel Sedan Villacorta', 'daniel.sedan@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(52, 'Oswaldo Wetzell L.O.', 'oswaldo.wetzell@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(53, 'Guillermo Uribe Córdova', 'guillermo.uribe@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(54, 'Marco Bermúdez Torres', 'marco.bermudez@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(55, 'Luis Espinal Redondez', 'luis.espinal@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(56, 'Luis Toledo Zatta', 'luis.toledo@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(57, 'Victor Asin Chumpitaz', 'victor.asin@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(58, 'Renato Sandoval González', 'renato.sandoval@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(59, 'Carolina Díaz Maldonado', 'carolina.diaz@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(60, 'Aydeé Luna Lezama', 'aydee.luna@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(61, 'Patricia Salazar Velarde', 'patricia.salazar.nc@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(62, 'José Jaramillo Narváez', 'jose.jaramillo@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(63, 'Carlos Loyola Escajadillo', 'carlos.loyola@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(64, 'María Livaque Garay', 'maria.livaque@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(65, 'Wilfredo Cárdenas Cortez', 'wilfredo.cardenas@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(66, 'Gatsby Loayza Párraga', 'gatsby.loayza@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(67, 'Joao Pacheco Castro', 'joao.pacheco@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(68, 'Zusi Castro Grandez', 'zusi.castro@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(69, 'Mariela Farro Torres', 'mariela.farro@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
(184, 'Iber Ari Gomez', 'igomez@contraloria.gob.pe', NULL, NULL, NULL, '2024-05-08 23:27:55', NULL);

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
-- Indices de la tabla `contexto_analisis`
--
ALTER TABLE `contexto_analisis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contexto_analisis_contexto_determinacion_id_foreign` (`contexto_determinacion_id`),
  ADD KEY `contexto_analisis_internal_context_id_foreign` (`internal_context_id`),
  ADD KEY `contexto_analisis_external_context_id_foreign` (`external_context_id`);

--
-- Indices de la tabla `contexto_determinacion`
--
ALTER TABLE `contexto_determinacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `determinacion_contexto_proceso_id_year_version_unique` (`proceso_id`,`year`,`version`);

--
-- Indices de la tabla `contexto_externo`
--
ALTER TABLE `contexto_externo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contexto_externo_contexto_determinacion_id_foreign` (`contexto_determinacion_id`);

--
-- Indices de la tabla `contexto_interno`
--
ALTER TABLE `contexto_interno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contexto_interno_contexto_determinacion_id_foreign` (`contexto_determinacion_id`);

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
-- Indices de la tabla `especialistas`
--
ALTER TABLE `especialistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialistas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `especialista_hallazgo`
--
ALTER TABLE `especialista_hallazgo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialista_hallazgo_especialista_id_foreign` (`especialista_id`),
  ADD KEY `especialista_hallazgo_hallazgo_id_foreign` (`hallazgo_id`);

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
  ADD KEY `hallazgos_proceso_cod_foreign` (`proceso_id`);

--
-- Indices de la tabla `hallazgos_acciones`
--
ALTER TABLE `hallazgos_acciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hallazgos_acciones_hallazgo_id_foreign` (`hallazgo_id`);

--
-- Indices de la tabla `hallazgos_causas`
--
ALTER TABLE `hallazgos_causas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hallazgos_causas_hallazgo_id_foreign` (`hallazgo_id`);

--
-- Indices de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicadores_proceso_cod_foreign` (`proceso_id`);

--
-- Indices de la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicadores_historico_indicador_id_foreign` (`indicador_id`);

--
-- Indices de la tabla `indicadores_proceso_ouo`
--
ALTER TABLE `indicadores_proceso_ouo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indicadores_proceso_ouo_id_proceso_ouo_foreign` (`id_proceso_ouo`),
  ADD KEY `indicadores_proceso_ouo_id_indicador_foreign` (`id_indicador`);

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
-- Indices de la tabla `inventario_procesos`
--
ALTER TABLE `inventario_procesos`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `inventario_procesos_id_proceso_foreign` (`id_proceso`),
  ADD KEY `inventario_procesos_id_ouo_responsable_foreign` (`id_ouo_responsable`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `ouo`
--
ALTER TABLE `ouo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ouo_codigo_unique` (`codigo`),
  ADD KEY `ouo_ouo_padre_foreign` (`ouo_padre`),
  ADD KEY `ouo_subgerente_id_foreign` (`subgerente_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

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
  ADD UNIQUE KEY `cod_proceso` (`cod_proceso`),
  ADD UNIQUE KEY `sigla` (`sigla`);

--
-- Indices de la tabla `procesos_ouo`
--
ALTER TABLE `procesos_ouo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `procesos_ouo_id_proceso_foreign` (`id_proceso`),
  ADD KEY `procesos_ouo_id_ouo_foreign` (`id_ouo`);

--
-- Indices de la tabla `proceso_user`
--
ALTER TABLE `proceso_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_proceso_user_id_foreign` (`user_id`),
  ADD KEY `user_proceso_proceso_id_foreign` (`proceso_id`);

--
-- Indices de la tabla `programa_auditorias`
--
ALTER TABLE `programa_auditorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requerimientos_proceso_id_foreign` (`proceso_id`),
  ADD KEY `requerimientos_user_id_foreign` (`user_id`),
  ADD KEY `facilitador_id` (`facilitador_id`);

--
-- Indices de la tabla `requerimiento_movimientos`
--
ALTER TABLE `requerimiento_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requerimiento_movimientos_requerimiento_id_foreign` (`requerimiento_id`),
  ADD KEY `requerimiento_movimientos_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `requerimiento_tipo_documento`
--
ALTER TABLE `requerimiento_tipo_documento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requerimiento_tipo_documento_requerimiento_id_foreign` (`requerimiento_id`),
  ADD KEY `requerimiento_tipo_documento_tipo_documento_id_foreign` (`tipo_documento_id`);

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
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

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
-- AUTO_INCREMENT de la tabla `contexto_analisis`
--
ALTER TABLE `contexto_analisis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contexto_determinacion`
--
ALTER TABLE `contexto_determinacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contexto_externo`
--
ALTER TABLE `contexto_externo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contexto_interno`
--
ALTER TABLE `contexto_interno`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especialistas`
--
ALTER TABLE `especialistas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `especialista_hallazgo`
--
ALTER TABLE `especialista_hallazgo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `hallazgos_acciones`
--
ALTER TABLE `hallazgos_acciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `hallazgos_causas`
--
ALTER TABLE `hallazgos_causas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `indicadores_proceso_ouo`
--
ALTER TABLE `indicadores_proceso_ouo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `indicadores_seguimiento`
--
ALTER TABLE `indicadores_seguimiento`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT de la tabla `informe_auditoria`
--
ALTER TABLE `informe_auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_procesos`
--
ALTER TABLE `inventario_procesos`
  MODIFY `id_inventario` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `ouo`
--
ALTER TABLE `ouo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT de la tabla `procesos_ouo`
--
ALTER TABLE `procesos_ouo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proceso_user`
--
ALTER TABLE `proceso_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `programa_auditorias`
--
ALTER TABLE `programa_auditorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `requerimiento_movimientos`
--
ALTER TABLE `requerimiento_movimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `requerimiento_tipo_documento`
--
ALTER TABLE `requerimiento_tipo_documento`
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
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_programa_auditoria_id_foreign` FOREIGN KEY (`programa_auditoria_id`) REFERENCES `programa_auditorias` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `contexto_analisis`
--
ALTER TABLE `contexto_analisis`
  ADD CONSTRAINT `contexto_analisis_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contexto_analisis_external_context_id_foreign` FOREIGN KEY (`external_context_id`) REFERENCES `contexto_externo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contexto_analisis_internal_context_id_foreign` FOREIGN KEY (`internal_context_id`) REFERENCES `contexto_interno` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contexto_determinacion`
--
ALTER TABLE `contexto_determinacion`
  ADD CONSTRAINT `determinacion_contexto_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contexto_externo`
--
ALTER TABLE `contexto_externo`
  ADD CONSTRAINT `contexto_externo_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contexto_interno`
--
ALTER TABLE `contexto_interno`
  ADD CONSTRAINT `contexto_interno_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_documento_referencia_id_foreign` FOREIGN KEY (`documento_referencia_id`) REFERENCES `documentos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documentos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documentos_tipo_documento_id_foreign` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipos_documentos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `especialistas`
--
ALTER TABLE `especialistas`
  ADD CONSTRAINT `especialistas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `especialista_hallazgo`
--
ALTER TABLE `especialista_hallazgo`
  ADD CONSTRAINT `especialista_hallazgo_especialista_id_foreign` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`),
  ADD CONSTRAINT `especialista_hallazgo_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`);

--
-- Filtros para la tabla `hallazgos`
--
ALTER TABLE `hallazgos`
  ADD CONSTRAINT `hallazgos_proceso_cod_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`);

--
-- Filtros para la tabla `hallazgos_acciones`
--
ALTER TABLE `hallazgos_acciones`
  ADD CONSTRAINT `hallazgos_acciones_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `hallazgos_causas`
--
ALTER TABLE `hallazgos_causas`
  ADD CONSTRAINT `hallazgos_causas_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD CONSTRAINT `indicadores_proceso_cod_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`);

--
-- Filtros para la tabla `indicadores_historico`
--
ALTER TABLE `indicadores_historico`
  ADD CONSTRAINT `indicadores_historico_indicador_id_foreign` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`);

--
-- Filtros para la tabla `indicadores_proceso_ouo`
--
ALTER TABLE `indicadores_proceso_ouo`
  ADD CONSTRAINT `indicadores_proceso_ouo_id_indicador_foreign` FOREIGN KEY (`id_indicador`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `indicadores_proceso_ouo_id_proceso_ouo_foreign` FOREIGN KEY (`id_proceso_ouo`) REFERENCES `procesos_ouo` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `informe_auditoria`
--
ALTER TABLE `informe_auditoria`
  ADD CONSTRAINT `informe_auditoria_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`);

--
-- Filtros para la tabla `inventario_procesos`
--
ALTER TABLE `inventario_procesos`
  ADD CONSTRAINT `inventario_procesos_id_ouo_responsable_foreign` FOREIGN KEY (`id_ouo_responsable`) REFERENCES `ouo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventario_procesos_id_proceso_foreign` FOREIGN KEY (`id_proceso`) REFERENCES `procesos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ouo`
--
ALTER TABLE `ouo`
  ADD CONSTRAINT `ouo_ouo_padre_foreign` FOREIGN KEY (`ouo_padre`) REFERENCES `ouo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ouo_subgerente_id_foreign` FOREIGN KEY (`subgerente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `procesos_ouo`
--
ALTER TABLE `procesos_ouo`
  ADD CONSTRAINT `procesos_ouo_id_ouo_foreign` FOREIGN KEY (`id_ouo`) REFERENCES `ouo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `procesos_ouo_id_proceso_foreign` FOREIGN KEY (`id_proceso`) REFERENCES `procesos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proceso_user`
--
ALTER TABLE `proceso_user`
  ADD CONSTRAINT `user_proceso_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_proceso_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  ADD CONSTRAINT `requerimientos_ibfk_1` FOREIGN KEY (`facilitador_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requerimientos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`),
  ADD CONSTRAINT `requerimientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `requerimiento_movimientos`
--
ALTER TABLE `requerimiento_movimientos`
  ADD CONSTRAINT `requerimiento_movimientos_requerimiento_id_foreign` FOREIGN KEY (`requerimiento_id`) REFERENCES `requerimientos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requerimiento_movimientos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `requerimiento_tipo_documento`
--
ALTER TABLE `requerimiento_tipo_documento`
  ADD CONSTRAINT `requerimiento_tipo_documento_requerimiento_id_foreign` FOREIGN KEY (`requerimiento_id`) REFERENCES `requerimientos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requerimiento_tipo_documento_tipo_documento_id_foreign` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipos_documentos` (`id`);

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

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
