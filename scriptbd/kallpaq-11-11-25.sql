-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.27-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para kallpaq
CREATE DATABASE IF NOT EXISTS `kallpaq` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `kallpaq`;

-- Volcando estructura para tabla kallpaq.acciones
CREATE TABLE IF NOT EXISTS `acciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned DEFAULT NULL,
  `hallazgo_proceso` bigint(20) unsigned DEFAULT NULL,
  `accion_cod` varchar(255) NOT NULL,
  `accion_tipo` enum('corrección','acción correctiva') NOT NULL,
  `accion_descripcion` text NOT NULL,
  `accion_comentario` text DEFAULT NULL,
  `accion_fecha_inicio` date NOT NULL,
  `accion_fecha_fin_planificada` date NOT NULL,
  `accion_fecha_fin_reprogramada` date DEFAULT NULL,
  `accion_fecha_cancelada` date DEFAULT NULL,
  `accion_fecha_fin_real` date DEFAULT NULL,
  `accion_justificacion` text DEFAULT NULL,
  `accion_ruta_evidencia` text DEFAULT NULL,
  `accion_responsable` varchar(255) DEFAULT NULL,
  `accion_responsable_correo` varchar(255) DEFAULT NULL,
  `accion_estado` varchar(255) DEFAULT NULL,
  `accion_ciclo` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acciones_hallazgo_proceso` (`hallazgo_proceso`),
  KEY `FK_acciones_hallazgos` (`hallazgo_id`),
  CONSTRAINT `FK_acciones_hallazgo_proceso` FOREIGN KEY (`hallazgo_proceso`) REFERENCES `hallazgo_proceso` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `FK_acciones_hallazgos` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.acciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.accion_movimientos
CREATE TABLE IF NOT EXISTS `accion_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `accion_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `estado` varchar(255) NOT NULL,
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accion_movimientos_accion_id_foreign` (`accion_id`),
  KEY `accion_movimientos_user_id_foreign` (`user_id`),
  CONSTRAINT `accion_movimientos_accion_id_foreign` FOREIGN KEY (`accion_id`) REFERENCES `acciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accion_movimientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.accion_movimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.areas_compliance
