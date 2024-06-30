-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2024 a las 00:42:39
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
(1, 'SMP-RH-IN-0044', '03-2024(I)', 196, 'No se ha designado al Oficial de Compliance del SGCM, por lo que no pudo evidenciarse su legajo.', 'Conforme indica la Norma ISO 37301:2021 (Requisito 7.2.1),  la organización debe asegurarse de que las personas sean competentes sobre la base de una educación, formación o experiencia adecuadas; asimismo, la información documentada apropiada debe estar disponible como evidencia de competencia.\r\n\r\nSegún lo revisado durante el proceso de auditoría, las competencias de compliance para el puesto de Oficial de Compliance se encuentran descritas en el documento \"MATRIZ DE COMPETENCIAS PARA EL SISTEMA DE GESTIÓN COMPLIANCE Función de compliance: Oficial de Compliance”, que cuenta con los V°B° de la Subgerente de Modernización y Subgerente de Políticas y Desarrollo Humano con fecha 17/04/24. Se solicita, la información que sustente lo indicado en la Matriz de Competencias, sin embargo, no fue posible evidenciar el legajo del puesto de Oficial de Compliance. Conforme indica personal de Capital Humano debido a que aún no se ha designado este puesto dentro del SGCM.', 'Según lo revisado durante el proceso de auditoría, las competencias de compliance para el puesto de Oficial de Compliance se encuentran descritas en el documento \"MATRIZ DE COMPETENCIAS PARA EL SISTEMA DE GESTIÓN COMPLIANCE Función de compliance: Oficial de Compliance”, que cuenta con los V°B° de la Subgerente de Modernización y Subgerente de Políticas y Desarrollo Humano con fecha 17/04/24. Se solicita, la información que sustente lo indicado en la Matriz de Competencias, sin embargo, no fue posible evidenciar el legajo del puesto de Oficial de Compliance. Conforme indica personal de Capital Humano debido a que aún no se ha designado este puesto dentro del SGCM.', 'Norma ISO 37301, requisitos: 7.2 Competencia, 7.2.1. Generalidades.', 'Ncme', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-06-26', '2024-06-25', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 15:43:44', '2024-06-26 21:35:48'),
(2, 'SMP-MODER-IN-0015', '03-2024(I)', 107, 'Se identificaron desviaciones en la identificación de riesgos de compliance.', 'La norma ISO 37301:2021, en su requisito 6.1, establece que la organización debe considerar los problemas a los que se hace referencia en 4.1 y los requisitos mencionados en 4.2 y determinar los riesgos y  oportunidades que deben abordarse. (Requisito 6.1 Acciones para abordar riesgos y oportunidades), sin embargo, se identificaron algunas desviaciones en la identificación de riesgos.', 'Durante el desarrollo de la auditoría, se identificó las siguientes desviaciones en los procesos evaluados:\r\nProcesos Administración de información del personal:\r\nSe evidenció en la auditoria, en la revisión documentaria que el proceso en mención, no contaba con algún riesgo del \"Contexto de la organización\" F01(PR-MODER-04)02, como, por ejemplo: \r\nA1 Falta de interiorización por parte de los colaboradores de la obligatoriedad del cumplimiento del procedimiento.\r\nGestión de Capital Humano - Entrega de Puesto del Colaborador:\r\nRiesgo MO-CHP-001 y MO-CHP-002 no están relacionados con los factores externos e internos.\r\nGestión de las Comunicaciones (Diseño del plan de comunicación corporativa, Gestión de la comunicación interna y Gestión de la publicación institucional):\r\nEl riesgo MO-COM-007 no está relacionados con los factores externos e internos.\r\nPlaneamiento Estratégico:\r\nEl riesgo MO-PEI-001 \"No contar con la documentación administrativa que sustente la elaboración, seguimiento y evaluación del PEI (hoja informativa, reportes, anexos de la Guía CEPLAN, debido a la omisión por parte del personal\" no están relacionado con los factores externos e internos. Asimismo, no se evidencia la evaluación del riesgo MO-PEI-002 \"No cumplir con el plazo establecido para el seguimiento del PEI, de acuerdo a los \r\nestablecido en la Guía CEPLAN debido a la falta de información necesaria para el análisis y evaluación del PEI, que debe ser remitida por los OUO responsables de indicadores\".\r\nGestión de Activos Documentarios (Archivo, custodia y conservación de Documentos):\r\nEl riesgo MO-ARCH-004 Solicitudes de eliminación rechazadas debido a desconocimiento del procedimiento vigente, no están relacionado con los factores externos e internos.\r\nEvaluación de Prestaciones Adicionales de Obra - 1era instancia y Evaluación de Prestaciones Adicionales de Supervisión de Obra - 1era instancia:\r\nEl riesgo MO-CPRE-003 “Posible aprobación del PO o PASO a causa de inobservar el plazo que establece la Ley de Contrataciones del Estado y su Reglamento” no están relacionado con los factores externos e internos.\r\nAuditoría de la Cuenta General de la República:\r\nEl riesgo MO-ACGR-001 \"Presentación inconsistente del Informe de la Auditoría de la Cuenta General de la República, a causa de que los informes de la auditoría de las entidades (materia de insumo del informe de la \r\nauditoría de la cuenta general de la República)\" no están relacionado con los factores externos e internos.\r\nOperativo de Control Simultáneo:\r\nEl riesgo MO-GSCS-CS-001 \"Probabilidad de presentación del Informe de Operativo de Control Simultáneo incompleto (no incluye el total de resultados esperados, los cuales se encuentran contenidos en el Plan Operativo), debido a la falta de priorización de las visitas de control por parte de las unidades orgánicas participantes\" no están relacionado con los factores externos e internos.\r\nAuditoría de Cumplimiento:\r\nEl riesgo MO-SCP-AC-001 \"Que el planeamiento de la auditoría de cumplimiento (carpeta de servicio) se efectúe sin cumplir con lo dispuesto en la normativa y lineamientos aplicables (*), a causa de la limitada \r\ncompetencia del personal” no están relacionado con los factores externos e internos.\r\nGestión de Capital Humano - Encargo de Jefatura de Órgano o Unidad Orgánica:\r\nEl Riesgo MO-EFJUO-001 \"Incumplir la normativa dejando sin encargar de funciones a la unidad orgánica por mala comunicación no están relacionados con los factores externos e internos”.\r\nGestión de Abastecimiento - Gestión de Bienes Patrimoniales:\r\nPara la Oportunidad (O1, F1), se ha establecido como plan de tratamiento \"Realizar capacitaciones, al personal de Patrimonio y a los usuarios”, sin embargo, no se indica en qué temas se darán las capacitaciones ni el mecanismo para asegurar que la acción sea permanente. \r\nPara el Riesgo (MO-GBPAT-005), se ha establecido como plan de tratamiento \" Emitir por correo electrónico el enlace a los procedimientos internos del proceso de Gestión de Bienes Patrimoniales”, sin embargo, no se indica el mecanismo para garantizar que la acción de envío de correos electrónicos sea de manera permanente para mitigar el riesgo.\r\nProceso Gestión Antisoborno:\r\nEl proceso ha identificado el riesgo (D1, A4) el cual está registrado en la Matriz integral de riesgos y oportunidades F02(PR-MODER-04)04, Vs.04 aprobado el 18/04/2024, sin embargo, el factor \"A4\" de la \"Determinación del contexto\" del proceso, no es correspondiente con el riesgo en mención.', 'Norma ISO 37301, requisitos: 6.1 Acciones para abordar los riesgos y oportunidades.', 'Ncme', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 16:47:15', '2024-06-26 18:55:54'),
(3, 'SMP-GCM-IN-001', '03-2024(I)', 114, 'No se evidencia la designación de la función de compliance de la CGR', 'No se evidencia que se haya designado la función de compliance en la CGR, ni que se implementen los principios de acceso directo, independencia, autoridad y competencia adecuada de la función de compliance.', 'Durante la auditoría a los procesos Administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance, en la cual se entrevistó a la Subgerenta de Modernización , Supervisora General de Modernización y la Supervisora del SIG, se declara que a la fecha no se cuenta con el nombramiento de la función de cumplimiento.', 'Norma ISO 37301, 5.1.1 Órgano y  Alta Dirección.', 'NCM', 'IN', 'Aprobado', 'SGC', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-06-26', '2024-06-25', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 21:53:09', '2024-06-26 22:10:34'),
(4, 'SMP-GCM-IN-002', '03-2024(I)', 114, 'No se cuenta con Política de Compliance', 'No se evidencia que se haya aprobado, implementado, comunicado la Política de Compliance y que esté disponible para las partes interesadadas, según corresponda.', 'Al respecto, la Política de Compliance presentada en la auditoría de los procesos de \r\n administración de los Sistemas de Gestión, Gestión de Riesgos, Gestión por Procesos y Gestión Compliance, no se encuentra aprobada, implementada, comunicada dentro de la organización ni está disponible para las partes interesadas.', 'Norma ISO 37301, requisito: 5.2 Política de Compliance.', 'NCM', 'IN', 'Aprobado', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', '2024-06-26', '2024-06-30', '0.00', NULL, NULL, NULL, NULL, NULL, '2024-06-26 22:16:38', '2024-06-26 22:37:55'),
(5, 'SMP-114-IN-003', '03-2024(I)', 114, 'No se cuenta con objetivos para el Sistema de Gestión de Compliance', 'No se evidencia que se haya aprobado y comunicado los Objetivos de Compliance en las funciones y niveles relevantes', 'Durante la auditoría a los procesos de \"Administración de los Sistemas de Gestión\", \"Gestión de Riesgos\", \"Gestión por Procesos\" y \"Gestión Compliance\" se presentó el documento \"Planificación de los objetivos del Sistema Integrado de Gestión\", indicando que que los Objetivos de Compliance se  encuentran en proceso de \"revisión\", en consecuencia no están implementados, ni comunicados en la entidad.', 'Norma ISO 37301, requisito:  6.2. Objetivos de cumplimiento y planificación para lograrlos.', 'NCM', 'IN', 'Abierto', 'SGCM', 'Maria Claudia Campos García', 'auditor externo', '2024-05-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 22:40:54', '2024-06-26 22:40:54');

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
(12, 1, 'SMP-RH-IN-0044-001', 'Verificación del perfil del Oficial de Compliance.', '2024-06-13', '2024-06-17', 'Daniel Sedan Villacorta', 'dsedan@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 20:57:03', '2024-06-26 20:57:03'),
(13, 1, 'SMP-RH-IN-0044-002', 'Resguardo de legajo, con los documentos presentados por el Oficial de Compliance.', '2024-06-13', '2024-06-19', 'Daniel Sedan Villacorta', 'dsedan@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 20:58:31', '2024-06-26 20:58:31'),
(14, 1, 'SMP-RH-IN-0044-003', 'Designar la función de compliance en la CGR .', '2024-06-13', '2024-06-21', 'Luis Miguel Iglesias León', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 21:00:26', '2024-06-26 21:00:26'),
(15, 1, 'SMP-RH-IN-0044-004', 'Difundir dicha designacion a todo el personal en CGR.', '2024-06-13', '2024-06-25', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 21:01:39', '2024-06-26 21:01:39'),
(16, 3, 'SMP-GCM-IN-001-001', 'Designar la función de compliance en la CGR', '2024-06-06', '2024-06-21', 'Luis Miguel Iglesias León (Ata Dirección)', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:09:17', '2024-06-26 22:09:17'),
(17, 3, 'SMP-GCM-IN-001-002', 'Difundir dicha designacion a todo el personal en la CGR', '2024-06-06', '2024-06-25', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:10:03', '2024-06-26 22:10:03'),
(18, 4, 'SMP-GCM-IN-002-001', 'Presentar la propuesta de la Política de Compliance para la evaluación de las unidades orgánicas que conforman el \r\nproceso de revisión establecido dentro de la CGR.', '2024-06-06', '2024-06-30', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:31:53', '2024-06-26 22:32:08'),
(19, 4, 'SMP-GCM-IN-002-002', 'Aprobar la Política Compliance', '2024-06-06', '2024-06-21', 'Luis Miguel Iglesias León (Ata Dirección)', 'liglesias@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:32:58', '2024-06-26 22:32:58'),
(20, 4, 'SMP-GCM-IN-002-003', 'Comunicar la Política Compliance', '2024-06-06', '2024-06-26', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:33:30', '2024-06-26 22:33:30'),
(21, 4, 'SMP-GCM-IN-002-004', 'Poner a disposicion de las partes internas la Política Compliance', '2024-06-06', '2024-06-26', 'Adriana Arciniega Muñoz', 'aarciniega@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, 'Programada', 0, '2024-06-26 22:34:05', '2024-06-26 22:34:05');

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
(9, 5, 'cinco_porques', '¿Por qué no se evidencia que se haya aprobado y comunicado los Objetivos de Compliance en las funciones y niveles relevantes?\r\nPorque al momento de la auditoria interna se encontraba en proceso de revision para aprobación', 'Por qué al momento de la auditoria interna los Objetivos de Compliance se encontraban en proceso de revisión?\r\nDada la relavancia y el alcance previsto para el Sistema de Gestión de Compliance (SGCM), para la evaluación de los Objetivos de Copmpliance se solicitó opinion a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Se solicitó opinion a otras unidades orgánicas que no forman parte del proceso de revisión establecido dentro de la CGR.', '2024-06-26 22:41:25', '2024-06-26 22:41:56');

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
(90, '2024_06_11_142926_add_cinco_porques_to_analisis_causa_raiz_table', 19);

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
(68, 'PM04.03', 'Gestión de los procesos judiciales resultantes de los servicios de control', 'GPJU', 'Misional', 'PM04', 1, NULL, '2023-08-09 17:28:23', NULL),
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
(97, 'PA06.02', 'Gestión de los procesos judiciales de la CGR', NULL, 'Apoyo', 'PA06', 1, NULL, '2023-08-09 18:30:44', NULL),
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
(256, 'PA01.03.05.03', 'Traslados del personal (rotación)', 'TPER', 'Apoyo', 'PA01.03.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(257, 'PA01.03.05.04', 'Encargaturas del personal', 'ECPR', 'Apoyo', 'PA01.03.05', 1, NULL, '2024-06-25 22:20:25', NULL),
(258, 'PA01.03.05.05', 'Entrega de puesto del colaborador', 'EPCL', 'Apoyo', 'PA01.03.05', 1, NULL, '2024-06-25 22:20:25', NULL),
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
(1, 3);

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
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Juan Almeyda Requejo', 'jalmeyda@contraloria.gob.pe', '2023-08-30 15:54:20', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', 'MbDCjeSqUzT3iiAD1c7BrdCMVeixFqOwbrgd57LLeLSz7PEb9whusEsuWzeO', '2023-05-26 23:01:48', '2023-08-29 04:37:54'),
(2, 'Manuel Perez Effus', 'manuelperez@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(3, 'Angel Arturo Bendezu Cardenas\r\n', 'abendezuc@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(4, 'Maria Isabel Hiyo Huapaya\r\n', 'mhiyo@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(5, 'Ana Elsa Gonzales Napaico\r\n', 'agonzalesn@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(6, 'Gatsby Loayza Parraga\r\n', 'gloayza@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(7, 'Gustavo Adolfo Villanueva Salvador\r\n', 'gvillanuevas@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
(8, 'Elias Martin Tresierra Paz\r\n', 'etresierra@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38');

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
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `hallazgos_acciones`
--
ALTER TABLE `hallazgos_acciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `hallazgos_causas`
--
ALTER TABLE `hallazgos_causas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

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
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