CREATE TABLE IF NOT EXISTS `areas_compliance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `area_compliance_nombre` varchar(255) NOT NULL,
  `area_compliance_descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `areas_compliance_area_compliance_nombre_unique` (`area_compliance_nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.areas_compliance: ~13 rows (aproximadamente)
REPLACE INTO `areas_compliance` (`id`, `area_compliance_nombre`, `area_compliance_descripcion`, `created_at`, `updated_at`) VALUES
	(1, 'Contrataciones y Abastecimiento', 'Gestión de adquisiciones, bienes y servicios institucionales.', NULL, NULL),
	(2, 'Laboral y Talento Humano', 'Gestión del personal, desempeño y bienestar laboral.', NULL, NULL),
	(3, 'Gobierno Digital y Tecnologías de la Información', 'Transformación digital, seguridad y sistemas tecnológicos institucionales.', NULL, NULL),
	(4, 'Gestión Documental y Archivo Institucional', 'Administración de documentos y archivos institucionales.', NULL, NULL),
	(5, 'Presupuesto y Finanzas Públicas', 'Planificación, ejecución y control del presupuesto y recursos financieros públicos.', NULL, NULL),
	(6, 'Sostenibilidad, Medio Ambiente y Responsabilidad Social', 'Promoción de la sostenibilidad, equidad y responsabilidad institucional.', NULL, NULL),
	(7, 'Compromiso Social / Convenios', 'Gestión de convenios y compromisos institucionales con la sociedad.', NULL, NULL),
	(8, 'Transparencia, Rendición de Cuentas y Participación', 'Promoción del acceso a la información, participación ciudadana y rendición pública de cuentas.', NULL, NULL),
	(9, 'Planeamiento Estratégico y Modernización', 'Diseño e implementación de estrategias, procesos y mejora continua institucional.', NULL, NULL),
	(10, 'Gestión de Proyectos y Cooperación Internacional', 'Formulación, ejecución y evaluación de proyectos institucionales y de cooperación.', NULL, NULL),
	(11, 'Comunicación Institucional', 'Gestión de la comunicación interna y externa de la entidad.', NULL, NULL),
	(13, 'Ética Pública, Integridad y Anticorrupción', 'Fortalecimiento de la cultura ética, integridad y prevención de la corrupción.', NULL, NULL),
	(14, 'Control Gubernamental', 'Desarrollo y aplicación de mecanismos de control interno y externo institucional.', NULL, NULL);

-- Volcando estructura para tabla kallpaq.auditorias
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `programa_auditoria_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_programa_auditoria_id_foreign` (`programa_auditoria_id`),
  CONSTRAINT `auditorias_programa_auditoria_id_foreign` FOREIGN KEY (`programa_auditoria_id`) REFERENCES `programa_auditorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditorias: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.auditoria_equipo
CREATE TABLE IF NOT EXISTS `auditoria_equipo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auditoria_id` bigint(20) unsigned NOT NULL,
  `personal_id` bigint(20) unsigned NOT NULL,
  `rol` varchar(255) NOT NULL,
  `equipo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_equipo_auditoria_id_foreign` (`auditoria_id`),
  KEY `auditoria_equipo_personal_id_foreign` (`personal_id`),
  CONSTRAINT `auditoria_equipo_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_equipo_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_equipo: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.auditoria_procesos
CREATE TABLE IF NOT EXISTS `auditoria_procesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auditoria_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_procesos_auditoria_id_foreign` (`auditoria_id`),
  KEY `auditoria_procesos_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `auditoria_procesos_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_procesos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_procesos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuracion_clave_unique` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.configuracion: ~3 rows (aproximadamente)
REPLACE INTO `configuracion` (`id`, `clave`, `valor`, `created_at`, `updated_at`) VALUES
	(1, 'periodo_actual', '2023', '2023-08-19 02:27:59', NULL),
	(2, 'fecha_inicio_bloqueo', '01/02/2023', '2023-08-19 02:28:31', NULL),
	(3, 'fecha_fin_bloqueo', '31/07/2023', '2023-08-19 02:27:59', NULL);

-- Volcando estructura para tabla kallpaq.contexto_analisis
CREATE TABLE IF NOT EXISTS `contexto_analisis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contexto_determinacion_id` bigint(20) unsigned NOT NULL,
  `internal_context_id` bigint(20) unsigned NOT NULL,
  `external_context_id` bigint(20) unsigned NOT NULL,
  `analisis` text NOT NULL,
  `nivel` enum('Muy Alto','Alto','Medio','Bajo') NOT NULL,
  `valoracion` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contexto_analisis_contexto_determinacion_id_foreign` (`contexto_determinacion_id`),
  KEY `contexto_analisis_internal_context_id_foreign` (`internal_context_id`),
  KEY `contexto_analisis_external_context_id_foreign` (`external_context_id`),
  CONSTRAINT `contexto_analisis_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contexto_analisis_external_context_id_foreign` FOREIGN KEY (`external_context_id`) REFERENCES `contexto_externo` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contexto_analisis_internal_context_id_foreign` FOREIGN KEY (`internal_context_id`) REFERENCES `contexto_interno` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.contexto_analisis: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.contexto_determinacion
CREATE TABLE IF NOT EXISTS `contexto_determinacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `determinacion_contexto_proceso_id_year_version_unique` (`proceso_id`,`year`,`version`),
  CONSTRAINT `determinacion_contexto_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.contexto_determinacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.contexto_externo
CREATE TABLE IF NOT EXISTS `contexto_externo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contexto_determinacion_id` bigint(20) unsigned NOT NULL,
  `perspective_type` enum('legal','politico','institucional','tecnologia','social','economico') NOT NULL,
  `amenazas` text NOT NULL,
  `oportunidades` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contexto_externo_contexto_determinacion_id_foreign` (`contexto_determinacion_id`),
  CONSTRAINT `contexto_externo_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.contexto_externo: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.contexto_interno
CREATE TABLE IF NOT EXISTS `contexto_interno` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contexto_determinacion_id` bigint(20) unsigned NOT NULL,
  `perspective_type` enum('normativa','infraestructura','tecnologia','organizacion','personal','cultura_organizacional') NOT NULL,
  `fortalezas` text NOT NULL,
  `debilidades` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contexto_interno_contexto_determinacion_id_foreign` (`contexto_determinacion_id`),
  CONSTRAINT `contexto_interno_contexto_determinacion_id_foreign` FOREIGN KEY (`contexto_determinacion_id`) REFERENCES `contexto_determinacion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.contexto_interno: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.diagrama_contexto
CREATE TABLE IF NOT EXISTS `diagrama_contexto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `archivo` varchar(255) NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `version` varchar(255) NOT NULL,
  `vigencia` date NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'inactivo',
  `inactive_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diagrama_proceso` (`proceso_id`),
  CONSTRAINT `diagrama_proceso` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.diagrama_contexto: ~3 rows (aproximadamente)
REPLACE INTO `diagrama_contexto` (`id`, `archivo`, `proceso_id`, `version`, `vigencia`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
	(1, 'diagramas/RlCWzSZx9Bq0seahOq10GZBlqnB3RvGZ8hSh71Yt.png', 71, 'Version 2', '2025-05-12', 'activo', '2025-05-08 23:11:34', '2025-05-08 23:19:02', '2025-05-08 23:19:02'),
	(2, 'diagramas/DX9yVqBDAvnwtZcjBzFx3sh1L1ZZCyiChpnpT2eY.png', 62, 'Version 1', '2025-05-09', 'activo', '2025-05-09 16:58:40', '2025-05-09 16:55:04', '2025-05-09 16:55:04'),
	(5, 'diagramas/E5nAfpWIKdfEG7DgHiW7NSD1b0Qw2WSV8CYeKqDA.jpg', 30, '04', '2025-01-31', 'activo', '2025-05-28 16:50:53', '2025-05-28 16:03:32', '2025-05-28 16:03:32');

-- Volcando estructura para tabla kallpaq.documentos
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cod_documento` varchar(255) NOT NULL,
  `tipo_documento_id` bigint(20) unsigned DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_documento` varchar(255) DEFAULT NULL,
  `fuente_documento` varchar(255) DEFAULT NULL,
  `estado_documento` varchar(255) DEFAULT NULL,
  `fecha_vigencia_documento` timestamp NULL DEFAULT NULL,
  `area_compliance_id` bigint(20) unsigned DEFAULT NULL,
  `subarea_compliance_id` bigint(20) unsigned DEFAULT NULL,
  `documento_padre_id` bigint(20) unsigned DEFAULT NULL,
  `resumen_documento` text DEFAULT NULL,
  `palabras_clave_documento` text DEFAULT NULL,
  `observaciones_documento` text DEFAULT NULL,
  `archivo_path_documento` varchar(255) DEFAULT NULL,
  `usa_versiones_documento` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_aprobacion_documento` timestamp NULL DEFAULT NULL,
  `fecha_derogacion_documento` timestamp NULL DEFAULT NULL,
  `frecuencia_revision_documento` int(11) DEFAULT NULL,
  `instrumento_aprueba_documento` varchar(255) DEFAULT NULL,
  `instrumento_deroga_documento` varchar(255) DEFAULT NULL,
  `origen_documento` varchar(255) DEFAULT NULL,
  `enlace_valido` tinyint(1) DEFAULT NULL,
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `user_pubilshed` bigint(20) unsigned DEFAULT NULL,
  `user_deleted` bigint(20) unsigned DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pubished_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documentos_cod_documento_unique` (`cod_documento`),
  KEY `documentos_proceso_id_foreign` (`proceso_id`),
  KEY `documentos_tipo_documento_id_foreign` (`tipo_documento_id`),
  CONSTRAINT `documentos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documentos_tipo_documento_id_foreign` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documentos: ~8 rows (aproximadamente)
REPLACE INTO `documentos` (`id`, `cod_documento`, `tipo_documento_id`, `proceso_id`, `nombre_documento`, `fuente_documento`, `estado_documento`, `fecha_vigencia_documento`, `area_compliance_id`, `subarea_compliance_id`, `documento_padre_id`, `resumen_documento`, `palabras_clave_documento`, `observaciones_documento`, `archivo_path_documento`, `usa_versiones_documento`, `fecha_aprobacion_documento`, `fecha_derogacion_documento`, `frecuencia_revision_documento`, `instrumento_aprueba_documento`, `instrumento_deroga_documento`, `origen_documento`, `enlace_valido`, `user_created`, `user_pubilshed`, `user_deleted`, `user_updated`, `inactivate_at`, `created_at`, `updated_at`, `pubished_at`, `deleted_at`) VALUES
	(1, 'PR-GSCS-07', 4, 125, 'Procedimiento "Visita de Control"', 'interno', '1', '2024-12-02 05:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 22:56:50', '2025-05-19 15:43:18', NULL, NULL),
	(2, 'Directiva n.° 007-2022-CG/DOC', 11, 30, 'Directiva Notificaciones electrónicas en el Sistema Nacional de Control', 'interno', '1', '2022-05-09 05:00:00', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:17:20', '2025-05-16 21:46:45', NULL, NULL),
	(3, 'Directiva n.° 013-2022-CG/NORM', 11, 30, 'Servicio de Control Simultáneo', 'interno', '1', '2025-05-09 05:00:00', 3, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:19:59', '2025-05-16 19:27:46', NULL, NULL),
	(4, 'RC n.°  245-2023-CG', 10, 30, 'Normas Generales de Control Gubernamental', 'externo', '1', '2023-06-27 05:00:00', 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:25:23', NULL, NULL, NULL),
	(5, 'PR-GSCS-06', 4, 127, 'Procedimiento "Control Concurrente"', 'interno', '1', '2024-12-03 05:00:00', 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-13 01:06:25', NULL, NULL, NULL),
	(6, 'Directiva Nº 002-2025-CG/GMPL', 11, 127, 'Directiva Interna que establece Disposiciones Complementarias a la Ley N° 31358, Ley que establece medidas para la expansión del Control Concurrente', 'interno', '1', '2025-04-24 05:00:00', 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-13 01:06:26', '2025-05-19 14:39:28', NULL, NULL),
	(7, 'PR-GSCS-08', 4, 128, 'Procedimiento Operativo Control Simultaneo', 'interno', '1', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-20 21:44:11', '2025-05-20 21:44:11', NULL, NULL),
	(8, 'PR-PEI-01', 4, 30, 'Procedimiento “Elaboración, seguimiento y evaluación del Plan Estratégico Institucional”', 'interno', '1', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-28 21:10:26', '2025-05-28 21:10:26', NULL, NULL);

-- Volcando estructura para tabla kallpaq.documento_alertas
CREATE TABLE IF NOT EXISTS `documento_alertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `documento_impactado_id` bigint(20) unsigned NOT NULL,
  `comentario` text NOT NULL,
  `estado` enum('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_alertas_documento_id_foreign` (`documento_id`),
  KEY `documento_alertas_documento_impactado_id_foreign` (`documento_impactado_id`),
  CONSTRAINT `documento_alertas_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`),
  CONSTRAINT `documento_alertas_documento_impactado_id_foreign` FOREIGN KEY (`documento_impactado_id`) REFERENCES `documentos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_alertas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_movimientos
CREATE TABLE IF NOT EXISTS `documento_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `accion` enum('creado','modificado','publicado','eliminado','reactivado') NOT NULL,
  `observacion` text DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `documento_movimientos_documento_id_foreign` (`documento_id`),
  KEY `documento_movimientos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `documento_movimientos_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_movimientos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_movimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_proceso
CREATE TABLE IF NOT EXISTS `documento_proceso` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documento_proceso_documento_id_proceso_id_unique` (`documento_id`,`proceso_id`),
  KEY `documento_proceso_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `documento_proceso_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_proceso_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_proceso: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_relacionado
CREATE TABLE IF NOT EXISTS `documento_relacionado` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `relacionado_id` bigint(20) unsigned NOT NULL,
  `tipo_relacion` enum('impacta','modifica','deroga') NOT NULL DEFAULT 'impacta',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_relacionado_documento_id_foreign` (`documento_id`),
  KEY `documento_relacionado_relacionado_id_foreign` (`relacionado_id`),
  CONSTRAINT `documento_relacionado_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_relacionado_relacionado_id_foreign` FOREIGN KEY (`relacionado_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_relacionado: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_tag
CREATE TABLE IF NOT EXISTS `documento_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_tag_documento_id_foreign` (`documento_id`),
  KEY `documento_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `documento_tag_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_tag: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_versions
CREATE TABLE IF NOT EXISTS `documento_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `version` int(11) NOT NULL,
  `control_cambios` varchar(250) NOT NULL DEFAULT '',
  `archivo_path` varchar(255) NOT NULL,
  `enlace_valido` int(1) DEFAULT NULL,
  `fecha_aprobacion` date NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_versiones_documento_id_foreign` (`documento_id`),
  CONSTRAINT `documento_versiones_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_versions: ~5 rows (aproximadamente)
REPLACE INTO `documento_versions` (`id`, `documento_id`, `version`, `control_cambios`, `archivo_path`, `enlace_valido`, `fecha_aprobacion`, `fecha_publicacion`, `created_at`, `updated_at`) VALUES
	(25, 2, 1, 'Version del Peruano', 'https://cdn.www.gob.pe/uploads/document/file/2907982/Resoluci%C3%B3n%20de%20Contralor%C3%ADa%20N%C2%B0102-2022-CG.pdf.pdf?v=1647275017', 1, '2022-03-11', '2025-05-28', '2025-05-20 19:23:36', '2025-05-28 23:25:12'),
	(26, 3, 1, 'publicada en la web contraloria', 'https://cdn.www.gob.pe/uploads/document/file/3839885/3656507-directiva-n-013-2022-cg-norm-directiva-de-servicio-de-control-simultaneo.pdf?v=1708034518', 1, '2023-12-21', '2025-05-20', '2025-05-20 20:06:31', '2025-05-28 23:25:13'),
	(27, 8, 4, 'Version 05', 'PE01.01/Procedimiento/PR-PEI-01-v01.pdf', 1, '2025-01-30', '2025-05-28', '2025-05-28 21:11:56', '2025-05-28 21:20:23'),
	(31, 4, 1, 'version inicial', 'https://cdn.www.gob.pe/uploads/document/file/2907982/Resoluci%C3%B3n%20de%20Contralor%C3%ADa%20N%C2%B0102-2022-CG.pdf.pdf?v=1647275017', 1, '2025-05-28', '2025-05-28', '2025-05-28 23:26:38', '2025-05-28 23:27:36'),
	(33, 1, 1, 'version 01', 'PM03.02.01/Procedimiento/PR-GSCS-07-v01.pdf', 1, '2025-05-13', '2025-05-28', '2025-05-28 23:53:48', '2025-05-28 23:53:48');

-- Volcando estructura para tabla kallpaq.especialistas
CREATE TABLE IF NOT EXISTS `especialistas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `inactived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `especialistas_user_id_foreign` (`user_id`),
  CONSTRAINT `especialistas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.especialistas: ~8 rows (aproximadamente)
REPLACE INTO `especialistas` (`id`, `user_id`, `cargo`, `estado`, `created_at`, `updated_at`, `inactived_at`) VALUES
	(1, 1, 'Especialista SIG', 1, '2024-06-05 19:38:32', NULL, NULL),
	(2, 2, 'Especialista TUPA', 1, '2024-06-05 19:38:32', NULL, NULL),
	(3, 3, 'Especialista Riesgos', 1, '2024-06-05 19:38:32', NULL, NULL),
	(4, 4, 'Especialista SIG', 1, '2024-06-05 19:38:32', NULL, NULL),
	(5, 5, 'Especialista SIG', 1, NULL, NULL, NULL),
	(6, 6, 'Especialista SIG', 0, NULL, NULL, '2025-11-03 18:33:24'),
	(7, 7, 'Especialista SIG', 1, NULL, NULL, NULL),
	(8, 8, 'Especialista SIG', 1, NULL, NULL, NULL);

-- Volcando estructura para tabla kallpaq.factores
CREATE TABLE IF NOT EXISTS `factores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `valor` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `inactivate_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.factores: ~7 rows (aproximadamente)
REPLACE INTO `factores` (`id`, `nombre`, `valor`, `estado`, `created_at`, `updated_at`, `inactivate_at`, `deleted_at`) VALUES
	(1, 'Estratégico', 3, 1, NULL, NULL, NULL, NULL),
	(2, 'Operacional', 2, 1, NULL, NULL, NULL, NULL),
	(3, 'Corrupción', 4, 1, NULL, NULL, NULL, NULL),
	(4, 'Cumplimiento', 3, 1, NULL, NULL, NULL, NULL),
	(5, 'Reputacional', 3, 1, NULL, NULL, NULL, NULL),
	(6, 'Ambiental', 2, 1, NULL, NULL, NULL, NULL),
	(7, 'Seguridad', 4, 1, NULL, NULL, NULL, NULL);

-- Volcando estructura para tabla kallpaq.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para procedimiento kallpaq.generar_frecuencias
DELIMITER //
CREATE PROCEDURE `generar_frecuencias`(IN `p_indicador_id` INT, IN `p_periodo_actual` YEAR)
BEGIN
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
END//
DELIMITER ;

-- Volcando estructura para tabla kallpaq.hallazgos
CREATE TABLE IF NOT EXISTS `hallazgos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_cod` varchar(255) NOT NULL,
  `informe_id` bigint(20) unsigned DEFAULT NULL,
  `especialista_id` bigint(20) unsigned DEFAULT NULL,
  `auditor_id` bigint(20) unsigned DEFAULT NULL,
  `facilitador_id` bigint(20) unsigned DEFAULT NULL,
  `emisor` varchar(50) DEFAULT NULL,
  `hallazgo_resumen` text NOT NULL,
  `hallazgo_sig` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `hallazgo_descripcion` text NOT NULL,
  `hallazgo_criterio` text NOT NULL,
  `hallazgo_evidencia` text NOT NULL,
  `hallazgo_clasificacion` enum('NCM','Ncme','Obs','Odm') NOT NULL,
  `hallazgo_origen` enum('RD','IN','EX','SN','GI','GR','SC','OT') NOT NULL,
  `hallazgo_origen_ot` varchar(50) DEFAULT NULL,
  `hallazgo_avance` int(11) DEFAULT NULL,
  `hallazgo_tipo_cierre` enum('con eficacia','sin eficacia') DEFAULT NULL,
  `hallazgo_estado` enum('creado','asignado','desestimado','en proceso','concluido','evaluado','cerrado') DEFAULT NULL,
  `hallazgo_ciclo` int(10) unsigned DEFAULT 1,
  `hallazgo_fecha_identificacion` date DEFAULT NULL,
  `hallazgo_fecha_asignacion` date DEFAULT NULL,
  `hallazgo_fecha_desestimacion` date DEFAULT NULL,
  `hallazgo_fecha_conclusion` date DEFAULT NULL,
  `hallazgo_fecha_evaluacion` date DEFAULT NULL,
  `hallazgo_fecha_cierre` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgos: ~15 rows (aproximadamente)
REPLACE INTO `hallazgos` (`id`, `hallazgo_cod`, `informe_id`, `especialista_id`, `auditor_id`, `facilitador_id`, `emisor`, `hallazgo_resumen`, `hallazgo_sig`, `hallazgo_descripcion`, `hallazgo_criterio`, `hallazgo_evidencia`, `hallazgo_clasificacion`, `hallazgo_origen`, `hallazgo_origen_ot`, `hallazgo_avance`, `hallazgo_tipo_cierre`, `hallazgo_estado`, `hallazgo_ciclo`, `hallazgo_fecha_identificacion`, `hallazgo_fecha_asignacion`, `hallazgo_fecha_desestimacion`, `hallazgo_fecha_conclusion`, `hallazgo_fecha_evaluacion`, `hallazgo_fecha_cierre`, `created_at`, `updated_at`) VALUES
	(55, 'H-87920', 922, 346, 324, 297, NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Registros de logs del sistema de gestión documental, periodo 01/09/2025 - 31/10/2025. Accesos no autorizados detectados.', 'NCM', 'GR', 'vitae', NULL, NULL, 'evaluado', 2, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(56, 'H-73160', NULL, 17, NULL, 309, 'Flatley, Donnelly and Haley', 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgo...', NULL, 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgos de soborno, lo que podría exponer a la organización a proveedores con prácticas no éticas.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'Ncme', 'GR', 'aspernatur', 65, 'con eficacia', NULL, 3, '2024-12-11', '2025-06-07', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(57, 'H-26625', 985, 186, 24, 331, 'Schuster LLC', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'Odm', 'GI', 'repellendus', 41, 'con eficacia', NULL, 2, NULL, NULL, NULL, '2024-12-30', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(58, 'H-94131', NULL, 377, NULL, 225, 'Fadel Inc', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGAS","SGCM"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Actas de reunión de revisión por la dirección. No se encontró acta correspondiente al segundo semestre de 2025.', 'NCM', 'SN', 'aliquam', NULL, 'con eficacia', NULL, 1, '2025-03-06', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(59, 'H-17660', 757, NULL, 277, NULL, NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGCO","SGCM"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'SN', NULL, 84, NULL, 'desestimado', 2, '2025-07-31', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(60, 'H-88833', 901, 257, NULL, NULL, 'Kunze-Kassulke', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGSI"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 9001:2015, Cláusula 7.1.5 Recursos de seguimiento y medición', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'NCM', 'OT', 'quo', NULL, NULL, NULL, 3, NULL, '2025-10-26', NULL, '2025-09-28', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(61, 'H-26775', 851, 375, 362, NULL, 'Monahan-Hoeger', 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgo...', NULL, 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgos de soborno, lo que podría exponer a la organización a proveedores con prácticas no éticas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'IN', NULL, NULL, 'sin eficacia', NULL, 1, '2025-03-19', '2025-05-11', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(62, 'H-86599', NULL, NULL, 348, 36, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'Ncme', 'GI', 'explicabo', NULL, NULL, 'asignado', 3, NULL, '2025-10-21', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(63, 'H-7932', NULL, NULL, NULL, NULL, NULL, 'Se detectaron inconsistencias en los registros de control de acceso a información confidencial (INF...', NULL, 'Se detectaron inconsistencias en los registros de control de acceso a información confidencial (INF-SEG-002), lo que podría comprometer la seguridad de los datos de clientes y proyectos.', 'ISO 9001:2015, Cláusula 7.1.5 Recursos de seguimiento y medición', 'Registros de logs del sistema de gestión documental, periodo 01/09/2025 - 31/10/2025. Accesos no autorizados detectados.', 'Obs', 'EX', NULL, NULL, NULL, NULL, 1, NULL, '2025-10-13', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(64, 'H-72731', NULL, NULL, 6, NULL, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', '["SGCO","SGSI"]', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 37001:2016, Cláusula 8.2 Debida diligencia', 'Procedimiento PRO-COM-003 \'Selección y Evaluación de Proveedores\'. No se menciona evaluación de riesgos de soborno.', 'Obs', 'SC', NULL, NULL, NULL, 'en proceso', 3, NULL, '2025-11-09', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(65, 'H-45464', 733, NULL, NULL, 53, 'Cormier, Sporer and Balistreri', 'La capacitación sobre la política antisoborno (POL-ASB-001) no ha sido completada por el 30% del p...', NULL, 'La capacitación sobre la política antisoborno (POL-ASB-001) no ha sido completada por el 30% del personal de ventas, lo que representa un riesgo de incumplimiento de la normativa interna y externa.', 'ISO 9001:2015, Cláusula 7.5 Información documentada', 'Informe de auditoría interna N° 2025-003, sección 4.2. Registros de acciones correctivas incompletos.', 'Ncme', 'RD', NULL, 63, NULL, 'en proceso', 3, NULL, NULL, NULL, '2025-07-23', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(66, 'H-67184', NULL, 40, NULL, NULL, NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'Ncme', 'GI', 'corrupti', NULL, 'con eficacia', 'concluido', 3, '2025-02-08', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(67, 'H-65130', NULL, NULL, NULL, 193, 'Wehner-Leffler', 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 9001:2015, Cláusula 7.5 Información documentada', 'Informe de auditoría interna N° 2025-003, sección 4.2. Registros de acciones correctivas incompletos.', 'NCM', 'RD', 'nulla', NULL, 'sin eficacia', 'creado', 1, '2025-06-29', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(68, 'H-89799', NULL, NULL, NULL, 232, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', '["SGSI","SGC"]', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'GR', 'corporis', NULL, NULL, NULL, 3, NULL, '2025-05-16', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(69, 'H-88979', 587, NULL, 385, 413, 'Larson, Auer and Kreiger', 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Actas de reunión de revisión por la dirección. No se encontró acta correspondiente al segundo semestre de 2025.', 'Ncme', 'SC', 'consequatur', 82, 'con eficacia', NULL, 3, NULL, '2025-11-11', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52');

-- Volcando estructura para tabla kallpaq.hallazgo_causas
CREATE TABLE IF NOT EXISTS `hallazgo_causas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `causa_metodo` enum('ishikawa','cinco_porques') NOT NULL,
  `causa_por_que1` text DEFAULT NULL,
  `causa_por_que2` text DEFAULT NULL,
  `causa_por_que3` text DEFAULT NULL,
  `causa_por_que4` text DEFAULT NULL,
  `causa_por_que5` text DEFAULT NULL,
  `causa_mano_obra` text DEFAULT NULL,
  `causa_metodologias` text DEFAULT NULL,
  `causa_materiales` text DEFAULT NULL,
  `causa_maquinas` text DEFAULT NULL,
  `causa_medicion` text DEFAULT NULL,
  `causa_medio_ambiente` text DEFAULT NULL,
  `causa_resultado` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgos_causas_hallazgo_id_foreign` (`hallazgo_id`),
  CONSTRAINT `hallazgos_causas_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_causas: ~15 rows (aproximadamente)
REPLACE INTO `hallazgo_causas` (`id`, `hallazgo_id`, `causa_metodo`, `causa_por_que1`, `causa_por_que2`, `causa_por_que3`, `causa_por_que4`, `causa_por_que5`, `causa_mano_obra`, `causa_metodologias`, `causa_materiales`, `causa_maquinas`, `causa_medicion`, `causa_medio_ambiente`, `causa_resultado`, `created_at`, `updated_at`) VALUES
	(1, 55, 'ishikawa', NULL, NULL, NULL, 'In rem eum et dignissimos cum.', NULL, 'Quis saepe ea hic est nam. Eos consequatur unde atque earum ut aliquam dolor tempora. Unde corporis ut aut sint tenetur nesciunt dolore. Quis natus architecto esse consectetur est rerum.', 'Eos consequatur at accusamus maxime. Consequuntur sed error nihil deserunt. Esse voluptate est dolor quia ratione non molestiae. Fugiat dolorem tenetur neque et. Ad tempora id qui ut.', NULL, NULL, NULL, 'Non exercitationem illo cumque libero. Iure distinctio quo nobis eum. Qui sequi eligendi occaecati dolorem sint dolores.', 'Voluptates reiciendis consequuntur fuga. Qui et eius iste ex eius quia earum. Magni quia aliquid modi aliquam provident similique. Sit non facilis possimus reprehenderit.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(2, 56, 'cinco_porques', 'Sunt at et aut rerum aut.', 'Culpa unde ullam cumque ipsam hic qui.', NULL, NULL, 'Ipsa non blanditiis eos praesentium sed sed.', 'Rerum natus cupiditate quis corporis saepe modi molestiae. Rem ullam dolores itaque asperiores.', 'Dolores in illo necessitatibus expedita. Deleniti voluptates et id ut. Dicta omnis quo aut rem similique.', NULL, NULL, NULL, NULL, 'Optio aliquid molestias omnis quisquam officia. Atque veritatis rerum architecto ut accusamus delectus. Et ratione et aliquam sit vel aut.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(3, 57, 'ishikawa', NULL, NULL, 'Porro sunt at sit sit et quae animi.', 'Quas iure esse delectus nobis quas.', 'Magnam laudantium dolor aliquam iure consequuntur repudiandae.', 'Aut iste sit vero reiciendis ut odit maiores minima. At veritatis deserunt quam odit accusantium et incidunt. Rem in ipsam fugiat nulla ea rerum. Aperiam odio laboriosam odio cum aut eaque omnis.', NULL, NULL, 'Praesentium veniam rerum rem minus. Omnis rerum consequuntur totam sit sed. Doloribus et non veniam eligendi exercitationem. Odio eum ut sapiente.', 'Minus error sit hic praesentium cumque modi consectetur. Et omnis quo dolores vero eligendi. Est commodi ut soluta et qui.', NULL, 'Sed laudantium similique reiciendis eos explicabo et. Quaerat voluptate expedita ut eligendi omnis. Excepturi facilis tenetur qui temporibus.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(4, 58, 'cinco_porques', NULL, NULL, 'Illum ipsum id amet quia.', 'Necessitatibus ipsa nam voluptas.', NULL, 'Incidunt et aperiam quidem autem dolor. Iure rerum harum modi. Eum aut earum aut rerum et officia. Iusto cum quam asperiores et.', NULL, NULL, 'Eum quia fuga natus qui ut nostrum in. Accusamus voluptas architecto sed adipisci dolores officiis. Voluptas praesentium ipsa doloremque voluptatem saepe dolore nobis. Eos distinctio dolore deserunt sit sunt qui. Perferendis in quia qui a.', 'Quasi corrupti nesciunt voluptas sed cum. Qui nam rerum tempora officia et. Veritatis soluta quaerat omnis ut fuga. Explicabo facere animi quaerat nobis.', 'Reiciendis accusamus optio autem blanditiis impedit iure est et. Hic nobis ipsum dolores veniam numquam temporibus ullam voluptatem. Qui fuga consequatur excepturi beatae deleniti at. Vitae qui deserunt dolores non at vel.', 'Possimus porro illo cum tempore vero iusto minima. In sed pariatur harum est natus earum. Enim aut quia voluptas maiores repudiandae. Inventore fuga eos et omnis.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(5, 59, 'ishikawa', 'Voluptas molestiae sed voluptas error non quas.', NULL, 'Ex itaque et exercitationem maiores sint.', 'Sed inventore autem enim doloribus vitae blanditiis.', 'Enim odit iusto ullam omnis qui.', 'Praesentium adipisci et rerum doloribus provident aspernatur. Aut voluptatem qui eos aliquam voluptate ea harum velit. Et omnis doloremque cumque qui fuga est. Ea doloribus veritatis doloribus.', 'Explicabo voluptatem veritatis iure deserunt rerum qui nobis. Laboriosam laudantium consequatur aut repellendus quasi. Quia est exercitationem doloremque odio aut aperiam alias. Officia id rerum consectetur dolore blanditiis vel.', 'Qui quam amet unde aut. Eius reiciendis odio maxime quibusdam quaerat. Qui est repellendus et velit. Repellendus ad quod assumenda occaecati tempora. Nobis rerum velit consequatur repellat.', NULL, NULL, NULL, 'Rerum sunt numquam quae ipsa corrupti quisquam. Numquam eius deleniti et omnis repellendus. Veniam aut cumque itaque recusandae alias. Excepturi officiis et adipisci et.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(6, 60, 'cinco_porques', NULL, 'Ut occaecati illum reiciendis accusamus.', NULL, NULL, NULL, NULL, NULL, 'Nostrum maiores nostrum quis impedit quia sed voluptatem sed. Sint quia rerum numquam dolores nostrum et ut. Temporibus pariatur facilis nobis ut mollitia. Similique voluptate et dolores rerum.', 'Soluta laudantium enim unde eius. Placeat deleniti voluptatem dolore dolorem accusantium accusamus voluptatibus. Aut quam quo voluptatem numquam nam facilis. Sed voluptas tempore quis qui ut.', NULL, NULL, 'Et autem soluta magnam est voluptatem. Eum doloribus sint impedit laudantium. Eaque a ut consectetur ab mollitia quos et. Officia sit eaque quae repudiandae in.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(7, 61, 'ishikawa', 'Occaecati earum quasi sint numquam mollitia.', NULL, NULL, 'Laboriosam voluptatum quod quasi laboriosam.', NULL, NULL, 'Sit eveniet quae veniam nobis culpa. Nisi sed quo suscipit quidem eum. Rerum delectus quidem saepe recusandae mollitia impedit aut. Voluptatem ut et reiciendis temporibus consequatur.', 'Officia fugit est error est aliquid sit fuga odio. Fugiat blanditiis et vel eos nisi libero voluptatibus id.', NULL, 'Rem impedit ea illum tempore neque recusandae. Fuga qui illum qui rerum veniam nihil aut. Dolores dolor et sit dolore cumque. Velit aliquid non natus quasi ullam et qui.', NULL, 'Nisi aut rerum perferendis. Dolor voluptas blanditiis earum voluptatem ad quas. Accusamus et perspiciatis asperiores et maxime est. Hic quos ad fugit voluptatem debitis quia perferendis.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(8, 62, 'cinco_porques', NULL, 'Magnam ut quo sed ipsum placeat doloremque ea.', NULL, 'Exercitationem voluptas quia sit unde eum expedita.', NULL, NULL, NULL, 'Pariatur unde id officia accusantium saepe. Cumque ad velit incidunt saepe dolor qui sunt et.', NULL, NULL, 'Quidem ullam qui rerum dolor quisquam aliquam consequatur. Itaque sed debitis maiores saepe fugiat quia et. Culpa perferendis fugit qui vel.', 'Fugit sed quia voluptatum voluptatem rerum consectetur cum. Et aliquam modi odio molestiae libero et. Rerum maxime illo nihil sed dolor id. Ut qui et et provident.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(9, 63, 'ishikawa', NULL, 'Cumque velit exercitationem sed quam similique sint.', NULL, 'Non quibusdam voluptatibus ipsam autem.', 'Repellat unde fuga quaerat possimus.', 'Enim unde reiciendis laboriosam quasi placeat et. Magni omnis eum mollitia at et tempora accusamus. Fuga accusamus ipsum earum sunt reprehenderit autem eum repudiandae. Ut est voluptatem assumenda et et.', NULL, NULL, NULL, 'Odit quidem sapiente corporis rerum facere placeat. Est rem praesentium eum. Cumque omnis dolor facere fuga quia illum ullam qui.', 'Autem velit impedit rerum rerum temporibus. Sint velit suscipit ad consequuntur fugiat. Optio eum amet fugit vitae ut possimus.', 'Consequatur voluptates labore et voluptatibus amet. Fuga ipsam saepe incidunt. Ducimus deleniti nulla tempora maxime pariatur. Iure in est aut voluptatibus qui.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(10, 64, 'ishikawa', 'Non quas cupiditate tempora perspiciatis quis praesentium enim.', NULL, 'Possimus optio exercitationem vero suscipit qui.', NULL, 'Qui nobis explicabo possimus labore a non.', NULL, 'Sit eligendi assumenda laudantium aut beatae voluptate perspiciatis est. Ut reiciendis modi eos est sed incidunt. Inventore aliquam optio optio laboriosam beatae esse. Debitis est beatae deserunt ratione vel.', NULL, NULL, 'Laudantium eos molestiae dolore. Consequatur autem quos ut. Rerum voluptatibus magnam error porro est.', NULL, 'Omnis voluptate velit maiores quo eum non. Ipsam laudantium iste rerum occaecati quidem esse aut. Aut id accusantium officiis aut consequatur. Ut eaque ut id iusto est id repellat omnis.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(11, 65, 'ishikawa', NULL, 'Fugit et suscipit temporibus laudantium est necessitatibus.', 'Repudiandae neque quas et est soluta eos.', NULL, NULL, 'Similique asperiores quam reiciendis autem doloribus ratione. Ab qui et doloremque maiores blanditiis laudantium. Praesentium labore nulla incidunt voluptas qui quia.', NULL, 'Quisquam minus iste itaque reprehenderit et nulla magni. Exercitationem est est qui sed quis beatae. Et sit veritatis sint nihil nobis et alias totam. Ad inventore saepe totam itaque.', 'Quia ullam quos repellat. Quisquam veniam sit maxime id alias enim nam et. Unde omnis ducimus expedita. Dolorem et fugit id minima possimus.', NULL, 'Dolores minima rem voluptates. Sequi sit eaque et molestiae quis. Doloremque quis voluptatibus commodi enim eum et ex.', 'Quo aspernatur dicta ratione debitis. Quaerat blanditiis qui ut excepturi corporis voluptatem. Sit ipsum in omnis. Repellendus vitae ea provident maxime saepe ut eum.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(12, 66, 'cinco_porques', NULL, 'Eum consequatur consequatur exercitationem cupiditate cupiditate facilis beatae.', 'Eum repudiandae ipsa quo at sed quia quam.', NULL, NULL, 'Sed quidem qui qui possimus alias. Quas dignissimos labore fugiat quae sit. Id consequatur officia qui maiores tempora sed officia. Rem laudantium exercitationem autem et.', NULL, NULL, NULL, 'Atque ut dolor quasi repudiandae qui id voluptatibus. Labore quisquam error blanditiis est ducimus sint. Illo hic ut aut doloremque ut ab iusto id. Nesciunt ex corrupti facere.', NULL, 'Iure sequi eos vitae qui placeat praesentium fugiat deleniti. Aut dignissimos dolor quis enim explicabo velit facere. Nulla porro recusandae non nisi. Consequuntur alias totam ratione.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(13, 67, 'cinco_porques', NULL, 'Repudiandae dignissimos facilis ut quia consequatur dignissimos a nihil.', NULL, 'Nostrum vel aspernatur nihil aliquid.', NULL, NULL, NULL, NULL, 'Ut quod nihil porro ipsam nisi. Error est sunt eum debitis cumque. Quod voluptas temporibus est facere est asperiores.', 'Veniam laudantium aut asperiores nesciunt repellat. Aut facere quis voluptatem sapiente corrupti. Cupiditate et in voluptas. Maiores aliquid cupiditate voluptatum architecto.', 'Rerum cum autem suscipit vel molestiae. Sed sint omnis dolore et rerum ut.', 'Est dolores qui blanditiis ducimus sit officia aut. Dignissimos qui dolorem quia voluptas animi. Dicta aut molestiae dignissimos tempora magni. Earum exercitationem voluptatem animi excepturi tempore ipsum commodi.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(14, 68, 'cinco_porques', NULL, NULL, 'Non debitis enim perspiciatis dolorem consequatur necessitatibus perferendis necessitatibus.', 'Et sit eaque excepturi nisi.', NULL, 'Repellat cupiditate iusto sapiente in laudantium qui. Ut consequatur nam eaque voluptatem repellat sint perferendis. Ad sint mollitia consectetur quos earum. Laborum dolores id sint perspiciatis et tenetur dolores.', 'Sit eveniet ipsum quas id sit fugiat iste. Quas voluptatum recusandae aut.', 'Consequatur earum veniam voluptatibus quisquam. Quibusdam ea quo sit quo. Excepturi aspernatur sapiente veritatis ipsum laboriosam nostrum. Odit et aut est quis aspernatur ipsa.', NULL, 'Sed hic consequatur illo est. Aut nobis iusto velit dolore a cupiditate. Qui ipsum rerum et et quia.', NULL, 'Sed hic in reprehenderit perspiciatis architecto veritatis sequi. Quis minima facere dolore incidunt velit perspiciatis hic veritatis. Ut perferendis voluptatem magni libero saepe maiores ipsum. Sint sit voluptatem ut dignissimos harum accusantium.', '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(15, 69, 'cinco_porques', NULL, 'Id blanditiis ut labore in tempore et error.', NULL, NULL, NULL, NULL, NULL, 'Error tempora enim est et. Qui nisi aliquid dignissimos perspiciatis reiciendis quae dolorem. Sunt nulla et non architecto in aliquid suscipit. Voluptas aut doloremque ea aut in possimus.', NULL, 'Consequatur necessitatibus illo non reprehenderit. Quis odio labore sed. Nemo earum eligendi autem voluptatibus sunt. Aspernatur quia vel quo qui ut.', 'Voluptatem est omnis est veniam. Sunt ea nihil tenetur et. Vitae hic rerum velit accusantium. Aperiam ducimus reiciendis corrupti commodi eum nobis commodi. Cupiditate ab mollitia inventore assumenda et deleniti.', 'Reiciendis vitae ut non. Reprehenderit quae numquam quia explicabo vel odio ipsa. Voluptas et non reprehenderit molestiae ullam. Dolores hic quo et est et.', '2025-11-11 22:59:52', '2025-11-11 22:59:52');

-- Volcando estructura para tabla kallpaq.hallazgo_evaluaciones
CREATE TABLE IF NOT EXISTS `hallazgo_evaluaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `evaluador_id` bigint(20) unsigned NOT NULL,
  `resultado` varchar(255) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgo_evaluacions_hallazgo_id_foreign` (`hallazgo_id`),
  KEY `hallazgo_evaluacions_evaluador_id_foreign` (`evaluador_id`),
  CONSTRAINT `hallazgo_evaluacions_evaluador_id_foreign` FOREIGN KEY (`evaluador_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hallazgo_evaluacions_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_evaluaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.hallazgo_movimientos
CREATE TABLE IF NOT EXISTS `hallazgo_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `estado` varchar(255) NOT NULL,
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgo_movimientos_hallazgo_id_foreign` (`hallazgo_id`),
  KEY `hallazgo_movimientos_user_id_foreign` (`user_id`),
  CONSTRAINT `hallazgo_movimientos_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hallazgo_movimientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_movimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.hallazgo_proceso
CREATE TABLE IF NOT EXISTS `hallazgo_proceso` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgo_proceso_hallazgo_id_foreign` (`hallazgo_id`),
  KEY `hallazgo_proceso_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `hallazgo_proceso_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hallazgo_proceso_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_proceso: ~0 rows (aproximadamente)
REPLACE INTO `hallazgo_proceso` (`id`, `hallazgo_id`, `proceso_id`, `created_at`, `updated_at`) VALUES
	(62, 55, 114, NULL, NULL),
	(63, 55, 274, NULL, NULL),
	(64, 55, 119, NULL, NULL),
	(65, 56, 60, NULL, NULL),
	(66, 57, 138, NULL, NULL),
	(67, 57, 86, NULL, NULL),
	(68, 57, 212, NULL, NULL),
	(69, 58, 118, NULL, NULL),
	(70, 58, 42, NULL, NULL),
	(71, 59, 70, NULL, NULL),
	(72, 60, 169, NULL, NULL),
	(73, 60, 245, NULL, NULL),
	(74, 60, 92, NULL, NULL),
	(75, 61, 274, NULL, NULL),
	(76, 61, 39, NULL, NULL),
	(77, 61, 264, NULL, NULL),
	(78, 62, 36, NULL, NULL),
	(79, 63, 91, NULL, NULL),
	(80, 63, 260, NULL, NULL),
	(81, 63, 267, NULL, NULL),
	(82, 64, 128, NULL, NULL),
	(83, 65, 282, NULL, NULL),
	(84, 66, 116, NULL, NULL),
	(85, 67, 149, NULL, NULL),
	(86, 67, 226, NULL, NULL),
	(87, 68, 142, NULL, NULL),
	(88, 68, 163, NULL, NULL),
	(89, 68, 83, NULL, NULL),
	(90, 69, 224, NULL, NULL),
	(91, 69, 99, NULL, NULL);

-- Volcando estructura para tabla kallpaq.indicadores
CREATE TABLE IF NOT EXISTS `indicadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `planificacion_pei_id` bigint(255) DEFAULT NULL,
  `planificacion_sig_id` bigint(255) DEFAULT NULL,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `fuente` text NOT NULL,
  `tipo_indicador` enum('Producto','Servicio','Resultado','Calidad') NOT NULL,
  `sgc` tinyint(1) NOT NULL,
  `sgas` tinyint(1) NOT NULL,
  `sgcm` tinyint(1) NOT NULL,
  `sgsi` tinyint(1) NOT NULL,
  `sgce` tinyint(1) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicadores_proceso_cod_foreign` (`proceso_id`),
  CONSTRAINT `indicadores_proceso_cod_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores: ~3 rows (aproximadamente)
REPLACE INTO `indicadores` (`id`, `proceso_id`, `planificacion_pei_id`, `planificacion_sig_id`, `nombre`, `descripcion`, `fuente`, `tipo_indicador`, `sgc`, `sgas`, `sgcm`, `sgsi`, `sgce`, `estado`, `formula`, `frecuencia`, `meta`, `tipo_agregacion`, `parametro_medida`, `sentido`, `var1`, `var2`, `var3`, `var4`, `var5`, `var6`, `created_at`, `updated_at`) VALUES
	(1, 110, 1, 2, 'Porcentaje de Procedimientos del TUPA actualizados', 'Medir la actualización del TUPA de la CGR', 'Información del tupa', 'Producto', 0, 1, 1, 0, 0, 1, 'var1/var2', 'mensual', 0.80, 'no acumulada', 'porcentaje', 'ascendente', 'Cantidad de  Procedimientos Administrativos (PA) actualizados', 'Total de PA', NULL, NULL, NULL, NULL, '2023-05-26 23:01:48', '2025-02-18 19:03:11'),
	(2, 35, 2, 2, 'Indicador prueba1', 'Prueba1', 'Nuevo1', 'Producto', 0, 0, 0, 0, 0, 1, 'var1/var2', 'trimestral', 0.90, 'acumulada', 'tasa', 'ascendente', 'var1', 'var2', NULL, NULL, NULL, NULL, '2024-06-04 21:06:47', '2025-02-19 14:24:17'),
	(3, 36, 3, 3, 'Ejemploi', 'Ejempó', 'Ejempñpo', 'Producto', 0, 0, 0, 0, 0, 1, 'var1+var2+var3', 'mensual', 120.00, 'no acumulada', 'ratio', 'ascendente', 'v1', 'v2', 'v3', NULL, NULL, NULL, '2024-06-05 05:02:47', '2024-06-05 05:02:47');

-- Volcando estructura para tabla kallpaq.indicadores_historico
CREATE TABLE IF NOT EXISTS `indicadores_historico` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `indicador_proceso_ouo_id` bigint(20) unsigned NOT NULL,
  `año` year(4) NOT NULL,
  `meta` double(8,2) NOT NULL,
  `valor` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicadores_historico_indicador_id_foreign` (`indicador_proceso_ouo_id`),
  CONSTRAINT `indicadores_historico_indicador_id_foreign` FOREIGN KEY (`indicador_proceso_ouo_id`) REFERENCES `indicadores_proceso_ouo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores_historico: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.indicadores_proceso_ouo
CREATE TABLE IF NOT EXISTS `indicadores_proceso_ouo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_proceso_ouo` bigint(20) unsigned NOT NULL,
  `id_indicador` bigint(20) unsigned NOT NULL,
  `meta_programada` double(8,2) NOT NULL,
  `year_programado` char(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_proceso_ouo` (`id_proceso_ouo`),
  KEY `id_indicador` (`id_indicador`),
  CONSTRAINT `indicadores_proceso_ouo_id_indicador_foreign` FOREIGN KEY (`id_indicador`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `indicadores_proceso_ouo_id_proceso_ouo_foreign` FOREIGN KEY (`id_proceso_ouo`) REFERENCES `procesos_ouo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores_proceso_ouo: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.indicadores_seguimiento
CREATE TABLE IF NOT EXISTS `indicadores_seguimiento` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `indicador_proceso_ouo_id` bigint(20) unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicador_proceso_ouo_id` (`indicador_proceso_ouo_id`),
  CONSTRAINT `indicadores_seguimiento_ibfk_1` FOREIGN KEY (`indicador_proceso_ouo_id`) REFERENCES `indicadores_proceso_ouo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores_seguimiento: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.informe_auditoria
CREATE TABLE IF NOT EXISTS `informe_auditoria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auditoria_id` bigint(20) unsigned NOT NULL,
  `fecha_emision` date NOT NULL,
  `informe_pdf` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `informe_auditoria_auditoria_id_foreign` (`auditoria_id`),
  CONSTRAINT `informe_auditoria_auditoria_id_foreign` FOREIGN KEY (`auditoria_id`) REFERENCES `auditorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.informe_auditoria: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.inventarios
CREATE TABLE IF NOT EXISTS `inventarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `documento_aprueba` text NOT NULL,
  `vigencia` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enlace` text NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.inventarios: ~2 rows (aproximadamente)
REPLACE INTO `inventarios` (`id`, `nombre`, `descripcion`, `documento_aprueba`, `vigencia`, `enlace`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 'Mapa de Procesos, la Gobernanza de Procesos y el Inventario de procesos de la Contraloría General de la República 2021.', 'version inicial', 'Resolución de Contraloría N° 279-2021-CG ', '2021-12-06 17:44:07', 'http://webserverapp.contraloria.gob.pe/Inicio/Bienestar_Docs/RC_279-2021-CG.pdf', 0, NULL, NULL),
	(2, 'Mapa de Procesos de la Contraloría General de la República 2021 v2', 'PM01 "Prevención de la corrupción", el cual pasa a denominarse PM01 "Prevención y detección de la corrupción".', 'Resolución de Contraloría N° 255-2022-CG', '2025-05-13 17:45:30', 'http://webserverapp.contraloria.gob.pe/Calidad/Documentos/RC-255-2022-CG_Modifica_Mapa_de_Procesos.pdf', 1, NULL, NULL);

-- Volcando estructura para tabla kallpaq.inventario_procesos
CREATE TABLE IF NOT EXISTS `inventario_procesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_inventario` bigint(20) unsigned NOT NULL DEFAULT 0,
  `id_proceso` bigint(20) unsigned NOT NULL,
  `id_ouo_responsable` bigint(20) unsigned NOT NULL,
  `id_ouo_delegada` bigint(20) unsigned NOT NULL,
  `estado` tinyint(4) NOT NULL,
  `inactive_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `inventario_procesos_id_proceso_foreign` (`id_proceso`),
  KEY `inventario_procesos_id_ouo_responsable_foreign` (`id_ouo_responsable`),
  CONSTRAINT `inventario_procesos_id_ouo_responsable_foreign` FOREIGN KEY (`id_ouo_responsable`) REFERENCES `ouos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_procesos_id_proceso_foreign` FOREIGN KEY (`id_proceso`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.inventario_procesos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.migrations: ~75 rows (aproximadamente)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
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
	(100, '2025_01_16_182503_create_indicadores_proceso_ouo', 26),
	(102, '2025_02_24_142211_create_area_compliance', 27),
	(103, '2025_02_24_144821_create_obligaciones', 27),
	(105, '2025_02_24_151737_create_obligacion_riesgo_table', 28),
	(106, '2025_05_08_172140_create_diagrama_contextos_table', 29),
	(107, '2025_05_09_104555_create_sipocs_table', 30),
	(108, '2025_05_09_104823_create_salidas_table', 30),
	(109, '2025_05_09_104835_create_requisitos_table', 30),
	(110, '2025_05_13_114418_create_inventario_table', 31),
	(111, '2025_05_19_095638_create_documento_versiones_table', 32),
	(112, '2025_06_12_115310_create_requerimiento_evaluacions_table', 33),
	(113, '2025_06_13_140718_create_requerimiento_avances_table', 33),
	(114, '2025_06_17_173257_create_documento_relacionado_table', 33),
	(115, '2025_06_17_203044_create_documento_alertas_table', 33),
	(116, '2025_06_20_154542_create_tags_table', 33),
	(117, '2025_06_20_154750_create_documento_tag_table', 33),
	(118, '2025_06_23_105358_create_documento_movimientos_table', 33),
	(119, '2025_06_26_121132_create_subcategorias_compliance_table', 33),
	(120, '2025_07_15_145508_create_partes_interesadas_table', 33),
	(121, '2025_07_15_145821_create_necesidades_expectativas_table', 33),
	(122, '2025_07_15_150420_create_procesos_expectativas_table', 33),
	(123, '2025_08_13_201343_create_documentos_procesos_table', 33),
	(125, '2025_11_03_120000_add_missing_columns_to_procesos_table', 34),
	(126, '2025_11_05_190622_update_documentos_table', 35),
	(127, '2025_11_05_191728_create_planificacion_pei_table', 36),
	(129, '2025_11_10_171758_update_hallazgos_table_for_model', 37),
	(130, '2025_11_10_174106_update_acciones_table_for_model', 37),
	(131, '2025_11_10_180423_create_hallazgo_proceso_table', 38),
	(132, '2025_11_10_180553_update_hallazgos_causas_table_for_model', 38),
	(133, '2025_11_10_180946_create_hallazgo_movimientos_table', 39),
	(134, '2025_11_11_110801_create_accion_movimientos_table', 40),
	(135, '2025_11_11_164108_create_hallazgo_evaluacions_table', 41),
	(136, '2025_11_11_165343_add_ciclo_to_hallazgos_and_acciones_tables', 42);

-- Volcando estructura para tabla kallpaq.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.model_has_permissions: ~6 rows (aproximadamente)
REPLACE INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 1),
	(3, 'App\\Models\\User', 2),
	(4, 'App\\Models\\User', 1);

-- Volcando estructura para tabla kallpaq.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `model_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.model_has_roles: ~3 rows (aproximadamente)
REPLACE INTO `model_has_roles` (`model_id`, `model_type`, `role_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(21, 'App\\Models\\User', 5);

-- Volcando estructura para tabla kallpaq.necesidades_expectativas
CREATE TABLE IF NOT EXISTS `necesidades_expectativas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_interesada_id` bigint(20) unsigned NOT NULL,
  `expectativa_tipo` enum('necesidad','expectativa') NOT NULL,
  `expectativa_descripcion` text NOT NULL,
  `expectativ_sig` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expectativ_sig`)),
  `proceso_id` bigint(20) unsigned NOT NULL,
  `expectativa_observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `necesidades_expectativas_parte_interesada_id_foreign` (`parte_interesada_id`),
  CONSTRAINT `necesidades_expectativas_parte_interesada_id_foreign` FOREIGN KEY (`parte_interesada_id`) REFERENCES `partes_interesadas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.necesidades_expectativas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.obligaciones
CREATE TABLE IF NOT EXISTS `obligaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `area_compliance_id` bigint(20) unsigned NOT NULL,
  `documento_tecnico_normativo` text NOT NULL,
  `obligacion_principal` text NOT NULL,
  `obligacion_controles` text NOT NULL,
  `consecuencia_incumplimiento` text NOT NULL,
  `documento_deroga` text DEFAULT NULL,
  `estado_obligacion` enum('pendiente','mitigada','controlada','vencida','inactiva','suspendida') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obligaciones_proceso_id_foreign` (`proceso_id`),
  KEY `obligaciones_area_compliance_id_foreign` (`area_compliance_id`),
  CONSTRAINT `obligaciones_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.obligaciones: ~48 rows (aproximadamente)
REPLACE INTO `obligaciones` (`id`, `proceso_id`, `area_compliance_id`, `documento_tecnico_normativo`, `obligacion_principal`, `obligacion_controles`, `consecuencia_incumplimiento`, `documento_deroga`, `estado_obligacion`, `created_at`, `updated_at`) VALUES
	(2, 117, 14, 'Directiva N° 003-2024-CG/GJNC “Gestión Normativa en la Contraloría General de la República”, aprobada con Resolución de Contraloría N° 159-2024-CG.', 'Asegurar que la documentación de los procesos se realice de manera transversal y secuencial para satisfacer las necesidades y expectativas de las partes interesadas.', 'PR-NORM-06 Procedimiento "Gestión de documentos en el alcance del Sistema Integrado de Gestión"', 'Disminución en la capacidad de la organización para cumplir con los objetivos institucionales, lo que podría comprometer el éxito de los proyectos y la satisfacción de los ciudadanos.', NULL, 'pendiente', '2025-02-25 17:55:11', '2025-03-26 15:55:51'),
	(4, 105, 13, 'Normas ISO 19011, ISO 9001, ISO 37001, ISO 37301.', 'Programación y ejecución de auditorías para la evaluación de la eficacia del Sistema Integrado de Gestión', 'MG-MODER-02 Manual del Sistema Integrado de Gestión\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección, PR-MODER-07 Procedimiento "Auditorías Internas", PR-MODER-11 Procedimiento "Revisión de la Función de Cumplimiento"', 'Perdida de los certificados, daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', '2025-02-25 23:13:09', '2025-03-24 20:49:01'),
	(5, 106, 14, 'Norma ISO 9001:2015, Sistemas de gestión de la Calidad – Requisitos', 'Cumplir con los requisitos dispuestos por la norma internacional', 'MG-MODER-02 Manual del Sistema Integrado de Gestión.\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección.\r\nPR-MODER-07 Procedimiento "Auditorías Internas"\r\nPR-MODER-01 Procedimiento ""Gestión de Indicadores de Desempeño de Procesos"\r\nPR-MODER-05 Procedimiento "Control de Salidas No Conformes"\r\nPR-MODER-03 Procedimiento "Satisfacción del Cliente"\r\nPR-NORM-06 Procedimiento "Gestión de documentos en el alcance del SIG"', 'Daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', '2025-02-25 23:15:20', '2025-03-26 15:57:05'),
	(6, 107, 14, 'Norma ISO 31000:2018, Gestión del riesgo,  \r\nDirectiva N° 017-2020 “Gestión del riesgo en la Contraloría General de la República”.', 'Planificar, identificar, analizar, evaluar e identificar los riesgos que afecten los cumplimientos de los objetivos institucionales', 'MG-MODER-02 Manual del Sistema Integrado de Gestión\r\nPR-MODER-04 Procedimiento "Gestión del Riesgos"', 'Disminución en la capacidad de la organización para cumplir con los objetivos institucionales, lo que podría comprometer el éxito de los proyectos y la satisfacción de los ciudadanos.', NULL, 'pendiente', '2025-02-25 23:17:01', '2025-02-26 17:58:33'),
	(7, 113, 14, 'Norma Técnica “Implementación de la Gestión por Procesos en las Entidades de la Administración Pública”, aprobada mediante RSGP N° 002-2025-PCM/SGP.', 'Cumplir con los requisitos dispuestos por la norma internacional', 'PR-MODER-09 Procedimiento "Implementación de Mejora de Procesos"\r\nPR-MODER-14 Procedimiento "Mapeo de Procesos"\r\nPR-MODER-16 Procedimiento "Diseño de procesos', 'Impacto en la calidad de los servicios prestados y consecuente pérdida de la confianza y reputación ante los ciudadanos', NULL, 'pendiente', '2025-02-25 23:17:51', '2025-03-26 16:00:47'),
	(8, 114, 14, 'Norma ISO 37301:2021, Sistemas de gestión del compliance – Requisitos con orientación para su uso.', 'Cumplir con los requisitos dispuestos por la norma internacional', 'MG-MODER-02 Manual del Sistema Integrado de Gestión.\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección.\r\nPR-MODER-07 Procedimiento "Auditorías Internas"\r\nPR-MODER-18 Procedimiento "Gestión de las Obligaciones de Compliance"\r\nPR-MODER-19 Procedimiento "Revisión de la función de Compliance"\r\nPR-NORM-06 Procedimiento "Gestión de documentos en el alcance del SIG"', 'Daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', '2025-02-25 23:18:06', '2025-03-26 15:57:21'),
	(10, 164, 14, 'Directiva No 013-2023-CG/GAD Requerimiento,\r\notorgamiento y rendición de viáticos por comisión de servicio para la Contraloría General de la Republica.', 'Atención de los requerimientos de viáticos\r\ndentro del plazo establecido.\r\nRealización de la rendición de los viáticos\r\ndentro del plazo establecido.\r\nEvaluación de la rendición de viáticos.', 'Seguimiento y control del proceso mediante el aplicativo informático SIGA viáticos, por parte del Analista de viáticos.\r\n(PR-ARGF-03) Gestión de viáticos', 'Saldos elevados pendientes de rendir en la cuenta\r\ncontable "Otras entregas a rendir cuenta".', NULL, 'pendiente', '2025-02-28 00:15:48', '2025-02-28 00:17:03'),
	(11, 164, 14, 'Ley No 27619, Ley que regula la autorización de viajes al exterior de servidores y funcionarios públicos y modificatorias.', 'Atención de los requerimientos de viáticos de viajes al exterior de acuerdo a la escala proporcionada en este decreto supremo.', 'Coordinación para la emisión de Resolución.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA viáticos, por parte del Analista de viáticos.', 'Atención fuera de tiempo del requerimiento de viáticos al exterior y reclamos por parte de los comisionados.', NULL, 'pendiente', '2025-02-28 00:26:02', '2025-02-28 00:26:02'),
	(12, 166, 14, 'Resolución Directoral N.º 004-2009-EF/77.15 que aprueba la Modificación del art. 40 de la Directiva de Tesorería N.º 001-2007-EF/77.15.', 'Autorización el requerimiento de anticipos mediante Resolución de la Gerencia de\r\nAdministración.\r\nRegistro de la rendición de cuentas del anticipo\r\nen el SIAF.\r\nPlazos para rendición del anticipo de 03 días hábiles, luego de concluida la ejecución del gasto.', '(PR-EJPRE-05) Gestión de anticipos.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA anticipos.\r\nNiveles de revisión del requerimiento de\r\nanticipos.', 'Incumplimiento de las actividades programadas por parte del usuario y posibles reclamos.', NULL, 'pendiente', '2025-02-28 00:48:46', '2025-02-28 00:48:46'),
	(13, 166, 14, 'Resolución Directoral N.º 040-2011-EF/52.03, que ha\r\nestablecido el monto máximo para el otorgamiento de Encargos al Personal de la Institución.', 'Establece monto máximo para el otorgamiento\r\nde anticipos.', '(PR-EJPRE-05) Gestión de anticipos.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA anticipos, el cual no permite registrar requerimiento por importes mayores a los establecidos.', 'Observaciones en las auditorías anuales a la cuenta.', NULL, 'pendiente', '2025-02-28 18:58:57', '2025-02-28 18:58:57'),
	(14, 166, 14, '-Resolución de Superintendencia No 007-99-SUNAT, Reglamento de comprobantes de Pago y sus modificatorias.\r\n-Resolución de Superintendencia N.º 037-2002/SUNAT, que aprueba el Régimen de Retenciones del IGV aplicables a los proveedores y designación de agentes de retención y sus modificatorias.\r\n-Resolución de Superintendencia N.º 183-2004/SUNAT, que aprueba normas para la aplicación del Sistema de Pago de Obligaciones Tributarias con el Gobierno Central al que se refiere el Decreto Legislativo N.º 940 y sus modificatorias.\r\n-Resolución de Superintendencia N.º 287-2014/SUNAT, que modifica la Resolución de Superintendencia Nº182- 2014/SUNAT y modificatoria que implementó la emisión electrónica de recibo de honorarios.', '-Verificar que los comprobantes de pagos cuenten con las especificaciones determinadas por SUNAT.\r\n-Verificación de la aplicación de retenciones a\r\nlos comprobantes de pago, en los casos correspondientes.\r\n-Verificación de la aplicación de detracciones a\r\nlos comprobantes de pago, en los casos correspondientes.\r\n-Verificar que los recibos por honorarios que\r\nforman parte de la rendición de cuentas sean electrónicos.', 'Seguimiento y control del proceso mediante el aplicativo informático SIGA anticipos.', 'El incumplimiento de las obligaciones tributarias puede generar responsabilidades administrativas, sanciones y afectar la reputación de la institución.', NULL, 'pendiente', '2025-02-28 19:43:36', '2025-02-28 19:46:11'),
	(15, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica".\r\n-Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Apertura de Caja Chica de manera oportuna', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\nCoordinación con los OUO competentes por parte de la Gerencia de Administración.', 'No se pueden ejecutar gastos por caja chica.', NULL, 'pendiente', '2025-02-28 19:53:23', '2025-02-28 19:53:23'),
	(16, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Pago por caja chica de gastos que cumplan con\r\nlos requisitos establecidos', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\n-Revisión del gasto por parte del responsable del manejo de caja chica, si cumple con lo señalado en la normativa.\r\n-Verificación por parte del responsable del manejo de la caja chica, del registro de los gastos en el aplicativo SIGA Caja Chica, realizado por el colaborador.\r\n-Coordinación y asistencia con personal de la Gerencia de Administración, respecto al gasto.', 'No reconocimiento de gastos ejecutados que incumplan la normativa.\r\nFalta de disponibilidad de efectivo por la demora en la subsanación de observaciones del expediente de rendición de cuentas.', NULL, 'pendiente', '2025-02-28 20:43:37', '2025-02-28 20:43:37'),
	(17, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Validar que el expediente de rendición de caja chica cumpla con las especificaciones establecidas en la normativa para la reposición correspondiente.', '-Seguimiento y control del proceso mediante el aplicativo informático SIGA Caja chica.\r\n-Seguimiento y control del expediente para pago a través del SGD.', 'No atención de gastos urgentes, debido a la falta de liquidez por la demora de la reposición.', NULL, 'pendiente', '2025-02-28 20:46:25', '2025-02-28 20:46:25'),
	(18, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Liquidación final de Caja Chica dentro del plazo\r\nestablecido.', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\nCoordinación con los OUO competentes por parte de la Gerencia de Administración.\r\n-Control y revisión de los expedientes de cierre de caja chica, tramitados previamente por correo y luego a través del SGD.', 'Emisión de los estados financieros y presupuestarios con observaciones', NULL, 'pendiente', '2025-02-28 20:49:40', '2025-02-28 20:49:40'),
	(19, 92, 14, 'Decreto Legislativo Nº 1439, Decreto Legislativo del Sistema Nacional de Abastecimiento.', 'Decreto Legislativo que genera obligación de: Revisar y/o validar el Acta de Conciliación de Bienes y Suministros de manera mensual con la\r\nSubgerencia de Abastecimiento, con las \r\n suscripciones de las áreas usuarias\r\n(Abastecimiento y Contabilidad).', 'Revisión y evaluación del Acta de Conciliación de Bienes y Suministros, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', '2025-02-28 20:58:53', '2025-02-28 20:58:53'),
	(20, 92, 14, 'Decreto Legislativo Nº 1440, Decreto Legislativo del Sistema Nacional de Presupuesto Público.', 'Decreto Legislativo que genera obligación de:\r\n- Revisar y/o validar el Acta de Conciliación del\r\nMarco Legal y Ejecución del Presupuesto, con\r\nlas suscripciones de Contabilidad y Presupuesto', 'Revisión y evaluación del Acta de Conciliación del Marco Legal y Ejecución del Presupuesto, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', '2025-02-28 21:00:00', '2025-02-28 21:00:00'),
	(21, 92, 14, 'Decreto Legislativo Nº 1441, Decreto Legislativo del Sistema Nacional de Tesorería.', 'Decreto Legislativo que genera obligación de:\r\n- Revisar y/o validar el Acta de Conciliación con\r\nUnidad de Tesorería sobre cuentas corrientes y\r\ncon la Cuenta Única del Tesoro Púbico con la\r\nfuente de financiamiento que corresponda.', 'Revisión y evaluación del Acta de Conciliación con Unidad de Tesorería, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', '2025-02-28 21:00:58', '2025-02-28 21:00:58'),
	(22, 92, 14, 'Decreto Supremo N° 057-2022-EF, Aprueban Texto Único Ordenado del Decreto Legislativo N° 1438 Decreto Legislativo del Sistema Nacional de Contabilidad.\r\nInstructivo Nº003-2024-EF/51.01, Resolución Directoral Nº 007-2024-\r\nEF/51.01.Instructivo para la presentación de la información financiera e\r\ninformación presupuestaria de las entidades del sector público durante el proceso\r\nde transición al Marco de las Normas Internacionales de Contabiliad del Sector\r\nPúblico.\r\nInstructivo Nº004-2024-EF/51.01, Resolución Directoral Nº 011-2024-EF/51.01\r\n"Manual de adopción por primera vez del Marco de las Normas Internacionales de\r\nContabilidad del Sector Público"', 'Decreto Legislativo que genera obligación de:\r\nElaboración de los Estados Financieros y Estados Presupuestarios de la entidad, y los plazos de presentación.', 'Elaboración y seguimiento del cronograma de trabajo de los estados financieros y presupuestarios.\r\nRequerimiento de información financiera y presupuestaria (memorándum, hojas informativas, correos), validados por la GAD.', 'La CGR puede ser considerada omisa ante la DGCP.', NULL, 'pendiente', '2025-02-28 21:02:22', '2025-02-28 21:04:43'),
	(23, 81, 14, 'Resolución Jefatural N° 107-2023-AGN/JEF, que aprueba la Directiva N° 001-2023-AGN/DDPA “Norma de administración de archivos en las entidades públicas”.', 'Se establece disposiciones para la Elaboración y actualización de normativa que regule los procesos archivísticos.', 'Profesional a cargo para las funciones consignadas.', 'Posibles observaciones por parte del Archivo General de la Nación a los Informes Técnicos de Evaluación de Actividades Archivísticos Ejecutadas (ITEA) anuales por ausencia y/o desactualización de normativa archivística para la CGR.', NULL, 'pendiente', '2025-03-05 23:33:53', '2025-03-05 23:33:53'),
	(24, 81, 14, 'Resolución Jefatural N°010-2020-AGN/J, que aprueba la Directiva N° 001-2020-AGN/DDPA “Norma para Servicios Archivísticos en la Entidad Pública".', 'Se establece el cumplimiento de brindar acceso a los documentos archivados considerando las restricciones por confidencialidad, datos personales y grado de deterioro del documento solicitado.', 'Procedimiento "Préstamo de Documentos del Archivo" (PR-TD-05), la actividad 2 señala que el responsable de la OUO en el MTD autoriza la solicitud de préstamo de documentos del archivo.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad, y afectación a los derechos de los involucrados. Denuncias por incidentes en uso de documentación que custodia los archivos de la CGR con carácter confidencial.', NULL, 'pendiente', '2025-03-05 23:36:53', '2025-03-05 23:36:53'),
	(25, 81, 14, 'Resolución Jefatural N° 304-2019-AGN/J, que aprueba la Directiva N° 001-2019-AGN/DC “Norma para la conservación de documentos en los archivos administrativos Sector Público Nacional”', 'Se establece los cumplimientos para adoptar medidas de conservación en lo referente a la sede o local, contenedores y mobiliario, conservación del soporte o medio físico, control de condiciones medioambientales y biológicos y preservación digital.', 'Dotar a los repositorios de Archivo de equipos de control medio ambientales (generadores ozono, deshumedecedores, termohigrómetros), Control de lecturas de temperatura y/o humedad. Recomendaciones respecto a los espacios a asignar para los Archivos.', 'Perdida o deterioro de documentos en el archivo.', NULL, 'pendiente', '2025-03-05 23:39:51', '2025-03-05 23:39:51'),
	(26, 81, 14, 'Resolución de Contraloría N" 220-2024-CG, que aprueba el Programa de Control de Documentos Archivísticos (PCDA) de la Contraloría General de la República.', 'Se establece obligaciones para custodiar la documentación remitida por las unidades de organización al cumplirse el plazo de retención en el archivo de gestión y por series documentales.', 'Programa de Control de Documentos Archivísticos (PCDA) de la CGR vigente.', 'Dificultades en el proceso de eliminación de \r\ndocumentos con valor temporal y la gestión a realizar ante el Archivo General de la Nación. Resguardo deficiente en el archivo de los documentos con valor permanente.', NULL, 'pendiente', '2025-03-05 23:40:55', '2025-03-05 23:40:55'),
	(27, 81, 14, 'Resolución Jefatural N°242-2018-AGN/J, que aprueba la Directiva N° 001-2018-AGN/DAI “Norma para la eliminación de documentos de archivo del sector público".', 'Se establece la obligación de presentar cronograma de eliminación de documentos en el Plan Anual de Trabajo Archivístico. - Solicitar al AGN o AR la autorización de eliminación a través del PCDA o Comité Evaluador de Documentos.', 'Procedimiento "Eliminación de Documentos del Archivo Central y Archivos Periféricos de las Gerencias Regionales de Control" PR-ARCH-05', 'Observaciones del Archivo General de la Nación por la inadecuada conservación de los documentos y mantenimiento de documentación fuera del plazo de retención establecido en el Programa de Control de Documentos Archivísticos de CGR.', NULL, 'pendiente', '2025-03-05 23:42:16', '2025-03-05 23:42:16'),
	(28, 219, 14, 'Contrato de préstamo N° 4724/OC-PE', 'Establece los mecanismos, procedimientos, instancias, responsabilidades y nomas que debe impartirse para la ejecución del proyecto, en al cual es de obligatorio el cumplimiento', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las condiciones del contrato para la ejecucion de los proyectos internosNO objeciones por parte del BID sobre elegibilidad de gastos', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', '2025-03-06 14:02:40', '2025-03-06 14:02:40'),
	(29, 219, 14, 'Contrato de préstamo N° 4724/OC-PE', 'Establece los acuerdos contractuales del préstamo para la ejecución del proyecto BID 3.', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las condiciones del contrato para la ejecucion de los proyectos internosNO objeciones por parte del BID sobre elegibilidad de gastos', 'Posible suspensión en la ejecucion del proyecto', NULL, 'pendiente', '2025-03-06 14:03:38', '2025-03-06 14:03:38'),
	(30, 220, 14, 'Políticas BID para la Selección y Contratación de Consultores Financiados por el Banco Interamericano de Desarrollo. GN-2350-15', 'Establece las políticas de selección y contratación de consultores Financiados por el BID, en el marco del desarrollo del proyecto BID 3.', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las políticas de selección y contratación de consultores y de las condiciones del contrato de prestamoNO objeciones por parte del BID sobre las condiciones de las contrataciones de consultores individuales', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', '2025-03-06 14:04:20', '2025-03-24 22:28:43'),
	(31, 219, 14, 'Políticas BID para la Adquisición de Bienes y Obras financiadas por el Banco Interamericano de Desarrollo. GN-2349-15', 'Establece las políticas para la adquisición de bienes y obras financiadas por el BID, en el marco del desarrollo del proyecto BID', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las políticas para adquisición de bienes y obras y de las condiciones del contrato de prestamoNO objeciones por parte del BID sobre las condiciones de las contrataciones de adquisición de bienes y obras', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', '2025-03-06 14:04:57', '2025-03-06 14:04:57'),
	(32, 30, 14, 'Normas Técnicas de CEPLAN, Procedimiento PR-PEI-01', 'Alinear el planeamiento estratégico de la CGR al Plan Nacional de Desarrollo e implementar las políticas institucionales de la CGR', 'Evaluación anual del cumplimiento de los indicadores', 'Falta de implementación de la Política Institucional', NULL, 'pendiente', '2025-03-24 21:34:46', '2025-03-24 21:34:46'),
	(33, 32, 14, 'Directiva N° 001-2024 - CEPLAN/PCD Directiva General de Planeamiento Estrtaégico\r\nComunicado de CEPLAN-Dirección General de Abastecimiento del MEF', 'Alcanzar a la Subgerencia de Abastecimiento el archivo "TXT" con la programación operativa para el siguiente año, en el plazo establecido por CEPLAN-DGA del MEF (primera quincena del mes de marzo del año anterior)', 'Emisión de Memorando solicitando a los OUO de la CGR que elaboren su programación operativa para el siguiente año y que realicen la distribución del presupuesto asignado y el registro correspondiente en el aplicativo CEPLAN', 'Incumplimiento de la elaboración oportuna del Cuadro de Necesidades antes de la quincena del mes de marzo del año anterior. Percepción inadecuada de transparencia e integridad al no tomar en cuenta la Directiva de CEPLAN', NULL, 'pendiente', '2025-03-24 21:38:41', '2025-03-24 21:38:41'),
	(34, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN\r\n(PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Aprobar el Plan Operativo Institucional Multianual dentro del plazo establecido, antes del 30 de abril del año anterior', 'Emisión de Memorando Circular por parte de la GMPL solicitando la elaboración del Plan Operativo Institucional Multianual del siguiente período y su registro en el aplicativo CEPLAN', 'Afectación del prestigio y transparencia de la Contraloría General', NULL, 'pendiente', '2025-03-24 21:40:12', '2025-03-24 21:40:12'),
	(35, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN (PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Efectuar el Seguimiento del Plan Operativo Institucional en el aplicativo CEPLAN antes del día 20 del mes del siguiente mes', 'Seguimiento a los registros mensuales de avance de servicios de control y actividades en el aplicativo de la CGR y en el aplicativo de CEPLAN', 'Instancias competentes no cuenten con información oportuna para la toma de decisiones', NULL, 'pendiente', '2025-03-24 21:41:07', '2025-03-24 21:41:07'),
	(36, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN (PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Efectuar la Evaluación Trimestral del Plan Operativo Institucional Anual', 'Seguimiento a los registros trimestrales de avance de servicios de control y actividades', 'Incumplimiento de elaborar la Evaluación del Plan Operativo Institucional a más tardar el siguiente mes de concluido el trimestre', NULL, 'pendiente', '2025-03-24 21:42:01', '2025-03-24 21:42:01'),
	(37, 109, 13, 'Resolución de Contraloría N° 229-2022-CG que actualiza la Política de Gestión Antisoborno de la Contraloría General de la República', 'Cumplir con la Política y Objetivos de Gestión Antisoborno de la Contraloría General de la República', 'Auditorías internas y externas\r\nPlan de mantenimiento del SGAS', 'Incumplimiento de la norma ISO 37001:2016 y tener no conformidades.', NULL, 'pendiente', '2025-03-24 21:45:41', '2025-03-24 21:45:41'),
	(38, 109, 13, 'Norma ISO 37001:2016, Sistemas de gestión antisoborno – Requisitos con orientación para su uso.', 'Cumplir con los requisitos dispuestos por la norma internacional ISO 37001:2016', 'Auditorías internas y externas\r\nPlan de mantenimiento del SGAS', 'Incumplimiento de la norma ISO 37001:2016 y tener no conformidades.', NULL, 'pendiente', '2025-03-24 21:47:12', '2025-03-24 21:47:12'),
	(39, 40, 13, 'Política Nacional de Integridad y Lucha contra la Corrupción, aprobado mediante Decreto Supremo 092-2017-PCM.\r\nResolución de Contraloría N° 287-2021-CG, aprueba la Política de Integridad y ética Pública para los funcionarios y servidores públicos de la Contraloría General de la República y de los Órganos de Control Institucional.', 'Cumplir con las orientaciones vigentes descritas en la Política Nacional de Integridad y Lucha contra la Corrupción.\r\nCumplir con las orientaciones vigentes descritas en la Política de Integridad y ética Pública para los funcionarios y servidores públicos de la Contraloría General de la República y de los Órganos de Control Institucional.', 'Seguimiento al Programa de Integridad de la CGR', 'Nivel del Índice de Capacidad Preventiva (ICP) frente a la corrupción disminuido.\r\nNo obtener premio de ICP el próximo año.\r\nNivel de confianza en la labor de la Contraloría disminuido.', NULL, 'pendiente', '2025-03-24 22:38:53', '2025-03-24 22:38:53'),
	(40, 40, 13, 'Decreto Supremo N° 044-2018-PCM, Aprueba el Plan Nacional de Integridad y Lucha contra la Corrupción 2018-2021.\r\nResolución de secretaría de integridad pública 002-2021-PCM-SIP que aprueba la Directiva N 002-2021-PCM-SIP Lineamiento para establecer una cultura de integridad en las entidades del sector publico.\r\nDecreto Supremo N° 180-2021-PCM, Decreto Supremo que aprueba la Estrategia de Integridad del Poder Ejecutivo al 2022 para la Prevención de Actos de Corrupción.\r\nDecreto Supremo N° 148-2024-PCM, Decreto Supremo que aprueba el Modelo de Integridad para fortalecer la capacidad de prevención y respuesta frente a la corrupción en las entidades del sector público.', 'Cumplir y realizar el seguimiento de las orientaciones vigentes de cada uno de los nueve componentes del Modelo de Integridad, así como su supervisión.\r\nImpulsar actividades estratégicas de los componentes establecidos en el DS.', 'Seguimiento al Programa de Integridad de la CGR', 'Nivel del Índice de Capacidad Preventiva (ICP) frente a la corrupción disminuido.\r\nNo obtener premio de ICP el próximo año.\r\nNivel de confianza en la labor de la Contraloría disminuido.', NULL, 'pendiente', '2025-03-24 22:40:20', '2025-03-24 22:40:20'),
	(41, 276, 14, 'Decreto Legislativo N° 1327, que establece medidas de protección para el denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar preliminarmente las denuncias, verificando el cumplimiento de los requisitos establecidos en las normas, y derivarlas a las instancias respectivas, según corresponda.', 'Verificación diaria de la Plataforma Digital Única de Denuncias del Ciudadano.\r\nCoordinación con la Secretaría de Integridad Pública de la PCM', 'Afectación de la gestión de denuncias.', NULL, 'pendiente', '2025-03-24 22:53:53', '2025-03-24 22:53:53'),
	(42, 276, 14, 'Decreto Supremo N° 010-2017-JUS, que aprueba el Reglamento del Decreto Legislativo N° 1327 que establece medidas de protección al denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar preliminarmente las denuncias, verificando el cumplimiento de los requisitos establecidos en las normas, y derivarlas a las instancias respectivas, según corresponda.', 'PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', 'Afectación y pérdida de confianza de la gestión de denuncias.\r\nRiesgo en la seguridad, salud y vida del denunciante o de su familia.\r\nRiesgo de impunidad en el caso que amenazas contra el denunciante causen el retiro o el desistimiento de la denuncia.', NULL, 'pendiente', '2025-03-24 22:55:00', '2025-03-24 22:55:00'),
	(43, 276, 14, 'Decreto Supremo N° 002-2020-JUS, que modifica el Reglamento del Decreto Legislativo Nº 1327 que establece medidas de protección al denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar las solicitudes de medidas de protección, y otorgarlas según las normas establecidas y según corresponda.', 'Numeral 6.10 del procedimiento PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', 'Afectación de la gestión de denuncias.\r\nRiesgo en la seguridad personal y/o laboral, salud y vida del denunciante o de su familia', NULL, 'pendiente', '2025-03-24 22:55:50', '2025-03-24 23:05:35'),
	(44, 276, 14, 'Directiva N° 002-2023-PCM-SIP, Directiva para la gestión de denuncias y solicitudes de medidas de protección al denunciante de actos de corrupción recibidas a través de la plataforma digital única de denuncias al ciudadano, aprobada mediante la Resolución de Secretaría de Integridad Pública N° 005-2023-PCM-SIP.', 'Evaluar las solicitudes de medidas de protección, y otorgarlas según las normas establecidas y según corresponda.', 'Numeral 6.10 del procedimiento PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', '"Afectación y pérdida de confianza de la gestión de denuncias.\r\nRiesgo en la seguridad personal y/o laboral, salud y vida del denunciante o de su familia.\r\nRiesgo de impunidad en el caso de las amenazas contra el denunciante, que causen el retiro o desistimiento, o la ratificación de la denuncia."', NULL, 'pendiente', '2025-03-24 23:03:11', '2025-03-24 23:04:44'),
	(45, 78, 14, 'Directiva N° 009-2022-CG/DOC “Gestión Documental de la Contraloría General de la República”, aprobado con RC N° 169-2022-CG.\r\nTUPA de la Contraloría General de la República aprobado con RC N° 237-2022-CG.\r\nTUO de la Ley N° 27444, Ley del Procedimiento Administrativo General, aprobado por DS N° 004-2019-JUS.', 'Establece los criterios de recepción, emisión de documentos presentados en la CGR.', 'Reforzar los conocimientos, mediante la capacitación sobre la directiva vigente y aplicable al riesgo señalado, procedimiento "Generación de Expedientes" PR-TD-01. y la Ley 27444, dirigido al personal de recepción de documentos.', 'Incumplimiento de los plazos legales de atención y respuesta de los documentos por parte de las unidades orgánicas responsables de la evaluación, y posibles sanciones.', NULL, 'pendiente', '2025-03-24 23:53:44', '2025-03-24 23:53:44'),
	(46, 78, 14, 'Manual de Documentos de Acceso Restringido de la Contraloría General de la República, aprobado con Resolución de Contraloría N° 091-2004-CG y modificatoria', 'Establece los requisitos de recepción y derivación de documentos, presentados y clasificados como reservados, secretos y confidenciales.', 'Reforzar los conocimientos, mediante la capacitación sobre la directiva vigente, procedimiento "Generación de Expedientes" PR-TD-01. y la Ley 27444, dirigido al personal de recepción de documentos.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad', NULL, 'pendiente', '2025-03-24 23:55:25', '2025-03-24 23:55:25'),
	(47, 78, 14, '"Manual para Mejorar la Atención a la Ciudadanía" en las entidades de la Administración Pública, aprobado mediante RM N° 186-2015-PCM\r\nRSGP Nº 001-2015-PCM-SGP que aprueban los lineamientos para el proceso de implementación progresiva del Manual para Mejorar la Atención a la Ciudadanía en las entidades de la administración pública\r\nDirectriz para el uso de la Plataforma de Mesa de Partes Virtual de la Contraloría General de la República, aprobada mediante Resolución de Secretaría General N° 090-2020-CG/SGE', 'Garantizan el proceso de atención a la ciudadanía, a través de los canales de atención.', 'No hay controles', 'Inadecuada atención y orientación a la ciudadanía.', NULL, 'pendiente', '2025-03-24 23:56:57', '2025-03-24 23:56:57'),
	(48, 78, 14, 'Directiva N° 013-2015-CG/GPROD “Presentación, procesamiento y archivo de Las declaraciones juradas de ingresos y de bienes y rentas de los funcionarios y servidores públicos del estado”, aprobado mediante RC N° 328-2015-CG.\r\nDirectiva N° 010-2018-CG/GDET “Declaraciones Juradas para la Gestión de Conflicto de Intereses”, aprobado mediante RC N° 480-2018-CG y sus modificatoria RC N° 063-2019-CG y RC N° 095-2020-CG.', 'Establece los requisitos para una correcta presentación de las declaraciones juradas, verificado por mesa de partes.', 'Reforzar los conocimientos, mediante la capacitación sobre el procedimiento "Generación de Expedientes" PR-TD-01, dirigido al personal de recepción de documentos.', 'Incumplimiento de los plazos legales para la atención, derivación y trámite de las Declaraciones Juradas obligatorias de los servidores públicos y entidades.', NULL, 'pendiente', '2025-03-25 00:24:11', '2025-03-25 00:24:11'),
	(49, 78, 14, 'Ley 29542 “Ley de Protección al Denunciante en el Ámbito Administrativo y de Colaboración Eficaz en el Ámbito Penal”', 'Ejecutar los requisitos de protección al denunciante al recibir la denuncia administrativa del usuario.', 'Capacitación de "Ética e Integridad", dirigida al área de mesa de partes.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad, y afectación a los derechos de los involucrados. Denuncias por incidentes en uso de documentación que custodia los archivos de la CGR con carácter confidencial.', NULL, 'pendiente', '2025-03-25 00:25:09', '2025-03-25 00:25:09'),
	(50, 118, 14, 'Resolución jefatural N° 386.2002-INEI, que aprueba la Directiva N°016-2002-INEI/DTNP Normas técnicas para el almacenamiento y Respaldo de la Información procesada por las entidades de la Administración Pública.', 'Tomar las medidas necesarias para proteger y salvaguardar la integridad de los respaldos de la información', 'Mantenimiento preventivo de hardware y software, pruebas semestrales de recuperación de los respaldos\r\nPR-TI-06 Procedimiento "Respaldo y Restauración de Información"', 'No se logra recuperar la información requerida', NULL, 'pendiente', '2025-03-25 00:35:57', '2025-03-25 00:35:57'),
	(51, 118, 14, 'Contrato de servicio de correo electrónico (solución de respaldo)', 'Tomar las medidas necesarias para proteger y salvaguardar la integridad de los respaldos de la información de los correos electrónicos', 'Reportar al proveedor mediante ticket para que tome acciones de corrección y hacer seguimiento hasta la corrección.', 'No se logra recuperar la información requerida en un tiempo adecuado', NULL, 'pendiente', '2025-03-25 00:36:57', '2025-03-25 00:36:57');

-- Volcando estructura para tabla kallpaq.obligacion_riesgo
CREATE TABLE IF NOT EXISTS `obligacion_riesgo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obligacion_id` bigint(20) unsigned NOT NULL,
  `riesgo_id` bigint(20) unsigned NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obligacion_riesgo_obligacion_id_foreign` (`obligacion_id`),
  KEY `obligacion_riesgo_riesgo_id_foreign` (`riesgo_id`),
  CONSTRAINT `obligacion_riesgo_obligacion_id_foreign` FOREIGN KEY (`obligacion_id`) REFERENCES `obligaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obligacion_riesgo_riesgo_id_foreign` FOREIGN KEY (`riesgo_id`) REFERENCES `riesgos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.obligacion_riesgo: ~51 rows (aproximadamente)
REPLACE INTO `obligacion_riesgo` (`id`, `obligacion_id`, `riesgo_id`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 4, 1, 'activo', '2025-02-27 16:41:57', NULL),
	(15, 10, 23, 'activo', NULL, NULL),
	(16, 10, 24, 'activo', NULL, NULL),
	(17, 11, 25, 'activo', NULL, NULL),
	(18, 12, 26, 'activo', NULL, NULL),
	(19, 12, 30, 'activo', NULL, NULL),
	(20, 13, 31, 'activo', NULL, NULL),
	(21, 14, 32, 'activo', NULL, NULL),
	(22, 15, 33, 'activo', NULL, NULL),
	(23, 16, 34, 'activo', NULL, NULL),
	(24, 17, 35, 'activo', NULL, NULL),
	(25, 18, 36, 'activo', NULL, NULL),
	(26, 19, 37, 'activo', NULL, NULL),
	(27, 20, 38, 'activo', NULL, NULL),
	(28, 21, 39, 'activo', NULL, NULL),
	(29, 22, 40, 'activo', NULL, NULL),
	(31, 5, 42, 'activo', NULL, NULL),
	(32, 6, 43, 'activo', NULL, NULL),
	(33, 7, 44, 'activo', NULL, NULL),
	(34, 8, 45, 'activo', NULL, NULL),
	(36, 2, 47, 'activo', NULL, NULL),
	(37, 28, 48, 'activo', NULL, NULL),
	(38, 29, 49, 'activo', NULL, NULL),
	(39, 31, 50, 'activo', NULL, NULL),
	(40, 30, 51, 'activo', NULL, NULL),
	(42, 29, 53, 'activo', NULL, NULL),
	(43, 32, 58, 'activo', NULL, NULL),
	(44, 33, 59, 'activo', NULL, NULL),
	(45, 34, 60, 'activo', NULL, NULL),
	(46, 35, 61, 'activo', NULL, NULL),
	(47, 36, 62, 'activo', NULL, NULL),
	(48, 37, 63, 'activo', NULL, NULL),
	(49, 38, 64, 'activo', NULL, NULL),
	(50, 39, 65, 'activo', NULL, NULL),
	(51, 40, 66, 'activo', NULL, NULL),
	(52, 41, 67, 'activo', NULL, NULL),
	(53, 42, 68, 'activo', NULL, NULL),
	(54, 43, 69, 'activo', NULL, NULL),
	(55, 44, 70, 'activo', NULL, NULL),
	(56, 23, 71, 'activo', NULL, NULL),
	(58, 24, 73, 'activo', NULL, NULL),
	(59, 26, 74, 'activo', NULL, NULL),
	(60, 27, 75, 'activo', NULL, NULL),
	(61, 25, 76, 'activo', NULL, NULL),
	(62, 45, 77, 'activo', NULL, NULL),
	(63, 46, 78, 'activo', NULL, NULL),
	(64, 47, 79, 'activo', NULL, NULL),
	(65, 48, 80, 'activo', NULL, NULL),
	(66, 49, 81, 'activo', NULL, NULL),
	(67, 50, 82, 'activo', NULL, NULL),
	(68, 51, 83, 'activo', NULL, NULL);

-- Volcando estructura para tabla kallpaq.ouos
CREATE TABLE IF NOT EXISTS `ouos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ouo_nombre` varchar(255) NOT NULL,
  `ouo_codigo` varchar(255) NOT NULL,
  `ouo_padre` bigint(20) unsigned DEFAULT NULL,
  `subgerente_id` bigint(20) unsigned DEFAULT NULL,
  `subgerente_condicion` enum('encargatura','designacion','suplencia') DEFAULT NULL,
  `nivel_jerarquico` int(11) NOT NULL,
  `doc_vigencia_alta` varchar(255) DEFAULT NULL,
  `fecha_vigencia_inicio` date NOT NULL,
  `doc_vigencia_baja` varchar(255) DEFAULT NULL,
  `fecha_vigencia_fin` date DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ouo_codigo_unique` (`ouo_codigo`),
  KEY `ouo_ouo_padre_foreign` (`ouo_padre`),
  KEY `ouo_subgerente_id_foreign` (`subgerente_id`),
  CONSTRAINT `ouo_ouo_padre_foreign` FOREIGN KEY (`ouo_padre`) REFERENCES `ouos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ouo_subgerente_id_foreign` FOREIGN KEY (`subgerente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.ouos: ~99 rows (aproximadamente)
REPLACE INTO `ouos` (`id`, `ouo_nombre`, `ouo_codigo`, `ouo_padre`, `subgerente_id`, `subgerente_condicion`, `nivel_jerarquico`, `doc_vigencia_alta`, `fecha_vigencia_inicio`, `doc_vigencia_baja`, `fecha_vigencia_fin`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
	(1, 'Despacho del Contralor', 'D100', NULL, 9, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(2, 'Órgano de Auditoría Interna', 'D200', 1, 10, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(3, 'Procuraduría Pública', 'D900', 1, 11, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(4, 'Oficina de Gestión de la Potestad Administrativa Sancionadora', 'E200', 1, 12, 'encargatura', 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(5, 'Oficina de Integridad Institucional', 'A260', 1, 13, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(6, 'Tribunal Superior de Responsabilidades Administrativas', 'E300', 1, 14, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 17:58:13', '2025-01-16 17:58:13'),
	(7, 'Vicecontraloría de Gestión Estratégica, Integridad y Control', 'L110', 1, 15, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:27:32', '2025-01-16 20:27:32'),
	(8, 'Gerencia de Prevención y Control Social', 'C601', 7, 16, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
	(9, 'Gerencia de Análisis de Información para el Control', 'C120', 7, 17, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
	(10, 'Gerencia de Recursos Estratégicos', 'D500', 7, 18, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
	(11, 'Escuela Nacional de Control', 'D400', 7, 19, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:29:29', '2025-01-16 20:29:29'),
	(12, 'Subgerencia de Prevención e Integridad', 'C370', 8, 20, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
	(13, 'Subgerencia de Auditoría de Desempeño', 'L200', 8, 21, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
	(14, 'Subgerencia de Participación Ciudadana y Control Social', 'C600', 8, 22, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:31:09', '2025-01-16 20:31:09'),
	(15, 'Subgerencia del Observatorio Anticorrupción', 'C602', 9, 23, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(16, 'Subgerencia de Gestión de Declaraciones Juradas', 'C122', 9, 24, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(17, 'Subgerencia de Fiscalización', 'L1540', 9, 25, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(18, 'Subgerencia de Contrataciones Estratégicas', 'D501', 10, 26, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(19, 'Subgerencia de Gestión de Inversiones', 'C322', 10, 27, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(20, 'Subdirección Académica', 'D401', 11, 19, NULL, 4, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(21, 'Subdirección de Posgrado', 'D403', 11, 28, NULL, 4, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:32:40', '2025-01-16 20:32:40'),
	(22, 'Vicecontraloría de Control Sectorial y Territorial', 'L100', 1, 30, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:41:44', '2025-01-16 20:41:44'),
	(23, 'Gerencia de Control Político, Institucional y Económico', 'L301', 22, 31, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
	(24, 'Gerencia de Control de Servicios Públicos Básicos', 'L303', 22, 32, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
	(25, 'Gerencia de Control de Megaproyectos', 'L304', 22, 33, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:44:34', '2025-01-16 20:44:34'),
	(26, 'Subgerencia de Control del Sector Seguridad Interna y Externa', 'L340', 23, 34, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(27, 'Subgerencia de Control del Sector Justicia, Político y Electoral', 'L352', 23, 35, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(28, 'Subgerencia de Control del Sector Social y Cultura', 'L315', 23, 36, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(29, 'Subgerencia de Control del Sector Económico y Financiero', 'L320', 23, 37, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(30, 'Subgerencia de Control del Sector Productivo y Trabajo', 'L330', 23, 38, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(31, 'Subgerencia de Control del Sector Transportes y Comunicaciones', 'L331', 24, 39, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(32, 'Subgerencia de Control del Sector Vivienda, Construcción y Saneamiento', 'L336', 24, 40, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(33, 'Subgerencia de Control del Sector Agricultura y Ambiente', 'L332', 24, 32, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(34, 'Subgerencia de Control del Sector Educación', 'L351', 24, 41, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(35, 'Subgerencia de Control del Sector Salud', 'L316', 24, 42, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(36, 'Subgerencia de Control de Universidades', 'L353', 24, 43, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(37, 'Subgerencia de Control de Megaproyectos', 'L334', 25, 184, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(38, 'Subgerencia de Control de Asociaciones Público Privadas y Obras por Impuestos', 'C920', 25, 44, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(39, 'Subgerencia de Control Previo de Adicionales de Obra', 'L556', 25, 33, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 20:48:12', '2025-01-16 20:48:12'),
	(40, 'Secretaría General', 'D300', 1, 46, NULL, 1, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:01:05', '2025-01-16 21:01:05'),
	(41, 'Oficina de Seguridad y Defensa Nacional', 'D531', 40, 47, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(42, 'Gerencia de Administración', 'C200', 40, 48, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(43, 'Gerencia de Capital Humano', 'D550', 40, 50, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(44, 'Gerencia de Tecnologías de la Información', 'D600', 40, 53, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(45, 'Gerencia de Comunicación Corporativa', 'C401', 40, 57, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(46, 'Gerencia de Asesoría Jurídica y Normatividad en Control Gubernamental', 'D700', 40, 60, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(47, 'Gerencia de Modernización y Planeamiento', 'L527', 40, 63, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(48, 'Gerencia de Relaciones Institucionales', 'C381', 40, 67, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:03:07', '2025-01-16 21:03:07'),
	(69, 'Subgerencia de Abastecimiento', 'D530', 42, NULL, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(70, 'Subgerencia de Gestión Documentaria', 'D320', 42, 49, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(71, 'Subgerencia de Políticas y Desarrollo Humano', 'D517', 43, 50, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(72, 'Subgerencia de Personal y Compensaciones', 'D510', 43, 51, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(73, 'Subgerencia de Bienestar y Relaciones Laborales', 'D511', 43, 52, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(74, 'Subgerencia de Sistemas de Información', 'D610', 44, 54, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(75, 'Subgerencia de Operaciones y Plataforma Tecnológica', 'D602', 44, 55, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(76, 'Subgerencia de Gobierno Digital', 'D603', 44, 56, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(77, 'Subgerencia de Prensa', 'C360', 45, 58, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(78, 'Subgerencia de Imagen y Relaciones Corporativas', 'D310', 45, 59, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(79, 'Subgerencia de Comunicación y Medios Digitales', 'C402', 45, 58, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(80, 'Subgerencia de Asesoría Jurídica', 'D710', 46, 60, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(81, 'Subgerencia de Normatividad en Control Gubernamental', 'C312', 46, 61, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(82, 'Subgerencia de Aseguramiento de la Calidad', 'L157', 46, 62, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(83, 'Subgerencia de Planeamiento, Presupuesto y Programación de Inversiones', 'L520', 47, 64, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(84, 'Subgerencia de Seguimiento y Evaluación del SNC', 'L590', 47, 65, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(85, 'Subgerencia de Modernización', 'C321', 47, 66, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(86, 'Subgerencia de Coordinación Parlamentaria', 'C380', 48, 67, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(87, 'Subgerencia de Coordinación Institucional Nacional', 'C382', 48, 68, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(88, 'Subgerencia de Cooperación y Asuntos Internacionales', 'D800', 48, 69, NULL, 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-01-16 21:07:08', '2025-01-16 21:07:08'),
	(114, 'Gerencia Regional de Control Lima Provincias', 'C823', 22, 185, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(115, 'Gerencia Regional de Control Ancash', 'L425', 22, 186, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(116, 'Gerencia Regional de Control de Ica', 'L445', 22, 187, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(117, 'Gerencia Regional de Control de Loreto', 'L440', 22, 188, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(118, 'Gerencia Regional de Control de Lima Metropolitana', 'L401', 22, 189, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(119, 'Gerencia Regional de Control del Callao', 'C824', 22, 190, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(120, 'Gerencia Regional de Control de Tumbes', 'L422', 22, 191, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(121, 'Gerencia Regional de Control de Piura', 'L420', 22, 192, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(122, 'Gerencia Regional de Control de Lambayeque', 'L430', 22, 193, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(123, 'Gerencia Regional de Control de La Libertad', 'L495', 22, 194, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(124, 'Gerencia Regional de Control de Cajamarca', 'L435', 22, 210, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(125, 'Gerencia Regional de Control de San Martín', 'L450', 22, 195, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(126, 'Gerencia Regional de Control de Amazonas', 'L452', 22, 196, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(127, 'Gerencia Regional de Control Junín', 'L460', 22, 197, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(128, 'Gerencia Regional de Control Huancavelica', 'L446', 22, 198, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(129, 'Gerencia Regional de Control Ayacucho', 'L490', 22, 199, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(130, 'Gerencia Regional de Control Ucayali', 'L466', 22, 200, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(131, 'Gerencia Regional de Control Huánuco', 'L465', 22, 209, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(132, 'Gerencia Regional de Control Pasco', 'L467', 22, 201, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(133, 'Gerencia Regional de Control Arequipa', 'L470', 22, 202, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(134, 'Gerencia Regional de Control Moquegua', 'L476', 22, 203, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(135, 'Gerencia Regional de Control Tacna', 'L475', 22, 204, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(136, 'Gerencia Regional de Control de Madre de Dios', 'L482', 22, 205, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(137, 'Gerencia Regional de Control Apurímac', 'L485', 22, 206, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(138, 'Gerencia Regional de Control Cusco', 'L480', 22, 207, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(139, 'Gerencia Regional de Control Puno', 'L455', 22, 208, NULL, 2, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, NULL, NULL),
	(140, 'Órgano Instructor', 'E210', 4, 211, 'encargatura', 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-02-04 19:26:35', NULL),
	(142, 'Órgano Instructor Lambayeque', 'E211', 4, 212, 'encargatura', 3, 'R.C. N° 293-2024-CG', '2024-06-29', NULL, NULL, 1, NULL, '2025-02-04 19:26:35', NULL),
	(143, 'Órgano Sancionador', 'E220', 4, 213, 'encargatura', 3, NULL, '0000-00-00', NULL, NULL, 1, NULL, '2025-02-04 19:26:35', NULL),
	(144, 'Órgano Instructor Arequipa', 'E213', 4, 214, 'encargatura', 3, NULL, '0000-00-00', NULL, NULL, 1, NULL, '2025-02-04 19:26:35', NULL),
	(145, 'Órgano Instructor Junín', 'E212', 4, 215, 'encargatura', 3, NULL, '0000-00-00', NULL, NULL, 1, NULL, '2025-02-04 19:26:35', NULL);

-- Volcando estructura para tabla kallpaq.partes_interesadas
CREATE TABLE IF NOT EXISTS `partes_interesadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pi_nombre` varchar(255) NOT NULL,
  `pi_tipo` enum('interna','externa','cliente','proveedor','regulador') NOT NULL DEFAULT 'interna',
  `pi_nivel_influencia` enum('bajo','medio','alto') DEFAULT NULL,
  `pi_descripcion` text DEFAULT NULL,
  `pi_activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.partes_interesadas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.password_resets: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.permissions: ~4 rows (aproximadamente)
REPLACE INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Módulo Indicadores', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(2, 'Modulo Procesos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(3, 'Modulo Hallazgos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(4, 'Modulo Riesgos', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30');

-- Volcando estructura para tabla kallpaq.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.personal_access_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.planificacion_pei
CREATE TABLE IF NOT EXISTS `planificacion_pei` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `planificacion_pei_cod` varchar(255) NOT NULL,
  `planificacion_pei_nombre` varchar(255) NOT NULL,
  `alcance` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.planificacion_pei: ~5 rows (aproximadamente)
REPLACE INTO `planificacion_pei` (`id`, `planificacion_pei_cod`, `planificacion_pei_nombre`, `alcance`, `created_at`, `updated_at`) VALUES
	(1, 'OEI.01', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas', '2022-2026, ampliado al 2027', '2025-02-17 19:25:17', NULL),
	(2, 'OEI.02', 'Contribuir a la gestión eficiente y eficaz de los recursos públicos en beneficio de la población', '2022-2026, ampliado al 2027', '2025-02-17 19:25:17', NULL),
	(3, 'OEI.03', 'Promover la participación ciudadana a través del control social y la formación en valores de Integridad', '2022-2026, ampliado al 2027', '2025-02-17 19:25:17', NULL),
	(4, 'OEI.04', 'Fortalecer la gestión institucional del Sistema Nacional de Control', '2022-2026, ampliado al 2027', '2025-02-17 19:25:17', NULL),
	(5, 'OEI.05', 'Implementar la gestión de riesgos de desastres', '2022-2026, ampliado al 2027', '2025-02-17 19:25:17', NULL);

-- Volcando estructura para tabla kallpaq.planificacion_sig
CREATE TABLE IF NOT EXISTS `planificacion_sig` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objetivo_sig_cod` varchar(255) NOT NULL,
  `sistema` enum('SGC','SGAS') NOT NULL,
  `objetivo_sig_nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.planificacion_sig: ~4 rows (aproximadamente)
REPLACE INTO `planificacion_sig` (`id`, `objetivo_sig_cod`, `sistema`, `objetivo_sig_nombre`, `created_at`, `updated_at`) VALUES
	(1, 'OSGC1', 'SGC', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2023-08-19 02:27:59', NULL),
	(2, 'OSGC2', 'SGC', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población', '2023-08-19 02:27:59', NULL),
	(3, 'OSGC3', 'SGC', 'Promover la participación ciudadana en el control social.', '2023-08-19 02:28:31', NULL),
	(4, 'OSGC4', 'SGC', 'Fortalecer la gestión del Sistema Nacional de Control.', NULL, NULL);

-- Volcando estructura para tabla kallpaq.procesos
CREATE TABLE IF NOT EXISTS `procesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cod_proceso` varchar(255) NOT NULL,
  `proceso_sigla` varchar(255) DEFAULT NULL,
  `proceso_nombre` varchar(255) NOT NULL,
  `proceso_objetivo` text DEFAULT NULL,
  `proceso_tipo` enum('Misional','Estratégico','Apoyo') NOT NULL,
  `cod_proceso_padre` bigint(20) unsigned DEFAULT NULL,
  `proceso_nivel` varchar(255) DEFAULT NULL,
  `planificacion_pei_id` bigint(20) unsigned DEFAULT NULL,
  `proceso_estado` varchar(255) DEFAULT NULL,
  `sgc` tinyint(1) NOT NULL DEFAULT 0,
  `sgas` tinyint(1) NOT NULL DEFAULT 0,
  `sgcm` tinyint(1) NOT NULL DEFAULT 0,
  `sgsi` tinyint(1) NOT NULL DEFAULT 0,
  `sgco` tinyint(1) NOT NULL DEFAULT 0,
  `inactivated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cod_proceso` (`cod_proceso`)
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.procesos: ~231 rows (aproximadamente)
REPLACE INTO `procesos` (`id`, `cod_proceso`, `proceso_sigla`, `proceso_nombre`, `proceso_objetivo`, `proceso_tipo`, `cod_proceso_padre`, `proceso_nivel`, `planificacion_pei_id`, `proceso_estado`, `sgc`, `sgas`, `sgcm`, `sgsi`, `sgco`, `inactivated_at`, `created_at`, `updated_at`) VALUES
	(1, 'PE01', NULL, 'Gestión Estratégica', NULL, 'Estratégico', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-05-26 23:01:48', '2023-06-02 04:00:48'),
	(2, 'PE02', NULL, 'Desarrollo Institucional', NULL, 'Estratégico', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-02-03 21:23:17', NULL),
	(3, 'PE03', NULL, 'Comunicación y Relaciones Interinstitucionales', NULL, 'Estratégico', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-02-03 21:23:41', NULL),
	(4, 'PM01', NULL, 'Prevención y Detección de la Corrupción', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(5, 'PM02', NULL, 'Atención a las Entidades y Partes Interesadas', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(6, 'PM03', NULL, 'Realización de los Servicios de Control Simultáneo, Posterior y Relacionados', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(7, 'PM04', NULL, 'Gestión de Sanciones y Procesos Judiciales', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(8, 'PM05', NULL, 'Gestión de los Resultados del Control', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(9, 'PA01', 'GCAHU', 'Gestión del Capital Humano', 'Gestionar el talento humano del sector público para garantizar personal competente, motivado y alineado con los objetivos institucionales y el servicio al ciudadano.', 'Apoyo', NULL, '0', 1, '1', 0, 0, 0, 0, 0, NULL, NULL, '2025-11-06 00:36:39'),
	(10, 'PA02', NULL, 'Gestión de Activos Documentarios', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(11, 'PA03', NULL, 'Gestión de Abastecimiento', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(12, 'PA04', NULL, 'Gestión Financiera', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(13, 'PA05', NULL, 'Gestión de Tecnologías de la Información y Comunicaciones', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(14, 'PA06', NULL, 'Gestión Jurídico Legal', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(15, 'PA07', NULL, 'Gestión de la Seguridad', NULL, 'Apoyo', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL),
	(30, 'PE01.01', NULL, 'Planeamiento Estratégico', NULL, 'Estratégico', 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', '2025-05-28 20:51:47'),
	(31, 'PE01.02', NULL, 'Gestión de Entidades Sujetad a Control', NULL, 'Estratégico', 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(32, 'PE01.03', NULL, 'Planeamiento Operativo', NULL, 'Estratégico', 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(33, 'PE01.04', NULL, 'Control Institucional', NULL, 'Estratégico', 1, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(34, 'PE02.01', NULL, 'Diseño Organizacional', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(35, 'PE02.02', NULL, 'Gestión de la Modernización', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(36, 'PE02.03', NULL, 'Gestión Normativa', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(37, 'PE02.04', NULL, 'Gestión de la Inversión', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(38, 'PE02.05', NULL, 'Gestión del Conocimiento', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(39, 'PE02.06', NULL, 'Gestión de la Continuidad del Negocio', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(40, 'PE02.07', NULL, 'Gestión de la Integridad Institucional', NULL, 'Estratégico', 2, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(41, 'PE03.01', NULL, 'Gestión de la Comunicación Institucional', NULL, 'Estratégico', 3, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(42, 'PE03.02', NULL, 'Gestión de las Relaciones Interinstitucionales', NULL, 'Estratégico', 3, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:21:22', NULL),
	(56, 'PM01.01', NULL, 'Gestión de mecanismos de prevención y detección de la corrupción', NULL, 'Misional', 4, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(57, 'PM01.02', NULL, 'Participación ciudadana', NULL, 'Misional', 4, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(58, 'PM02.01', NULL, 'Atención de la demanda imprevisible de control', NULL, 'Misional', 5, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(59, 'PM02.02', NULL, 'Atención de pedidos de información y solicitudes de opinión', NULL, 'Misional', 5, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(60, 'PM02.03', NULL, 'Atención de quejas y reclamos', NULL, 'Misional', 5, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(61, 'PM03.01', NULL, 'Programación de los servicios de control y de fiscalización', NULL, 'Misional', 6, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(62, 'PM03.02', NULL, 'Realización de los servicios de control simultáneo', NULL, 'Misional', 6, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', '2025-05-09 22:51:03'),
	(63, 'PM03.03', NULL, 'Realización de los servicios de control posterior', NULL, 'Misional', 6, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(64, 'PM03.04', NULL, 'Realización de los servicios relacionados', NULL, 'Misional', 6, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(65, 'PM03.05', NULL, 'Supervisión técnica y revisión de oficio de los servicios de control', NULL, 'Misional', 6, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(66, 'PM04.01', NULL, 'Gestión de sanciones administrativas', NULL, 'Misional', 7, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(67, 'PM04.02', NULL, 'Gestión del procedimiento sancionador por infracción al ejercicio del control gubernamental', NULL, 'Misional', 7, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(68, 'PM04.03', NULL, 'Gestión de los procesos judiciales resultantes de los servicios de control', NULL, 'Misional', 7, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(69, 'PM05.01', NULL, 'Seguimiento y evaluación a la implementación de las recomendaciones, acciones y pronunciamientos, resultados de los servicios de control', NULL, 'Misional', 8, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(70, 'PM05.02', NULL, 'Desarrollo de buenas prácticas y propuestas de mejora para la gestión de las entidades', NULL, 'Misional', 8, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 17:28:23', NULL),
	(71, 'PA01.01', NULL, 'Planificación del capital humano', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', '2025-05-09 00:47:51'),
	(72, 'PA01.02', NULL, 'Incorporación del capital humano', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(73, 'PA01.03', NULL, 'Desarrollo del capital humano', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(74, 'PA01.04', NULL, 'Administración del capital humano', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(75, 'PA01.05', NULL, 'Gestión del bienestar del capital humano', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(76, 'PA01.06', NULL, 'Gestión del jefe y personal del OCI', NULL, 'Apoyo', 9, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(77, 'PA02.01', NULL, 'Planificación del activo documentario', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(78, 'PA02.02', NULL, 'Recepción de documentos', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(79, 'PA02.03', NULL, 'Clasificación, reclasificación y desclasificación de documentos secretos y reservados', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(80, 'PA02.04', NULL, 'Distribución de documentos y valijas', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(81, 'PA02.05', NULL, 'Archivo, custodia y conservación de documentos', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(82, 'PA02.06', NULL, 'Autenticación de firmas y certificación de documentos', NULL, 'Apoyo', 10, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(83, 'PA03.01', NULL, 'Elaboración del plan anual de contrataciones', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(84, 'PA03.02', NULL, 'Contratación de bienes y servicios', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(85, 'PA03.03', NULL, 'Gestión de bienes patrimoniales', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(86, 'PA03.04', NULL, 'Gestión de almacén', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(87, 'PA03.05', NULL, 'Administración de servicios generales', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(88, 'PA03.06', NULL, 'Gestión de sociedades de auditoria', NULL, 'Apoyo', 11, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(89, 'PA04.01', NULL, 'Programación multianual, formulación y aprobación del presupuesto', NULL, 'Apoyo', 12, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(90, 'PA04.02', NULL, 'Ejecución presupuestal', NULL, 'Apoyo', 12, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(91, 'PA04.03', NULL, 'Evaluación presupuestal', NULL, 'Apoyo', 12, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(92, 'PA04.04', NULL, 'Gestión contable', NULL, 'Apoyo', 12, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(93, 'PA05.01', NULL, 'Planificación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 13, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(94, 'PA05.02', NULL, 'Implementación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 13, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(95, 'PA05.03', NULL, 'Operación de tecnologías de la información y comunicaciones', NULL, 'Apoyo', 13, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(96, 'PA06.01', NULL, 'Gestión y difusión de productos de interés legal', NULL, 'Apoyo', 14, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(97, 'PA06.02', NULL, 'Gestión de los procesos judiciales de la CGR', NULL, 'Apoyo', 14, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(98, 'PA06.03', NULL, 'Gestión de los procesos arbitrales de la CGR', NULL, 'Apoyo', 14, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(99, 'PA06.04', NULL, 'Defensa legal de los colaboradores y ex colaboradores', NULL, 'Apoyo', 14, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(100, 'PA06.05', NULL, 'Absolución de consultas internas de carácter jurídico', NULL, 'Apoyo', 14, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(101, 'PA07.01', NULL, 'Gestión de prevención de riesgos de desastres', NULL, 'Apoyo', 15, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(102, 'PA07.02', NULL, 'Operación de la gestión de la seguridad', NULL, 'Apoyo', 15, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(103, 'PA07.03', NULL, 'Fomento de una cultura de seguridad', NULL, 'Apoyo', 15, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-08-09 18:30:44', NULL),
	(104, 'PM06', NULL, 'Gestión Educativa', NULL, 'Misional', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2023-09-27 15:13:56', NULL),
	(105, 'PE02.02.02', NULL, 'Administración de los Sistemas de Gestión', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:38:59', NULL),
	(106, 'PE02.02.03', NULL, 'Gestión de la Calidad', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:45:13', NULL),
	(107, 'PE02.02.04', NULL, 'Gestión de Riesgos', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:45:13', NULL),
	(108, 'PE02.02.05', NULL, 'Gestión del Control Interno', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:47:23', NULL),
	(109, 'PE02.02.06', NULL, 'Gestión Antisoborno', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:47:23', NULL),
	(110, 'PE02.02.07', NULL, 'Gestión de la Simplificación Administrativa', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:51:57', NULL),
	(111, 'PE02.02.08', NULL, 'Aseguramiento de la Calidad', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:51:57', NULL),
	(112, 'PE02.02.09', NULL, 'Gestión de la Seguridad de la Información', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:51:57', NULL),
	(113, 'PE02.02.01', NULL, 'Gestión por Procesos', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:53:40', NULL),
	(114, 'PE02.02.10', NULL, 'Gestión de Compliance', NULL, 'Estratégico', 35, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 14:53:40', NULL),
	(115, 'PE02.03.01', NULL, 'Gestión de Inciativas Legislativas', NULL, 'Estratégico', 36, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 15:11:26', NULL),
	(116, 'PE02.03.02', NULL, 'Gestión de Documentos Normativos', NULL, 'Estratégico', 36, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 15:11:26', NULL),
	(117, 'PE02.03.03', NULL, 'Gestión de documentos en el Alcance del SIG', NULL, 'Estratégico', 36, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-19 15:11:26', NULL),
	(118, 'PA05.03.01', NULL, 'Respaldo de información', NULL, 'Apoyo', 95, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:18:37', NULL),
	(119, 'PA05.03.02', NULL, 'Atención de requeremientos de recursos informáticos', NULL, 'Apoyo', 95, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:34:32', NULL),
	(120, 'PA05.03.03', NULL, 'Seguimiento y control de los servicios de tecnologías de información y comunicaciones', NULL, 'Apoyo', 95, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:37:25', NULL),
	(121, 'PA05.03.04', NULL, 'Mantenimiento preventivo y correctivo de activos informáticos y de comunicaciones', NULL, 'Apoyo', 95, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:38:13', NULL),
	(122, 'PA05.02.01', NULL, 'Desarrollo de arquitectura informática y de comunicaciones', NULL, 'Apoyo', 94, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:42:35', NULL),
	(123, 'PA05.02.02', NULL, 'Desarrollo de soluciones', NULL, 'Apoyo', 94, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 17:43:08', NULL),
	(125, 'PM03.02.01', NULL, 'Visita de Control', NULL, 'Misional', 62, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:43:12', '2025-05-12 20:02:09'),
	(126, 'PM03.02.02', NULL, 'Orientación de oficio', NULL, 'Misional', 62, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:43:12', NULL),
	(127, 'PM03.02.03', NULL, 'Control Concurrente', NULL, 'Misional', 62, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:45:44', NULL),
	(128, 'PM03.02.04', NULL, 'Operativo de Control Simultaneo', NULL, 'Misional', 62, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:45:44', NULL),
	(129, 'PM02.01.01', NULL, 'Realización de los servicios de control previo', NULL, 'Misional', 58, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:49:07', NULL),
	(130, 'PM02.01.01.01', NULL, 'Evaluación de prestaciones de adicionales de obra', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 18:52:50', NULL),
	(131, 'PM02.01.01.02', NULL, 'Evaluación de recursos de apelación de prestaciones adicionales de obra', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(132, 'PM02.01.01.03', NULL, 'Evaluación de prestaciones adicionales de supervisión de obra', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(133, 'PM02.01.01.04', NULL, 'Evaluación de recursos de apelación de prestaciones adicionales de supervisión de obra', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(134, 'PM02.01.01.05', NULL, 'Evaluación de solicitudes de emisión de informe previo a las operaciones de asociaciones público privadas y obras por impuestos', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(135, 'PM02.01.01.06', NULL, 'Evaluación de solicitudes de emisión de informe previo a las operaciones de endeudamiento público interno y externo', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(136, 'PM02.01.01.07', NULL, 'Emisión de opinión previa a las compras con carácter de secreto militar o de orden interno', NULL, 'Misional', 129, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:02:26', NULL),
	(137, 'PM02.02.01.01', NULL, 'Atención de solicitudes de acceso a la información pública', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:08:40', NULL),
	(138, 'PM02.02.01.02', NULL, 'Atención de requerimientos de información del congreso', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:08:40', NULL),
	(139, 'PM02.02.01.03', NULL, 'Atención de requerimientos de información de entidades', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:08:40', NULL),
	(140, 'PM02.02.02.01', NULL, 'Atención de consulta legal externa respecto a la interpretación y alcance de la normativa de servicios de control o servicios relacionados', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:08:40', NULL),
	(141, 'PM02.02.02.02', NULL, 'Atención de solicitudes de opinión sobre proyectos de ley y otras normas con rango de ley', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:08:40', NULL),
	(142, 'PM03.03.01', NULL, 'Auditoría de cumplimiento', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(143, 'PM03.03.02', NULL, 'Auditoría de desempeño', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(144, 'PM03.03.03', NULL, 'Auditoría financiera', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(145, 'PM03.03.04', NULL, 'Auditoría de la Cuenta General de la República', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(146, 'PM03.03.05', NULL, 'Servicio de control específico a hechos con presunta irregularidad', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(147, 'PM03.03.06', NULL, 'Acción de oficio posterior', NULL, 'Misional', 63, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:12:46', NULL),
	(148, 'PE03.01.01', NULL, 'Diseño del plan de comunicación corporativa', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(149, 'PE03.01.02', NULL, 'Gestión de la comunicación interna', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(150, 'PE03.01.03', NULL, 'Organización y ejecución de eventos para la promoción de la imagen y desarrollo institucional', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(151, 'PE03.01.04', NULL, 'Gestión de la publicación institucional', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(152, 'PE03.01.05', NULL, 'Actualización de contenidos del portal de transparencia estándar de la contraloría general de la república', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(153, 'PE03.01.06', NULL, 'Gestión de prensa', NULL, 'Estratégico', 41, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(154, 'PE03.02.01', NULL, 'Diseño de la estrategia de relacionamiento interinstitucional', NULL, 'Estratégico', 42, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(155, 'PE03.02.02', NULL, 'Atención de necesidades interinstitucionales de representación de autoridades y funcionarios de la cgr', NULL, 'Estratégico', 42, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(156, 'PE03.02.03', NULL, 'Gestión de la representación institucional en eventos internacionales', NULL, 'Estratégico', 42, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(157, 'PE03.02.04', NULL, 'Gestión de las necesidades institucionales de cooperación técnica y financiera', NULL, 'Estratégico', 42, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(158, 'PE03.02.05', NULL, 'Gestión de instrumentos de cooperación', NULL, 'Estratégico', 42, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:25:53', NULL),
	(159, 'PA04.02.01', NULL, 'Control de la disponibilidad de los créditos presupuestarios', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(160, 'PA04.02.02', NULL, 'Gestión de la modificación presupuestal a nivel institucional', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(161, 'PA04.02.03', NULL, 'Modificación presupuestal a nivel funcional programático', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(162, 'PA04.02.04', NULL, 'Ejecución de ingresos', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(163, 'PA04.02.05', NULL, 'Ejecución del gasto', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(164, 'PA04.02.06', NULL, 'Gestión de viáticos', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(165, 'PA04.02.07', NULL, 'Gestión del fondo de caja chica', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(166, 'PA04.02.08', NULL, 'Gestión de anticipos', NULL, 'Apoyo', 90, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(167, 'PA03.02.01', NULL, 'Formulación del requerimiento para la contratación de bienes y servicios', NULL, 'Apoyo', 84, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(168, 'PA03.02.02', NULL, 'Procesos de selección', NULL, 'Apoyo', 84, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(169, 'PA03.02.03', NULL, 'Contrataciones de bienes y servicios excluidas de la norma', NULL, 'Apoyo', 84, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 19:39:57', NULL),
	(194, 'PA01.01.01', NULL, 'Diseño de estrategias, políticas y herramientas para la gestión del capital humano', NULL, 'Apoyo', 71, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(195, 'PA01.01.02', NULL, 'Planificación de recursos humanos', NULL, 'Apoyo', 71, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(196, 'PA01.01.03', NULL, 'Administración de puestos y perfiles', NULL, 'Apoyo', 71, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(197, 'PA01.02.01', NULL, 'Reclutamiento y selección', NULL, 'Apoyo', 72, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(198, 'PA01.02.02', NULL, 'Vinculación de personal', NULL, 'Apoyo', 72, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(199, 'PA01.02.03', NULL, 'Inducción de personal', NULL, 'Apoyo', 72, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(200, 'PA01.02.04', NULL, 'Designación de personal en puestos de confianza', NULL, 'Apoyo', 72, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(201, 'PA01.03.01', NULL, 'Gestión de la capacitación', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(202, 'PA01.03.02', NULL, 'Gestión del rendimiento', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(203, 'PA01.03.03', NULL, 'Gestión de incentivos', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(204, 'PA01.03.04', NULL, 'Progresión de la carrera', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(205, 'PA01.03.05', NULL, 'Convocatoria interna', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(206, 'PA01.03.06', NULL, 'Traslado y encargo del personal', NULL, 'Apoyo', 73, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(207, 'PA01.04.01', NULL, 'Gestión de las compensaciones', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(208, 'PA01.04.02', NULL, 'Atención de solicitudes de personal', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(209, 'PA01.04.03', NULL, 'Gestión de seguros', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(210, 'PA01.04.04', NULL, 'Administración de información de personal', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(211, 'PA01.04.05', NULL, 'Proceso disciplinario de personal', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(212, 'PA01.04.06', NULL, 'Desvinculación de personal', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(213, 'PA01.04.07', NULL, 'Entrega y recepción de puesto de los servidores', NULL, 'Apoyo', 74, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(214, 'PA01.05.01', NULL, 'Seguridad y salud en el trabajo', NULL, 'Apoyo', 75, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(215, 'PA01.05.02', NULL, 'Relaciones labores individuales y colectivas', NULL, 'Apoyo', 75, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(216, 'PA01.05.03', NULL, 'Cultura y clima organizacional', NULL, 'Apoyo', 75, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(217, 'PA01.05.04', NULL, 'Bienestar social', NULL, 'Apoyo', 75, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:04:19', NULL),
	(218, 'PE02.04.01', NULL, 'Programación de las inversiones', NULL, 'Estratégico', 37, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:09:53', NULL),
	(219, 'PE02.04.02', NULL, 'Formulación, evaluación, ejecución y cierre de proyectos', NULL, 'Estratégico', 37, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:09:53', NULL),
	(220, 'PE02.04.03', NULL, 'Elaboración, aprobación, registro, ejecución física y cierre de las IOARR', NULL, 'Estratégico', 37, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:09:53', NULL),
	(221, 'PE02.04.04', NULL, 'Gestión del seguimiento de las inversiones', NULL, 'Estratégico', 37, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:09:53', NULL),
	(222, 'PM02.03.01', NULL, 'Atención de reclamos del libro de reclamaciones', NULL, 'Misional', 60, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:17:04', NULL),
	(223, 'PM02.03.02', NULL, 'Atención de quejas por defecto de tramitación', NULL, 'Misional', 60, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 20:17:04', NULL),
	(224, 'PM04.01.01', NULL, 'Determinación de la existencia de infracción', NULL, 'Misional', 66, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:03:13', NULL),
	(225, 'PM04.01.02', NULL, 'Determinación de la sanción', NULL, 'Misional', 66, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:03:13', NULL),
	(226, 'PM04.01.03', NULL, 'Gestión para el cumplimiento de sanciones', NULL, 'Misional', 66, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:03:13', NULL),
	(227, 'PM04.03.01', NULL, 'Gestión a los procesos civiles resultantes de los servicios de control', NULL, 'Misional', 68, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:03:13', NULL),
	(228, 'PM04.03.02', NULL, 'Gestión de procesos penales resultantes de los servicios de control', NULL, 'Misional', 68, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:03:13', NULL),
	(229, 'PM05.01.01', NULL, 'Seguimiento y evaluación a la implementación de las recomendaciones de control posterior', NULL, 'Misional', 69, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:10:34', NULL),
	(230, 'PM05.01.02', NULL, 'Seguimiento y evaluación a la implementación de acciones respecto a los resultados de los informes de control simultáneo', NULL, 'Misional', 69, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:10:34', NULL),
	(231, 'PM05.01.03', NULL, 'Seguimiento y evaluación a la implementación de los pronunciamientos de control previo', NULL, 'Misional', 69, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:10:34', NULL),
	(232, 'PM01.01.01.01', NULL, 'Gestión eventos de prevención de la corrupción', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(233, 'PM01.01.01.02', NULL, 'Capacitación en temas de ética, integridad pública y lucha contra la corrupción', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(234, 'PM01.01.01.03', NULL, 'Difusión de contenidos para la prevención y lucha contra la corrupción e inconducta funcional', NULL, 'Misional', 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(235, 'PM01.01.02.01', NULL, 'Gestión del registro de avance de obras públicas', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(236, 'PM01.01.02.02', NULL, 'Administración y verificación de las transferencias de gestión', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(237, 'PM01.01.02.03', NULL, 'Administración y verificación de rendición de cuentas de titulares', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(238, 'PM01.01.02.04', NULL, 'Recepción y verificación de declaraciones juradas', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(239, 'PM01.01.02.05', NULL, 'Verificación de la rendición de cuenta del programa de vaso de leche', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(240, 'PM01.01.02.06', NULL, 'Recopilación de información', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(241, 'PM01.01.02.07', NULL, 'Gestión de la información de las donaciones de bienes provenientes del exterior', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(242, 'PM01.01.02.08', NULL, 'Gestión del registro de información de funcionarios y servidores públicos que administren y manejen fondos públicos', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(243, 'PM01.01.02.09', NULL, 'Gestión del registro para el control de contratos de consultoría en el estado', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(244, 'PM01.01.02.10', NULL, 'Gestión para la presentación del balance semestral de los regidores municipales y los consejeros regionales sobre la utilización del monto destinado al fortalecimiento de la función de fiscalización', NULL, 'Misional', 247, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:23:10', NULL),
	(245, 'PM01.01.04', NULL, 'Gestión del observatorio anticorrupción', NULL, 'Misional', 56, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:26:59', NULL),
	(246, 'PM01.01.05', NULL, 'Administración y evaluación de la implementación del control interno en las entidades públicas', NULL, 'Misional', 56, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 21:26:59', NULL),
	(247, 'PM01.01.02', NULL, 'Aprovisionamiento de información específica de operaciones relacionadas a la gestión de recursos públicos', NULL, 'Misional', 56, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:00:53', NULL),
	(248, 'PM01.01.03', NULL, 'Aprovisionamiento de información masiva de operaciones relacionadas a la gestión de recursos públicos', NULL, 'Misional', 56, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:00:53', NULL),
	(249, 'PM03.04.01', NULL, 'Fiscalización de los funcionarios y servidores públicos', NULL, 'Misional', 64, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:09:26', NULL),
	(250, 'PM03.04.02', NULL, 'Análisis y evaluación de la ejecución del gasto del programa vaso de leche', NULL, 'Misional', 64, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:09:26', NULL),
	(251, 'PM03.05.01', NULL, 'Supervisión técnica de los servicios de control', NULL, 'Misional', 65, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:11:46', NULL),
	(252, 'PM03.05.02', NULL, 'Revisión de oficio de informes de control', NULL, 'Misional', 65, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:11:46', NULL),
	(253, 'PM03.05.03', NULL, 'Reformulación de informes de control', NULL, 'Misional', 65, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:11:46', NULL),
	(254, 'PA01.03.05.01', NULL, 'Recategorización de personal', NULL, 'Apoyo', 205, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(255, 'PA01.03.05.02', NULL, 'Convocatoria interna', NULL, 'Apoyo', 205, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(256, 'PA01.03.06.01', NULL, 'Traslados del personal (rotación)', NULL, 'Apoyo', 206, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(257, 'PA01.03.06.02', NULL, 'Encargo de jefatura del órgano o unidad órganica', NULL, 'Apoyo', 206, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(259, 'PA01.04.01.01', NULL, 'Control de asistencia del personal', NULL, 'Apoyo', 207, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(260, 'PA01.04.01.02', NULL, 'Control de vacaciones del personal', NULL, 'Apoyo', 207, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(261, 'PA01.04.01.03', NULL, 'Administración de remuneración del personal', NULL, 'Apoyo', 207, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(262, 'PA01.04.01.04', NULL, 'Administración de pensiones', NULL, 'Apoyo', 207, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(263, 'PA01.04.01.05', NULL, 'Evaluación de solicitudes de pensiones (de cesantía)', NULL, 'Apoyo', 207, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(264, 'PA01.04.02.01', NULL, 'Evaluación de licencias del personal', NULL, 'Apoyo', 208, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(265, 'PA01.04.02.02', NULL, 'Evaluación de horarios especiales del personal', NULL, 'Apoyo', 208, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(266, 'PA01.04.02.03', NULL, 'Emisión de certificados y constancias de trabajo del personal', NULL, 'Apoyo', 208, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(267, 'PA01.04.02.04', NULL, 'Emisión de cartas de presentación del personal', NULL, 'Apoyo', 208, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(268, 'PA01.04.03.01', NULL, 'Afiliación a seguros EPS', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(269, 'PA01.04.03.02', NULL, 'Afiliación a seguros Es Salud', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(270, 'PA01.04.03.03', NULL, 'Desafiliación a seguros EPS', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(271, 'PA01.04.03.04', NULL, 'Desafiliación a seguros Es Salud', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(272, 'PA01.04.03.05', NULL, 'Reembolso de seguros EPS', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(273, 'PA01.04.03.06', NULL, 'Atención de solicitudes de subsidios (incluye canje CITT)', NULL, 'Apoyo', 209, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(274, 'PA01.04.04.01', NULL, 'Administración de legajos', NULL, 'Apoyo', 210, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(275, 'PA01.04.04.02', NULL, 'Verificación de autenticidad de documentos', NULL, 'Apoyo', 210, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(276, 'PA01.04.05.01', NULL, 'Evaluación de denuncias de corrupción contra el personal de la CGR', NULL, 'Apoyo', 211, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(277, 'PA01.04.05.02', NULL, 'Evaluación de denuncias contra el gerente y personal del órgano de auditoría interna de la CGR', NULL, 'Apoyo', 211, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(278, 'PA01.04.05.03', NULL, 'Evaluación de denuncias contra los jefes y personal del OCI', NULL, 'Apoyo', 211, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(279, 'PA01.04.05.04', NULL, 'Gestión del procedimiento administrativo disciplinario', NULL, 'Apoyo', 211, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(280, 'PA01.04.06.01', NULL, 'Tramite documental para el cese de personal', NULL, 'Apoyo', 212, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(281, 'PA01.04.06.02', NULL, 'Generación y pago de la liquidación de beneficios sociales', NULL, 'Apoyo', 212, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:20:25', NULL),
	(282, 'PM01.02.01', NULL, 'Participación ciudadana en el control social a través de auditores juveniles', NULL, 'Misional', 57, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:26:03', NULL),
	(283, 'PM01.02.02', NULL, 'Participación ciudadana en el control social a través de monitores ciudadanos de control', NULL, 'Misional', 57, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:26:03', NULL),
	(284, 'PM01.02.03', NULL, 'Participación ciudadana en el control social a través de audiencias públicas', NULL, 'Misional', 57, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, '2024-06-25 22:26:03', NULL);

-- Volcando estructura para tabla kallpaq.procesos_ouo
CREATE TABLE IF NOT EXISTS `procesos_ouo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_proceso` bigint(20) unsigned NOT NULL,
  `id_ouo` bigint(20) unsigned NOT NULL,
  `responsable` int(11) DEFAULT 0,
  `delegada` int(11) DEFAULT 0,
  `sgc` tinyint(4) DEFAULT 0,
  `sgas` tinyint(4) DEFAULT 0,
  `sgcm` tinyint(4) DEFAULT 0,
  `sgsi` tinyint(4) DEFAULT 0,
  `sgco` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_proceso_ouo` (`id_proceso`,`id_ouo`),
  KEY `procesos_ouo_ibfk_2` (`id_ouo`),
  CONSTRAINT `procesos_ouo_ibfk_1` FOREIGN KEY (`id_proceso`) REFERENCES `procesos` (`id`),
  CONSTRAINT `procesos_ouo_ibfk_2` FOREIGN KEY (`id_ouo`) REFERENCES `ouos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.procesos_ouo: ~270 rows (aproximadamente)
REPLACE INTO `procesos_ouo` (`id`, `id_proceso`, `id_ouo`, `responsable`, `delegada`, `sgc`, `sgas`, `sgcm`, `sgsi`, `sgco`, `created_at`, `updated_at`) VALUES
	(1, 30, 83, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(3, 105, 85, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(4, 106, 85, NULL, NULL, 1, 0, 0, 0, 0, NULL, NULL),
	(5, 107, 85, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(6, 114, 85, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(7, 113, 85, NULL, NULL, 1, 0, 1, 0, 0, NULL, NULL),
	(8, 109, 5, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(9, 117, 85, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(10, 110, 85, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(12, 218, 83, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL),
	(13, 219, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(14, 219, 19, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(15, 220, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(16, 220, 19, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(17, 221, 83, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL),
	(18, 158, 88, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(19, 222, 45, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(20, 111, 82, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(21, 40, 5, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(22, 130, 39, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(23, 131, 22, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(24, 132, 39, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(25, 133, 22, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(26, 134, 38, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(27, 135, 29, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(28, 136, 26, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(29, 145, 29, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(30, 125, 115, NULL, NULL, 1, 0, 1, 0, 0, NULL, NULL),
	(31, 125, 114, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(32, 125, 116, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(33, 125, 117, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(34, 125, 118, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(35, 125, 119, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(36, 125, 120, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(37, 125, 121, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(38, 125, 122, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(39, 125, 123, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(40, 125, 124, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(41, 125, 125, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(42, 125, 126, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(43, 125, 127, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(44, 125, 128, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(45, 125, 129, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(46, 125, 130, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(47, 125, 131, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(48, 125, 132, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(49, 125, 133, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(50, 125, 134, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(51, 125, 135, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(52, 125, 136, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(53, 125, 137, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(54, 125, 138, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(55, 125, 139, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(56, 126, 25, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(57, 128, 34, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(58, 127, 37, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL),
	(59, 127, 38, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL),
	(60, 127, 35, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(61, 127, 114, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(62, 127, 115, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(63, 127, 116, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(64, 127, 117, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(65, 127, 118, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(66, 127, 119, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(67, 127, 120, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(68, 127, 121, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(69, 127, 122, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(70, 127, 123, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(71, 127, 124, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(72, 127, 125, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(73, 127, 126, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(74, 127, 127, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(75, 127, 128, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(76, 127, 129, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(77, 127, 130, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(78, 127, 131, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(79, 127, 132, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(80, 127, 133, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(81, 127, 134, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(82, 127, 135, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(83, 127, 136, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(84, 127, 137, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(85, 127, 138, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(86, 127, 139, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(87, 142, 38, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(88, 142, 114, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(89, 142, 115, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(90, 142, 116, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(91, 142, 117, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(92, 142, 118, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(93, 142, 119, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(94, 142, 120, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(95, 142, 121, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(96, 142, 122, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(97, 142, 123, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(98, 142, 124, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(99, 142, 125, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(100, 142, 126, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(101, 142, 127, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(102, 142, 128, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(103, 142, 129, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(104, 142, 130, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(105, 142, 131, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(106, 142, 132, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(107, 142, 133, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(108, 142, 134, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(109, 142, 135, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(110, 142, 136, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(111, 142, 137, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(112, 142, 138, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(113, 142, 139, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(114, 142, 26, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(115, 142, 27, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(116, 142, 28, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(117, 142, 29, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(118, 142, 30, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(119, 142, 31, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(120, 142, 32, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(121, 142, 33, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(122, 142, 34, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(123, 142, 35, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(124, 142, 36, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(125, 142, 37, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(127, 142, 39, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(128, 125, 26, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(129, 125, 27, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(130, 125, 28, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(131, 125, 29, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(132, 125, 30, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(133, 125, 31, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(134, 125, 32, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(135, 125, 33, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(136, 125, 34, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(137, 125, 35, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(138, 125, 36, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(139, 125, 37, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(140, 125, 38, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(141, 125, 39, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(142, 127, 26, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(143, 127, 27, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(144, 127, 28, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(145, 127, 29, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(146, 127, 30, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(147, 127, 31, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(148, 127, 32, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(149, 127, 33, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(150, 127, 34, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(151, 127, 36, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(152, 127, 39, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(153, 146, 122, NULL, NULL, 1, 0, 1, 0, 0, NULL, NULL),
	(154, 146, 26, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(155, 146, 27, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(156, 146, 28, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(157, 146, 29, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(158, 146, 30, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(159, 146, 31, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(160, 146, 32, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(161, 146, 33, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(162, 146, 34, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(163, 146, 35, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL),
	(164, 146, 36, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(165, 146, 37, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(166, 146, 38, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(167, 146, 39, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(168, 146, 114, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(169, 146, 115, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(170, 146, 116, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(171, 146, 117, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(172, 146, 118, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(173, 146, 119, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(174, 146, 120, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(175, 146, 121, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(176, 146, 123, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(177, 146, 124, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(178, 146, 125, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(179, 146, 126, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(180, 146, 127, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(181, 146, 128, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(182, 146, 129, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(183, 146, 130, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(184, 146, 131, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(185, 146, 132, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(186, 146, 133, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(187, 146, 134, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(188, 146, 135, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(189, 146, 136, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(190, 146, 137, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(191, 146, 138, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(192, 146, 139, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(193, 143, 13, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(194, 137, 45, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(195, 138, 86, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(196, 139, 87, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(197, 246, 12, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(198, 237, 12, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(199, 236, 12, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(200, 238, 16, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(201, 249, 17, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(202, 283, 14, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(203, 284, 14, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(204, 97, 3, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(205, 276, 5, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(206, 118, 75, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(207, 119, 44, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(208, 121, 44, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(209, 81, 70, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(210, 80, 70, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(211, 78, 70, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(212, 148, 45, NULL, NULL, 0, 0, 1, 0, 0, NULL, NULL),
	(213, 151, 45, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(214, 153, 45, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(215, 149, 45, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(216, 150, 45, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(217, 152, 45, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(218, 197, 71, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(219, 205, 71, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(220, 202, 71, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(221, 199, 71, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(222, 201, 71, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(223, 198, 43, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(224, 210, 43, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(225, 213, 43, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(226, 200, 43, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(227, 203, 43, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(228, 257, 43, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(229, 211, 43, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(230, 76, 22, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(231, 85, 69, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(232, 86, 69, NULL, NULL, 1, 1, 0, 0, 0, NULL, NULL),
	(233, 87, 69, NULL, NULL, 1, 0, 0, 0, 0, NULL, NULL),
	(234, 84, 69, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(235, 162, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(236, 163, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(237, 164, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(238, 165, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(239, 166, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(240, 92, 42, NULL, NULL, 0, 1, 1, 0, 0, NULL, NULL),
	(241, 159, 83, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(242, 160, 83, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(243, 224, 140, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(244, 224, 142, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(245, 224, 144, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(246, 224, 145, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(247, 225, 143, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(248, 225, 6, NULL, NULL, 1, 1, 1, 0, 0, NULL, NULL),
	(252, 9, 43, 1, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(259, 35, 85, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(260, 38, 85, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(261, 32, 83, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(262, 41, 45, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(263, 36, 46, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(264, 34, 85, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(265, 37, 19, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(266, 39, 40, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(273, 72, 43, NULL, NULL, 0, 0, 0, 0, 0, NULL, NULL),
	(283, 11, 69, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(284, 13, 44, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(285, 1, 47, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(286, 3, 45, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(287, 14, 46, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(288, 15, 41, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(289, 2, 85, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(290, 104, 11, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(291, 6, 22, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(292, 5, 48, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(293, 4, 8, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(294, 12, 42, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(295, 8, 7, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(296, 7, 4, 1, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(297, 10, 70, 1, 0, 0, 0, 0, 0, 0, NULL, NULL);

-- Volcando estructura para tabla kallpaq.proceso_expectativa
CREATE TABLE IF NOT EXISTS `proceso_expectativa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expectativa_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proceso_expectativa_expectativa_id_foreign` (`expectativa_id`),
  KEY `proceso_expectativa_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `proceso_expectativa_expectativa_id_foreign` FOREIGN KEY (`expectativa_id`) REFERENCES `necesidades_expectativas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proceso_expectativa_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.proceso_expectativa: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.proceso_user
CREATE TABLE IF NOT EXISTS `proceso_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_proceso_user_id_foreign` (`user_id`),
  KEY `user_proceso_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `user_proceso_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_proceso_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.proceso_user: ~3 rows (aproximadamente)
REPLACE INTO `proceso_user` (`id`, `user_id`, `proceso_id`, `created_at`, `updated_at`) VALUES
	(9, 1, 8, NULL, NULL),
	(12, 1, 9, NULL, NULL),
	(26, 1, 35, NULL, NULL);

-- Volcando estructura para tabla kallpaq.programa_auditorias
CREATE TABLE IF NOT EXISTS `programa_auditorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `observacion` varchar(255) NOT NULL,
  `avance` decimal(10,2) NOT NULL,
  `version` int(11) NOT NULL,
  `periodo` varchar(200) NOT NULL,
  `presupuesto` double NOT NULL,
  `fecha_aprobacion` date NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.programa_auditorias: ~0 rows (aproximadamente)
REPLACE INTO `programa_auditorias` (`id`, `observacion`, `avance`, `version`, `periodo`, `presupuesto`, `fecha_aprobacion`, `fecha_publicacion`, `archivo_pdf`, `created_at`, `updated_at`) VALUES
	(1, 'Nuevo Programa 2024', 0.00, 0, '2024', 130000, '2024-06-05', '2024-06-05', '', '2024-06-05 19:38:32', NULL);

-- Volcando estructura para tabla kallpaq.requerimientos
CREATE TABLE IF NOT EXISTS `requerimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `user_asigna_id` bigint(20) unsigned DEFAULT NULL,
  `facilitador_id` bigint(20) unsigned DEFAULT NULL,
  `especialista_id` bigint(20) unsigned DEFAULT NULL,
  `asunto` text NOT NULL,
  `descripcion` text NOT NULL,
  `justificacion` text NOT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  `estado` enum('creado','aprobado','evaluado','asignado','atendido','desestimado') DEFAULT NULL,
  `prioridad` enum('baja','media','alta','muy alta') DEFAULT NULL,
  `complejidad` enum('baja','media','alta','muy alta') DEFAULT NULL,
  `ruta_archivo_desistimacion` text DEFAULT NULL,
  `ruta_archivo_requerimiento` text DEFAULT NULL,
  `fecha_limite` timestamp NULL DEFAULT NULL,
  `fecha_asignacion` timestamp NULL DEFAULT NULL,
  `fecha_inicio` timestamp NULL DEFAULT NULL,
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `requerimientos_proceso_id_foreign` (`proceso_id`),
  KEY `facilitador_id` (`facilitador_id`),
  KEY `requerimientos_user_id_foreign` (`user_asigna_id`) USING BTREE,
  KEY `FK_requerimientos_especialistas` (`especialista_id`),
  CONSTRAINT `FK_requerimientos_especialistas` FOREIGN KEY (`especialista_id`) REFERENCES `especialistas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_requerimientos_users` FOREIGN KEY (`user_asigna_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `requerimientos_facilitador_id` FOREIGN KEY (`facilitador_id`) REFERENCES `users` (`id`),
  CONSTRAINT `requerimientos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimientos: ~16 rows (aproximadamente)
REPLACE INTO `requerimientos` (`id`, `proceso_id`, `user_asigna_id`, `facilitador_id`, `especialista_id`, `asunto`, `descripcion`, `justificacion`, `comentario`, `estado`, `prioridad`, `complejidad`, `ruta_archivo_desistimacion`, `ruta_archivo_requerimiento`, `fecha_limite`, `fecha_asignacion`, `fecha_inicio`, `fecha_fin`, `updated_at`, `created_at`) VALUES
	(1, 162, 207, 1, 1, 'Revisión y optimización del Proceso de Logística', 'Realizar una revisión integral del proceso PR-102 para identificar cuellos de botella y proponer mejoras en el flujo de aprobación.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', '', 'atendido', 'muy alta', 'alta', NULL, 'documentos/ejemplo_requerimiento.pdf', '2026-04-03 00:21:00', '2025-11-05 19:03:33', '2025-11-04 00:21:00', '2025-11-05 19:24:02', '2025-11-05 19:24:02', '2025-10-13 02:33:10'),
	(2, 58, 12, 198, 2, 'Elaboración de Manual de Perfiles de Puesto (MPP)', 'Se solicita la elaboración de la Directiva MA-942 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Resolución N° 055-2025-CGR.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', NULL, 'atendido', 'media', 'alta', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-06-12 14:49:39', '2025-05-29 04:20:59', '2025-06-09 03:03:45', '2025-07-15 00:24:08', '2025-05-26 16:00:37', '2025-05-26 16:00:37'),
	(3, 208, 57, 207, 7, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva PR-286 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Ley N° 30512.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', 'Documento ha sido desestimado por parte del solicitante', 'desestimado', 'media', 'muy alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-07-06 18:42:17', '2025-07-06 18:42:17'),
	(4, 261, 202, 189, 4, 'Revisión y optimización del Proceso de Logística', 'Actualizar el procedimiento FO-951 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'atendido', 'baja', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-06 14:05:31', '2025-09-03 21:30:26', '2025-09-28 06:58:59', '2025-10-09 07:02:02', '2025-09-02 08:56:56', '2025-09-02 08:56:56'),
	(5, 84, 29, 187, 2, 'Elaboración de nueva Directiva de Gestión Documental', 'Incluir el indicador "Porcentaje de satisfacción del usuario" en la Ficha de Proceso de MA-694, con su respectiva fórmula de cálculo.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', 'Se desestimará', 'desestimado', 'alta', 'media', 'requerimientos/5/deestimacion/4PZQ0fPsz8ML7l6ChPYhnwj2Bz9j3HEWFu5SHqN5.pdf', 'documentos/ejemplo_requerimiento.pdf', '2025-01-23 00:20:34', '2025-11-04 00:49:27', '2025-11-04 00:20:34', '2025-11-05 22:16:47', '2025-11-05 22:16:47', '2025-09-12 05:15:56'),
	(6, 130, 54, 210, 1, 'Creación de formato de registro para Control de Calidad', 'Elaborar el Manual de Perfiles de Puesto para las 3 nuevas posiciones creadas en el área de Planificación y Presupuesto.', 'Alineamiento con los objetivos estratégicos del Plan Operativo Institucional (POI) 2026.', NULL, 'asignado', 'muy alta', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2026-02-12 22:37:31', '2025-11-10 17:41:15', '2025-11-04 22:37:31', NULL, '2025-11-10 17:41:15', '2025-06-14 23:48:44'),
	(7, 101, 37, 1, 1, 'Inclusión de nuevo indicador en el Proceso de Atención al Ciudadano', 'Elaborar el Manual de Perfiles de Puesto para las 3 nuevas posiciones creadas en el área de Planificación y Presupuesto.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', NULL, 'desestimado', 'media', 'baja', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-08-09 17:16:50', '2025-08-09 17:16:50'),
	(8, 14, 203, 1, 2, 'Inclusión de nuevo indicador en el Proceso de Atención al Ciudadano', 'Actualizar el procedimiento FO-885 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'asignado', 'muy alta', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-04-03 00:23:42', '2025-11-04 00:30:49', '2025-11-04 00:23:42', NULL, '2025-11-04 00:30:49', '2025-10-06 22:58:32'),
	(9, 119, 194, 11, 5, 'Actualización del Manual de Procedimientos (MAPRO)', 'Actualizar el procedimiento FO-495 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'atendido', 'media', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-29 05:43:32', '2025-09-03 23:49:32', '2025-09-18 18:46:33', '2025-10-17 09:36:20', '2025-06-11 11:09:35', '2025-06-11 11:09:35'),
	(10, 252, 16, 1, 1, 'Revisión y optimización del Proceso de Logística', 'Incluir el indicador "Porcentaje de satisfacción del usuario" en la Ficha de Proceso de MA-731, con su respectiva fórmula de cálculo.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'asignado', 'baja', 'media', NULL, '[{"path":"requerimientos\\/10\\/signed_requerimiento\\/CTvQ04RIIKtChOjKTImrX9WDXiGFZzBkwg3nBHgq.pdf","name":"requerimiento-10.pdf"}]', '2026-01-09 17:37:41', '2025-11-10 17:37:41', '2025-11-10 17:37:41', NULL, '2025-11-10 17:37:41', '2025-08-23 03:02:00'),
	(11, 153, 48, 40, 5, 'Actualización del Manual de Procedimientos (MAPRO)', 'Modificar la Ficha de Proceso FO-874 para añadir las nuevas actividades de control interno detectadas en la última auditoría.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'atendido', 'muy alta', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-01 04:02:35', '2025-08-22 21:48:17', '2025-10-07 07:24:16', '2025-10-07 08:09:57', '2025-06-19 08:17:59', '2025-06-19 08:17:59'),
	(12, 112, 200, 1, 7, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva MA-933 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Resolución N° 055-2025-CGR.', 'Alineamiento con los objetivos estratégicos del Plan Operativo Institucional (POI) 2026.', NULL, 'atendido', 'muy alta', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-03 01:10:36', '2025-08-29 03:58:36', '2025-09-19 01:19:10', '2025-10-31 21:34:58', '2025-07-03 19:34:54', '2025-07-03 19:34:54'),
	(13, 153, 68, 211, 1, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva MA-910 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Ley N° 30512.', 'Necesidad de mejora de la eficiencia operativa y reducción de costos en el área solicitante.', NULL, 'desestimado', 'media', 'alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-09-26 07:39:39', '2025-09-26 07:39:39'),
	(14, 137, 36, 187, 1, 'Actualización del Manual de Procedimientos (MAPRO)', 'Actualizar el procedimiento MA-471 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Necesidad de mejora de la eficiencia operativa y reducción de costos en el área solicitante.', NULL, 'desestimado', 'baja', 'alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-10-27 15:10:04', '2025-10-27 15:10:04'),
	(15, 96, 1, 191, NULL, 'Actualización del Manual de Procedimientos (MAPRO)', 'Modificar la Ficha de Proceso MA-562 para añadir las nuevas actividades de control interno detectadas en la última auditoría.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'creado', 'muy alta', 'muy alta', NULL, 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-10-23 00:15:58', '2025-10-23 00:15:58'),
	(16, 3, NULL, 1, NULL, 'sadasd', 'dsaddasdsad', 'dsadsadasddsaddsad', NULL, 'creado', NULL, 'media', NULL, '[{"path":"requerimientos\\/16\\/signed_requerimiento\\/kcC8NvCNhIZbrIuJRXSRdhv2mRs3hgoXMw4ydQJy.pdf","name":"Ficha de Requerimiento - RQ - 016.pdf"},{"path":"requerimientos\\/16\\/signed_requerimiento\\/Biu1IlOcrj1zAT22HTUHNRURP8odESn0PHM2mVxe.pdf","name":"Hazle+una+pregunta+al+asistente+y+analiza+su+fuente.pdf"},{"path":"requerimientos\\/16\\/signed_requerimiento\\/a77VtKtV9pImlbEoXVUq56Y9shAnah4SMwLik1M7.pdf","name":"Listado+de+Prompts+Resumen+Ejecutivo+en+NotebookLM.pdf"}]', NULL, NULL, NULL, NULL, '2025-11-10 17:46:06', '2025-11-07 00:59:10');

-- Volcando estructura para tabla kallpaq.requerimiento_avances
CREATE TABLE IF NOT EXISTS `requerimiento_avances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requerimiento_id` bigint(20) unsigned NOT NULL,
  `levantamiento` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_levantamiento` text DEFAULT NULL,
  `contexto` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_contexto` text DEFAULT NULL,
  `caracterizacion` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_caracterizacion` text DEFAULT NULL,
  `formatos` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_formatos` text DEFAULT NULL,
  `revision_interna` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_revision_interna` text DEFAULT NULL,
  `revision_tecnica` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_revision_tecnica` text DEFAULT NULL,
  `firma` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_firma` text DEFAULT NULL,
  `publicacion` tinyint(1) NOT NULL DEFAULT 0,
  `comentario_publicacion` text DEFAULT NULL,
  `ruta_evidencias` text DEFAULT NULL,
  `avance_registrado` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requerimiento_id` (`requerimiento_id`),
  CONSTRAINT `requerimiento_avances_requerimiento_id_foreign` FOREIGN KEY (`requerimiento_id`) REFERENCES `requerimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_avances: ~5 rows (aproximadamente)
REPLACE INTO `requerimiento_avances` (`id`, `requerimiento_id`, `levantamiento`, `comentario_levantamiento`, `contexto`, `comentario_contexto`, `caracterizacion`, `comentario_caracterizacion`, `formatos`, `comentario_formatos`, `revision_interna`, `comentario_revision_interna`, `revision_tecnica`, `comentario_revision_tecnica`, `firma`, `comentario_firma`, `publicacion`, `comentario_publicacion`, `ruta_evidencias`, `avance_registrado`, `created_at`, `updated_at`) VALUES
	(6, 5, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 1.00, '2025-11-04 00:20:34', '2025-11-04 00:20:34'),
	(7, 1, 1, 'dsadsad', 1, 'nuevo avance', 1, NULL, 1, NULL, 1, NULL, 1, NULL, 1, NULL, 1, NULL, '[{"name":"MEMORANDO-000365-2025-SCP.pdf","path":"requerimientos\\/1\\/evidencias\\/JhodXUmVhWXFJzZXV9IpR1Pi1eWEAMoGWEV1r45r.pdf","url":"http:\\/\\/localhost\\/storage\\/requerimientos\\/1\\/evidencias\\/JhodXUmVhWXFJzZXV9IpR1Pi1eWEAMoGWEV1r45r.pdf"},{"name":"01Taller _ Inter37001.docx","path":"requerimientos\\/1\\/evidencias\\/hhz3mXt3Tu28JQoYQhl2SCEXTWCXM5kBnCoAnWA6.docx","url":"http:\\/\\/localhost\\/storage\\/requerimientos\\/1\\/evidencias\\/hhz3mXt3Tu28JQoYQhl2SCEXTWCXM5kBnCoAnWA6.docx"},{"name":"Memo._Circ._00125.pdf","path":"requerimientos\\/1\\/evidencias\\/Xny2OteQ0kgnpxAs3Rrz6OZaN7CNyoHOcbb63utJ.pdf","url":"http:\\/\\/127.0.0.1:8000\\/storage\\/requerimientos\\/1\\/evidencias\\/Xny2OteQ0kgnpxAs3Rrz6OZaN7CNyoHOcbb63utJ.pdf"}]', 100.00, '2025-11-04 00:21:00', '2025-11-05 19:23:50'),
	(8, 8, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '[{"name":"MEMORANDO ABAS.docx","path":"requerimientos\\/8\\/evidencias\\/USXXL9UJaDu4kbEWkhrNc023ejf0J1ry9yKNP3RV.docx","url":"http:\\/\\/127.0.0.1:8000\\/storage\\/requerimientos\\/8\\/evidencias\\/USXXL9UJaDu4kbEWkhrNc023ejf0J1ry9yKNP3RV.docx"}]', 1.00, '2025-11-04 00:23:42', '2025-11-05 19:51:34'),
	(9, 6, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 2.00, '2025-11-04 22:03:12', '2025-11-04 22:37:31'),
	(13, 10, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 2.00, '2025-11-10 17:37:41', '2025-11-10 17:37:41');

-- Volcando estructura para tabla kallpaq.requerimiento_evaluaciones
CREATE TABLE IF NOT EXISTS `requerimiento_evaluaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requerimiento_id` bigint(20) unsigned NOT NULL,
  `num_actividades` tinyint(3) unsigned NOT NULL COMMENT '1–4 de acuerdo a la complejidad',
  `num_areas` tinyint(3) unsigned NOT NULL COMMENT '1–4 de acuerdo a la complejidad',
  `num_requisitos` tinyint(3) unsigned NOT NULL COMMENT '1–4 de acuerdo a la complejidad',
  `nivel_documentacion` tinyint(3) unsigned NOT NULL COMMENT '1–4 de acuerdo a la complejidad',
  `impacto_requerimiento` tinyint(3) unsigned NOT NULL COMMENT '1–4 de acuerdo a la complejidad',
  `complejidad_valor` tinyint(3) unsigned NOT NULL,
  `complejidad_nivel` enum('baja','media','alta','muy alta') NOT NULL,
  `fecha_evaluacion` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requerimiento_evaluaciones_requerimiento_id_index` (`requerimiento_id`),
  CONSTRAINT `requerimiento_evaluaciones_requerimiento_id_foreign` FOREIGN KEY (`requerimiento_id`) REFERENCES `requerimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_evaluaciones: ~1 rows (aproximadamente)
REPLACE INTO `requerimiento_evaluaciones` (`id`, `requerimiento_id`, `num_actividades`, `num_areas`, `num_requisitos`, `nivel_documentacion`, `impacto_requerimiento`, `complejidad_valor`, `complejidad_nivel`, `fecha_evaluacion`, `created_at`, `updated_at`) VALUES
	(2, 5, 1, 2, 2, 3, 4, 12, 'media', '2025-11-03', '2025-11-03 23:57:45', '2025-11-04 00:00:52'),
	(3, 6, 1, 2, 3, 3, 3, 12, 'media', '2025-11-04', '2025-11-04 22:31:54', '2025-11-04 22:31:54'),
	(4, 16, 1, 1, 2, 3, 4, 11, 'media', '2025-11-11', '2025-11-07 19:10:05', '2025-11-11 15:12:55'),
	(5, 10, 1, 1, 2, 2, 4, 10, 'media', '2025-11-10', '2025-11-10 17:24:27', '2025-11-10 17:37:25');

-- Volcando estructura para tabla kallpaq.requerimiento_movimientos
CREATE TABLE IF NOT EXISTS `requerimiento_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requerimiento_id` bigint(20) unsigned NOT NULL,
  `estado` enum('creado','aprobado','evaluado','asignado','derivado','atendido','desestimado','cerrado') NOT NULL,
  `comentario` text DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requerimiento_movimientos_requerimiento_id_foreign` (`requerimiento_id`),
  KEY `requerimiento_movimientos_usuario_id_foreign` (`user_id`) USING BTREE,
  CONSTRAINT `requerimiento_movimientos_requerimiento_id_foreign` FOREIGN KEY (`requerimiento_id`) REFERENCES `requerimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `requerimiento_movimientos_usuario_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_movimientos: ~51 rows (aproximadamente)
REPLACE INTO `requerimiento_movimientos` (`id`, `requerimiento_id`, `estado`, `comentario`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 4', 1, '2025-11-03 22:27:14', '2025-11-03 22:27:14'),
	(2, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 22:27:30', '2025-11-03 22:27:30'),
	(3, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 22:27:31', '2025-11-03 22:27:31'),
	(4, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 3', 1, '2025-11-03 22:44:21', '2025-11-03 22:44:21'),
	(5, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 22:44:32', '2025-11-03 22:44:32'),
	(6, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 22:45:03', '2025-11-03 22:45:03'),
	(7, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 3', 1, '2025-11-03 22:53:41', '2025-11-03 22:53:41'),
	(8, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 3', 1, '2025-11-03 22:54:55', '2025-11-03 22:54:55'),
	(9, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 3', 1, '2025-11-03 22:54:56', '2025-11-03 22:54:56'),
	(10, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:15:40', '2025-11-03 23:15:40'),
	(11, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 23:17:13', '2025-11-03 23:17:13'),
	(12, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:17:41', '2025-11-03 23:17:41'),
	(13, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:17:56', '2025-11-03 23:17:56'),
	(14, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 23:18:10', '2025-11-03 23:18:10'),
	(15, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:19:21', '2025-11-03 23:19:21'),
	(16, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 23:21:14', '2025-11-03 23:21:14'),
	(17, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-03 23:21:22', '2025-11-03 23:21:22'),
	(18, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:21:39', '2025-11-03 23:21:39'),
	(19, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:21:55', '2025-11-03 23:21:55'),
	(20, 1, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 13 y nivel: alta', 1, '2025-11-03 23:27:50', '2025-11-03 23:27:50'),
	(21, 1, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-03 23:49:40', '2025-11-03 23:49:40'),
	(22, 5, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 12 y nivel: media', 1, '2025-11-03 23:57:45', '2025-11-03 23:57:45'),
	(23, 5, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 12 y nivel: media', 1, '2025-11-04 00:00:52', '2025-11-04 00:00:52'),
	(24, 5, 'asignado', 'Asignación del requerimiento al especialista ID: ', 1, '2025-11-04 00:12:22', '2025-11-04 00:12:22'),
	(25, 5, 'asignado', 'Asignación del requerimiento al especialista ID: ', 1, '2025-11-04 00:14:18', '2025-11-04 00:14:18'),
	(26, 5, 'asignado', 'Asignación del requerimiento al especialista ID: ', 1, '2025-11-04 00:14:27', '2025-11-04 00:14:27'),
	(27, 5, 'asignado', 'Asignación del requerimiento al especialista ID: ', 1, '2025-11-04 00:16:31', '2025-11-04 00:16:31'),
	(28, 5, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 00:20:34', '2025-11-04 00:20:34'),
	(29, 1, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 00:21:00', '2025-11-04 00:21:00'),
	(30, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 3', 1, '2025-11-04 00:23:42', '2025-11-04 00:23:42'),
	(31, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 00:28:23', '2025-11-04 00:28:23'),
	(32, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 00:28:42', '2025-11-04 00:28:42'),
	(33, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 00:29:37', '2025-11-04 00:29:37'),
	(34, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 00:29:56', '2025-11-04 00:29:56'),
	(35, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 00:30:17', '2025-11-04 00:30:17'),
	(36, 8, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 00:30:49', '2025-11-04 00:30:49'),
	(37, 1, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 00:41:33', '2025-11-04 00:41:33'),
	(38, 5, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 00:49:27', '2025-11-04 00:49:27'),
	(39, 1, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-04 01:37:47', '2025-11-04 01:37:47'),
	(40, 6, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 12 y nivel: media', 1, '2025-11-04 22:31:54', '2025-11-04 22:31:54'),
	(41, 6, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-04 22:37:31', '2025-11-04 22:37:31'),
	(42, 1, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-05 19:03:33', '2025-11-05 19:03:33'),
	(43, 1, 'atendido', 'El requerimiento ha sido finalizado.', 1, '2025-11-05 19:21:21', '2025-11-05 19:21:21'),
	(44, 1, 'atendido', 'El requerimiento ha sido finalizado.', 1, '2025-11-05 19:24:02', '2025-11-05 19:24:02'),
	(45, 6, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-11-07 19:29:58', '2025-11-07 19:29:58'),
	(46, 10, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 10 y nivel: media', 1, '2025-11-10 17:37:25', '2025-11-10 17:37:25'),
	(47, 10, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-10 17:37:41', '2025-11-10 17:37:41'),
	(48, 6, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2025-11-10 17:41:15', '2025-11-10 17:41:15'),
	(49, 16, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 5 y nivel: baja', 1, '2025-11-10 17:44:18', '2025-11-10 17:44:18'),
	(50, 16, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 5 y nivel: baja', 1, '2025-11-10 17:45:19', '2025-11-10 17:45:19'),
	(51, 16, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 11 y nivel: media', 1, '2025-11-10 17:46:06', '2025-11-10 17:46:06');

-- Volcando estructura para tabla kallpaq.requisitos
CREATE TABLE IF NOT EXISTS `requisitos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `salida_id` bigint(20) unsigned NOT NULL,
  `requisito_cod` tinytext DEFAULT NULL,
  `requisito` text NOT NULL,
  `documento` mediumtext DEFAULT NULL,
  `fecha_requisito` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requisitos_salida_id_foreign` (`salida_id`),
  CONSTRAINT `requisitos_salida_id_foreign` FOREIGN KEY (`salida_id`) REFERENCES `salidas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requisitos: ~4 rows (aproximadamente)
REPLACE INTO `requisitos` (`id`, `salida_id`, `requisito_cod`, `requisito`, `documento`, `fecha_requisito`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'Debe ser conciso, exacto, lógico, objetivo, oportuno, ordenado y sistemático', 'Directiva N. 013-2022-CG/NORM ?Servicio de Control Simult?neo?', '2022-05-31', '2025-05-09 17:52:16', NULL),
	(2, 1, NULL, 'Debe ser elaborado y suscrito por la Comisión de Control (jefe de Comisión, Supervisor y/o Especialista), en un plazo máximo de tres (3) días hábiles de terminada la etapa de ejecución del servicio, y revisado y aprobado por la unidad orgánica u órgano de la CGR o por el OCI en un plazo de máximo de dos (2) días hábiles, para ser comunicado a las instancias que correspondan.', 'Directiva N. 013-2022-CG/NORM ?Servicio de Control Simult?neo?', '2022-05-31', '2025-05-09 17:59:05', NULL),
	(3, 1, NULL, 'El Informe de Visita de Control debe contener el detalle de las situaciones adversas identificadas en la actividad o hito de control, las cuales se describen de forma objetiva, clara y \r\nprecisa, identificando sus elementos, la evidencia que las sustentan e incluyendo sus conclusiones y la recomendación general a la que haya lugar. De igual forma, en caso no se hayan identificado situaciones adversas, se deja constancia de ello, dando cuenta de la \r\nevaluación realizada. Asimismo, de corresponder, incluye el detalle de los Reportes de Avance ante Situaciones Adversas y las acciones preventivas y correctivas adoptadas y comunicadas \r\npor la entidad o dependencia, y aquellas que respecto de las cuales no se ha comunicado acción alguna.', 'Directiva N. 013-2022-CG/NORM ?Servicio de Control Simult?neo?', '2022-05-31', '2025-05-09 17:59:06', NULL),
	(5, 1, NULL, 'La publicacion de los informes se realiza dentros de los cinco (5) dias hábiles siguientes de la notificación de los mismos ', 'Directiva N. 013-2022-CG/NORM ?Servicio de Control Simult?neo?', '2022-05-31', '2025-05-09 19:30:17', NULL);

-- Volcando estructura para tabla kallpaq.riesgos
CREATE TABLE IF NOT EXISTS `riesgos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_cod` varchar(255) DEFAULT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `riesgo_nombre` text NOT NULL,
  `riesgo_tipo` enum('Riesgo','Oportunidad') DEFAULT NULL,
  `factor_id` bigint(20) unsigned DEFAULT NULL,
  `controles` text DEFAULT NULL,
  `probabilidad` int(11) NOT NULL,
  `impacto` int(11) NOT NULL,
  `riesgo_valor` int(11) NOT NULL,
  `riesgo_valoracion` varchar(255) NOT NULL,
  `riesgo_tratamiento` enum('Reducir','Aceptar','Trasladar') DEFAULT NULL,
  `estado` enum('abierto','cerrado','pendiente','proyecto') NOT NULL,
  `fecha_valoracion_rr` date DEFAULT NULL,
  `probabilidad_rr` int(11) DEFAULT NULL,
  `impacto_rr` int(11) DEFAULT NULL,
  `evaluacion_rr` int(11) DEFAULT NULL,
  `riesgo_estado_rr` enum('Con Eficacia','Sin eficacia') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `riesgos_riesgo_cod_unique` (`riesgo_cod`),
  KEY `riesgos_proceso_cod_foreign` (`proceso_id`),
  KEY `riesgos_factor_cod_foreign` (`factor_id`),
  CONSTRAINT `riesgos_factor_cod_foreign` FOREIGN KEY (`factor_id`) REFERENCES `factores` (`id`),
  CONSTRAINT `riesgos_proceso_cod_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgos: ~51 rows (aproximadamente)
REPLACE INTO `riesgos` (`id`, `riesgo_cod`, `proceso_id`, `riesgo_nombre`, `riesgo_tipo`, `factor_id`, `controles`, `probabilidad`, `impacto`, `riesgo_valor`, `riesgo_valoracion`, `riesgo_tratamiento`, `estado`, `fecha_valoracion_rr`, `probabilidad_rr`, `impacto_rr`, `evaluacion_rr`, `riesgo_estado_rr`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, NULL, 105, 'La falta de respaldo activo y compromiso por parte de la alta dirección y de los OUO en la programación y ejecución de las auditorías internas puede generar resistencia al cambio y disminuir el interés en implementar las mejoras identificadas.', 'Riesgo', 4, 'Sin controles', 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-26 16:01:33', NULL),
	(23, NULL, 164, 'Rendición de viáticos fuera del plazo establecido debido a la no priorización de estas actividades por parte del Comisionado.', 'Riesgo', 4, NULL, 6, 6, 36, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:19:15', '2025-03-05 17:21:55', NULL),
	(24, NULL, 164, 'Incumplimiento de plazos para la solicitud de viáticos debido al desconocimiento del procedimiento y directiva de viáticos por parte de los comisionados', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:21:50', '2025-03-05 18:28:48', NULL),
	(25, NULL, 164, 'No atender los requerimientos de viáticos al exterior debido a la demora en la firma de la resolución administrativa.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:39:54', '2025-02-28 00:39:54', NULL),
	(26, NULL, 164, 'Que no se autorice el requerimiento de anticipo, debido a que no se cuenta con la opinión favorable de ABAS, a la tardía generación de requerimiento de anticipo y a la falta de disponibilidad presupuestal.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:51:32', '2025-02-28 00:51:32', NULL),
	(30, NULL, 166, 'Rendición de cuentas de anticipo fuera de plazo, debido a la no priorización de las\nactividades por parte del colaborador OUO.', 'Riesgo', 4, NULL, 6, 6, 36, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 18:47:50', '2025-02-28 18:47:50', NULL),
	(31, NULL, 166, 'Otorgar anticipos por un monto mayor al establecido debido a desconocimiento de la normativa relacionada a Anticipos.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 19:00:07', '2025-02-28 19:00:07', NULL),
	(32, NULL, 166, 'Que las rendiciones de anticipos no cumplan con los criterios establecidos, debido a desconocimiento por parte del colaborador OUO acerca de la normativa de SUNAT.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 19:49:22', '2025-02-28 19:49:22', NULL),
	(33, NULL, 165, 'Apertura de Caja Chica de manera oportuna', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:40:32', '2025-02-28 20:40:32', NULL),
	(34, NULL, 165, 'Atención de gastos por caja chica que no cumplen requisitos establecidos, debido a la aplicación inadecuada de la norma o desconocimiento de la norma de parte de los colaboradores.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:44:46', '2025-02-28 20:44:46', NULL),
	(35, NULL, 165, 'Desembolso de la caja chica fuera del plazo, debido a demora en el levantamiento de las observaciones, problemas con los aplicativos del sistema de caja chica, SGD o SIAF.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:47:25', '2025-02-28 20:47:25', NULL),
	(36, NULL, 165, 'Liquidación final de caja chica fuera del plazo establecido, debido a demora en el levantamiento de las observaciones, demora en el depósito T6, problemas con los aplicativos: SIGA- Caja Chica, SGD, SIAF.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:50:16', '2025-02-28 20:50:16', NULL),
	(37, NULL, 92, 'Acta de Conciliación de Bienes y Suministros que ha sido validado o firmado y que cuente con información inconsistente (errores de información no detectada), a causa de errores en la revisión.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:10:23', '2025-02-28 21:10:23', NULL),
	(38, NULL, 92, 'Acta de Conciliación del Marco Legal y Ejecución del Presupuesto que ha sido validado o firmado y que cuente con información inconsistente (errores de información) no detectada, a causa de errores en la revisión.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:11:32', '2025-02-28 21:11:32', NULL),
	(39, NULL, 92, 'Información inconsistente no detectada en el Acta de Conciliación de Cuentas Corrientes y Cuenta Única del Tesoro Púbico (validada o firmada), a causa de errores en la revisión.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:11:59', '2025-02-28 21:11:59', NULL),
	(40, NULL, 92, 'Presentación de los estados financieros y presupuestarios fuera de los plazos establecidos, por información tardía por parte de los órganos o unidades orgánicas que conforman el pliego CGR, así como por deficiencia del módulo cliente y web del aplicativo SIAFSP y del SIAF - Módulo contable.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:12:43', '2025-02-28 21:12:43', NULL),
	(42, NULL, 106, 'Perdida de la certificación por no cumplir con los requisitos de la norma internacional.', 'Riesgo', 4, 'sin controles', 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-05 19:21:19', '2025-03-13 23:35:26', NULL),
	(43, NULL, 107, 'No cumplir con los lineamientos establecidos por no contar con el soporte documentario, falta de compromiso de la Alta dirección o unidades orgánicas.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-05 19:25:30', '2025-03-24 21:17:17', NULL),
	(44, NULL, 113, 'No cumplir con los lineamientos establecidos en la Norma Técnica a “Implementación de la Gestión por Procesos en las Entidades de la Administración Pública” por no contar con el marco normativo interno actualizado.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-05 20:41:44', '2025-03-12 22:28:59', NULL),
	(45, NULL, 114, 'Perdida de la certificación por no cumplir con los requisitos de la norma internacional', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-05 21:05:00', '2025-03-05 21:05:00', NULL),
	(47, NULL, 117, 'Sobrerregulación de los procesos debido a una visión centrada en los aspectos funcionales, sin considerar de manera integral el proceso en su totalidad.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:03:32', '2025-03-26 15:56:11', NULL),
	(48, NULL, 219, 'Posible presentación de casos de inelegibilidad de gastos, debido al incumplimiento de procedimiento, instancias, responsabilidades y normas que se debe seguir para la ejecución del proyecto.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:09:50', '2025-03-06 14:09:50', NULL),
	(49, NULL, 219, 'Posible suspensión en los desembolsos del prestamo BID, debido al incumplimiento de acuerdos contractuales.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:10:09', '2025-03-06 14:10:09', NULL),
	(50, NULL, 219, 'Posibilidad de no obtener la "no objeción" por parte del banco, debido al incumplimiento de la política para la adquisición de bienes y obras financiados por el BID.', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:10:26', '2025-03-24 22:29:31', NULL),
	(51, NULL, 220, 'Posibilidad de no obtener la "no objeción" por parte del banco, debido al incumplimiento de la pólitica de selección y contratación de consultores financiados por el BID', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:13:22', '2025-03-24 22:29:00', NULL),
	(53, NULL, 219, 'Posible afectación en el cumplimiento de los objetivos de los proyectos de inversión debido a modificaciones normativas sobre inversiones y políticas de gobierno, desactualizada o cambios en la normativa para la fase de ejecución de proyectos de inversión, insuficiente cultura en la gestión de proyectos, cambios en la estructura orgánica de la CGR o nuevos roles y funciones asignados al personal.', 'Riesgo', 4, NULL, 8, 8, 64, 'Alto', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:46:41', '2025-03-06 14:46:41', NULL),
	(58, NULL, 30, 'Incumplimiento de los Objetivos Estratégicos Institucionales', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:35:17', '2025-03-24 21:35:17', NULL),
	(59, NULL, 32, 'La Subgerencia de Abastecimiento no pueda iniciar oportunamente el desarrollo del Cuadro de Necesidades', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:42:36', '2025-03-24 21:42:36', NULL),
	(60, NULL, 32, 'Aprobación del Plan Operativo Institucional del siguiente año, fuera del plazo establecido', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:43:08', '2025-03-24 21:43:08', NULL),
	(61, NULL, 32, 'Incumplimiento de elabora el Seguimiento del Plan Operativo dentro de los plazos establecidos', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:43:24', '2025-03-24 21:43:24', NULL),
	(62, NULL, 32, 'Incumplimiento de elaborar la Evaluación del Plan Operativo Institucional a más tardar el siguiente mes de concluido el trimestre', 'Riesgo', 4, NULL, 4, 4, 16, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:43:49', '2025-03-24 21:43:49', NULL),
	(63, NULL, 109, 'Los colaboradores podrían incumplir la Política y Objetivos Antisoborno al no considerar los controles establecidos en los riesgos o procesos dentro del alcance del certificado del Sistema de Gestión Antisoborno.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:47:56', '2025-03-24 21:48:51', NULL),
	(64, NULL, 109, 'Los colaboradores podrían incumplir los requisitos de la norma ISO 37001:2016, al no considerar los controles establecidos en los riesgos o procesos dentro del alcance del certificado del Sistema de Gestión Antisoborno.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:48:27', '2025-03-24 21:48:27', NULL),
	(65, NULL, 40, 'La Entidad podría incumplir con la Política Nacional de Integridad al no contar con el compromiso o interés de Alta Dirección y funcionarios que garantice una gestión efectiva y sostenible de la integridad pública.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:40:52', '2025-03-24 22:40:52', NULL),
	(66, NULL, 40, 'La ejecución de las actividades del Programa de integridad podría incumplirse por falta de compromiso de los responsables de los Órganos o Unidades Orgánicas involucradas en la sostenibilidad de la implementación del modelo de integridad', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:41:13', '2025-03-24 22:41:13', NULL),
	(67, NULL, 276, 'La gestión de las denuncias podría ser realizada fuera del plazo establecido en la normativa.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:08:56', '2025-03-24 23:08:56', NULL),
	(68, NULL, 276, 'En el marco de la gestión de denuncias, podría vulnerarse la identidad del denunciante y/o la materia de la denuncia y/o las actuaciones derivadas de la misma.', 'Riesgo', 4, NULL, 6, 8, 48, 'Alto', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:09:49', '2025-03-24 23:09:49', NULL),
	(69, NULL, 276, 'Especialista de denuncias podría evaluar la solicitud de medida de protección por el denunciante incumpliendo las normas establecidas.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:10:12', '2025-03-24 23:11:15', NULL),
	(70, NULL, 276, 'El denunciante podría verse afectado en el ejercicio de los derechos personales y/o laborales, debido a que no se dé cumplimiento a la medida de protección otorgada.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:10:42', '2025-03-24 23:10:42', NULL),
	(71, NULL, 81, 'No elaborar y/o actualizar la normativa que regule los procesos archivísticos a causa de no haber determinado la responsabilidad y prioridad de tal obligación.', 'Riesgo', 4, NULL, 6, 8, 48, 'Alto', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:26:08', '2025-03-24 23:26:08', NULL),
	(73, NULL, 81, 'Proporcionar información confidencial a los usuarios por no contar con controles de seguridad establecidos (documentados).', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:29:32', '2025-03-24 23:29:32', NULL),
	(74, NULL, 81, 'Archivo de documentos con series documentales combinadas a causa de escasos mecanismos de difusión y socialización con las unidades de organización.', 'Riesgo', 4, NULL, 8, 4, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:30:18', '2025-03-24 23:30:41', NULL),
	(75, NULL, 81, 'Solicitudes de eliminación rechazadas debido a desconocimiento de procedimiento vigente.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:30:57', '2025-03-24 23:30:57', NULL),
	(76, NULL, 81, 'La degradación de soportes físicos y digitales puede comprometer la integridad y accesibilidad de la información, afectando la eficiencia operativa y la seguridad de la información en la organización.', 'Riesgo', 4, NULL, 6, 8, 48, 'Alto', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:34:53', '2025-03-24 23:34:53', NULL),
	(77, NULL, 78, 'Posible incumplimiento de los criterios, actividades y roles asignados en los procedimientos, TUPA y directiva de "Gestión documental", debido a la poca experiencia o desconocimiento por parte del responsable de la actividad correspondiente a los presentes procedimientos.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:25:50', '2025-03-25 00:25:50', NULL),
	(78, NULL, 78, 'Posible incumplimiento de los requisitos de recepción y errores en las derivaciones de documentos (clasificados como reservados, secretos y confidenciales), debido a la poca experiencia del responsable del proceso.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:26:17', '2025-03-25 00:26:17', NULL),
	(79, NULL, 78, 'Posible incumplimiento de los criterios establecidos en el manual de atención a la ciudadanía, debido al desconocimiento o poca experiencia por parte del responsable de la actividad.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:27:19', '2025-03-25 00:27:19', NULL),
	(80, NULL, 78, 'Demora por una inadecuada verificación de los requisitos e incorrecto registro de la presentación de la declaración jurada, debido al desconocimiento o errores involuntarios por parte del responsable de la actividad.', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:27:55', '2025-03-25 00:27:55', NULL),
	(81, NULL, 78, 'Posible soborno hacia personal de Gestión Documentaria para proporcionar indebidamente información a favor de terceros para fines personales, debido a la falta de ética del personal que realiza la gestión de recepción de documentos.', 'Riesgo', 4, NULL, 4, 8, 32, 'Medio', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:29:06', '2025-03-25 00:29:06', NULL),
	(82, NULL, 118, 'Indisponibilidad de la información respaldada por una falla de software o hardware', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:37:13', '2025-03-25 00:37:13', NULL),
	(83, NULL, 118, 'Demora en el resguardo y recuperación de la información del correo electrónico respaldada por el proveedor debido que los recursos e infraestructura no están bajo el control de CGR', 'Riesgo', 4, NULL, 4, 6, 24, 'Bajo', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:37:28', '2025-03-25 00:37:28', NULL);

-- Volcando estructura para tabla kallpaq.riesgo_acciones
CREATE TABLE IF NOT EXISTS `riesgo_acciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_cod` bigint(20) unsigned NOT NULL,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riesgo_acciones_riesgo_cod_foreign` (`riesgo_cod`),
  CONSTRAINT `riesgo_acciones_riesgo_cod_foreign` FOREIGN KEY (`riesgo_cod`) REFERENCES `riesgos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgo_acciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.riesgo_controles
CREATE TABLE IF NOT EXISTS `riesgo_controles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `riesgo_cod` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riesgo_controles_riesgo_cod_foreign` (`riesgo_cod`),
  CONSTRAINT `riesgo_controles_riesgo_cod_foreign` FOREIGN KEY (`riesgo_cod`) REFERENCES `riesgos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgo_controles: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.roles: ~5 rows (aproximadamente)
REPLACE INTO `roles` (`id`, `name`, `descripcion`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Administración de Tablas Maestras y configuraciones del Sistema.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(2, 'especialista_sig', 'Especialista del SIG, puede tener permisos de los modulos de Indicadores, Riesgos, Hallazgos o Procesos.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(3, 'facilitador', 'Tiene acceso a la vista de facilitador del proceso y se puede habilitar los diferentes módulos del SIG.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(4, 'especialista', 'Tienes acceso de sólo lectura a los reportes y dashboards del SIG de acuerdo a los procesos de su propiedad.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30'),
	(5, 'propietario', 'Tienes acceso de sólo lectura a los reportes y dashboards del SIG de acuerdo a los procesos de su propiedad.', 'web', '2023-08-25 20:55:30', '2023-08-25 20:55:30');

-- Volcando estructura para tabla kallpaq.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.role_has_permissions: ~0 rows (aproximadamente)
REPLACE INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1);

-- Volcando estructura para tabla kallpaq.salidas
CREATE TABLE IF NOT EXISTS `salidas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `salida` varchar(255) NOT NULL,
  `tipo` enum('servicio','producto','ambos') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proceso_salida` (`proceso_id`),
  CONSTRAINT `fk_proceso_salida` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.salidas: ~4 rows (aproximadamente)
REPLACE INTO `salidas` (`id`, `proceso_id`, `salida`, `tipo`, `created_at`, `updated_at`) VALUES
	(1, 125, 'Informe de Visita de Control', 'producto', '2025-05-09 17:08:31', NULL),
	(2, 126, 'Informe de Orientación de Oficio', 'producto', '2025-05-09 17:09:59', NULL),
	(3, 127, 'Informe de Control Concurrente', 'producto', '2025-05-09 17:10:00', NULL),
	(4, 128, 'Informe de Control Simutaneo', 'producto', '2025-05-09 17:10:01', NULL);

-- Volcando estructura para tabla kallpaq.sipocs
CREATE TABLE IF NOT EXISTS `sipocs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `proveedores` varchar(255) NOT NULL,
  `entradas` varchar(255) NOT NULL,
  `clientes` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sipocs_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `sipocs_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.sipocs: ~0 rows (aproximadamente)
REPLACE INTO `sipocs` (`id`, `proceso_id`, `proveedores`, `entradas`, `clientes`, `created_at`, `updated_at`) VALUES
	(1, 62, 'Entidades, Ciudadanos, Medios de Comunicacion', 'Denuncias, Alertas', 'Entidad, Ciudadano', '2025-05-09 17:02:00', NULL);

-- Volcando estructura para tabla kallpaq.subareas_compliance
CREATE TABLE IF NOT EXISTS `subareas_compliance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `area_compliance_id` bigint(20) unsigned NOT NULL,
  `subarea_compliance_nombre` varchar(255) NOT NULL,
  `subarea_compliance_descripcion` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subarea_compliance_area_compliance_id_foreign` (`area_compliance_id`),
  CONSTRAINT `subarea_compliance_area_compliance_id_foreign` FOREIGN KEY (`area_compliance_id`) REFERENCES `areas_compliance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.subareas_compliance: ~41 rows (aproximadamente)
REPLACE INTO `subareas_compliance` (`id`, `area_compliance_id`, `subarea_compliance_nombre`, `subarea_compliance_descripcion`, `created_at`, `updated_at`) VALUES
	(1, 8, 'Acceso a la información pública', 'Normas que regulan el acceso de la ciudadanía a la información de la entidad.', NULL, NULL),
	(2, 8, 'Participación ciudadana', 'Lineamientos que promueven el involucramiento de la ciudadanía en la gestión pública.', NULL, NULL),
	(3, 8, 'Gobierno abierto', 'Directrices sobre apertura de datos, transparencia activa y colaboración.', NULL, NULL),
	(4, 8, 'Rendición de cuentas', 'Procedimientos para informar y justificar la gestión institucional.', NULL, NULL),
	(5, 6, 'Gestión ambiental', 'Normas sobre manejo sostenible de recursos naturales y protección del ambiente.', NULL, NULL),
	(6, 6, 'Inclusión social', 'Lineamientos que garantizan la equidad y no discriminación en las intervenciones.', NULL, NULL),
	(7, 6, 'Responsabilidad social institucional', 'Acciones que refuerzan el compromiso ético con el entorno y las personas.', NULL, NULL),
	(8, 5, 'Ejecución presupuestal', 'Normas que rigen el uso adecuado y oportuno de los recursos públicos.', NULL, NULL),
	(9, 5, 'Contabilidad pública', 'Disposiciones sobre el registro y control contable del gasto público.', NULL, NULL),
	(10, 5, 'Tesorería y caja institucional', 'Procedimientos para el manejo financiero y pago de obligaciones.', NULL, NULL),
	(11, 2, 'Selección y contratación', 'Normativa sobre procesos de reclutamiento y vinculación del personal.', NULL, NULL),
	(12, 2, 'Evaluación del desempeño', 'Criterios y procesos para medir el rendimiento del personal.', NULL, NULL),
	(13, 2, 'Bienestar y clima laboral', 'Lineamientos para fomentar un ambiente organizacional positivo.', NULL, NULL),
	(14, 3, 'Transformación digital', 'Estrategias y proyectos que impulsan la digitalización institucional.', NULL, NULL),
	(15, 3, 'Seguridad de la información', 'Políticas de protección de la confidencialidad, integridad y disponibilidad de datos.', NULL, NULL),
	(16, 3, 'Interoperabilidad y sistemas', 'Normas sobre integración y operación conjunta de plataformas digitales.', NULL, NULL),
	(17, 4, 'Archivo institucional', 'Disposiciones sobre clasificación, conservación y disposición final de documentos.', NULL, NULL),
	(18, 4, 'Digitalización documental', 'Lineamientos para convertir documentos físicos en archivos electrónicos.', NULL, NULL),
	(19, 4, 'Trazabilidad documental', 'Mecanismos para rastrear el ciclo de vida de los documentos.', NULL, NULL),
	(20, 10, 'Formulación de proyectos', 'Guías para estructurar iniciativas alineadas con los objetivos estratégicos.', NULL, NULL),
	(21, 10, 'Cooperación técnica', 'Normas sobre acuerdos y convenios con organismos nacionales e internacionales.', NULL, NULL),
	(22, 10, 'Evaluación de proyectos', 'Herramientas para medir resultados e impacto de intervenciones institucionales.', NULL, NULL),
	(23, 14, 'Auditoría interna', 'Normas y procedimientos de control interno conforme a estándares técnicos.', NULL, NULL),
	(24, 14, 'Control posterior', 'Lineamientos para fiscalización una vez ejecutadas las actividades institucionales.', NULL, NULL),
	(25, 14, 'Supervisión preventiva', 'Acciones de control simultáneo que buscan mitigar riesgos antes de que ocurran.', NULL, NULL),
	(26, 1, 'Contratación pública', 'Normas sobre adquisición de bienes, servicios y obras conforme a la Ley de Contrataciones.', NULL, NULL),
	(27, 1, 'Gestión de proveedores', 'Lineamientos para evaluar, seleccionar y administrar proveedores.', NULL, NULL),
	(28, 1, 'Abastecimiento institucional', 'Normas sobre almacenamiento, distribución y control de bienes institucionales.', NULL, NULL),
	(29, 7, 'Convenios institucionales', 'Disposiciones para formalizar alianzas con otras entidades o actores.', NULL, NULL),
	(30, 7, 'Responsabilidad institucional', 'Compromisos asumidos para mejorar el entorno social o ambiental.', NULL, NULL),
	(31, 7, 'Compromisos voluntarios', 'Obligaciones no legales asumidas ante la ciudadanía o grupos de interés.', NULL, NULL),
	(32, 9, 'Gestión de calidad', 'Normas relacionadas con la mejora continua y cumplimiento de estándares.', NULL, NULL),
	(33, 9, 'Gestión por procesos', 'Lineamientos para estructurar, mapear y optimizar procesos institucionales.', NULL, NULL),
	(34, 9, 'Gestión de riesgos', 'Identificación, análisis y tratamiento de riesgos institucionales.', NULL, NULL),
	(35, 9, 'Planeamiento estratégico', 'Estrategias y herramientas para definir y ejecutar los objetivos institucionales.', NULL, NULL),
	(36, 13, 'Sistema de gestión antisoborno', 'Implementación de controles basados en la ISO 37001.', NULL, NULL),
	(37, 13, 'Cultura ética', 'Normas para promover valores institucionales y buenas prácticas.', NULL, NULL),
	(38, 13, 'Prevención de conflictos de interés', 'Lineamientos para declarar, gestionar y prevenir conflictos éticos.', NULL, NULL),
	(39, 11, 'Comunicación interna', 'Estrategias para compartir información dentro de la organización.', NULL, NULL),
	(40, 11, 'Relación con medios y redes', 'Normas sobre vocería, uso de redes sociales y relaciones con prensa.', NULL, NULL),
	(41, 11, 'Difusión de resultados', 'Lineamientos para comunicar logros, actividades y productos de gestión.', NULL, NULL);

-- Volcando estructura para tabla kallpaq.tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.tags: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.tipo_documentos
CREATE TABLE IF NOT EXISTS `tipo_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sigla_tipodocumento` varchar(255) NOT NULL,
  `nombre_tipodocumento` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_documentos_sigla_unique` (`sigla_tipodocumento`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.tipo_documentos: ~12 rows (aproximadamente)
REPLACE INTO `tipo_documentos` (`id`, `sigla_tipodocumento`, `nombre_tipodocumento`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
	(1, 'MG', 'Manual de Sistema de Gestión', 1, NULL, '2025-11-06 01:38:08', NULL),
	(2, 'MN', 'Manual de aplicativos informáticos', 1, NULL, '2025-11-06 01:38:09', NULL),
	(3, 'PC', 'Plan de la Calidad', 1, NULL, '2025-11-06 01:38:10', NULL),
	(4, 'PR', 'Procedimiento', 1, NULL, '2025-11-06 01:38:11', NULL),
	(5, 'GU', 'Guía', 1, NULL, '2025-11-06 01:38:11', NULL),
	(6, 'IT', 'Instructivo', 1, NULL, '2025-11-06 01:38:12', NULL),
	(7, 'DI', 'Directriz', 1, NULL, '2025-11-06 01:38:13', NULL),
	(8, 'PT', 'Protocolo', 1, NULL, '2025-11-06 01:38:14', NULL),
	(9, 'F', 'Formato', 1, NULL, '2025-11-06 01:38:14', NULL),
	(10, 'MA', 'Manual', 1, NULL, '2025-11-06 01:38:15', NULL),
	(11, 'DA', 'Directiva', 1, NULL, '2025-11-06 01:38:16', NULL),
	(12, 'RE', 'Reglamento', 1, NULL, '2025-11-06 01:38:16', NULL);

-- Volcando estructura para tabla kallpaq.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `sigla` varchar(255) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.users: ~311 rows (aproximadamente)
REPLACE INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `sigla`, `foto_url`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Juan Almeyda Requejo', 'jalmeyda@contraloria.gob.pe', '2023-08-30 15:54:20', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, 'yUYvlPt3sGZ02aabUsVW5DeQpjBhsWQH1WBinK40LnP2Xayiq9MpGU04Ap8T', '2023-05-26 23:01:48', '2023-08-29 04:37:54'),
	(2, 'Manuel Perez Effus', 'manuelperez@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(3, 'Angel Arturo Bendezu Cardenas\r\n', 'abendezuc@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(4, 'Maria Isabel Hiyo Huapaya\r\n', 'mhiyo@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(5, 'Ana Elsa Gonzales Napaico\r\n', 'agonzalesn@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(6, 'Gatsby Loayza Parraga\r\n', 'gloayza@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(7, 'Gustavo Adolfo Villanueva Salvador\r\n', 'gvillanuevas@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(8, 'Elias Martin Tresierra Paz\r\n', 'etresierra@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(9, 'César Aguilar Surichaqui', 'despachocontralorcgr@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(10, 'Giovanna Muñoz Silva (e)', 'gmunoz@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(11, 'Amado Enco Tirado', 'aenco@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(12, 'Hilda Jaramillo Velarde (e)', 'hjaramillo@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(13, 'Ester Díaz Segura', 'ediazse@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(14, 'Richard León Vargas', 'rleonva@contraloria.gob.pe', NULL, '', NULL, NULL, NULL, '2025-01-16 17:50:24', '2025-01-16 17:50:24'),
	(15, 'Luigino Pilotto Carreño', 'lpilotto@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(16, 'Víctor Mejía Zuloeta', 'emejia@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(17, 'Luis Ramírez Moscoso', 'luis.ramirez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(18, 'Giancarlo Ugaz Figueroa', 'giancarlo.ugaz@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(19, 'Magda Salgado Rubianes', 'magda.salgado@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(20, 'Solange Pérez Montero', 'solange.perez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(21, 'Carlos Jaime Rivero Morales', 'crivero@contraloria.gob.pe', '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(22, 'Michell Sifuentes Sifuentes', 'michell.sifuentes@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(23, 'Frank Mauricio Morales', 'frank.morales@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(24, 'Karla Pérez Guzmán', 'karla.perez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(25, 'Vanessa Walde Ortega', 'vanessa.walde@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(26, 'María Luna Torres', 'maria.luna@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(27, 'Ganimedes Rosales Reyes', 'ganimedes.rosales@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(28, 'Areli Valencia Vargas', 'areli.valencia@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(29, 'Patricia Salazar Velarde', 'patricia.salazar@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(30, 'Marco Argandoña Dueñas', 'margandona@contraloria.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(31, 'Janes Rodríguez López', 'janes.rodriguez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(32, 'Luis Castillo Torrealva', 'luis.castillo@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(33, 'Edwars Cotrina Chávez', 'edwars.cotrina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(34, 'Johnny Rubina Meza', 'johnny.rubina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(35, 'Felix Li Padilla', 'felix.li@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(36, 'Juana Llacsahuanga Chávez', 'juana.llacsahuanga@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(37, 'Jorge Llamoctanta Trejo', 'jorge.llamoctanta@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(38, 'Moisés Vera Rodríguez', 'moises.vera@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(39, 'Tito Medina Sánchez', 'tito.medina@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(40, 'Fidel Hernández Vega', 'fidel.hernandez@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(41, 'Flabio García Esquivel', 'flabio.garcia@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(42, 'Dante Yorges Avalos', 'dante.yorges@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(43, 'Francisco Ochoa Uriarte', 'francisco.ochoa@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(44, 'Iván Cieza Yaipén', 'ivan.cieza@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(45, 'Paco Toledo Yallico', 'paco.toledo@cgr.gob.pe', NULL, '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, NULL, '2025-01-16 20:04:06', '2025-01-16 20:04:06'),
	(46, 'Gonzalo Pérez Wicht', 'gonzalo.perez@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(47, 'Luis Peralta Guzmán', 'luis.peralta@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(48, 'Oscar Mestanza Malaspina', 'oscar.mestanza@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(49, 'Eduardo Dionisio Astuhuamán', 'eduardo.dionisio@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(50, 'Alan Saldaña Bustamante', 'alan.saldana@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(51, 'Daniel Sedan Villacorta', 'daniel.sedan@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(52, 'Oswaldo Wetzell L.O.', 'oswaldo.wetzell@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(53, 'Guillermo Uribe Córdova', 'guillermo.uribe@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(54, 'Marco Bermúdez Torres', 'marco.bermudez@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(55, 'Luis Espinal Redondez', 'luis.espinal@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(56, 'Luis Toledo Zatta', 'luis.toledo@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(57, 'Victor Asin Chumpitaz', 'victor.asin@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(58, 'Renato Sandoval González', 'renato.sandoval@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(59, 'Carolina Díaz Maldonado', 'carolina.diaz@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(60, 'Aydeé Luna Lezama', 'aydee.luna@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(61, 'Patricia Salazar Velarde', 'patricia.salazar.nc@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(62, 'José Jaramillo Narváez', 'jose.jaramillo@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(63, 'Carlos Loyola Escajadillo', 'carlos.loyola@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(64, 'María Livaque Garay', 'maria.livaque@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(65, 'Wilfredo Cárdenas Cortez', 'wilfredo.cardenas@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(66, 'Gatsby Loayza Párraga', 'gatsby.loayza@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(67, 'Joao Pacheco Castro', 'joao.pacheco@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(68, 'Zusi Castro Grandez', 'zusi.castro@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(69, 'Mariela Farro Torres', 'mariela.farro@cgr.gob.pe', NULL, '*A0F874BC7F54EE086FCE60A37CE7887D8B31086B', NULL, NULL, NULL, '2025-01-16 20:09:23', '2025-01-16 20:09:23'),
	(184, 'Iber Ari Gomez', 'igomez@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2024-05-08 23:27:55', NULL),
	(185, 'Ginna Gamarra Solano', 'ggamarra@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(186, 'Darío Valverde Cueva', 'dvalverde@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(187, 'David Quiroga Paiva', 'dquiroga@contraloria.gob.pem', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(188, 'María Guevara Ríos', 'mguevara@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(189, 'Raúl Ramírez Aguirre', 'rramirez@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(190, 'Hubert Salazar Velásquez', 'hsalazar@contraloria.gob.pem', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(191, 'Harrinson Godoy Barreto', 'hgodoy@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(192, 'William Boulanger Jimenez', 'wboulanger@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(193, 'Tomás Tello Benzaquen', 'ttello@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(194, 'Felipe Vegas Palomino', 'fvegas@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(195, 'Luis Díaz Salazar', 'ldiaz@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(196, 'Jorge Tafur Domínguez', 'jtafur@contraloria.gob.p', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(197, 'Jean Vásquez Neira', 'jvasquez@contraloria.gob.p', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(198, 'Roberto Hipólito Domínguez', 'rhipolito@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(199, 'Johannes García Guzmán', 'jgarcia@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(200, 'Julio Aliaga Silva', 'jaliaga@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(201, 'Edwin Gonzáles Boza', 'egonzales@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(202, 'Samuel Rivera Vásquez', 'srivera@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(203, 'César Justo Gómez', 'cjusto@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(204, 'Ander Cruz Torres', 'acruz@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(205, 'Frank Venero Torres', 'fvenero@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(206, 'Joél Rodríguez Paz', 'jrodriguez@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(207, 'Indira Yábar Gutiérrez', 'iyabar@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(208, 'Pedro de la Peña Álvarez', 'pdelapena@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:01:38', '2025-01-20 22:01:38'),
	(209, 'Gerald Flores Morán', 'gflores@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:41:24', '2025-01-20 05:00:00'),
	(210, 'Jonatan Montenegro Duarez', 'jmontenegro@contraloria.gob.pe', NULL, NULL, NULL, NULL, NULL, '2025-01-20 22:42:39', '2025-01-20 05:00:00'),
	(211, 'NAVARRO DE LA CRUZ KELLY ROXANA', 'knavarro@contraloria.gob.pe', NULL, 'password', NULL, NULL, NULL, '2025-02-04 19:29:58', NULL),
	(212, 'LOPEZ RENGIFO HILTON', 'hlopezr@contraloria.gob.pe', NULL, 'password', NULL, NULL, NULL, '2025-02-04 19:33:56', NULL),
	(213, 'Maria Violeta Santin Alfageme ', 'vsantin@contraloria.gob.pe', NULL, 'password', NULL, NULL, NULL, '2025-02-04 21:04:36', NULL),
	(214, 'Nicolas Efrain Carbajarl Torres', 'ncarbajal@contraloria.gob.pe', NULL, 'password', NULL, NULL, NULL, '2025-02-04 21:07:25', NULL),
	(215, 'Alex Herbet León Oscano', 'hleon@contraloria.gob.pe', NULL, 'password', NULL, NULL, NULL, '2025-02-04 21:09:19', NULL),
	(216, 'Douglas Marks', 'ehayes@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'P1zloGRYBC', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(217, 'Georgianna Bailey PhD', 'schmeler.catalina@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'YprGYr8IKa', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(218, 'Blaze Veum', 'njohnston@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'WEeCqKyKWm', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(219, 'Dr. Trystan Boyer DDS', 'chester40@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'PbX9SbG4zG', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(220, 'Dr. Laurie Bashirian PhD', 'conner87@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'GHmiJLvUkB', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(221, 'Vladimir Macejkovic', 'kasey.beahan@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 't2nzOL5qu6', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(222, 'Hoyt O\'Conner', 'kmclaughlin@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'PuQDjyCNkU', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(223, 'Mr. Albin Marquardt PhD', 'terrence.rolfson@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2AgcGVDMJj', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(224, 'Dr. Clyde Schultz', 'vtorp@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'AnBQjypZKW', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(225, 'Prof. Lew Gleason', 'carroll.casper@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cttysMP42Y', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(226, 'Kenya Tillman', 'stacey.schultz@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '0cjBVQPhxK', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(227, 'Mr. Brayan Sipes', 'aufderhar.araceli@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'OnB0FxcAYt', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(228, 'Lera Stracke', 'hilpert.else@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '0wscBmhq0u', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(229, 'Houston Windler', 'xhowe@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '3AFqR13jrf', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(230, 'Shane Bins', 'herzog.filomena@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'FHrauyBU8Y', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(231, 'Nova Ward', 'jpouros@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '6IPxyAdd9V', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(232, 'Jayne Windler', 'gislason.alphonso@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'lSRAeIPhRe', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(233, 'Mrs. Alyson Douglas', 'johnson.cruickshank@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'iCIrW8jZQE', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(234, 'Amiya White DVM', 'orn.millie@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'VICYFpcW75', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(235, 'Maxime Boyle II', 'americo.connelly@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'WEwoUxIx55', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(236, 'Zoe Bogan', 'nadams@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'z0N5D21yH6', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(237, 'Dr. Eloy Graham DDS', 'waylon.walter@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'zx8Fr62xfd', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(238, 'Kacie Quigley', 'dana.blick@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'n7pcKVahLx', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(239, 'Alexandria Dach', 'feest.drew@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'DCRjd883cE', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(240, 'Ms. Antonette Ullrich', 'verona.rodriguez@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '1S9iLmTFed', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(241, 'Jasen Hilpert', 'vthiel@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'miG17LWgX2', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(242, 'Prof. Eduardo Stark', 'madison.daugherty@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'nM9wRhnsrw', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(243, 'Dakota Schmeler IV', 'elissa37@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'KlKsMrDwVV', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(244, 'Dr. Eldon Flatley', 'oconnell.sage@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'B6uNApktjx', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(245, 'Jace Leuschke', 'rschaden@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vPqreZYdF2', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(246, 'Vernice Bauch', 'lconn@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'IvDw2uKOYs', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(247, 'Adella Hayes DVM', 'ahmed39@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'YbiVaPOS2V', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(248, 'Dr. Sasha Brakus I', 'yesenia92@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'g4J08JuRS9', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(249, 'Ansel Padberg', 'anabel.weimann@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'uFrRQDnx15', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(250, 'Osvaldo Donnelly', 'dstrosin@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'HexJz28CZg', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(251, 'Prof. Angus Frami DVM', 'jasmin07@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'CHQm6Wgs5z', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(252, 'Obie Murray', 'grimes.miracle@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'uNwPjOq5sO', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(253, 'Prof. Kaley Hodkiewicz', 'allie.monahan@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '5yBz502ZeA', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(254, 'Kameron Mitchell', 'maufderhar@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vB6kT4vd7I', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(255, 'Dr. Mercedes Ernser', 'alycia18@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2ojdghoIGl', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(256, 'Dr. Alfred Dooley DVM', 'kirk.daugherty@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'OBhRigwY9I', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(257, 'Santa Toy II', 'pohara@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ZGnHyjtPwO', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(258, 'Carlo Konopelski', 'shanelle.paucek@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'moFpXiAflv', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(259, 'Darion Halvorson', 'rory17@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'M0gQR6MJOL', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(260, 'Prof. Leif Brakus', 'oyundt@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Kpss95WQP1', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(261, 'Taurean Haag', 'hellen.stiedemann@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'N0p7ZILIC6', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(262, 'Ms. Ayla Halvorson IV', 'urolfson@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'U2WJ8QwHyh', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(263, 'Dr. Aliza Nicolas', 'amparo08@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'uCnNNMa2WN', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(264, 'Tiana Brakus', 'ctromp@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'oAZBnx2xbq', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(265, 'Donato Jenkins IV', 'winifred.strosin@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'AQKpwzNIGN', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(266, 'Lessie Stamm DDS', 'hlynch@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Jni6bLreU2', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(267, 'Orrin Yost', 'cloyd.collier@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'NLO6ELEbCn', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(268, 'Mr. Darius Marvin MD', 'reichert.ulices@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'oCHB01O4ST', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(269, 'Mr. Zachery Langworth', 'qlarkin@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'q5v5eKyuBS', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(270, 'Ashley Streich', 'oconner.ciara@example.net', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'lHcSVb94GB', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(271, 'Prof. Reid Brekke V', 'deborah77@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'MKYRXArq7t', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(272, 'Waldo Bashirian', 'mylene47@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'mjqhDHZzq8', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(273, 'Mr. Justyn Reichert', 'janis77@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'rL3bZQ1Gnr', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(274, 'Garry Schmidt', 'audie.kuhlman@example.org', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Ic4FG30qdU', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(275, 'Sarina Hegmann', 'cstehr@example.com', '2025-11-11 22:27:56', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Ur2cqCIZso', '2025-11-11 22:27:56', '2025-11-11 22:27:56'),
	(276, 'Ignacio Crona', 'windler.ernest@example.org', '2025-11-11 22:30:08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'HD7vPbQqWq', '2025-11-11 22:30:08', '2025-11-11 22:30:08'),
	(277, 'Elliott Blanda', 'tpacocha@example.com', '2025-11-11 22:30:08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vZNeIxjt7V', '2025-11-11 22:30:08', '2025-11-11 22:30:08'),
	(278, 'Brant O\'Kon', 'block.elody@example.net', '2025-11-11 22:30:08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '1Nukz3uGK5', '2025-11-11 22:30:08', '2025-11-11 22:30:08'),
	(279, 'Prof. Astrid Hamill MD', 'arianna.bernier@example.org', '2025-11-11 22:30:08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cPvTD5i3vr', '2025-11-11 22:30:08', '2025-11-11 22:30:08'),
	(280, 'Prof. Griffin Cummings', 'lbailey@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qEVTU9qG0A', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(281, 'Dr. Naomi Greenfelder V', 'kstracke@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'fNW6rXGn25', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(282, 'Walter Murray', 'gerhold.olaf@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ONvKX56Hxp', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(283, 'Kiel Schoen', 'haley.maryjane@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Y4W05wXPPr', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(284, 'Dr. Matt Krajcik', 'maureen.leffler@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '8VwcODNMiZ', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(285, 'Doyle Tremblay', 'ebony45@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'BJY4ZJSnTp', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(286, 'Brad Auer', 'ziemann.elisha@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ZBJyUl5PZO', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(287, 'Samanta McKenzie', 'flatley.garnet@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'QQTxdJCs1h', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(288, 'Dr. Werner Weissnat', 'bfisher@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'QEUDyQQsH0', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(289, 'Amalia Dooley MD', 'dolores.schimmel@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'QW5qKFSFH4', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(290, 'Lelah McLaughlin', 'mathilde.bins@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vdaB6Z2wsR', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(291, 'Dr. General Moore MD', 'kuhn.maxine@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'YBegXzl1AH', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(292, 'Althea Mraz DDS', 'dannie23@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '03ilGB34bN', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(293, 'Helen Daniel II', 'jamir.schiller@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ElYuj2kwqI', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(294, 'Ms. Retha Tillman', 'vonrueden.charity@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'mACDk80mz4', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(295, 'Audie Thompson', 'spencer.thurman@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'FLVRsonCy5', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(296, 'Frankie Grimes', 'lboyle@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '4obCJU7y89', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(297, 'Kay Beer', 'green.edison@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'J1QApOrN9J', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(298, 'Omer Turner II', 'durgan.jaiden@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'OyN2MxNNwt', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(299, 'Sandrine Quigley', 'lyla.osinski@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'uAtadJEAru', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(300, 'Craig Jacobi', 'jokeefe@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Ks0QkCzZU3', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(301, 'Flossie Wintheiser', 'rwilderman@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '6VVjsiKqZg', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(302, 'Gilberto Orn', 'lilyan79@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'UqZXzvexSQ', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(303, 'Mr. Alvis Klocko IV', 'fay78@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'egVqNtLymV', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(304, 'Dr. Jayce Gottlieb', 'milan.mckenzie@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'YAnOfNhqB4', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(305, 'Velda White', 'myles.predovic@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'm4jGgAJB69', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(306, 'Mrs. Yvette Bode DVM', 'yruecker@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'UU6I3R1pkD', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(307, 'Jarrett Beahan', 'neha.cummings@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Xn6eT8G82A', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(308, 'Ethelyn Sporer', 'ldurgan@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Zxux1uldrh', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(309, 'Miss Katheryn Ward', 'leopoldo58@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'MAlFMukbKs', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(310, 'Judy McClure', 'jamil09@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '87QUz9VnZg', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(311, 'Mr. Barney Rau', 'trevor54@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '5bODioMDPt', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(312, 'Aleen Larkin', 'linwood16@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'eCmX96oH8y', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(313, 'Dr. Davonte Daniel II', 'aupton@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2hh3Zkebr9', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(314, 'Lisandro Abbott', 'nelle77@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Pn0jA2Z0AE', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(315, 'Prof. Tatum Kunze', 'josiane24@example.com', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'hIKZ2aF4Ha', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(316, 'Elta Labadie', 'austyn.damore@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Lsn3gSRfB0', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(317, 'Elias Towne', 'kris.moses@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'MhLal2jJpp', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(318, 'Heaven Haag MD', 'gunner63@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'wwAeCK62WM', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(319, 'Carlie Mohr', 'collier.ova@example.net', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ZdlR5u0P5g', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(320, 'Mona Ledner', 'streich.cara@example.org', '2025-11-11 22:30:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'NP69UiGAWE', '2025-11-11 22:30:09', '2025-11-11 22:30:09'),
	(321, 'Rossie Reinger', 'ebert.nona@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'VbAi74gktB', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(322, 'Benjamin Waelchi', 'windler.verner@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'KnXidWFhcj', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(323, 'Prof. Jaleel Reinger', 'tina.ratke@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'TdRSubFj7o', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(324, 'Ms. Gina Armstrong', 'rryan@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'O3ZOy9Y9gw', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(325, 'Anais Hessel Jr.', 'alfred31@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'UKDTyakNza', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(326, 'Prof. Alexander Mante', 'ecassin@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'XhnkxuPM4S', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(327, 'Adelbert Spinka', 'lauren30@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'hSuf9ipgez', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(328, 'Mrs. Jennifer Schroeder MD', 'melody.green@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'HcH8iyD5lx', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(329, 'Lexus Kessler', 'alba48@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2z4pggBU81', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(330, 'Sarina Miller', 'myah.nitzsche@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vKLJ9dX3My', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(331, 'Oleta Doyle', 'sallie98@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'MtSBAjUQAj', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(332, 'Zelma Ledner', 'berry82@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'SftPPtOy9a', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(333, 'Dr. Mac Kihn PhD', 'rcasper@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cLZhFnTvxW', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(334, 'Vena Kunze III', 'miller.rogelio@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '64jFOXlcy6', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(335, 'Tavares Sawayn', 'wisozk.ernestina@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qFjoCSQxMZ', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(336, 'Gisselle Daugherty', 'stella.dibbert@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qAzlQ6tgkl', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(337, 'Mr. Trevion Berge', 'spinka.thea@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Zk5DM5fc3Y', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(338, 'Jessie Beier', 'vraynor@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'trh6YRrVrA', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(339, 'Tyrique Hudson', 'cfriesen@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'VTlDzUmKCS', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(340, 'Olen Klein', 'wgoodwin@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'YJjYPCUQaH', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(341, 'Demarco Abbott', 'rfunk@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'BaWmkUl9x1', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(342, 'Adonis Breitenberg', 'bulah79@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Apmt3R3I31', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(343, 'Hugh Hirthe', 'treutel.keeley@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'pNWMEmGjrn', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(344, 'Mr. Talon Kassulke III', 'zcorwin@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'LqOVwC3dJa', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(345, 'Cleo Gutkowski', 'vsteuber@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'xJaJDVmMFw', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(346, 'Ramiro Johnson', 'lgutkowski@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'iUulVEdDn5', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(347, 'Laurie Harris', 'torp.annamae@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cDfRfW70Q5', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(348, 'Scotty Durgan', 'greynolds@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'rH3BJVxrwz', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(349, 'Terrence DuBuque', 'stephon.predovic@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'uJsQDpXct5', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(350, 'Kenton Nikolaus', 'don.buckridge@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'DURlhR1xQP', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(351, 'Miss Janae Lueilwitz', 'kaycee96@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'upkRY8osNN', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(352, 'Noah Balistreri', 'adalberto21@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qQq3BQGCRt', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(353, 'Flo Ankunding', 'langosh.mike@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'PuE3bHlQtl', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(354, 'Miss Stephanie Schuster III', 'xhudson@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'SgZ9sbXx35', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(355, 'Destiney Nienow', 'ozella.funk@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Gnjq4jdyxa', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(356, 'Zachariah Schumm', 'adalberto.dooley@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'PrVvQBYW7I', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(357, 'Gerard Mohr', 'jaden.breitenberg@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '0eZ86nWQzV', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(358, 'Emery Durgan', 'amya.white@example.org', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Ion1OhNstO', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(359, 'Elouise Abernathy', 'rodriguez.lenore@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ys0iKyGKQs', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(360, 'Cornell Predovic', 'oheller@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'BmBN3w42Gh', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(361, 'Miss Jermaine Collins', 'bernie49@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'nEUoz6CinK', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(362, 'Elbert Bartoletti', 'hirthe.raquel@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Mfvzp2iMxs', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(363, 'Mack Streich', 'maia96@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'QAcaPNn94O', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(364, 'Dr. Felicita Lockman DVM', 'monte.gutkowski@example.com', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'mv1DNR7ec5', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(365, 'Prof. Haylee Torphy MD', 'gorczany.skyla@example.net', '2025-11-11 22:32:09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '7nCe1hifL7', '2025-11-11 22:32:09', '2025-11-11 22:32:09'),
	(366, 'Lois Koss', 'xzboncak@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'tRYaB4t5I3', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(367, 'Percival Koepp III', 'ggreenholt@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'pnSIJqVkHO', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(368, 'Milford Okuneva', 'cormier.jamar@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qomHRSn5Gx', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(369, 'Greta Grimes', 'delfina.lemke@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'FbnwpzfJ4W', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(370, 'Dr. Pete Schumm MD', 'kturcotte@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'BIQlAoWXnI', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(371, 'Mrs. Cecelia Mayert II', 'macie11@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '6QLYAwRlRT', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(372, 'Kayley Dickens I', 'earlene.kovacek@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2IswhnBNdh', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(373, 'Sydnee Kilback', 'dimitri42@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'C8M39O2l1w', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(374, 'Miss Mylene Doyle', 'bledner@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'RNhFeLoK0A', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(375, 'Dr. Kaley Lebsack II', 'predovic.jeff@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'DNERVHtkzf', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(376, 'Ms. Rosina Swift', 'schaefer.ruby@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'qdPIOUhjTB', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(377, 'Xzavier Tillman', 'assunta.quigley@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '7JI5SYrLdN', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(378, 'Jacynthe Hegmann', 'selina.anderson@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'CQQZc3SXE1', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(379, 'Alfredo Herzog', 'lesch.jacinthe@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '5rUbB73WKC', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(380, 'Wendell Sipes V', 'tobin94@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '5nYfMvWPLC', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(381, 'Dawson Orn I', 'hermann.zoey@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'MwmFEiXkEb', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(382, 'Prof. Diego Kertzmann', 'amir16@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'fWQkGcs4Id', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(383, 'Kevon Paucek', 'pietro.haag@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'tjARUh14ou', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(384, 'Prof. Danika Conn', 'donny68@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'f8oyvlYwJf', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(385, 'Libby Bradtke', 'trever19@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'I8klCQUMH7', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(386, 'Betty Wyman', 'brakus.arturo@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'e5rSLRIt1s', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(387, 'Clinton Crist', 'reynold.volkman@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'xRIVJSFD6g', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(388, 'Prof. Annabelle Terry Sr.', 'drogahn@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'LKef5mmE4a', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(389, 'Audra Turner', 'astroman@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '6qeoUrwFqV', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(390, 'Mr. Judah Franecki Sr.', 'rbins@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'wmNoGmzYPW', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(391, 'Clifford Kunde DVM', 'owilkinson@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vLdCciKDKF', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(392, 'Dr. Garrett Altenwerth IV', 'amber44@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'D1LBOglMoR', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(393, 'Alford Marvin', 'iauer@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'CW2Gr8VAWj', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(394, 'Prof. Catalina Kuphal Sr.', 'dare.allene@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'aNdjxoede5', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(395, 'Prof. Alisa Beer Jr.', 'wdouglas@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'i63Yxsq9J8', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(396, 'Prof. Craig Rodriguez', 'gerson.kulas@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '16Y1R4Xjyp', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(397, 'Norris Kiehn', 'gleason.blanca@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2zkeCS3gFL', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(398, 'Daniella Huel', 'lwisoky@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'lccvbpnXFf', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(399, 'Prof. Theodore Goodwin', 'bparisian@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '3tdrmJNpWp', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(400, 'Kyleigh Mayert Jr.', 'roel48@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Fwow9MJwNc', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(401, 'Robert Corwin', 'lschumm@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'nWacqrqmiX', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(402, 'Nichole Baumbach', 'fbechtelar@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'AGg4tVClxc', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(403, 'Prof. Luther Larson', 'ritchie.jody@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '5vk1js68gs', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(404, 'Dolores Cruickshank', 'grady.pat@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'CJYDTsnovb', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(405, 'Wilfred Kuvalis', 'verona43@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Ia1kTV8ZWx', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(406, 'Lizzie O\'Conner', 'mrobel@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'j45VWV2dPD', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(407, 'Isaiah Langosh DDS', 'vada21@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'n88iCZOMSz', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(408, 'Ms. Cordie Hettinger', 'otho73@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ZaRgsrRZzg', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(409, 'Carmine Denesik III', 'carrie.schumm@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cMngEjGVs2', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(410, 'Katelyn Kirlin', 'giovani.medhurst@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'd5H6HfS0qV', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(411, 'Oleta Conn', 'heaney.syble@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'kO6C5cuKlS', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(412, 'Duane Hahn', 'jrice@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'Av5oZpCMHF', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(413, 'Electa Schaefer', 'joany39@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'HbY8ZWnKyV', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(414, 'Jacklyn Bayer', 'rau.velda@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'cMIOBFjwMP', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(415, 'Christopher Nienow Jr.', 'elijah21@example.net', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'vOMOIMuYAE', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(416, 'Johnson Schuppe', 'swill@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'lLSGM1s6RJ', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(417, 'Mr. Miguel Stroman PhD', 'jonathon20@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '2Hqy4otL82', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(418, 'Meredith Lang MD', 'katrine38@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'JXR9nXPKlL', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(419, 'Trinity Littel', 'sadye.parisian@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'nLFSRQVYkQ', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(420, 'Makenna Boyer', 'hailey.sipes@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ey0Pkpg5yN', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(421, 'Fabian Orn', 'logan94@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, '6pNU940W19', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(422, 'Mrs. Mya Hauck PhD', 'merl95@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'nlTYeZTQvd', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(423, 'Prof. Modesta Goodwin I', 'eframi@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'KpEF6kRzGK', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(424, 'Dr. Jazmyn Weimann DVM', 'rabernathy@example.org', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'fGIX1UIM7q', '2025-11-11 22:35:03', '2025-11-11 22:35:03'),
	(425, 'Mallory Watsica', 'luigi.krajcik@example.com', '2025-11-11 22:35:03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'ac1QNJ3wun', '2025-11-11 22:35:03', '2025-11-11 22:35:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
