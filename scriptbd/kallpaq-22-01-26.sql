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
  `hallazgo_proceso_id` bigint(20) unsigned DEFAULT NULL,
  `accion_cod` varchar(255) DEFAULT NULL,
  `accion_tipo` enum('corrección','acción correctiva') DEFAULT NULL,
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
  `accion_estado` enum('programada','desestimada','en proceso','implementada') DEFAULT NULL,
  `accion_ciclo` int(10) unsigned DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acciones_hallazgos` (`hallazgo_id`),
  KEY `FK_acciones_hallazgo_proceso` (`hallazgo_proceso_id`) USING BTREE,
  CONSTRAINT `FK_acciones_hallazgo_proceso_id` FOREIGN KEY (`hallazgo_proceso_id`) REFERENCES `hallazgo_proceso` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `FK_acciones_hallazgos` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.acciones: ~5 rows (aproximadamente)
REPLACE INTO `acciones` (`id`, `hallazgo_id`, `hallazgo_proceso_id`, `accion_cod`, `accion_tipo`, `accion_descripcion`, `accion_comentario`, `accion_fecha_inicio`, `accion_fecha_fin_planificada`, `accion_fecha_fin_reprogramada`, `accion_fecha_cancelada`, `accion_fecha_fin_real`, `accion_justificacion`, `accion_ruta_evidencia`, `accion_responsable`, `accion_responsable_correo`, `accion_estado`, `accion_ciclo`, `created_at`, `updated_at`) VALUES
	(1, 55, NULL, 'H-87920-001', 'corrección', 'Juan Almeyda', NULL, '2025-11-12', '2025-12-25', NULL, NULL, NULL, NULL, NULL, 'dasdsad', NULL, 'programada', 1, '2025-11-12 21:45:26', '2026-01-19 06:54:22'),
	(3, 55, NULL, 'H-87920-002', 'acción correctiva', 'dsadsd', NULL, '2025-11-18', '2025-11-29', NULL, NULL, NULL, NULL, NULL, 'Juan Almeyda Requejo', NULL, 'programada', 1, '2025-11-18 15:49:02', '2026-01-19 06:54:22'),
	(4, 55, NULL, 'H-87920-003', 'acción correctiva', 'Test Action Plan', NULL, '2025-11-21', '2025-11-22', NULL, NULL, NULL, NULL, NULL, 'Test User', NULL, 'programada', 1, '2025-11-21 20:53:22', '2026-01-19 06:54:22'),
	(5, 55, NULL, 'H-87920-004', 'acción correctiva', 'dsadsad', NULL, '2025-11-21', '2025-11-29', NULL, NULL, NULL, NULL, NULL, 'dsadsad', NULL, 'programada', 1, '2025-11-21 21:24:30', '2026-01-19 06:54:22'),
	(6, 55, NULL, 'H-87920-005', 'acción correctiva', 'Acción de prueba final', NULL, '2025-11-21', '2025-11-22', '2025-12-10', NULL, NULL, 'dsadsadasd prueba nueva', '[{"path":"hallazgos\\/H-87920\\/acciones\\/H-87920-005\\/pGzjjl8pAMPuWxCsOBMG9O0EJ0m8Wzyybi7SYBgw.jpg","name":"tolerancia_03-11-2025.jpg"}]', 'Usuario de Prueba', NULL, 'programada', 1, '2025-11-21 21:38:22', '2026-01-19 06:54:22');

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

-- Volcando estructura para tabla kallpaq.accion_reprogramaciones
CREATE TABLE IF NOT EXISTS `accion_reprogramaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `accion_id` bigint(20) unsigned NOT NULL,
  `ar_fecha_anterior` date NOT NULL,
  `ar_fecha_nueva` date NOT NULL,
  `ar_justificacion` text NOT NULL,
  `ar_usuario_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accion_reprogramaciones_accion_id_foreign` (`accion_id`),
  KEY `accion_reprogramaciones_ar_usuario_id_foreign` (`ar_usuario_id`),
  CONSTRAINT `accion_reprogramaciones_accion_id_foreign` FOREIGN KEY (`accion_id`) REFERENCES `acciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accion_reprogramaciones_ar_usuario_id_foreign` FOREIGN KEY (`ar_usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.accion_reprogramaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.activos_criticos
CREATE TABLE IF NOT EXISTS `activos_criticos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('personal','tecnologia','informacion','infraestructura','proveedor','proceso','otro') NOT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `responsable_id` bigint(20) unsigned DEFAULT NULL,
  `nivel_criticidad` enum('bajo','medio','alto','critico') NOT NULL DEFAULT 'medio',
  `rto` int(11) DEFAULT NULL COMMENT 'Recovery Time Objective en horas',
  `rpo` int(11) DEFAULT NULL COMMENT 'Recovery Point Objective en horas',
  `mtpd` int(11) DEFAULT NULL COMMENT 'Maximum Tolerable Period of Disruption en horas',
  `dependencias` text DEFAULT NULL,
  `ubicacion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activos_criticos_codigo_unique` (`codigo`),
  KEY `activos_criticos_proceso_id_foreign` (`proceso_id`),
  KEY `activos_criticos_responsable_id_foreign` (`responsable_id`),
  CONSTRAINT `activos_criticos_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activos_criticos_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.activos_criticos: ~0 rows (aproximadamente)

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

-- Volcando estructura para tabla kallpaq.auditores
CREATE TABLE IF NOT EXISTS `auditores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auditores_user_id_unique` (`user_id`),
  CONSTRAINT `auditores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditores: ~7 rows (aproximadamente)
REPLACE INTO `auditores` (`id`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, '2026-01-16 19:10:00', '2026-01-16 22:43:41', NULL),
	(2, 2, '2026-01-16 19:10:24', '2026-01-16 22:43:39', NULL),
	(3, 3, '2026-01-16 19:16:21', '2026-01-16 19:16:21', NULL),
	(4, 4, '2026-01-16 19:59:09', '2026-01-16 19:59:09', NULL),
	(5, 5, '2026-01-16 19:59:19', '2026-01-16 19:59:19', NULL),
	(6, 6, '2026-01-16 19:59:35', '2026-01-16 19:59:35', NULL),
	(7, 8, '2026-01-16 20:11:02', '2026-01-16 20:11:02', NULL);

-- Volcando estructura para tabla kallpaq.auditoria_agenda
CREATE TABLE IF NOT EXISTS `auditoria_agenda` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ae_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `aea_fecha` date NOT NULL,
  `aea_hora_inicio` time NOT NULL,
  `aea_hora_fin` time NOT NULL,
  `aea_actividad` text NOT NULL,
  `aea_tipo` enum('apertura','cierre','gabinete','ejecucion') NOT NULL DEFAULT 'ejecucion',
  `estado` varchar(255) NOT NULL DEFAULT 'Programada',
  `aea_archivo` varchar(255) DEFAULT NULL,
  `aea_ouo` varchar(255) DEFAULT NULL,
  `aea_auditado` varchar(255) DEFAULT NULL,
  `aea_auditor` varchar(255) DEFAULT NULL,
  `auditor_id` bigint(20) unsigned DEFAULT NULL,
  `aea_requisito` varchar(255) DEFAULT NULL,
  `aea_lugar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_agenda_ae_id_foreign` (`ae_id`),
  KEY `auditoria_agenda_auditor_id_foreign` (`auditor_id`),
  KEY `auditoria_agenda_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `auditoria_agenda_ae_id_foreign` FOREIGN KEY (`ae_id`) REFERENCES `auditoria_especifica` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_agenda_auditor_id_foreign` FOREIGN KEY (`auditor_id`) REFERENCES `auditores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `auditoria_agenda_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_agenda: ~5 rows (aproximadamente)
REPLACE INTO `auditoria_agenda` (`id`, `ae_id`, `proceso_id`, `aea_fecha`, `aea_hora_inicio`, `aea_hora_fin`, `aea_actividad`, `aea_tipo`, `estado`, `aea_archivo`, `aea_ouo`, `aea_auditado`, `aea_auditor`, `auditor_id`, `aea_requisito`, `aea_lugar`, `created_at`, `updated_at`) VALUES
	(6, 1, NULL, '2026-01-16', '08:00:00', '09:00:00', 'Reunión de Apertura', 'apertura', 'Programada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-16 23:34:55', '2026-01-16 23:40:32'),
	(7, 1, 5, '2026-01-16', '09:30:00', '17:30:00', 'GESTIÓN ESTRATÉGICA', 'ejecucion', 'En Curso', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-16 23:34:55', '2026-01-18 02:33:59'),
	(8, 1, 8, '2026-01-19', '08:30:00', '17:30:00', 'GESTIÓN DE LA COMUNICACIÓN', 'ejecucion', 'Programada', NULL, NULL, NULL, NULL, 2, NULL, NULL, '2026-01-16 23:34:55', '2026-01-16 23:40:32'),
	(9, 1, 7, '2026-01-20', '08:30:00', '16:30:00', 'GESTIÓN DE LA INVERSIÓN', 'ejecucion', 'Programada', NULL, NULL, NULL, NULL, 7, NULL, NULL, '2026-01-16 23:34:55', '2026-01-16 23:40:32'),
	(10, 1, NULL, '2026-01-20', '17:00:00', '18:00:00', 'Reunión de Cierre', 'cierre', 'Programada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-16 23:34:55', '2026-01-16 23:40:32');

-- Volcando estructura para tabla kallpaq.auditoria_auditados
CREATE TABLE IF NOT EXISTS `auditoria_auditados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agenda_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_auditados_agenda_id_foreign` (`agenda_id`),
  CONSTRAINT `auditoria_auditados_agenda_id_foreign` FOREIGN KEY (`agenda_id`) REFERENCES `auditoria_agenda` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_auditados: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.auditoria_checklists
CREATE TABLE IF NOT EXISTS `auditoria_checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agenda_id` bigint(20) unsigned DEFAULT NULL,
  `norma_referencia` varchar(255) DEFAULT NULL,
  `requisito_referencia` varchar(255) DEFAULT NULL,
  `requisito_contenido` text DEFAULT NULL,
  `pregunta` text DEFAULT NULL,
  `criterio_auditoria` text DEFAULT NULL,
  `evidencia_esperada` text DEFAULT NULL,
  `estado_cumplimiento` enum('Sin Evaluar','Conforme','No Conforme','Oportunidad de Mejora','No Aplica') NOT NULL DEFAULT 'Sin Evaluar',
  `hallazgo_clasificacion` varchar(255) DEFAULT NULL,
  `evidencia_registrada` text DEFAULT NULL,
  `hallazgo_detectado` text DEFAULT NULL,
  `hallazgo_redaccion` text DEFAULT NULL,
  `hallazgo_resumen` text DEFAULT NULL,
  `criterio_redaccion` text DEFAULT NULL,
  `evidencia_redaccion` text DEFAULT NULL,
  `tipo_hallazgo` enum('NCM','NCMen','OM','OBS') DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `ai_generated` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_checklists_agenda_id_foreign` (`agenda_id`),
  CONSTRAINT `auditoria_checklists_agenda_id_foreign` FOREIGN KEY (`agenda_id`) REFERENCES `auditoria_agenda` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_checklists: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.auditoria_equipo
CREATE TABLE IF NOT EXISTS `auditoria_equipo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ae_id` bigint(20) unsigned NOT NULL,
  `auditor_id` bigint(20) unsigned NOT NULL,
  `aeq_rol` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `aeq_horas_planificadas` decimal(8,2) NOT NULL DEFAULT 0.00,
  `aeq_horas_ejecutadas` decimal(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `auditoria_equipo_auditoria_id_foreign` (`ae_id`),
  KEY `auditoria_equipo_personal_id_foreign` (`auditor_id`),
  CONSTRAINT `FK_auditoria_equipo_auditores` FOREIGN KEY (`auditor_id`) REFERENCES `auditores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_auditoria_equipo_auditoria_especifica` FOREIGN KEY (`ae_id`) REFERENCES `auditoria_especifica` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_equipo: ~3 rows (aproximadamente)
REPLACE INTO `auditoria_equipo` (`id`, `ae_id`, `auditor_id`, `aeq_rol`, `created_at`, `updated_at`, `aeq_horas_planificadas`, `aeq_horas_ejecutadas`) VALUES
	(45, 1, 1, 'Auditor Líder', '2026-01-16 23:06:57', '2026-01-16 23:40:32', 0.00, -10.00),
	(46, 1, 2, 'Auditor Interno', '2026-01-16 23:06:57', '2026-01-16 23:40:32', 0.00, -11.00),
	(48, 1, 7, 'Auditor Interno', '2026-01-16 23:35:13', '2026-01-16 23:40:32', 0.00, -10.00);

-- Volcando estructura para tabla kallpaq.auditoria_especifica
CREATE TABLE IF NOT EXISTS `auditoria_especifica` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_id` bigint(20) unsigned NOT NULL,
  `ae_codigo` varchar(255) DEFAULT NULL,
  `ae_objetivo` text DEFAULT NULL,
  `ae_criterios` text DEFAULT NULL,
  `ae_alcance` text DEFAULT NULL,
  `ae_tipo` varchar(255) DEFAULT NULL,
  `ae_sistema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ae_lugar` text DEFAULT NULL,
  `ae_direccion` text DEFAULT NULL,
  `ae_estado` varchar(255) NOT NULL DEFAULT 'Programada',
  `ae_fecha_inicio` date DEFAULT NULL,
  `ae_fecha_fin` date DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `ae_equipo_auditor` text DEFAULT NULL,
  `ae_auditado` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ae_cantidad_auditores` int(11) DEFAULT NULL,
  `ae_horas_hombre` decimal(8,2) DEFAULT NULL,
  `ae_ciclo` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `auditoria_especifica_pa_id_foreign` (`pa_id`),
  CONSTRAINT `auditoria_especifica_pa_id_foreign` FOREIGN KEY (`pa_id`) REFERENCES `programa_auditoria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_especifica: ~1 rows (aproximadamente)
REPLACE INTO `auditoria_especifica` (`id`, `pa_id`, `ae_codigo`, `ae_objetivo`, `ae_criterios`, `ae_alcance`, `ae_tipo`, `ae_sistema`, `ae_lugar`, `ae_direccion`, `ae_estado`, `ae_fecha_inicio`, `ae_fecha_fin`, `proceso_id`, `ae_equipo_auditor`, `ae_auditado`, `created_at`, `updated_at`, `ae_cantidad_auditores`, `ae_horas_hombre`, `ae_ciclo`) VALUES
	(1, 1, '2026 - 001 - IN', 'Objetivo', 'ISO 9001, ISO 37001', 'Alcance', 'Interna', '["sgc","sgcm"]', NULL, 'Calle Camilo Carrillo 114, Jesus María', 'Programada', '2026-01-16', '2026-12-31', NULL, NULL, NULL, '2026-01-16 19:07:32', '2026-01-16 23:40:33', NULL, 27.00, 1);

-- Volcando estructura para tabla kallpaq.auditoria_evaluacion
CREATE TABLE IF NOT EXISTS `auditoria_evaluacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ae_id` bigint(20) unsigned NOT NULL,
  `evaluador_id` bigint(20) unsigned NOT NULL,
  `evaluado_id` bigint(20) unsigned NOT NULL,
  `aev_rol_evaluado` varchar(255) NOT NULL,
  `aev_criterios` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`aev_criterios`)),
  `aev_promedio` decimal(5,2) DEFAULT NULL,
  `aev_comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_evaluacion_ae_id_foreign` (`ae_id`),
  KEY `auditoria_evaluacion_evaluador_id_foreign` (`evaluador_id`),
  KEY `auditoria_evaluacion_evaluado_id_foreign` (`evaluado_id`),
  CONSTRAINT `auditoria_evaluacion_ae_id_foreign` FOREIGN KEY (`ae_id`) REFERENCES `auditoria_especifica` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_evaluacion_evaluado_id_foreign` FOREIGN KEY (`evaluado_id`) REFERENCES `users` (`id`),
  CONSTRAINT `auditoria_evaluacion_evaluador_id_foreign` FOREIGN KEY (`evaluador_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_evaluacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.auditoria_informes
CREATE TABLE IF NOT EXISTS `auditoria_informes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ae_id` bigint(20) unsigned NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `estado` enum('Borrador','En Revisión','Aprobado','Emitido') NOT NULL DEFAULT 'Borrador',
  `resumen_ejecutivo` text DEFAULT NULL,
  `alcance_criterios` text DEFAULT NULL,
  `hallazgos_conformidad` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hallazgos_conformidad`)),
  `hallazgos_no_conformidad` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hallazgos_no_conformidad`)),
  `oportunidades_mejora` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`oportunidades_mejora`)),
  `procesos_auditados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`procesos_auditados`)),
  `auditados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`auditados`)),
  `conclusiones` text DEFAULT NULL,
  `recomendaciones` text DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `elaborado_por` bigint(20) unsigned DEFAULT NULL,
  `aprobado_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auditoria_informes_codigo_unique` (`codigo`),
  KEY `auditoria_informes_ae_id_foreign` (`ae_id`),
  KEY `auditoria_informes_elaborado_por_foreign` (`elaborado_por`),
  KEY `auditoria_informes_aprobado_por_foreign` (`aprobado_por`),
  CONSTRAINT `auditoria_informes_ae_id_foreign` FOREIGN KEY (`ae_id`) REFERENCES `auditoria_especifica` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_informes_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `auditoria_informes_elaborado_por_foreign` FOREIGN KEY (`elaborado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_informes: ~1 rows (aproximadamente)
REPLACE INTO `auditoria_informes` (`id`, `ae_id`, `codigo`, `estado`, `resumen_ejecutivo`, `alcance_criterios`, `hallazgos_conformidad`, `hallazgos_no_conformidad`, `oportunidades_mejora`, `procesos_auditados`, `auditados`, `conclusiones`, `recomendaciones`, `fecha_emision`, `elaborado_por`, `aprobado_por`, `created_at`, `updated_at`) VALUES
	(1, 1, '2026 - 001 - IN-INF-20260118', 'Borrador', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-01-19 00:50:20', '2026-01-19 00:50:20');

-- Volcando estructura para tabla kallpaq.auditoria_proceso
CREATE TABLE IF NOT EXISTS `auditoria_proceso` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ae_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_proceso_ae_id_foreign` (`ae_id`),
  KEY `auditoria_proceso_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `auditoria_proceso_ae_id_foreign` FOREIGN KEY (`ae_id`) REFERENCES `auditoria_especifica` (`id`) ON DELETE CASCADE,
  CONSTRAINT `auditoria_proceso_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.auditoria_proceso: ~3 rows (aproximadamente)
REPLACE INTO `auditoria_proceso` (`id`, `ae_id`, `proceso_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 5, '2026-01-16 19:08:18', '2026-01-16 19:08:18'),
	(2, 1, 8, '2026-01-16 19:08:18', '2026-01-16 19:08:18'),
	(4, 1, 7, '2026-01-16 19:08:51', '2026-01-16 19:08:51');

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

-- Volcando estructura para tabla kallpaq.denied_permissions
CREATE TABLE IF NOT EXISTS `denied_permissions` (
  `user_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `denied_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `denied_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `denied_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.denied_permissions: ~0 rows (aproximadamente)

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
  KEY `diagrama_proceso` (`proceso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.diagrama_contexto: ~3 rows (aproximadamente)
REPLACE INTO `diagrama_contexto` (`id`, `archivo`, `proceso_id`, `version`, `vigencia`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
	(1, 'diagramas/RlCWzSZx9Bq0seahOq10GZBlqnB3RvGZ8hSh71Yt.png', 71, 'Version 2', '2025-05-12', 'activo', '2025-05-08 23:11:34', '2025-05-08 23:19:02', '2025-05-08 23:19:02'),
	(2, 'diagramas/DX9yVqBDAvnwtZcjBzFx3sh1L1ZZCyiChpnpT2eY.png', 62, 'Version 1', '2025-05-09', 'activo', '2025-05-09 16:58:40', '2025-05-09 16:55:04', '2025-05-09 16:55:04'),
	(5, 'diagramas/E5nAfpWIKdfEG7DgHiW7NSD1b0Qw2WSV8CYeKqDA.jpg', 30, '04', '2025-01-31', 'activo', '2025-05-28 16:50:53', '2025-05-28 16:03:32', '2025-05-28 16:03:32');

-- Volcando estructura para tabla kallpaq.documentos
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cod_documento` varchar(255) DEFAULT NULL,
  `tipo_documento_id` bigint(20) unsigned DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_documento` varchar(255) DEFAULT NULL,
  `fuente_documento` varchar(255) DEFAULT NULL,
  `estado_documento` enum('vigente','derogado') DEFAULT NULL,
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
  `frecuencia_revision_documento` varchar(255) DEFAULT NULL,
  `instrumento_aprueba_documento` varchar(255) DEFAULT NULL,
  `instrumento_deroga_documento` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documentos: ~8 rows (aproximadamente)
REPLACE INTO `documentos` (`id`, `cod_documento`, `tipo_documento_id`, `proceso_id`, `nombre_documento`, `fuente_documento`, `estado_documento`, `fecha_vigencia_documento`, `area_compliance_id`, `subarea_compliance_id`, `documento_padre_id`, `resumen_documento`, `palabras_clave_documento`, `observaciones_documento`, `archivo_path_documento`, `usa_versiones_documento`, `fecha_aprobacion_documento`, `fecha_derogacion_documento`, `frecuencia_revision_documento`, `instrumento_aprueba_documento`, `instrumento_deroga_documento`, `enlace_valido`, `user_created`, `user_pubilshed`, `user_deleted`, `user_updated`, `inactivate_at`, `created_at`, `updated_at`, `pubished_at`, `deleted_at`) VALUES
	(1, 'PR-GSCS-07', 4, NULL, 'Procedimiento "Visita de Control"', 'interno', 'vigente', '2024-12-02 05:00:00', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 22:56:50', '2025-05-19 15:43:18', NULL, NULL),
	(2, 'Directiva n.° 007-2022-CG/DOC', 11, NULL, 'Directiva Notificaciones electrónicas en el Sistema Nacional de Control', 'interno', 'vigente', '2022-05-09 05:00:00', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:17:20', '2025-05-16 21:46:45', NULL, NULL),
	(3, 'Directiva n.° 013-2022-CG/NORM', 11, NULL, 'Servicio de Control Simultáneo', 'interno', 'vigente', '2025-05-09 05:00:00', 3, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:19:59', '2025-05-16 19:27:46', NULL, NULL),
	(4, 'RC n.°  245-2023-CG', 10, NULL, 'Normas Generales de Control Gubernamental', 'externo', 'vigente', '2023-06-27 05:00:00', 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-09 23:25:23', NULL, NULL, NULL),
	(5, 'PR-GSCS-06', 4, NULL, 'Procedimiento "Control Concurrente"', 'interno', 'vigente', '2024-12-03 05:00:00', 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-13 01:06:25', NULL, NULL, NULL),
	(6, 'Directiva Nº 002-2025-CG/GMPL', 11, NULL, 'Directiva Interna que establece Disposiciones Complementarias a la Ley N° 31358, Ley que establece medidas para la expansión del Control Concurrente', 'interno', 'vigente', '2025-04-24 05:00:00', 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-13 01:06:26', '2025-05-19 14:39:28', NULL, NULL),
	(7, 'PR-GSCS-08', 4, NULL, 'Procedimiento Operativo Control Simultaneo', 'interno', 'vigente', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-20 21:44:11', '2025-05-20 21:44:11', NULL, NULL),
	(8, 'PR-PEI-01', 4, NULL, 'Procedimiento “Elaboración, seguimiento y evaluación del Plan Estratégico Institucional”', 'interno', 'vigente', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-28 21:10:26', '2025-05-28 21:10:26', NULL, NULL);

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

-- Volcando estructura para tabla kallpaq.documento_anexos
CREATE TABLE IF NOT EXISTS `documento_anexos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `da_codigo` varchar(50) NOT NULL,
  `da_nombre` varchar(255) NOT NULL,
  `da_tipo` enum('FORMATO','MATRIZ','INFOGRAFIA','LISTADO','OTROS') NOT NULL DEFAULT 'FORMATO',
  `da_archivo_ruta` varchar(500) NOT NULL,
  `da_version` int(11) NOT NULL DEFAULT 1,
  `da_estado` enum('VIGENTE','OBSOLETO') NOT NULL DEFAULT 'VIGENTE',
  `da_observacion` text DEFAULT NULL,
  `da_fecha_publicacion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_anexos_documento_id_foreign` (`documento_id`),
  CONSTRAINT `documento_anexos_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_anexos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.documento_movimientos
CREATE TABLE IF NOT EXISTS `documento_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `accion` varchar(255) NOT NULL,
  `observacion` text DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `documento_movimientos_documento_id_foreign` (`documento_id`),
  KEY `documento_movimientos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `documento_movimientos_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_movimientos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `instrumento_aprueba` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_versiones_documento_id_foreign` (`documento_id`),
  CONSTRAINT `documento_versiones_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.documento_versions: ~5 rows (aproximadamente)
REPLACE INTO `documento_versions` (`id`, `documento_id`, `version`, `control_cambios`, `archivo_path`, `enlace_valido`, `fecha_aprobacion`, `fecha_publicacion`, `created_at`, `updated_at`, `instrumento_aprueba`, `deleted_at`) VALUES
	(25, 2, 1, 'Version del Peruano', 'https://cdn.www.gob.pe/uploads/document/file/2907982/Resoluci%C3%B3n%20de%20Contralor%C3%ADa%20N%C2%B0102-2022-CG.pdf.pdf?v=1647275017', 1, '2022-03-11', '2025-05-28', '2025-05-20 19:23:36', '2025-05-28 23:25:12', NULL, NULL),
	(26, 3, 1, 'publicada en la web contraloria', 'https://cdn.www.gob.pe/uploads/document/file/3839885/3656507-directiva-n-013-2022-cg-norm-directiva-de-servicio-de-control-simultaneo.pdf?v=1708034518', 1, '2023-12-21', '2025-05-20', '2025-05-20 20:06:31', '2025-05-28 23:25:13', NULL, NULL),
	(27, 8, 4, 'Version 05', 'PE01.01/Procedimiento/PR-PEI-01-v01.pdf', 1, '2025-01-30', '2025-05-28', '2025-05-28 21:11:56', '2025-05-28 21:20:23', NULL, NULL),
	(31, 4, 1, 'version inicial', 'https://cdn.www.gob.pe/uploads/document/file/2907982/Resoluci%C3%B3n%20de%20Contralor%C3%ADa%20N%C2%B0102-2022-CG.pdf.pdf?v=1647275017', 1, '2025-05-28', '2025-05-28', '2025-05-28 23:26:38', '2025-05-28 23:27:36', NULL, NULL),
	(33, 1, 1, 'version 01', 'PM03.02.01/Procedimiento/PR-GSCS-07-v01.pdf', 1, '2025-05-13', '2025-05-28', '2025-05-28 23:53:48', '2025-05-28 23:53:48', NULL, NULL);

-- Volcando estructura para tabla kallpaq.encuestas_satisfaccion
CREATE TABLE IF NOT EXISTS `encuestas_satisfaccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `es_periodo` varchar(255) NOT NULL,
  `es_numero_periodo` int(11) NOT NULL,
  `es_anio` int(11) NOT NULL,
  `es_nps_score` decimal(5,2) DEFAULT NULL,
  `es_score` double(8,2) NOT NULL DEFAULT 0.00,
  `es_cantidad` int(11) NOT NULL DEFAULT 0,
  `es_informe_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `encuestas_satisfaccion_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `encuestas_satisfaccion_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.encuestas_satisfaccion: ~1 rows (aproximadamente)
REPLACE INTO `encuestas_satisfaccion` (`id`, `proceso_id`, `es_periodo`, `es_numero_periodo`, `es_anio`, `es_nps_score`, `es_score`, `es_cantidad`, `es_informe_path`, `created_at`, `updated_at`) VALUES
	(3, 151, 'Trimestral', 4, 2025, 89.00, 4.20, 120, '{"path":"satisfaccion\\/encuestas\\/151\\/Ilx3vXOPTjFYEl3ZmxrMR6Ctaw2E5RYZqr2oDMnZ.pdf","name":"Informe_Tecnico_Encuesta IP 2025 -IIIT[F].pdf"}', '2026-01-19 16:53:04', '2026-01-19 16:53:04');

-- Volcando estructura para tabla kallpaq.encuesta_satisfaccion_detalles
CREATE TABLE IF NOT EXISTS `encuesta_satisfaccion_detalles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `encuesta_id` bigint(20) unsigned NOT NULL,
  `esd_categoria` varchar(255) NOT NULL,
  `esd_puntaje` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `encuesta_satisfaccion_detalles_encuesta_id_foreign` (`encuesta_id`),
  CONSTRAINT `encuesta_satisfaccion_detalles_encuesta_id_foreign` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas_satisfaccion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.encuesta_satisfaccion_detalles: ~5 rows (aproximadamente)
REPLACE INTO `encuesta_satisfaccion_detalles` (`id`, `encuesta_id`, `esd_categoria`, `esd_puntaje`, `created_at`, `updated_at`) VALUES
	(151, 3, 'Oportunidad', 4.00, '2026-01-19 16:53:04', '2026-01-19 16:53:04'),
	(152, 3, 'Trato', 4.00, '2026-01-19 16:53:04', '2026-01-19 16:53:04'),
	(153, 3, 'Claridad', 4.00, '2026-01-19 16:53:04', '2026-01-19 16:53:04'),
	(154, 3, 'Seguridad', 4.00, '2026-01-19 16:53:04', '2026-01-19 16:53:04'),
	(155, 3, 'Calidad', 5.00, '2026-01-19 16:53:04', '2026-01-19 16:53:04');

-- Volcando estructura para tabla kallpaq.escenarios_continuidad
CREATE TABLE IF NOT EXISTS `escenarios_continuidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` enum('desastre_natural','falla_tecnologica','ciberataque','pandemia','incidente_seguridad','falla_proveedor','falla_infraestructura','otro') NOT NULL,
  `probabilidad` enum('muy_baja','baja','media','alta','muy_alta') NOT NULL DEFAULT 'media',
  `impacto` enum('insignificante','menor','moderado','mayor','catastrofico') NOT NULL DEFAULT 'moderado',
  `nivel_riesgo` int(11) DEFAULT NULL,
  `activos_afectados` text DEFAULT NULL,
  `procesos_afectados` text DEFAULT NULL,
  `controles_existentes` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `escenarios_continuidad_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.escenarios_continuidad: ~0 rows (aproximadamente)

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.especialistas: ~13 rows (aproximadamente)
REPLACE INTO `especialistas` (`id`, `user_id`, `cargo`, `estado`, `created_at`, `updated_at`, `inactived_at`) VALUES
	(1, 1, 'Especialista SIG', 1, '2024-06-05 19:38:32', '2026-01-19 00:47:25', '2026-01-19 00:47:25'),
	(2, 2, 'Especialista TUPA', 1, '2024-06-05 19:38:32', NULL, NULL),
	(3, 3, 'Especialista Riesgos', 1, '2024-06-05 19:38:32', NULL, NULL),
	(4, 4, 'Especialista SIG', 0, '2024-06-05 19:38:32', '2026-01-19 00:47:25', '2026-01-19 00:47:25'),
	(5, 5, 'Especialista SIG', 1, NULL, NULL, NULL),
	(6, 6, 'Especialista SIG', 0, NULL, '2026-01-19 00:47:25', '2026-01-19 00:47:25'),
	(7, 7, 'Especialista SIG', 0, NULL, '2026-01-19 00:47:25', '2026-01-19 00:47:25'),
	(8, 8, 'Especialista SIG', 1, NULL, NULL, NULL),
	(9, 9, 'Especialista', 1, '2026-01-19 00:47:25', '2026-01-19 00:47:25', NULL),
	(10, 10, 'Especialista', 1, '2026-01-19 00:47:25', '2026-01-19 00:47:25', NULL),
	(11, 11, 'Especialista', 1, '2026-01-19 00:47:25', '2026-01-19 00:47:25', NULL),
	(12, 12, 'Especialista', 1, '2026-01-19 00:47:25', '2026-01-19 00:47:25', NULL),
	(13, 13, 'Especialista', 1, '2026-01-19 00:47:25', '2026-01-19 00:47:25', NULL);

-- Volcando estructura para tabla kallpaq.estrategias_continuidad
CREATE TABLE IF NOT EXISTS `estrategias_continuidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` bigint(20) unsigned NOT NULL,
  `activo_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_estrategia` enum('respaldo','redundancia','alternativa','manual','outsourcing','otro') NOT NULL,
  `recursos_requeridos` text DEFAULT NULL,
  `costo_estimado` decimal(12,2) DEFAULT NULL,
  `tiempo_implementacion` int(11) DEFAULT NULL COMMENT 'En horas',
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estrategias_continuidad_plan_id_foreign` (`plan_id`),
  KEY `estrategias_continuidad_activo_id_foreign` (`activo_id`),
  CONSTRAINT `estrategias_continuidad_activo_id_foreign` FOREIGN KEY (`activo_id`) REFERENCES `activos_criticos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estrategias_continuidad_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes_continuidad` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.estrategias_continuidad: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.expectativas
CREATE TABLE IF NOT EXISTS `expectativas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_interesada_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `exp_descripcion` text NOT NULL,
  `exp_tipo` enum('necesidad','expectativa') NOT NULL DEFAULT 'expectativa',
  `exp_normas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exp_normas`)),
  `exp_criticidad` int(11) NOT NULL DEFAULT 1,
  `exp_viabilidad` int(11) NOT NULL DEFAULT 1,
  `exp_prioridad` double NOT NULL DEFAULT 0,
  `exp_estado` enum('pendiente','en_proceso','implementado') NOT NULL DEFAULT 'pendiente',
  `exp_observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expectativas_parte_interesada_id_foreign` (`parte_interesada_id`),
  KEY `expectativas_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `expectativas_parte_interesada_id_foreign` FOREIGN KEY (`parte_interesada_id`) REFERENCES `partes_interesadas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expectativas_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.expectativas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.expectativa_compromisos
CREATE TABLE IF NOT EXISTS `expectativa_compromisos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expectativa_id` bigint(20) unsigned NOT NULL,
  `ec_descripcion` text NOT NULL,
  `ec_responsable_id` bigint(20) unsigned DEFAULT NULL,
  `ec_fecha_limite` date DEFAULT NULL,
  `ec_estado` enum('pendiente','en_proceso','completado') NOT NULL DEFAULT 'pendiente',
  `ec_avance` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expectativa_compromisos_ec_responsable_id_foreign` (`ec_responsable_id`),
  KEY `expectativa_compromisos_expectativa_id_foreign` (`expectativa_id`),
  CONSTRAINT `expectativa_compromisos_ec_responsable_id_foreign` FOREIGN KEY (`ec_responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expectativa_compromisos_expectativa_id_foreign` FOREIGN KEY (`expectativa_id`) REFERENCES `expectativas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.expectativa_compromisos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.facilitadores
CREATE TABLE IF NOT EXISTS `facilitadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `cargo` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facilitadores_user_id_foreign` (`user_id`),
  CONSTRAINT `facilitadores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.facilitadores: ~1 rows (aproximadamente)
REPLACE INTO `facilitadores` (`id`, `user_id`, `cargo`, `estado`, `created_at`, `updated_at`) VALUES
	(1, 1, 'facilitador', 'activo', '2025-11-12 23:15:27', '2025-11-12 23:15:27');

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
  `ruta_plan_accion` varchar(255) DEFAULT NULL,
  `hallazgo_fecha_identificacion` date DEFAULT NULL,
  `hallazgo_fecha_aprobacion` date DEFAULT NULL,
  `hallazgo_fecha_desestimacion` date DEFAULT NULL,
  `hallazgo_fecha_conclusion` date DEFAULT NULL,
  `hallazgo_fecha_evaluacion` date DEFAULT NULL,
  `hallazgo_fecha_cierre` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgos: ~15 rows (aproximadamente)
REPLACE INTO `hallazgos` (`id`, `hallazgo_cod`, `informe_id`, `especialista_id`, `auditor_id`, `facilitador_id`, `emisor`, `hallazgo_resumen`, `hallazgo_sig`, `hallazgo_descripcion`, `hallazgo_criterio`, `hallazgo_evidencia`, `hallazgo_clasificacion`, `hallazgo_origen`, `hallazgo_origen_ot`, `hallazgo_avance`, `hallazgo_tipo_cierre`, `hallazgo_estado`, `hallazgo_ciclo`, `ruta_plan_accion`, `hallazgo_fecha_identificacion`, `hallazgo_fecha_aprobacion`, `hallazgo_fecha_desestimacion`, `hallazgo_fecha_conclusion`, `hallazgo_fecha_evaluacion`, `hallazgo_fecha_cierre`, `created_at`, `updated_at`) VALUES
	(55, 'H-87920', 922, 1, 324, 297, NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Registros de logs del sistema de gestión documental, periodo 01/09/2025 - 31/10/2025. Accesos no autorizados detectados.', 'NCM', 'GR', 'vitae', 0, NULL, 'creado', 2, 'hallazgos/planes/1768793673_Plan_Accion_H-87920__2_.pdf', '2025-11-18', NULL, NULL, NULL, NULL, '2025-12-25', '2025-11-11 22:59:52', '2026-01-19 06:54:22'),
	(56, 'H-73160', NULL, 17, NULL, 309, 'Flatley, Donnelly and Haley', 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgo...', NULL, 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgos de soborno, lo que podría exponer a la organización a proveedores con prácticas no éticas.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'Ncme', 'GR', 'aspernatur', 65, 'con eficacia', 'creado', 3, NULL, '2024-12-11', '2025-06-07', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(57, 'H-26625', 985, 186, 24, 331, 'Schuster LLC', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'Odm', 'GI', 'repellendus', 41, 'con eficacia', NULL, 2, NULL, NULL, NULL, NULL, '2024-12-30', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(58, 'H-94131', NULL, 377, NULL, 225, 'Fadel Inc', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGAS","SGCM"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Actas de reunión de revisión por la dirección. No se encontró acta correspondiente al segundo semestre de 2025.', 'NCM', 'SN', 'aliquam', NULL, 'con eficacia', NULL, 1, NULL, '2025-03-06', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(59, 'H-17660', 757, NULL, 277, NULL, NULL, 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGCO","SGCM"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'SN', NULL, 84, NULL, 'desestimado', 2, NULL, '2025-07-31', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(60, 'H-88833', 901, 257, NULL, NULL, 'Kunze-Kassulke', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certifi...', '["SGSI"]', 'Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.', 'ISO 9001:2015, Cláusula 7.1.5 Recursos de seguimiento y medición', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'NCM', 'OT', 'quo', NULL, NULL, NULL, 3, NULL, NULL, '2025-10-26', NULL, '2025-09-28', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(61, 'H-26775', 851, 375, 362, NULL, 'Monahan-Hoeger', 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgo...', NULL, 'El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgos de soborno, lo que podría exponer a la organización a proveedores con prácticas no éticas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'IN', NULL, NULL, 'sin eficacia', NULL, 1, NULL, '2025-03-19', '2025-05-11', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(62, 'H-86599', NULL, NULL, 348, 36, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'Ncme', 'GI', 'explicabo', NULL, NULL, '', 3, NULL, NULL, '2025-10-21', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(63, 'H-7932', NULL, NULL, NULL, NULL, NULL, 'Se detectaron inconsistencias en los registros de control de acceso a información confidencial (INF...', NULL, 'Se detectaron inconsistencias en los registros de control de acceso a información confidencial (INF-SEG-002), lo que podría comprometer la seguridad de los datos de clientes y proyectos.', 'ISO 9001:2015, Cláusula 7.1.5 Recursos de seguimiento y medición', 'Registros de logs del sistema de gestión documental, periodo 01/09/2025 - 31/10/2025. Accesos no autorizados detectados.', 'Obs', 'EX', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-10-13', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(64, 'H-72731', NULL, NULL, 6, NULL, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', '["SGCO","SGSI"]', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 37001:2016, Cláusula 8.2 Debida diligencia', 'Procedimiento PRO-COM-003 \'Selección y Evaluación de Proveedores\'. No se menciona evaluación de riesgos de soborno.', 'Obs', 'SC', NULL, NULL, NULL, '', 3, NULL, NULL, '2025-11-09', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(65, 'H-45464', 733, NULL, NULL, 53, 'Cormier, Sporer and Balistreri', 'La capacitación sobre la política antisoborno (POL-ASB-001) no ha sido completada por el 30% del p...', NULL, 'La capacitación sobre la política antisoborno (POL-ASB-001) no ha sido completada por el 30% del personal de ventas, lo que representa un riesgo de incumplimiento de la normativa interna y externa.', 'ISO 9001:2015, Cláusula 7.5 Información documentada', 'Informe de auditoría interna N° 2025-003, sección 4.2. Registros de acciones correctivas incompletos.', 'Ncme', 'RD', NULL, 63, NULL, '', 3, NULL, NULL, NULL, NULL, '2025-07-23', NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(66, 'H-67184', NULL, 40, NULL, NULL, NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 9001:2015, Cláusula 9.3 Revisión por la dirección', 'Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.', 'Ncme', 'GI', 'corrupti', NULL, 'con eficacia', '', 3, NULL, '2025-02-08', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(67, 'H-65130', NULL, NULL, NULL, 193, 'Wehner-Leffler', 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 9001:2015, Cláusula 7.5 Información documentada', 'Informe de auditoría interna N° 2025-003, sección 4.2. Registros de acciones correctivas incompletos.', 'NCM', 'RD', 'nulla', NULL, 'sin eficacia', 'creado', 1, NULL, '2025-06-29', NULL, NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(68, 'H-89799', NULL, NULL, NULL, 232, NULL, 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera...', '["SGSI","SGC"]', 'Se identificó que el procedimiento \'PR-CAL-001 Gestión de No Conformidades\' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.', 'ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva', 'Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.', 'NCM', 'GR', 'corporis', NULL, NULL, NULL, 3, NULL, NULL, '2025-05-16', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52'),
	(69, 'H-88979', 587, NULL, 385, 413, 'Larson, Auer and Kreiger', 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en...', NULL, 'No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.', 'ISO 37001:2016, Cláusula 8.1 Planificación y control operacional', 'Actas de reunión de revisión por la dirección. No se encontró acta correspondiente al segundo semestre de 2025.', 'Ncme', 'SC', 'consequatur', 82, 'con eficacia', NULL, 3, NULL, NULL, '2025-11-11', NULL, NULL, NULL, NULL, '2025-11-11 22:59:52', '2025-11-11 22:59:52');

-- Volcando estructura para tabla kallpaq.hallazgo_causas
CREATE TABLE IF NOT EXISTS `hallazgo_causas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `hc_metodo` enum('ishikawa','cinco_porques') DEFAULT NULL,
  `hc_por_que1` text DEFAULT NULL,
  `hc_por_que2` text DEFAULT NULL,
  `hc_por_que3` text DEFAULT NULL,
  `hc_por_que4` text DEFAULT NULL,
  `hc_por_que5` text DEFAULT NULL,
  `hc_mano_obra` text DEFAULT NULL,
  `hc_metodologias` text DEFAULT NULL,
  `hc_materiales` text DEFAULT NULL,
  `hc_maquinas` text DEFAULT NULL,
  `hc_medicion` text DEFAULT NULL,
  `hc_medio_ambiente` text DEFAULT NULL,
  `hc_resultado` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgos_causas_hallazgo_id_foreign` (`hallazgo_id`),
  CONSTRAINT `hallazgos_causas_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_causas: ~15 rows (aproximadamente)
REPLACE INTO `hallazgo_causas` (`id`, `hallazgo_id`, `hc_metodo`, `hc_por_que1`, `hc_por_que2`, `hc_por_que3`, `hc_por_que4`, `hc_por_que5`, `hc_mano_obra`, `hc_metodologias`, `hc_materiales`, `hc_maquinas`, `hc_medicion`, `hc_medio_ambiente`, `hc_resultado`, `created_at`, `updated_at`) VALUES
	(1, 55, 'cinco_porques', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'La causa raíz identificada es la combinación de personal sin experiencia y procesos no documentados', '2025-11-11 22:59:52', '2025-12-04 21:47:25'),
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
  `he_fecha` date DEFAULT NULL,
  `he_responsable_id` bigint(20) unsigned NOT NULL,
  `he_evidencias` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `he_comentario` varchar(300) DEFAULT NULL,
  `he_resultado` enum('con eficacia','sin eficacia') DEFAULT NULL,
  `he_ciclo` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgo_evaluacions_hallazgo_id_foreign` (`hallazgo_id`),
  KEY `hallazgo_evaluacions_evaluador_id_foreign` (`he_responsable_id`) USING BTREE,
  CONSTRAINT `hallazgo_evaluacions_evaluador_id_foreign` FOREIGN KEY (`he_responsable_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hallazgo_evaluacions_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_evaluaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.hallazgo_movimientos
CREATE TABLE IF NOT EXISTS `hallazgo_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hallazgo_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `hm_estado` varchar(255) NOT NULL,
  `hm_comentario` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hallazgo_movimientos_hallazgo_id_foreign` (`hallazgo_id`),
  KEY `hallazgo_movimientos_user_id_foreign` (`user_id`),
  CONSTRAINT `hallazgo_movimientos_hallazgo_id_foreign` FOREIGN KEY (`hallazgo_id`) REFERENCES `hallazgos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hallazgo_movimientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_movimientos: ~5 rows (aproximadamente)
REPLACE INTO `hallazgo_movimientos` (`id`, `hallazgo_id`, `user_id`, `hm_estado`, `hm_comentario`, `created_at`, `updated_at`) VALUES
	(1, 55, 1, 'asignado', 'Hallazgo asignado a Juan Almeyda Requejo por Juan Almeyda Requejo.', '2025-11-12 21:43:20', '2025-11-12 21:43:20'),
	(2, 55, 1, 'asignado', 'Hallazgo asignado a Juan Manuel Almeyda Requejo por Juan Manuel Almeyda Requejo.', '2026-01-19 03:58:46', '2026-01-19 03:58:46'),
	(3, 55, 1, 'evaluado', 'Evaluación de eficacia sin eficacia. Se requiere plan de acción actualizado. Ciclo: 2', '2026-01-19 04:13:49', '2026-01-19 04:13:49'),
	(4, 55, 1, 'aprobado', 'Estado cambiado a: aprobado', '2026-01-19 04:14:46', '2026-01-19 04:14:46'),
	(5, 55, 1, 'aprobado', 'Estado cambiado a: aprobado', '2026-01-19 06:54:22', '2026-01-19 06:54:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.hallazgo_proceso: ~2 rows (aproximadamente)
REPLACE INTO `hallazgo_proceso` (`id`, `hallazgo_id`, `proceso_id`, `created_at`, `updated_at`) VALUES
	(93, 56, 45, NULL, NULL),
	(94, 55, 151, NULL, NULL);

-- Volcando estructura para tabla kallpaq.incidentes_continuidad
CREATE TABLE IF NOT EXISTS `incidentes_continuidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `escenario_id` bigint(20) unsigned DEFAULT NULL,
  `plan_activado_id` bigint(20) unsigned DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `severidad` enum('baja','media','alta','critica') NOT NULL,
  `impacto_real` text DEFAULT NULL,
  `acciones_tomadas` text DEFAULT NULL,
  `lecciones_aprendidas` text DEFAULT NULL,
  `tiempo_respuesta_minutos` int(11) DEFAULT NULL,
  `tiempo_recuperacion_minutos` int(11) DEFAULT NULL,
  `plan_fue_efectivo` tinyint(1) DEFAULT NULL,
  `responsable_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incidentes_continuidad_codigo_unique` (`codigo`),
  KEY `incidentes_continuidad_escenario_id_foreign` (`escenario_id`),
  KEY `incidentes_continuidad_plan_activado_id_foreign` (`plan_activado_id`),
  KEY `incidentes_continuidad_responsable_id_foreign` (`responsable_id`),
  CONSTRAINT `incidentes_continuidad_escenario_id_foreign` FOREIGN KEY (`escenario_id`) REFERENCES `escenarios_continuidad` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incidentes_continuidad_plan_activado_id_foreign` FOREIGN KEY (`plan_activado_id`) REFERENCES `planes_continuidad` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incidentes_continuidad_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.incidentes_continuidad: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.indicadores
CREATE TABLE IF NOT EXISTS `indicadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `planificacion_pei_id` bigint(255) DEFAULT NULL,
  `planificacion_sig_id` bigint(255) DEFAULT NULL,
  `indicador_nombre` text NOT NULL,
  `indicador_descripcion` text NOT NULL,
  `indicador_fuente` text DEFAULT NULL,
  `indicador_tipo_indicador` enum('producto','servicio','resultado','calidad') NOT NULL,
  `indicador_sig` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`indicador_sig`)),
  `indicador_estado` tinyint(1) NOT NULL DEFAULT 1,
  `indicador_formula` varchar(255) NOT NULL,
  `indicador_frecuencia` enum('mensual','trimestral','semestral','anual') NOT NULL,
  `indicador_meta` double(8,2) NOT NULL,
  `indicador_tipo_agregacion` enum('acumulada','no acumulada') NOT NULL,
  `indicador_parametro_medida` enum('ratio','porcentaje','numero','indice','tasa','promedio') NOT NULL,
  `indicador_sentido` enum('ascendente','lineal','descendente') NOT NULL,
  `indicador_var1` varchar(255) DEFAULT NULL,
  `indicador_var2` varchar(255) DEFAULT NULL,
  `indicador_var3` varchar(255) DEFAULT NULL,
  `indicador_var4` varchar(255) DEFAULT NULL,
  `indicador_var5` varchar(255) DEFAULT NULL,
  `indicador_var6` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicadores_proceso_cod_foreign` (`proceso_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores: ~15 rows (aproximadamente)
REPLACE INTO `indicadores` (`id`, `proceso_id`, `planificacion_pei_id`, `planificacion_sig_id`, `indicador_nombre`, `indicador_descripcion`, `indicador_fuente`, `indicador_tipo_indicador`, `indicador_sig`, `indicador_estado`, `indicador_formula`, `indicador_frecuencia`, `indicador_meta`, `indicador_tipo_agregacion`, `indicador_parametro_medida`, `indicador_sentido`, `indicador_var1`, `indicador_var2`, `indicador_var3`, `indicador_var4`, `indicador_var5`, `indicador_var6`, `created_at`, `updated_at`) VALUES
	(1, 126, 2, 21, 'Indice de atención de las solicitudes de Prestaciones Adicionales de Obra (PAO) dentro del plazo legal', 'Indice de atención de las solicitudes de Prestaciones Adicionales de Obra (PAO) dentro del plazo legal', NULL, 'servicio', '["SGC"]', 1, 'v1/15*v2', 'mensual', 100.00, 'no acumulada', 'ratio', 'lineal', 'Suma Tn', 'cantidad de expedientes', NULL, NULL, NULL, NULL, '2025-12-19 01:01:43', '2025-12-19 01:03:19'),
	(2, 127, NULL, NULL, 'Índice de atención de recursos de apelación de Prestaciones Adicionales de Obra  (PAO) dentro del plazo legal  (30 días hábiles)', 'Índice de atención de recursos de apelación de Prestaciones Adicionales de Obra  (PAO) dentro del plazo legal  (30 días hábiles)', NULL, 'servicio', '["SGC"]', 1, 'v1/30*v2', 'mensual', 100.00, 'no acumulada', 'ratio', 'lineal', 'N° de recursos de apelación de PAO atendidos', 'Suma tiempo (días hábiles) de atención de recursos de apelación de PAO)', NULL, NULL, NULL, NULL, '2025-12-19 01:06:24', '2025-12-19 01:06:24'),
	(3, 130, 2, 21, 'Porcentaje de Informes Previos emitidos dentro del plazo establecido', 'Porcentaje de Informes Previos emitidos dentro del plazo establecido', NULL, 'servicio', '["SGC"]', 1, 'v1/v2*100', 'mensual', 100.00, 'no acumulada', 'ratio', 'lineal', 'Cantidad de Informes previos emitidos dentro del plazo establecido', 'Cantidad de informes previos emitidos', NULL, NULL, NULL, NULL, '2025-12-19 01:11:47', '2025-12-19 01:11:47'),
	(4, 63, 4, 24, 'Porcentaje de instrumentos de cooperación internacional suscritos', 'Porcentaje de instrumentos de cooperación internacional suscritos', NULL, 'servicio', '["SGC"]', 1, 'v1/v2*100', 'trimestral', 80.00, 'no acumulada', 'ratio', 'lineal', 'Número de instrumentos de cooperación internacional suscritos', 'Numero de instrumentos de cooperación internacional aprobados por Alta Dirección para suscribir', NULL, NULL, NULL, NULL, '2025-12-19 01:15:55', '2025-12-19 01:15:55'),
	(5, 63, 4, 24, 'Porcentaje de instrumentos de cooperación internacional suscritos con EFS y otras entidades internacionales, donde se ha efectuado al menos una acción comprendida en su alcance', 'Porcentaje de instrumentos de cooperación internacional suscritos con EFS y otras entidades internacionales, donde se ha efectuado al menos una acción comprendida en su alcance', NULL, 'servicio', '["SGC"]', 1, 'V1/V2*100', 'trimestral', 60.00, 'no acumulada', 'porcentaje', 'lineal', 'Número de instrumentos de cooperación internacional suscritos con EFS y otras entidades internacionales, donde se ha efectuado al menos una acción comprendida en su alcance durante el año', 'Número Total de instrumentos de cooperación internacional con EFS vigentes y otras entidades internacionales en el año', NULL, NULL, NULL, NULL, '2025-12-19 01:18:29', '2025-12-19 01:18:29'),
	(6, 54, 4, 1, 'Evaluación del Modelo de Integridad', 'Mide el grado de cumplimiento de los principios de integridad establecidos en la organización, evaluando la efectividad de las políticas y procedimientos implementados para prevenir y detectar conductas no éticas.', NULL, 'servicio', '["SGAS"]', 1, 'V1/V2*100', 'trimestral', 90.00, 'acumulada', 'porcentaje', 'lineal', 'Número de actividades realizadas del Modelo de Integridad', 'Total de actividades programadas del modelo de integridad', NULL, NULL, NULL, NULL, '2025-12-19 01:28:49', '2025-12-19 19:34:00'),
	(7, 176, 4, 2, 'Porcentaje de denuncias con evaluación preliminar realizadas', 'Porcentaje de denuncias con evaluación preliminar realizadas', NULL, 'servicio', '["SGAS"]', 1, 'v1/v2*100', 'trimestral', 90.00, 'no acumulada', 'porcentaje', 'lineal', 'Número de denuncias contra el personal CGR con evaluación realizadas dentro del plazo establecid', 'Número de denuncias contra el personal CGR  recibidas', NULL, NULL, NULL, NULL, '2025-12-19 01:32:52', '2025-12-19 01:32:52'),
	(8, 49, NULL, 7, 'Cantidad de capacitaciones en el Sistema de Gestión de Compliance efectuadas', 'Calcula la cantidad de capacitaciones realizadas sobre el Sistema de Gestión de Compliance, evaluando la efectividad en la formación del personal respecto a normativas y políticas de cumplimiento, y su impacto en la cultura organizacional.', NULL, 'resultado', '["SGCM"]', 1, 'V1', 'anual', 4.00, 'acumulada', 'numero', 'ascendente', 'Número de capacitaciones en el Sistema de Gestión de Compliance efectuadas', NULL, NULL, NULL, NULL, NULL, '2025-12-19 01:35:08', '2025-12-19 19:52:27'),
	(9, 49, 4, 8, 'Porcentaje de acciones implementadas del programa de supervisión del SGCM', 'Mide el porcentaje de acciones efectivamente implementadas del programa de supervisión del Sistema de Gestión de compliance, evaluando así el grado de cumplimiento de las actividades planificadas para asegurar la mejora continua y la conformidad con los requisitos establecidos.', NULL, 'resultado', '["SGCM"]', 1, 'v1/v2*100', 'semestral', 100.00, 'acumulada', 'porcentaje', 'ascendente', 'Número de acciones del programa de supervisión del SGCM', 'Número total de acciones del programa de supervisión del SGCM planificadas', NULL, NULL, NULL, NULL, '2025-12-19 19:25:37', '2025-12-19 19:52:49'),
	(10, 49, 4, 11, 'Porcentaje de solicitudes de mejora de procesos (SMP – NC) (dentro del SGCM) cerradas con eficacia', 'Mide el porcentaje de solicitudes de mejora de procesos (SMP)  dentro del Sistema de Gestión de Cumplimiento (SGCM) que han sido cerradas de manera efectiva, evaluando la eficacia de las acciones implementadas.', NULL, 'resultado', '["SGCM"]', 1, 'v1/v2*100', 'trimestral', 70.00, 'no acumulada', 'porcentaje', 'lineal', 'Número de SMP - NC, en el marco del SGCM, cerradas con eficacia', 'Total de SMP - NC, en el marco del SGCM, cerradas (planificadas para la verificación de la eficacia)', NULL, NULL, NULL, NULL, '2025-12-19 19:39:36', '2025-12-19 19:39:45'),
	(11, 52, 4, 24, 'Porcentaje de Procedimientos del TUPA actualizados', 'Mide el porcentaje de procedimientos del TUPA (Texto Único de Procedimientos Administrativos) que han sido actualizados en un periodo determinado, evaluando así la eficacia en la gestión de la documentación y el cumplimiento normativo de la entidad.', NULL, 'resultado', '["SGC"]', 1, 'V1/V2*100', 'anual', 75.00, 'no acumulada', 'porcentaje', 'lineal', 'Número de requerimientos o necesidades de modificaciones de TUPA atendidos en el plazo establecido', 'Total de requerimientos o necesidades de modificaciones de TUPA atendidos', NULL, NULL, NULL, NULL, '2025-12-19 19:55:44', '2025-12-19 19:55:44'),
	(12, 65, 4, 24, 'Porcentaje de requerimiento de documentos en el alcance del SIG atendidos en plazo', 'Mide el porcentaje de requerimientos de documentos relacionados con el Sistema Integrado de Gestión (SIG) que han sido atendidos dentro de los plazos establecidos, evaluando así la eficacia y eficiencia en la gestión documental del sistema.', NULL, 'resultado', '["SGC"]', 1, 'v1/v2*100', 'trimestral', 80.00, 'no acumulada', 'porcentaje', 'descendente', 'Número total de requerimientos de documentación normativa en el alcance del SIG atendidos', 'Número de requerimientos totales', NULL, NULL, NULL, NULL, '2025-12-19 20:45:38', '2025-12-19 20:45:38'),
	(13, 45, 4, 24, 'Cantidad  de procesos mejorados con resultados favorables', 'Calcula la cantidad de procesos que han sido mejorados y que han mostrado resultados favorables en términos de eficiencia, calidad y satisfacción del cliente, en comparación con los indicadores previos a la mejora.', NULL, 'resultado', '["SGC"]', 1, 'v1', 'trimestral', 10.00, 'acumulada', 'numero', 'ascendente', 'Numero de procesos mejorados', NULL, NULL, NULL, NULL, NULL, '2025-12-19 22:25:34', '2025-12-19 22:25:34'),
	(14, 151, 1, 69, 'Cantidad de fichas de evaluación de aseguramiento de la calidad emitidas.', 'Mide la cantidad de fichas de evaluación generadas para el aseguramiento de la calidad en un período determinado, reflejando la actividad de monitoreo y control de procesos dentro del sistema de gestión de calidad.', NULL, 'producto', '["SGC"]', 1, 'V1', 'trimestral', 92.00, 'acumulada', 'numero', 'ascendente', 'Fichas de evaluación emitidas', NULL, NULL, NULL, NULL, NULL, '2026-01-19 05:10:55', '2026-01-19 16:42:16'),
	(15, 151, 1, 21, 'Porcentaje de fichas de aseguramiento de la calidad emitidas oportunamente.', 'Mide el porcentaje de fichas de aseguramiento de la calidad que se emiten dentro de los plazos establecidos, evaluando la eficacia del proceso de gestión de calidad y su cumplimiento normativo.', NULL, 'calidad', '["SGC"]', 1, 'V1/V2*100', 'trimestral', 75.00, 'no acumulada', 'porcentaje', 'lineal', 'Cantidad de fichas de evaluación de aseguramiento de la calidad emitidas en el plazo', 'Total de fichas de evaluación de aseguramiento de la calidad', NULL, NULL, NULL, NULL, '2026-01-19 05:18:46', '2026-01-19 05:18:46');

-- Volcando estructura para tabla kallpaq.indicadores_historico
CREATE TABLE IF NOT EXISTS `indicadores_historico` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `indicador_id` bigint(20) unsigned NOT NULL,
  `ih_año` year(4) NOT NULL,
  `ih_meta` double(8,2) NOT NULL,
  `ih_valor` double(8,2) NOT NULL,
  `ih_evidencia` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicadores_historico_indicador_id_foreign` (`indicador_id`) USING BTREE,
  CONSTRAINT `FK_indicadores_historico_indicadores` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores_historico: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.indicadores_seguimiento
CREATE TABLE IF NOT EXISTS `indicadores_seguimiento` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `indicador_id` bigint(20) unsigned NOT NULL,
  `is_numero_periodo` int(11) NOT NULL DEFAULT 0,
  `is_periodo` year(4) NOT NULL,
  `is_fecha` date NOT NULL,
  `is_meta` double(8,5) DEFAULT 0.00000,
  `is_valor` double(8,2) DEFAULT 0.00,
  `is_comentario` text DEFAULT NULL,
  `is_var1` double(8,2) DEFAULT 0.00,
  `is_var2` double(8,2) DEFAULT 0.00,
  `is_var3` double(8,2) DEFAULT 0.00,
  `is_var4` double(8,2) DEFAULT 0.00,
  `is_var5` double(8,2) DEFAULT 0.00,
  `is_var6` double(8,2) DEFAULT 0.00,
  `is_evidencias` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_indicador_periodo_numero` (`indicador_id`,`is_periodo`,`is_numero_periodo`),
  KEY `indicador_proceso_ouo_id` (`indicador_id`),
  CONSTRAINT `indicadores_seguimiento_indicador_id_foreign` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.indicadores_seguimiento: ~8 rows (aproximadamente)
REPLACE INTO `indicadores_seguimiento` (`id`, `indicador_id`, `is_numero_periodo`, `is_periodo`, `is_fecha`, `is_meta`, `is_valor`, `is_comentario`, `is_var1`, `is_var2`, `is_var3`, `is_var4`, `is_var5`, `is_var6`, `is_evidencias`, `created_at`, `updated_at`) VALUES
	(304, 13, 1, '2025', '2025-12-19', 0.00000, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-12-19 22:40:52', '2025-12-19 22:40:52'),
	(305, 13, 2, '2025', '2025-12-19', 5.00000, 1.00, NULL, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-12-19 22:41:28', '2025-12-19 22:41:28'),
	(306, 13, 3, '2025', '2025-12-19', 7.00000, 2.00, NULL, 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-12-19 22:47:14', '2025-12-19 22:47:14'),
	(307, 13, 4, '2025', '2025-12-19', 10.00000, 10.00, NULL, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-12-19 22:47:44', '2025-12-19 22:47:44'),
	(308, 10, 1, '2025', '2025-12-19', 70.00000, 0.00, NULL, 0.00, 2.00, 0.00, 0.00, 0.00, 0.00, NULL, '2025-12-19 22:53:36', '2025-12-19 22:53:36'),
	(309, 1, 1, '2026', '2026-01-19', 100.00000, 9.60, 'dsadsadasd', 12.00, 12.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-01-19 15:44:51', '2026-01-19 15:57:44'),
	(310, 15, 1, '2025', '2026-01-19', 75.00000, 66.67, 'El personal tuvo que salir de vacaciones', 10.00, 15.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-01-19 16:27:12', '2026-01-19 16:27:12'),
	(311, 14, 1, '2025', '2026-01-19', 80.00000, 13.00, 'asasas', 13.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, '2026-01-19 19:05:55', '2026-01-19 19:29:26');

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
  `estado_flujo` enum('borrador','aprobado','cerrado') DEFAULT NULL,
  `inventario_cierre` bigint(20) unsigned DEFAULT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.inventarios: ~2 rows (aproximadamente)
REPLACE INTO `inventarios` (`id`, `nombre`, `descripcion`, `documento_aprueba`, `vigencia`, `enlace`, `estado`, `created_at`, `updated_at`, `estado_flujo`, `inventario_cierre`, `fecha_cierre`) VALUES
	(1, '2021 V1', 'Mapa de Procesos, la Gobernanza de Procesos y el Inventario de procesos de la Contraloría General de la República 2021.', 'Resolución de Contraloría N° 279-2021-CG ', '2025-11-18 15:51:24', 'http://webserverapp.contraloria.gob.pe/Inicio/Bienestar_Docs/RC_279-2021-CG.pdf', 0, NULL, NULL, 'cerrado', NULL, NULL),
	(2, '2021 V2', 'Mapa de Procesos de la Contraloría General de la República 2021 v2', 'Resolución de Contraloría N° 255-2022-CG', '2026-11-18 05:00:00', 'http://webserverapp.contraloria.gob.pe/Calidad/Documentos/RC-255-2022-CG_Modifica_Mapa_de_Procesos.pdf', 1, NULL, '2025-11-18 20:46:05', 'borrador', NULL, NULL);

-- Volcando estructura para tabla kallpaq.inventario_procesos
CREATE TABLE IF NOT EXISTS `inventario_procesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_inventario` bigint(20) unsigned NOT NULL DEFAULT 0,
  `id_proceso` bigint(20) unsigned NOT NULL,
  `id_ouo_propietario` bigint(20) unsigned DEFAULT NULL,
  `id_ouo_delegado` bigint(20) unsigned DEFAULT NULL,
  `id_ouo_ejecutor` bigint(20) unsigned DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `inventario_procesos_id_proceso_foreign` (`id_proceso`),
  KEY `inventario_procesos_id_ouo_responsable_foreign` (`id_ouo_propietario`) USING BTREE,
  CONSTRAINT `inventario_procesos_id_ouo_responsable_foreign` FOREIGN KEY (`id_ouo_propietario`) REFERENCES `ouos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventario_procesos_id_proceso_foreign` FOREIGN KEY (`id_proceso`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.inventario_procesos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.migrations: ~170 rows (aproximadamente)
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
	(136, '2025_11_11_165343_add_ciclo_to_hallazgos_and_acciones_tables', 42),
	(137, '2025_11_12_163513_create_facilitadores_table', 43),
	(138, '2025_11_12_163744_create_proceso_facilitador_table', 43),
	(139, '2025_11_13_094145_create_ouo_user_table', 44),
	(140, '2025_11_13_104324_create_ouo_user_movimientos_table', 44),
	(141, '2025_11_17_013923_create_auditores_table', 44),
	(142, '2025_11_17_201105_add_estado_flujo_inventario_cierre_fecha_cierre_to_inventarios_table', 44),
	(143, '2025_11_21_162500_fix_hallazgo_causas_columns', 45),
	(144, '2025_11_21_162600_make_causa_columns_nullable', 46),
	(145, '2025_11_23_151007_create_salidas_no_conformes_table', 47),
	(146, '2025_11_23_151009_create_snc_proceso_table', 47),
	(147, '2025_11_23_151011_create_snc_acciones_table', 47),
	(148, '2025_11_24_115620_add_evidencias_to_hallazgo_evaluaciones_table', 48),
	(149, '2025_11_24_120046_make_evaluation_fields_nullable_in_hallazgo_evaluaciones', 49),
	(150, '2025_11_24_124128_add_proceso_id_to_salidas_no_conformes_table', 50),
	(151, '2025_11_24_124449_drop_snc_proceso_table', 50),
	(152, '2025_11_24_124518_add_evidencia_to_salidas_no_conformes_table', 50),
	(153, '2025_11_24_130533_update_salidas_no_conformes_table', 51),
	(155, '2025_11_24_142548_remove_snc_detectado_por_from_salidas_no_conformes', 52),
	(156, '2025_11_24_160608_remove_snc_codigo_from_salidas_no_conformes_table', 53),
	(157, '2025_11_24_165533_drop_snc_acciones_table', 54),
	(158, '2025_11_24_223049_rename_snc_evidencia_to_snc_archivos_and_add_snc_evidencias', 55),
	(159, '2025_11_25_100246_create_sugerencias_table', 56),
	(160, '2025_11_25_125935_create_encuestas_satisfaccion_tables', 57),
	(161, '2025_11_25_140629_add_numero_periodo_to_encuestas_satisfaccion_table', 58),
	(162, '2025_11_25_150814_rename_columns_in_encuesta_satisfaccion_table', 59),
	(163, '2025_11_25_152321_rename_columns_in_encuesta_satisfaccion_detalles_table', 60),
	(164, '2025_11_26_092702_add_fields_to_sugerencias_table', 61),
	(165, '2025_11_26_150441_create_radar_normativo_table', 62),
	(166, '2025_11_26_150512_add_iso37301_fields_to_obligaciones_table', 62),
	(167, '2025_11_26_160837_make_fields_nullable_in_documentos_and_obligaciones', 63),
	(168, '2025_11_27_153106_add_url_fuente_to_radar_normativo_table', 64),
	(169, '2025_11_27_154036_add_missing_columns_to_radar_normativo_table', 65),
	(170, '2025_12_02_093811_create_riesgo_acciones_reprogramaciones_table', 66),
	(171, '2025_12_02_173320_create_riesgo_revisions_table', 67),
	(172, '2025_12_03_172247_fix_indicadores_seguimiento_fk', 68),
	(173, '2025_12_03_183000_update_indicadores_seguimiento_table', 69),
	(174, '2025_12_03_191007_add_unique_constraint_to_indicadores_seguimiento_table', 70),
	(175, '2025_12_04_160714_create_accion_reprogramaciones_table', 71),
	(176, '2025_12_27_180000_create_revision_direccion_tables', 72),
	(177, '2025_12_27_183000_create_continuidad_negocio_tables', 72),
	(178, '2025_12_29_172100_add_recursos_necesarios_to_revision_compromisos_table', 72),
	(179, '2025_12_30_133930_add_is_comentario_to_indicadores_seguimiento_table', 72),
	(180, '2026_01_05_123501_rename_columns_in_tipo_documentos_table', 72),
	(181, '2026_01_05_213608_create_documento_anexos_table', 72),
	(182, '2026_01_05_220112_add_da_tipo_to_documento_anexos_table', 72),
	(183, '2026_01_05_222156_add_fecha_publicacion_to_documento_anexos_table', 72),
	(184, '2026_01_05_233433_add_missing_columns_to_documento_versiones_table', 72),
	(185, '2026_01_05_235341_add_instrumento_aprueba_to_documento_versions_table', 72),
	(186, '2026_01_06_000615_change_accion_to_string_in_documento_movimientos_table', 72),
	(187, '2026_01_06_002205_add_deleted_at_to_documento_versions_table', 72),
	(188, '2026_01_06_110842_change_columns_in_documentos_table', 72),
	(189, '2026_01_06_133658_update_partes_interesadas_module', 72),
	(190, '2026_01_06_141000_add_deleted_at_to_partes_interesadas_table', 72),
	(191, '2026_01_06_143000_add_computed_columns_to_partes_interesadas', 72),
	(192, '2026_01_06_172000_refactor_expectativas_compromisos', 72),
	(193, '2026_01_06_192000_fix_compromisos_fk', 72),
	(194, '2026_01_06_194000_add_status_to_expectativas', 72),
	(195, '2026_01_08_094529_create_reporte_satisfaccions_table', 72),
	(196, '2026_01_08_124500_add_archivo_firmado_column_to_reportes_satisfaccion', 72),
	(197, '2026_01_08_124612_add_archivo_firmado_column_to_reportes_satisfaccion', 72),
	(198, '2026_01_08_153024_add_sistemas_gestion_to_revisiones_direccion_table', 72),
	(199, '2026_01_08_160314_add_sistemas_gestion_to_revision_compromisos_table', 72),
	(200, '2026_01_12_134113_refactor_audit_module_tables', 73),
	(201, '2026_01_13_111443_update_auditoria_especifica_add_resources_and_process_pivot', 73),
	(202, '2026_01_13_142010_update_auditoria_especifica_fields_v2', 73),
	(203, '2026_01_13_142733_add_ouo_to_auditoria_agenda', 73),
	(204, '2026_01_13_160916_add_soft_deletes_to_auditores_table', 73),
	(205, '2026_01_13_175905_create_normas_auditables_table', 73),
	(206, '2026_01_13_175921_create_requisitos_norma_table', 73),
	(207, '2026_01_13_180010_add_tipo_to_auditoria_agenda_table', 73),
	(208, '2026_01_14_122743_remove_meeting_times_from_auditoria_especifica_table', 73),
	(209, '2026_01_14_141040_rename_requisitos_norma_to_norma_requisitos_table', 73),
	(210, '2026_01_14_205237_create_auditoria_ejecucion_tables', 73),
	(211, '2026_01_14_211545_add_agenda_id_to_auditoria_ejecuciones_table', 73),
	(212, '2026_01_15_102907_add_auditor_id_to_auditoria_agenda_table', 73),
	(213, '2026_01_15_134603_refactor_schema_drop_ejecuciones', 73),
	(214, '2026_01_15_174500_add_aea_archivo_to_auditoria_agenda', 73),
	(215, '2026_01_15_182000_create_auditoria_auditados_table', 73),
	(216, '2026_01_15_190000_create_auditoria_informes_table', 73),
	(217, '2026_01_15_202548_add_extra_fields_to_auditoria_informes', 73),
	(218, '2026_01_16_103158_add_hallazgo_redaccion_to_auditoria_checklists', 73),
	(219, '2026_01_16_112725_add_clasificacion_resumen_to_auditoria_checklists_table', 73),
	(220, '2026_01_16_121839_rename_clasificacion_to_hallazgo_clasificacion_in_auditoria_checklists_table', 73),
	(221, '2026_01_16_153000_modify_users_table', 74),
	(222, '2026_01_16_162427_add_soft_deletes_to_roles_table', 75),
	(223, '2026_01_16_162825_add_description_to_roles_table', 76),
	(224, '2026_01_18_100000_add_description_to_permissions_table', 77),
	(225, '2026_01_18_103000_standardize_menu_permissions', 78),
	(226, '2026_01_18_113809_create_denied_permissions_table', 79),
	(227, '2026_01_18_124000_update_ouo_permission_description', 80),
	(228, '2026_01_18_125500_add_deleted_at_to_ouo_user_table', 81),
	(229, '2026_01_18_133456_modify_ouo_user_movimientos_columns', 82),
	(230, '2026_01_18_215915_add_ruta_plan_accion_to_hallazgos_table', 83),
	(231, '2026_01_18_221616_update_hallazgo_estado_enum_in_hallazgos_table', 84),
	(232, '2025_11_05_191728_create_planificacion_pei_table', 85);

-- Volcando estructura para tabla kallpaq.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.model_has_permissions: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `model_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.model_has_roles: ~41 rows (aproximadamente)
REPLACE INTO `model_has_roles` (`model_id`, `model_type`, `role_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(327, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(5, 'App\\Models\\User', 2),
	(8, 'App\\Models\\User', 2),
	(9, 'App\\Models\\User', 2),
	(10, 'App\\Models\\User', 2),
	(11, 'App\\Models\\User', 2),
	(12, 'App\\Models\\User', 2),
	(13, 'App\\Models\\User', 2),
	(14, 'App\\Models\\User', 2),
	(34, 'App\\Models\\User', 2),
	(39, 'App\\Models\\User', 2),
	(40, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 4),
	(4, 'App\\Models\\User', 4),
	(7, 'App\\Models\\User', 4),
	(14, 'App\\Models\\User', 4),
	(15, 'App\\Models\\User', 4),
	(16, 'App\\Models\\User', 4),
	(17, 'App\\Models\\User', 4),
	(18, 'App\\Models\\User', 4),
	(19, 'App\\Models\\User', 4),
	(20, 'App\\Models\\User', 4),
	(21, 'App\\Models\\User', 4),
	(22, 'App\\Models\\User', 4),
	(23, 'App\\Models\\User', 4),
	(24, 'App\\Models\\User', 4),
	(25, 'App\\Models\\User', 4),
	(26, 'App\\Models\\User', 4),
	(27, 'App\\Models\\User', 4),
	(28, 'App\\Models\\User', 4),
	(29, 'App\\Models\\User', 4),
	(30, 'App\\Models\\User', 4),
	(31, 'App\\Models\\User', 4),
	(32, 'App\\Models\\User', 4),
	(33, 'App\\Models\\User', 4),
	(35, 'App\\Models\\User', 4),
	(36, 'App\\Models\\User', 4),
	(37, 'App\\Models\\User', 4),
	(38, 'App\\Models\\User', 4),
	(41, 'App\\Models\\User', 4);

-- Volcando estructura para tabla kallpaq.normas_auditables
CREATE TABLE IF NOT EXISTS `normas_auditables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.normas_auditables: ~4 rows (aproximadamente)
REPLACE INTO `normas_auditables` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'ISO 9001:2025', 'Sistema de Gestión de la Calidad', '2026-01-16 19:54:10', '2026-01-16 19:54:10', NULL),
	(2, 'ISO 37001:2025', 'Sistema de Gestión Antisborno', '2026-01-16 19:54:55', '2026-01-16 19:54:55', NULL),
	(3, 'ISO 37301:2022', 'Sistema de Gestión de Cumplimiento', '2026-01-16 19:55:46', '2026-01-16 19:55:46', NULL),
	(4, 'ISO 21001:2025', 'Sistema de Gestión de la Calidad en Organizaciones Educativas', '2026-01-16 19:58:37', '2026-01-16 19:58:37', NULL);

-- Volcando estructura para tabla kallpaq.norma_requisitos
CREATE TABLE IF NOT EXISTS `norma_requisitos` (
  `nr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nr_norma_id` bigint(20) unsigned NOT NULL,
  `nr_numeral` varchar(255) NOT NULL,
  `nr_denominacion` varchar(255) NOT NULL,
  `nr_detalle` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nr_id`),
  KEY `requisitos_norma_norma_id_foreign` (`nr_norma_id`),
  CONSTRAINT `requisitos_norma_norma_id_foreign` FOREIGN KEY (`nr_norma_id`) REFERENCES `normas_auditables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.norma_requisitos: ~113 rows (aproximadamente)
REPLACE INTO `norma_requisitos` (`nr_id`, `nr_norma_id`, `nr_numeral`, `nr_denominacion`, `nr_detalle`, `created_at`, `updated_at`) VALUES
	(1, 1, '4.1', 'Comprensión de la organización y de su contexto', 'Determinar las cuestiones externas e internas que son relevantes para el propósito y la dirección estratégica de la organización.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(2, 1, '4.2', 'Comprensión de las necesidades y expectativas de las partes interesadas', 'Determinar quiénes son las partes interesadas pertinentes y cuáles son sus requisitos para el sistema de gestión de la calidad.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(3, 1, '4.3', 'Determinación del alcance del sistema de gestión de la calidad', 'Establecer los límites y la aplicabilidad del sistema, considerando productos, servicios y el contexto de la organización.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(4, 1, '4.4', 'Sistema de gestión de la calidad y sus procesos', 'Establecer, implementar, mantener y mejorar el sistema, identificando entradas, salidas y la interacción entre procesos.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(5, 1, '5.1', 'Liderazgo y compromiso', 'La alta dirección debe demostrar liderazgo asegurando que la política y objetivos de calidad se integren en el negocio.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(6, 1, '5.2', 'Política de la calidad', 'Establecer una política que sea apropiada al propósito, incluya compromiso de mejora y esté disponible para las partes interesadas.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(7, 1, '5.3', 'Roles, responsabilidades y autoridades', 'Asignar y comunicar las responsabilidades para asegurar que el sistema cumple los requisitos y los procesos entregan resultados.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(8, 1, '6.1', 'Acciones para abordar riesgos y oportunidades', 'Planificar acciones para abordar los riesgos y oportunidades identificados con el fin de asegurar el logro de resultados y la mejora.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(9, 1, '6.2', 'Objetivos de la calidad y planificación', 'Establecer objetivos medibles y coherentes con la política en todos los niveles pertinentes de la organización.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(10, 1, '6.3', 'Planificación de los cambios', 'Cuando se requieran cambios en el sistema, estos deben llevarse a cabo de forma planificada y controlada.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(11, 1, '7.1', 'Recursos', 'Determinar y proporcionar los recursos necesarios (personas, infraestructura, ambiente, seguimiento y medición) para el sistema.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(12, 1, '7.2', 'Competencia', 'Asegurar que el personal que realiza trabajos bajo su control sea competente basándose en su educación, formación o experiencia.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(13, 1, '7.3', 'Toma de conciencia', 'Garantizar que las personas comprendan la política de calidad, los objetivos y su contribución a la eficacia del sistema.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(14, 1, '7.4', 'Comunicación', 'Establecer las comunicaciones internas y externas pertinentes (qué, cuándo, a quién, cómo y quién comunica).', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(15, 1, '7.5', 'Información documentada', 'Controlar la creación, actualización y conservación de los documentos y registros requeridos por la norma.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(16, 1, '8.1', 'Planificación y control operacional', 'Planificar, implementar y controlar los procesos necesarios para cumplir los requisitos de productos y servicios.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(17, 1, '8.2', 'Requisitos para los productos y servicios', 'Gestionar la comunicación con clientes y determinar/revisar los requisitos antes de comprometerse con la entrega.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(18, 1, '8.3', 'Diseño y desarrollo de productos y servicios', 'Establecer un proceso para el diseño que incluya planificación, entradas, controles, salidas y gestión de cambios.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(19, 1, '8.4', 'Control de procesos, productos y servicios suministrados externamente', 'Asegurar que los productos o servicios de proveedores externos cumplen con los requisitos definidos por la organización.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(20, 1, '8.5', 'Producción y provisión del servicio', 'Realizar la producción bajo condiciones controladas (identificación, trazabilidad, preservación y actividades posentrega).', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(21, 1, '8.6', 'Liberación de productos y servicios', 'Verificar en las etapas adecuadas que se han cumplido los requisitos antes de entregar el producto o servicio al cliente.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(22, 1, '8.7', 'Control de las salidas no conformes', 'Identificar y controlar los productos o servicios que no cumplen los requisitos para prevenir su uso o entrega no intencionada.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(23, 1, '9.1', 'Seguimiento, medición, análisis y evaluación', 'Determinar qué necesita seguimiento, medir la satisfacción del cliente y analizar los datos obtenidos del desempeño.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(24, 1, '9.2', 'Auditoría interna', 'Realizar auditorías internas a intervalos planificados para asegurar que el sistema es conforme y se mantiene eficazmente.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(25, 1, '9.3', 'Revisión por la dirección', 'La alta dirección debe revisar el sistema periódicamente para asegurar su conveniencia, adecuación, eficacia y alineación.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(26, 1, '10.1', 'Generalidades (Mejora)', 'Determinar oportunidades de mejora e implementar acciones para aumentar la satisfacción del cliente.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(27, 1, '10.2', 'No conformidad y acción correctiva', 'Ante una no conformidad, tomar acciones para controlarla, evaluar la causa raíz e implementar correctivos para evitar su repetición.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(28, 1, '10.3', 'Mejora continua', 'Mejorar continuamente la idoneidad, adecuación y eficacia del sistema de gestión de la calidad.', '2026-01-16 19:54:10', '2026-01-16 19:54:10'),
	(29, 2, '4.1', 'Comprensión de la organización y de su contexto', 'Determinar cuestiones internas y externas, incluyendo ahora la relevancia de los riesgos del cambio climático en el entorno antisoborno.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(30, 2, '4.2', 'Necesidades y expectativas de partes interesadas', 'Identificar a los actores clave y sus requisitos. Se debe determinar si alguna necesidad de las partes interesadas deriva de compromisos climáticos.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(31, 2, '4.3', 'Alcance del sistema de gestión antisoborno', 'Establecer los límites y la aplicabilidad del sistema, considerando las actividades, productos y la estructura de la organización.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(32, 2, '4.4', 'Sistema de gestión antisoborno', 'Establecer, implementar, mantener y mejorar continuamente el sistema para prevenir, detectar y enfrentar el soborno.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(33, 2, '4.5', 'Evaluación del riesgo de soborno', 'Realizar evaluaciones periódicas para identificar, analizar y evaluar los riesgos de soborno específicos de la organización.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(34, 2, '5.1', 'Liderazgo y compromiso', 'El órgano de gobierno y la alta dirección deben demostrar liderazgo. Se añade el nuevo numeral 5.1.3 sobre el fomento de una cultura antisoborno.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(35, 2, '5.2', 'Política antisoborno', 'Establecer una política que prohíba el soborno, exija el cumplimiento de las leyes y promueva el planteamiento de inquietudes.', '2026-01-16 19:54:55', '2026-01-16 19:54:55'),
	(36, 2, '5.3', 'Roles, responsabilidades y autoridades', 'Asignar responsabilidades, destacando la función antisoborno con autoridad e independencia para supervisar el sistema.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(37, 2, '6.1', 'Acciones para abordar riesgos y oportunidades', 'Planificar medidas para asegurar que el sistema logre sus resultados y prevenir efectos no deseados.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(38, 2, '6.2', 'Objetivos antisoborno y planificación', 'Establecer metas medibles y coherentes con la política para reducir los riesgos de soborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(39, 2, '6.3', 'Planificación de los cambios', 'Asegurar que cualquier modificación al sistema se realice de forma controlada y planificada.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(40, 2, '7.1', 'Recursos', 'Proporcionar los recursos humanos, financieros y tecnológicos necesarios para la eficacia del sistema.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(41, 2, '7.2', 'Competencia y Debida Diligencia del personal', 'Asegurar que el personal sea apto. Se refuerza la gestión de conflictos de interés desde la contratación.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(42, 2, '7.3', 'Toma de conciencia y formación', 'Capacitar al personal y socios de negocio sobre la política antisoborno y las consecuencias del incumplimiento.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(43, 2, '7.4', 'Comunicación', 'Definir qué, cuándo, a quién y cómo comunicar internamente y externamente sobre el sistema antisoborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(44, 2, '7.5', 'Información documentada', 'Controlar los documentos y registros (creación, actualización y protección) que evidencian el cumplimiento.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(45, 2, '8.1', 'Planificación y control operacional', 'Implementar los procesos y controles necesarios para cumplir con los requisitos del sistema.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(46, 2, '8.2', 'Debida diligencia', 'Evaluar la naturaleza y alcance del riesgo de soborno con relación a transacciones, proyectos, actividades y socios de negocio.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(47, 2, '8.3', 'Controles financieros', 'Implementar controles en la gestión financiera (pagos, aprobaciones, registros) para mitigar el riesgo de soborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(48, 2, '8.4', 'Controles no financieros', 'Establecer controles en áreas como compras, operaciones, ventas y recursos humanos (ej. procesos de licitación).', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(49, 2, '8.5', 'Controles en socios de negocio', 'Implementar controles para asegurar que los socios de negocio con riesgo bajo o medio apliquen medidas antisoborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(50, 2, '8.6', 'Compromisos antisoborno', 'Exigir que los socios de negocio con riesgo mayor al bajo firmen compromisos de prevención del soborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(51, 2, '8.7', 'Regalos, hospitalidad y donaciones', 'Establecer procedimientos para prevenir que estos beneficios se utilicen con fines de soborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(52, 2, '8.8', 'Gestión de controles antisoborno inadecuados', 'Revisar y corregir los casos donde los controles antisoborno no sean suficientes o fallen.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(53, 2, '8.9', 'Planteamiento de inquietudes (Denuncias)', 'Garantizar canales de denuncia confidenciales y proteger a los informantes contra represalias.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(54, 2, '8.1', 'Investigación y tratamiento del soborno', 'Investigar y tomar medidas ante cualquier sospecha o hecho confirmado de soborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(55, 2, '9.1', 'Seguimiento, medición y análisis', 'Evaluar el desempeño del sistema y la eficacia de los controles mediante indicadores.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(56, 2, '9.2', 'Auditoría interna', 'Realizar inspecciones internas periódicas para verificar que el sistema cumple con la norma y los requisitos internos.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(57, 2, '9.3', 'Revisión por la dirección', 'La alta dirección debe analizar la idoneidad y eficacia del sistema en intervalos planificados.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(58, 2, '9.4', 'Revisión por la función antisoborno', 'La función encargada debe evaluar el sistema de forma continua y reportar al órgano de gobierno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(59, 2, '10.1', 'Mejora continua', 'Elevar constantemente la eficacia y madurez del sistema de gestión antisoborno.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(60, 2, '10.2', 'No conformidad y acción correctiva', 'Reaccionar ante fallas en el sistema, corregirlas y eliminar sus causas para prevenir recurrencias.', '2026-01-16 19:54:56', '2026-01-16 19:54:56'),
	(61, 3, '4.1', 'Comprensión de la organización y de su contexto', 'Determinar cuestiones externas e internas relevantes para el propósito y que afectan los resultados del sistema de cumplimiento.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(62, 3, '4.2', 'Necesidades y expectativas de partes interesadas', 'Identificar partes interesadas relevantes y sus requisitos (legales, regulatorios o compromisos voluntarios).', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(63, 3, '4.3', 'Alcance del sistema de gestión del cumplimiento', 'Establecer los límites físicos y organizativos donde se aplicará el sistema de cumplimiento.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(64, 3, '4.4', 'Sistema de gestión del cumplimiento', 'Establecer, implementar y mejorar el sistema, incluyendo procesos e interacciones bajo los principios de buen gobierno.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(65, 3, '4.5', 'Obligaciones de cumplimiento', 'Identificar sistemáticamente las obligaciones legales y los compromisos que la organización decide adoptar.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(66, 3, '4.6', 'Evaluación del riesgo de cumplimiento', 'Identificar, analizar y evaluar los riesgos de incumplimiento de forma periódica y ante cambios.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(67, 3, '5.1', 'Liderazgo y compromiso', 'La alta dirección y el órgano de gobierno deben demostrar liderazgo y asegurar los recursos para el sistema.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(68, 3, '5.2', 'Política de cumplimiento', 'Establecer una política escrita que prohíba el incumplimiento y fomente la comunicación de inquietudes.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(69, 3, '5.3', 'Roles, responsabilidades y autoridades', 'Asignar responsabilidades en toda la organización, incluyendo la función de cumplimiento independiente.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(70, 3, '6.1', 'Acciones para abordar riesgos y oportunidades', 'Planificar acciones para prevenir efectos no deseados y asegurar que el sistema logre sus objetivos.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(71, 3, '6.2', 'Objetivos de cumplimiento y planificación', 'Establecer objetivos medibles en funciones y niveles pertinentes, coherentes con la política.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(72, 3, '6.3', 'Planificación de los cambios', 'Realizar cambios al sistema de forma planificada para mantener su integridad.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(73, 3, '7.1', 'Recursos', 'Determinar y proporcionar los recursos necesarios (humanos, técnicos, financieros) para el sistema.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(74, 3, '7.2', 'Competencia', 'Asegurar que el personal sea competente basándose en educación, formación o experiencia.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(75, 3, '7.3', 'Toma de conciencia', 'Lograr que el personal conozca la política de cumplimiento y las consecuencias de no cumplirla.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(76, 3, '7.4', 'Comunicación', 'Determinar las comunicaciones internas y externas pertinentes al sistema de gestión.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(77, 3, '7.5', 'Información documentada', 'Crear, actualizar y controlar los documentos y registros requeridos por la norma.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(78, 3, '8.1', 'Planificación y control operacional', 'Implementar procesos para cumplir los requisitos y realizar las acciones planificadas en el capítulo 6.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(79, 3, '8.2', 'Controles y procedimientos', 'Establecer controles para asegurar que se cumplen las obligaciones y se mitigan los riesgos de cumplimiento.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(80, 3, '8.3', 'Planteamiento de inquietudes', 'Establecer procesos para reportar sospechas de incumplimiento (canales de denuncia) con protección al informante.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(81, 3, '8.4', 'Procesos de investigación', 'Establecer mecanismos para investigar y evaluar posibles incumplimientos de forma objetiva.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(82, 3, '9.1', 'Seguimiento, medición y análisis', 'Evaluar el desempeño del cumplimiento y la eficacia del sistema mediante indicadores.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(83, 3, '9.2', 'Auditoría interna', 'Realizar auditorías a intervalos planificados para verificar que el sistema es conforme y eficaz.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(84, 3, '9.3', 'Revisión por la dirección', 'La alta dirección debe revisar el sistema para asegurar su conveniencia, adecuación y eficacia.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(85, 3, '10.1', 'No conformidad y acción correctiva', 'Reaccionar ante incumplimientos, tomar medidas para controlarlos y eliminar las causas para que no vuelvan a ocurrir.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(86, 3, '10.2', 'Mejora continua', 'Mejorar continuamente la idoneidad y eficacia del sistema de gestión del cumplimiento.', '2026-01-16 19:55:46', '2026-01-16 19:55:46'),
	(87, 4, '4.1', 'Comprensión de la organización y de su contexto', 'Determinar cuestiones internas y externas relevantes para la misión, visión y la capacidad de lograr los resultados del servicio educativo.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(88, 4, '4.2', 'Necesidades y expectativas de las partes interesadas', 'Identificar a estudiantes, otros beneficiarios y personal, determinando sus requisitos y necesidades específicas (incluyendo necesidades especiales).', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(89, 4, '4.3', 'Alcance del sistema de gestión para organizaciones educativas', 'Definir los límites del sistema, considerando los productos educativos, servicios y el contexto de la institución.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(90, 4, '4.4', 'Sistema de gestión para organizaciones educativas', 'Establecer, implementar y mejorar el sistema, gestionando los procesos y sus interacciones para el aprendizaje y la enseñanza.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(91, 4, '5.1', 'Liderazgo y compromiso', 'La alta dirección debe demostrar liderazgo, enfocándose en la satisfacción de los estudiantes y la responsabilidad social de la institución.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(92, 4, '5.2', 'Política', 'Establecer una política de calidad educativa que respalde la misión y visión, y proporcione un marco para los objetivos educativos.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(93, 4, '5.3', 'Roles, responsabilidades y autoridades', 'Asignar responsabilidades para asegurar que el sistema es eficaz y que se promueve el enfoque en los estudiantes.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(94, 4, '6.1', 'Acciones para abordar riesgos y oportunidades', 'Planificar medidas para prevenir fallos en el servicio educativo y aprovechar oportunidades de mejora en el aprendizaje.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(95, 4, '6.2', 'Objetivos educativos y planificación', 'Establecer metas educativas medibles que sean coherentes con la política y relevantes para el éxito de los estudiantes.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(96, 4, '6.3', 'Planificación de los cambios', 'Asegurar que cualquier modificación en el sistema o en los programas educativos se realice de manera organizada.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(97, 4, '7.1', 'Recursos', 'Proporcionar recursos humanos, infraestructura (aulas, laboratorios), entorno de aprendizaje y recursos de seguimiento y medición.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(98, 4, '7.2', 'Competencia', 'Asegurar que los educadores y el personal administrativo tengan la formación y experiencia necesaria para sus funciones.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(99, 4, '7.3', 'Toma de conciencia', 'Garantizar que el personal conozca la política educativa y cómo su labor impacta en el éxito del aprendizaje de los alumnos.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(100, 4, '7.4', 'Comunicación', 'Definir canales internos y externos para comunicarse con estudiantes, padres, personal y reguladores.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(101, 4, '7.5', 'Información documentada', 'Controlar los documentos y registros del sistema, incluyendo planes de estudio y evidencias de evaluación.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(102, 4, '8.1', 'Planificación y control operacional', 'Organizar los procesos para la entrega de servicios educativos (diseño de currículo, admisión, enseñanza y evaluación).', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(103, 4, '8.2', 'Requisitos para los productos y servicios educativos', 'Determinar y revisar los requisitos de los cursos o programas antes de ofrecerlos a los estudiantes.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(104, 4, '8.3', 'Diseño y desarrollo de los productos y servicios educativos', 'Establecer un proceso riguroso para la creación de nuevos currículos, métodos de enseñanza y criterios de evaluación.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(105, 4, '8.4', 'Control de los procesos, productos y servicios suministrados externamente', 'Supervisar a proveedores externos (como servicios de limpieza, tecnología o plataformas de e-learning).', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(106, 4, '8.5', 'Entrega de los productos y servicios educativos', 'Realizar la enseñanza y el apoyo al aprendizaje bajo condiciones controladas, asegurando la trazabilidad y preservación de registros.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(107, 4, '8.6', 'Liberación de los productos y servicios educativos', 'Verificar que los estudiantes han cumplido con los requisitos de aprendizaje antes de otorgar certificaciones o grados.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(108, 4, '8.7', 'Control de las salidas educativas no conformes', 'Gestionar situaciones donde el servicio educativo no cumple los requisitos (ej. errores en evaluaciones o fallas en plataformas).', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(109, 4, '9.1', 'Seguimiento, medición, análisis y evaluación', 'Evaluar la satisfacción de estudiantes y beneficiarios, y el desempeño de los educadores y procesos educativos.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(110, 4, '9.2', 'Auditoría interna', 'Revisar periódicamente el cumplimiento del sistema con la norma y los propios objetivos de la institución.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(111, 4, '9.3', 'Revisión por la dirección', 'La dirección debe analizar el desempeño del sistema, incluyendo los resultados de aprendizaje y la retroalimentación de las partes interesadas.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(112, 4, '10.1', 'No conformidad y acción correctiva', 'Actuar ante incumplimientos en los procesos educativos, investigando la causa para evitar que se repitan.', '2026-01-16 19:58:37', '2026-01-16 19:58:37'),
	(113, 4, '10.2', 'Mejora continua', 'Implementar acciones constantes para elevar la calidad de la enseñanza y la eficacia del sistema de gestión.', '2026-01-16 19:58:37', '2026-01-16 19:58:37');

-- Volcando estructura para tabla kallpaq.obligaciones
CREATE TABLE IF NOT EXISTS `obligaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `area_compliance_id` bigint(20) unsigned NOT NULL,
  `documento_tecnico_normativo` text DEFAULT NULL,
  `obligacion_principal` text NOT NULL,
  `obligacion_controles` text DEFAULT NULL,
  `consecuencia_incumplimiento` text DEFAULT NULL,
  `documento_deroga` text DEFAULT NULL,
  `estado_obligacion` enum('pendiente','mitigada','controlada','vencida','inactiva','suspendida') NOT NULL DEFAULT 'pendiente',
  `radar_id` bigint(20) unsigned DEFAULT NULL,
  `documento_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_obligacion` enum('Legal','Contractual','Voluntaria') DEFAULT NULL,
  `nivel_riesgo_inherente` enum('Bajo','Medio','Alto','Muy Alto') DEFAULT NULL,
  `nivel_riesgo_residual` enum('Bajo','Medio','Alto','Muy Alto') DEFAULT NULL,
  `frecuencia_revision` int(11) DEFAULT NULL COMMENT 'En días',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obligaciones_proceso_id_foreign` (`proceso_id`),
  KEY `obligaciones_area_compliance_id_foreign` (`area_compliance_id`),
  KEY `obligaciones_radar_id_foreign` (`radar_id`),
  KEY `obligaciones_documento_id_foreign` (`documento_id`),
  CONSTRAINT `obligaciones_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `obligaciones_radar_id_foreign` FOREIGN KEY (`radar_id`) REFERENCES `radar_normativo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.obligaciones: ~48 rows (aproximadamente)
REPLACE INTO `obligaciones` (`id`, `proceso_id`, `area_compliance_id`, `documento_tecnico_normativo`, `obligacion_principal`, `obligacion_controles`, `consecuencia_incumplimiento`, `documento_deroga`, `estado_obligacion`, `radar_id`, `documento_id`, `tipo_obligacion`, `nivel_riesgo_inherente`, `nivel_riesgo_residual`, `frecuencia_revision`, `created_at`, `updated_at`) VALUES
	(2, 117, 14, 'Directiva N° 003-2024-CG/GJNC “Gestión Normativa en la Contraloría General de la República”, aprobada con Resolución de Contraloría N° 159-2024-CG.', 'Asegurar que la documentación de los procesos se realice de manera transversal y secuencial para satisfacer las necesidades y expectativas de las partes interesadas.', 'PR-NORM-06 Procedimiento "Gestión de documentos en el alcance del Sistema Integrado de Gestión"', 'Disminución en la capacidad de la organización para cumplir con los objetivos institucionales, lo que podría comprometer el éxito de los proyectos y la satisfacción de los ciudadanos.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 17:55:11', '2025-03-26 15:55:51'),
	(4, 105, 13, 'Normas ISO 19011, ISO 9001, ISO 37001, ISO 37301.', 'Programación y ejecución de auditorías para la evaluación de la eficacia del Sistema Integrado de Gestión', 'MG-MODER-02 Manual del Sistema Integrado de Gestión\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección, PR-MODER-07 Procedimiento "Auditorías Internas", PR-MODER-11 Procedimiento "Revisión de la Función de Cumplimiento"', 'Perdida de los certificados, daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 23:13:09', '2025-03-24 20:49:01'),
	(5, 106, 14, 'Norma ISO 9001:2015, Sistemas de gestión de la Calidad – Requisitos', 'Cumplir con los requisitos dispuestos por la norma internacional', 'MG-MODER-02 Manual del Sistema Integrado de Gestión.\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección.\r\nPR-MODER-07 Procedimiento "Auditorías Internas"\r\nPR-MODER-01 Procedimiento ""Gestión de Indicadores de Desempeño de Procesos"\r\nPR-MODER-05 Procedimiento "Control de Salidas No Conformes"\r\nPR-MODER-03 Procedimiento "Satisfacción del Cliente"\r\nPR-NORM-06 Procedimiento "Gestión de documentos en el alcance del SIG"', 'Daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 23:15:20', '2025-03-26 15:57:05'),
	(6, 107, 14, 'Norma ISO 31000:2018, Gestión del riesgo,  \r\nDirectiva N° 017-2020 “Gestión del riesgo en la Contraloría General de la República”.', 'Planificar, identificar, analizar, evaluar e identificar los riesgos que afecten los cumplimientos de los objetivos institucionales', 'MG-MODER-02 Manual del Sistema Integrado de Gestión\r\nPR-MODER-04 Procedimiento "Gestión del Riesgos"', 'Disminución en la capacidad de la organización para cumplir con los objetivos institucionales, lo que podría comprometer el éxito de los proyectos y la satisfacción de los ciudadanos.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 23:17:01', '2025-02-26 17:58:33'),
	(7, 113, 14, 'Norma Técnica “Implementación de la Gestión por Procesos en las Entidades de la Administración Pública”, aprobada mediante RSGP N° 002-2025-PCM/SGP.', 'Cumplir con los requisitos dispuestos por la norma internacional', 'PR-MODER-09 Procedimiento "Implementación de Mejora de Procesos"\r\nPR-MODER-14 Procedimiento "Mapeo de Procesos"\r\nPR-MODER-16 Procedimiento "Diseño de procesos', 'Impacto en la calidad de los servicios prestados y consecuente pérdida de la confianza y reputación ante los ciudadanos', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 23:17:51', '2025-03-26 16:00:47'),
	(8, 114, 14, 'Norma ISO 37301:2021, Sistemas de gestión del compliance – Requisitos con orientación para su uso.', 'Cumplir con los requisitos dispuestos por la norma internacional', 'MG-MODER-02 Manual del Sistema Integrado de Gestión.\r\nPR-MODER-06 Procedimiento "Revisión por la Dirección.\r\nPR-MODER-07 Procedimiento "Auditorías Internas"\r\nPR-MODER-18 Procedimiento "Gestión de las Obligaciones de Compliance"\r\nPR-MODER-19 Procedimiento "Revisión de la función de Compliance"\r\nPR-NORM-06 Procedimiento "Gestión de documentos en el alcance del SIG"', 'Daño a la reputación institucional y desconfianza de la ciudadanía', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-25 23:18:06', '2025-03-26 15:57:21'),
	(10, 164, 14, 'Directiva No 013-2023-CG/GAD Requerimiento,\r\notorgamiento y rendición de viáticos por comisión de servicio para la Contraloría General de la Republica.', 'Atención de los requerimientos de viáticos\r\ndentro del plazo establecido.\r\nRealización de la rendición de los viáticos\r\ndentro del plazo establecido.\r\nEvaluación de la rendición de viáticos.', 'Seguimiento y control del proceso mediante el aplicativo informático SIGA viáticos, por parte del Analista de viáticos.\r\n(PR-ARGF-03) Gestión de viáticos', 'Saldos elevados pendientes de rendir en la cuenta\r\ncontable "Otras entregas a rendir cuenta".', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:15:48', '2025-02-28 00:17:03'),
	(11, 164, 14, 'Ley No 27619, Ley que regula la autorización de viajes al exterior de servidores y funcionarios públicos y modificatorias.', 'Atención de los requerimientos de viáticos de viajes al exterior de acuerdo a la escala proporcionada en este decreto supremo.', 'Coordinación para la emisión de Resolución.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA viáticos, por parte del Analista de viáticos.', 'Atención fuera de tiempo del requerimiento de viáticos al exterior y reclamos por parte de los comisionados.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:26:02', '2025-02-28 00:26:02'),
	(12, 166, 14, 'Resolución Directoral N.º 004-2009-EF/77.15 que aprueba la Modificación del art. 40 de la Directiva de Tesorería N.º 001-2007-EF/77.15.', 'Autorización el requerimiento de anticipos mediante Resolución de la Gerencia de\r\nAdministración.\r\nRegistro de la rendición de cuentas del anticipo\r\nen el SIAF.\r\nPlazos para rendición del anticipo de 03 días hábiles, luego de concluida la ejecución del gasto.', '(PR-EJPRE-05) Gestión de anticipos.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA anticipos.\r\nNiveles de revisión del requerimiento de\r\nanticipos.', 'Incumplimiento de las actividades programadas por parte del usuario y posibles reclamos.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 00:48:46', '2025-02-28 00:48:46'),
	(13, 166, 14, 'Resolución Directoral N.º 040-2011-EF/52.03, que ha\r\nestablecido el monto máximo para el otorgamiento de Encargos al Personal de la Institución.', 'Establece monto máximo para el otorgamiento\r\nde anticipos.', '(PR-EJPRE-05) Gestión de anticipos.\r\nSeguimiento y control del proceso mediante el aplicativo informático SIGA anticipos, el cual no permite registrar requerimiento por importes mayores a los establecidos.', 'Observaciones en las auditorías anuales a la cuenta.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 18:58:57', '2025-02-28 18:58:57'),
	(14, 166, 14, '-Resolución de Superintendencia No 007-99-SUNAT, Reglamento de comprobantes de Pago y sus modificatorias.\r\n-Resolución de Superintendencia N.º 037-2002/SUNAT, que aprueba el Régimen de Retenciones del IGV aplicables a los proveedores y designación de agentes de retención y sus modificatorias.\r\n-Resolución de Superintendencia N.º 183-2004/SUNAT, que aprueba normas para la aplicación del Sistema de Pago de Obligaciones Tributarias con el Gobierno Central al que se refiere el Decreto Legislativo N.º 940 y sus modificatorias.\r\n-Resolución de Superintendencia N.º 287-2014/SUNAT, que modifica la Resolución de Superintendencia Nº182- 2014/SUNAT y modificatoria que implementó la emisión electrónica de recibo de honorarios.', '-Verificar que los comprobantes de pagos cuenten con las especificaciones determinadas por SUNAT.\r\n-Verificación de la aplicación de retenciones a\r\nlos comprobantes de pago, en los casos correspondientes.\r\n-Verificación de la aplicación de detracciones a\r\nlos comprobantes de pago, en los casos correspondientes.\r\n-Verificar que los recibos por honorarios que\r\nforman parte de la rendición de cuentas sean electrónicos.', 'Seguimiento y control del proceso mediante el aplicativo informático SIGA anticipos.', 'El incumplimiento de las obligaciones tributarias puede generar responsabilidades administrativas, sanciones y afectar la reputación de la institución.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 19:43:36', '2025-02-28 19:46:11'),
	(15, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica".\r\n-Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Apertura de Caja Chica de manera oportuna', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\nCoordinación con los OUO competentes por parte de la Gerencia de Administración.', 'No se pueden ejecutar gastos por caja chica.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 19:53:23', '2025-02-28 19:53:23'),
	(16, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Pago por caja chica de gastos que cumplan con\r\nlos requisitos establecidos', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\n-Revisión del gasto por parte del responsable del manejo de caja chica, si cumple con lo señalado en la normativa.\r\n-Verificación por parte del responsable del manejo de la caja chica, del registro de los gastos en el aplicativo SIGA Caja Chica, realizado por el colaborador.\r\n-Coordinación y asistencia con personal de la Gerencia de Administración, respecto al gasto.', 'No reconocimiento de gastos ejecutados que incumplan la normativa.\r\nFalta de disponibilidad de efectivo por la demora en la subsanación de observaciones del expediente de rendición de cuentas.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:43:37', '2025-02-28 20:43:37'),
	(17, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Validar que el expediente de rendición de caja chica cumpla con las especificaciones establecidas en la normativa para la reposición correspondiente.', '-Seguimiento y control del proceso mediante el aplicativo informático SIGA Caja chica.\r\n-Seguimiento y control del expediente para pago a través del SGD.', 'No atención de gastos urgentes, debido a la falta de liquidez por la demora de la reposición.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:46:25', '2025-02-28 20:46:25'),
	(18, 165, 14, 'Resolución Directoral N°008-2024-EF/52.01 que aprueba la Directiva N°003-2024-EF/52.06 "Directiva para el manejo de la Caja Chica". -Directiva N°001-2025-CG/GAD "Normas para la Apertura, Gestión y Liquidación de la Caja Chica" (periodicidad anual).', 'Liquidación final de Caja Chica dentro del plazo\r\nestablecido.', '(PR-FI-01) Procedimiento de Gestión de Fondo de Caja Chica.\r\nCoordinación con los OUO competentes por parte de la Gerencia de Administración.\r\n-Control y revisión de los expedientes de cierre de caja chica, tramitados previamente por correo y luego a través del SGD.', 'Emisión de los estados financieros y presupuestarios con observaciones', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:49:40', '2025-02-28 20:49:40'),
	(19, 92, 14, 'Decreto Legislativo Nº 1439, Decreto Legislativo del Sistema Nacional de Abastecimiento.', 'Decreto Legislativo que genera obligación de: Revisar y/o validar el Acta de Conciliación de Bienes y Suministros de manera mensual con la\r\nSubgerencia de Abastecimiento, con las \r\n suscripciones de las áreas usuarias\r\n(Abastecimiento y Contabilidad).', 'Revisión y evaluación del Acta de Conciliación de Bienes y Suministros, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 20:58:53', '2025-02-28 20:58:53'),
	(20, 92, 14, 'Decreto Legislativo Nº 1440, Decreto Legislativo del Sistema Nacional de Presupuesto Público.', 'Decreto Legislativo que genera obligación de:\r\n- Revisar y/o validar el Acta de Conciliación del\r\nMarco Legal y Ejecución del Presupuesto, con\r\nlas suscripciones de Contabilidad y Presupuesto', 'Revisión y evaluación del Acta de Conciliación del Marco Legal y Ejecución del Presupuesto, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:00:00', '2025-02-28 21:00:00'),
	(21, 92, 14, 'Decreto Legislativo Nº 1441, Decreto Legislativo del Sistema Nacional de Tesorería.', 'Decreto Legislativo que genera obligación de:\r\n- Revisar y/o validar el Acta de Conciliación con\r\nUnidad de Tesorería sobre cuentas corrientes y\r\ncon la Cuenta Única del Tesoro Púbico con la\r\nfuente de financiamiento que corresponda.', 'Revisión y evaluación del Acta de Conciliación con Unidad de Tesorería, antes de su aprobación.', 'Observación en la auditoría financiera.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:00:58', '2025-02-28 21:00:58'),
	(22, 92, 14, 'Decreto Supremo N° 057-2022-EF, Aprueban Texto Único Ordenado del Decreto Legislativo N° 1438 Decreto Legislativo del Sistema Nacional de Contabilidad.\r\nInstructivo Nº003-2024-EF/51.01, Resolución Directoral Nº 007-2024-\r\nEF/51.01.Instructivo para la presentación de la información financiera e\r\ninformación presupuestaria de las entidades del sector público durante el proceso\r\nde transición al Marco de las Normas Internacionales de Contabiliad del Sector\r\nPúblico.\r\nInstructivo Nº004-2024-EF/51.01, Resolución Directoral Nº 011-2024-EF/51.01\r\n"Manual de adopción por primera vez del Marco de las Normas Internacionales de\r\nContabilidad del Sector Público"', 'Decreto Legislativo que genera obligación de:\r\nElaboración de los Estados Financieros y Estados Presupuestarios de la entidad, y los plazos de presentación.', 'Elaboración y seguimiento del cronograma de trabajo de los estados financieros y presupuestarios.\r\nRequerimiento de información financiera y presupuestaria (memorándum, hojas informativas, correos), validados por la GAD.', 'La CGR puede ser considerada omisa ante la DGCP.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-28 21:02:22', '2025-02-28 21:04:43'),
	(23, 81, 14, 'Resolución Jefatural N° 107-2023-AGN/JEF, que aprueba la Directiva N° 001-2023-AGN/DDPA “Norma de administración de archivos en las entidades públicas”.', 'Se establece disposiciones para la Elaboración y actualización de normativa que regule los procesos archivísticos.', 'Profesional a cargo para las funciones consignadas.', 'Posibles observaciones por parte del Archivo General de la Nación a los Informes Técnicos de Evaluación de Actividades Archivísticos Ejecutadas (ITEA) anuales por ausencia y/o desactualización de normativa archivística para la CGR.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:33:53', '2025-03-05 23:33:53'),
	(24, 81, 14, 'Resolución Jefatural N°010-2020-AGN/J, que aprueba la Directiva N° 001-2020-AGN/DDPA “Norma para Servicios Archivísticos en la Entidad Pública".', 'Se establece el cumplimiento de brindar acceso a los documentos archivados considerando las restricciones por confidencialidad, datos personales y grado de deterioro del documento solicitado.', 'Procedimiento "Préstamo de Documentos del Archivo" (PR-TD-05), la actividad 2 señala que el responsable de la OUO en el MTD autoriza la solicitud de préstamo de documentos del archivo.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad, y afectación a los derechos de los involucrados. Denuncias por incidentes en uso de documentación que custodia los archivos de la CGR con carácter confidencial.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:36:53', '2025-03-05 23:36:53'),
	(25, 81, 14, 'Resolución Jefatural N° 304-2019-AGN/J, que aprueba la Directiva N° 001-2019-AGN/DC “Norma para la conservación de documentos en los archivos administrativos Sector Público Nacional”', 'Se establece los cumplimientos para adoptar medidas de conservación en lo referente a la sede o local, contenedores y mobiliario, conservación del soporte o medio físico, control de condiciones medioambientales y biológicos y preservación digital.', 'Dotar a los repositorios de Archivo de equipos de control medio ambientales (generadores ozono, deshumedecedores, termohigrómetros), Control de lecturas de temperatura y/o humedad. Recomendaciones respecto a los espacios a asignar para los Archivos.', 'Perdida o deterioro de documentos en el archivo.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:39:51', '2025-03-05 23:39:51'),
	(26, 81, 14, 'Resolución de Contraloría N" 220-2024-CG, que aprueba el Programa de Control de Documentos Archivísticos (PCDA) de la Contraloría General de la República.', 'Se establece obligaciones para custodiar la documentación remitida por las unidades de organización al cumplirse el plazo de retención en el archivo de gestión y por series documentales.', 'Programa de Control de Documentos Archivísticos (PCDA) de la CGR vigente.', 'Dificultades en el proceso de eliminación de \r\ndocumentos con valor temporal y la gestión a realizar ante el Archivo General de la Nación. Resguardo deficiente en el archivo de los documentos con valor permanente.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:40:55', '2025-03-05 23:40:55'),
	(27, 81, 14, 'Resolución Jefatural N°242-2018-AGN/J, que aprueba la Directiva N° 001-2018-AGN/DAI “Norma para la eliminación de documentos de archivo del sector público".', 'Se establece la obligación de presentar cronograma de eliminación de documentos en el Plan Anual de Trabajo Archivístico. - Solicitar al AGN o AR la autorización de eliminación a través del PCDA o Comité Evaluador de Documentos.', 'Procedimiento "Eliminación de Documentos del Archivo Central y Archivos Periféricos de las Gerencias Regionales de Control" PR-ARCH-05', 'Observaciones del Archivo General de la Nación por la inadecuada conservación de los documentos y mantenimiento de documentación fuera del plazo de retención establecido en el Programa de Control de Documentos Archivísticos de CGR.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-05 23:42:16', '2025-03-05 23:42:16'),
	(28, 219, 14, 'Contrato de préstamo N° 4724/OC-PE', 'Establece los mecanismos, procedimientos, instancias, responsabilidades y nomas que debe impartirse para la ejecución del proyecto, en al cual es de obligatorio el cumplimiento', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las condiciones del contrato para la ejecucion de los proyectos internosNO objeciones por parte del BID sobre elegibilidad de gastos', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:02:40', '2025-03-06 14:02:40'),
	(29, 219, 14, 'Contrato de préstamo N° 4724/OC-PE', 'Establece los acuerdos contractuales del préstamo para la ejecución del proyecto BID 3.', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las condiciones del contrato para la ejecucion de los proyectos internosNO objeciones por parte del BID sobre elegibilidad de gastos', 'Posible suspensión en la ejecucion del proyecto', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:03:38', '2025-03-06 14:03:38'),
	(30, 220, 14, 'Políticas BID para la Selección y Contratación de Consultores Financiados por el Banco Interamericano de Desarrollo. GN-2350-15', 'Establece las políticas de selección y contratación de consultores Financiados por el BID, en el marco del desarrollo del proyecto BID 3.', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las políticas de selección y contratación de consultores y de las condiciones del contrato de prestamoNO objeciones por parte del BID sobre las condiciones de las contrataciones de consultores individuales', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:04:20', '2025-03-24 22:28:43'),
	(31, 219, 14, 'Políticas BID para la Adquisición de Bienes y Obras financiadas por el Banco Interamericano de Desarrollo. GN-2349-15', 'Establece las políticas para la adquisición de bienes y obras financiadas por el BID, en el marco del desarrollo del proyecto BID', 'Revisiones multiples a nivel de SGIN sobre cumplimiento de las políticas para adquisición de bienes y obras y de las condiciones del contrato de prestamoNO objeciones por parte del BID sobre las condiciones de las contrataciones de adquisición de bienes y obras', 'Posible retraso en la ejecucion del proyecto', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-06 14:04:57', '2025-03-06 14:04:57'),
	(32, 30, 14, 'Normas Técnicas de CEPLAN, Procedimiento PR-PEI-01', 'Alinear el planeamiento estratégico de la CGR al Plan Nacional de Desarrollo e implementar las políticas institucionales de la CGR', 'Evaluación anual del cumplimiento de los indicadores', 'Falta de implementación de la Política Institucional', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:34:46', '2025-03-24 21:34:46'),
	(33, 32, 14, 'Directiva N° 001-2024 - CEPLAN/PCD Directiva General de Planeamiento Estrtaégico\r\nComunicado de CEPLAN-Dirección General de Abastecimiento del MEF', 'Alcanzar a la Subgerencia de Abastecimiento el archivo "TXT" con la programación operativa para el siguiente año, en el plazo establecido por CEPLAN-DGA del MEF (primera quincena del mes de marzo del año anterior)', 'Emisión de Memorando solicitando a los OUO de la CGR que elaboren su programación operativa para el siguiente año y que realicen la distribución del presupuesto asignado y el registro correspondiente en el aplicativo CEPLAN', 'Incumplimiento de la elaboración oportuna del Cuadro de Necesidades antes de la quincena del mes de marzo del año anterior. Percepción inadecuada de transparencia e integridad al no tomar en cuenta la Directiva de CEPLAN', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:38:41', '2025-03-24 21:38:41'),
	(34, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN\r\n(PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Aprobar el Plan Operativo Institucional Multianual dentro del plazo establecido, antes del 30 de abril del año anterior', 'Emisión de Memorando Circular por parte de la GMPL solicitando la elaboración del Plan Operativo Institucional Multianual del siguiente período y su registro en el aplicativo CEPLAN', 'Afectación del prestigio y transparencia de la Contraloría General', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:40:12', '2025-03-24 21:40:12'),
	(35, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN (PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Efectuar el Seguimiento del Plan Operativo Institucional en el aplicativo CEPLAN antes del día 20 del mes del siguiente mes', 'Seguimiento a los registros mensuales de avance de servicios de control y actividades en el aplicativo de la CGR y en el aplicativo de CEPLAN', 'Instancias competentes no cuenten con información oportuna para la toma de decisiones', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:41:07', '2025-03-24 21:41:07'),
	(36, 32, 14, 'Guía para el seguimiento y evaluación de políticas nacionales y planes del SINAPLAN (PR-POI-02) Procedimiento Seguimiento y evaluación del Plan Operativo Institucional y del Plan Nacional de Control', 'Efectuar la Evaluación Trimestral del Plan Operativo Institucional Anual', 'Seguimiento a los registros trimestrales de avance de servicios de control y actividades', 'Incumplimiento de elaborar la Evaluación del Plan Operativo Institucional a más tardar el siguiente mes de concluido el trimestre', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:42:01', '2025-03-24 21:42:01'),
	(37, 109, 13, 'Resolución de Contraloría N° 229-2022-CG que actualiza la Política de Gestión Antisoborno de la Contraloría General de la República', 'Cumplir con la Política y Objetivos de Gestión Antisoborno de la Contraloría General de la República', 'Auditorías internas y externas\r\nPlan de mantenimiento del SGAS', 'Incumplimiento de la norma ISO 37001:2016 y tener no conformidades.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:45:41', '2025-03-24 21:45:41'),
	(38, 109, 13, 'Norma ISO 37001:2016, Sistemas de gestión antisoborno – Requisitos con orientación para su uso.', 'Cumplir con los requisitos dispuestos por la norma internacional ISO 37001:2016', 'Auditorías internas y externas\r\nPlan de mantenimiento del SGAS', 'Incumplimiento de la norma ISO 37001:2016 y tener no conformidades.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 21:47:12', '2025-03-24 21:47:12'),
	(39, 40, 13, 'Política Nacional de Integridad y Lucha contra la Corrupción, aprobado mediante Decreto Supremo 092-2017-PCM.\r\nResolución de Contraloría N° 287-2021-CG, aprueba la Política de Integridad y ética Pública para los funcionarios y servidores públicos de la Contraloría General de la República y de los Órganos de Control Institucional.', 'Cumplir con las orientaciones vigentes descritas en la Política Nacional de Integridad y Lucha contra la Corrupción.\r\nCumplir con las orientaciones vigentes descritas en la Política de Integridad y ética Pública para los funcionarios y servidores públicos de la Contraloría General de la República y de los Órganos de Control Institucional.', 'Seguimiento al Programa de Integridad de la CGR', 'Nivel del Índice de Capacidad Preventiva (ICP) frente a la corrupción disminuido.\r\nNo obtener premio de ICP el próximo año.\r\nNivel de confianza en la labor de la Contraloría disminuido.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:38:53', '2025-03-24 22:38:53'),
	(40, 40, 13, 'Decreto Supremo N° 044-2018-PCM, Aprueba el Plan Nacional de Integridad y Lucha contra la Corrupción 2018-2021.\r\nResolución de secretaría de integridad pública 002-2021-PCM-SIP que aprueba la Directiva N 002-2021-PCM-SIP Lineamiento para establecer una cultura de integridad en las entidades del sector publico.\r\nDecreto Supremo N° 180-2021-PCM, Decreto Supremo que aprueba la Estrategia de Integridad del Poder Ejecutivo al 2022 para la Prevención de Actos de Corrupción.\r\nDecreto Supremo N° 148-2024-PCM, Decreto Supremo que aprueba el Modelo de Integridad para fortalecer la capacidad de prevención y respuesta frente a la corrupción en las entidades del sector público.', 'Cumplir y realizar el seguimiento de las orientaciones vigentes de cada uno de los nueve componentes del Modelo de Integridad, así como su supervisión.\r\nImpulsar actividades estratégicas de los componentes establecidos en el DS.', 'Seguimiento al Programa de Integridad de la CGR', 'Nivel del Índice de Capacidad Preventiva (ICP) frente a la corrupción disminuido.\r\nNo obtener premio de ICP el próximo año.\r\nNivel de confianza en la labor de la Contraloría disminuido.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:40:20', '2025-03-24 22:40:20'),
	(41, 276, 14, 'Decreto Legislativo N° 1327, que establece medidas de protección para el denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar preliminarmente las denuncias, verificando el cumplimiento de los requisitos establecidos en las normas, y derivarlas a las instancias respectivas, según corresponda.', 'Verificación diaria de la Plataforma Digital Única de Denuncias del Ciudadano.\r\nCoordinación con la Secretaría de Integridad Pública de la PCM', 'Afectación de la gestión de denuncias.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:53:53', '2025-03-24 22:53:53'),
	(42, 276, 14, 'Decreto Supremo N° 010-2017-JUS, que aprueba el Reglamento del Decreto Legislativo N° 1327 que establece medidas de protección al denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar preliminarmente las denuncias, verificando el cumplimiento de los requisitos establecidos en las normas, y derivarlas a las instancias respectivas, según corresponda.', 'PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', 'Afectación y pérdida de confianza de la gestión de denuncias.\r\nRiesgo en la seguridad, salud y vida del denunciante o de su familia.\r\nRiesgo de impunidad en el caso que amenazas contra el denunciante causen el retiro o el desistimiento de la denuncia.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:55:00', '2025-03-24 22:55:00'),
	(43, 276, 14, 'Decreto Supremo N° 002-2020-JUS, que modifica el Reglamento del Decreto Legislativo Nº 1327 que establece medidas de protección al denunciante de actos de corrupción y sanciona las denuncias realizadas de mala fe.', 'Evaluar las solicitudes de medidas de protección, y otorgarlas según las normas establecidas y según corresponda.', 'Numeral 6.10 del procedimiento PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', 'Afectación de la gestión de denuncias.\r\nRiesgo en la seguridad personal y/o laboral, salud y vida del denunciante o de su familia', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 22:55:50', '2025-03-24 23:05:35'),
	(44, 276, 14, 'Directiva N° 002-2023-PCM-SIP, Directiva para la gestión de denuncias y solicitudes de medidas de protección al denunciante de actos de corrupción recibidas a través de la plataforma digital única de denuncias al ciudadano, aprobada mediante la Resolución de Secretaría de Integridad Pública N° 005-2023-PCM-SIP.', 'Evaluar las solicitudes de medidas de protección, y otorgarlas según las normas establecidas y según corresponda.', 'Numeral 6.10 del procedimiento PR-ACH-01 Procedimiento "Evaluación, Atención y Seguimiento de Denuncias por Actos de Corrupción contra Colaboradores de la Contraloría General de la República"', '"Afectación y pérdida de confianza de la gestión de denuncias.\r\nRiesgo en la seguridad personal y/o laboral, salud y vida del denunciante o de su familia.\r\nRiesgo de impunidad en el caso de las amenazas contra el denunciante, que causen el retiro o desistimiento, o la ratificación de la denuncia."', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:03:11', '2025-03-24 23:04:44'),
	(45, 78, 14, 'Directiva N° 009-2022-CG/DOC “Gestión Documental de la Contraloría General de la República”, aprobado con RC N° 169-2022-CG.\r\nTUPA de la Contraloría General de la República aprobado con RC N° 237-2022-CG.\r\nTUO de la Ley N° 27444, Ley del Procedimiento Administrativo General, aprobado por DS N° 004-2019-JUS.', 'Establece los criterios de recepción, emisión de documentos presentados en la CGR.', 'Reforzar los conocimientos, mediante la capacitación sobre la directiva vigente y aplicable al riesgo señalado, procedimiento "Generación de Expedientes" PR-TD-01. y la Ley 27444, dirigido al personal de recepción de documentos.', 'Incumplimiento de los plazos legales de atención y respuesta de los documentos por parte de las unidades orgánicas responsables de la evaluación, y posibles sanciones.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:53:44', '2025-03-24 23:53:44'),
	(46, 78, 14, 'Manual de Documentos de Acceso Restringido de la Contraloría General de la República, aprobado con Resolución de Contraloría N° 091-2004-CG y modificatoria', 'Establece los requisitos de recepción y derivación de documentos, presentados y clasificados como reservados, secretos y confidenciales.', 'Reforzar los conocimientos, mediante la capacitación sobre la directiva vigente, procedimiento "Generación de Expedientes" PR-TD-01. y la Ley 27444, dirigido al personal de recepción de documentos.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:55:25', '2025-03-24 23:55:25'),
	(47, 78, 14, '"Manual para Mejorar la Atención a la Ciudadanía" en las entidades de la Administración Pública, aprobado mediante RM N° 186-2015-PCM\r\nRSGP Nº 001-2015-PCM-SGP que aprueban los lineamientos para el proceso de implementación progresiva del Manual para Mejorar la Atención a la Ciudadanía en las entidades de la administración pública\r\nDirectriz para el uso de la Plataforma de Mesa de Partes Virtual de la Contraloría General de la República, aprobada mediante Resolución de Secretaría General N° 090-2020-CG/SGE', 'Garantizan el proceso de atención a la ciudadanía, a través de los canales de atención.', 'No hay controles', 'Inadecuada atención y orientación a la ciudadanía.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-24 23:56:57', '2025-03-24 23:56:57'),
	(48, 78, 14, 'Directiva N° 013-2015-CG/GPROD “Presentación, procesamiento y archivo de Las declaraciones juradas de ingresos y de bienes y rentas de los funcionarios y servidores públicos del estado”, aprobado mediante RC N° 328-2015-CG.\r\nDirectiva N° 010-2018-CG/GDET “Declaraciones Juradas para la Gestión de Conflicto de Intereses”, aprobado mediante RC N° 480-2018-CG y sus modificatoria RC N° 063-2019-CG y RC N° 095-2020-CG.', 'Establece los requisitos para una correcta presentación de las declaraciones juradas, verificado por mesa de partes.', 'Reforzar los conocimientos, mediante la capacitación sobre el procedimiento "Generación de Expedientes" PR-TD-01, dirigido al personal de recepción de documentos.', 'Incumplimiento de los plazos legales para la atención, derivación y trámite de las Declaraciones Juradas obligatorias de los servidores públicos y entidades.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:24:11', '2025-03-25 00:24:11'),
	(49, 78, 14, 'Ley 29542 “Ley de Protección al Denunciante en el Ámbito Administrativo y de Colaboración Eficaz en el Ámbito Penal”', 'Ejecutar los requisitos de protección al denunciante al recibir la denuncia administrativa del usuario.', 'Capacitación de "Ética e Integridad", dirigida al área de mesa de partes.', 'Violación de la confidencialidad, posibles sanciones por filtración de información, pérdida de confianza y reputación de la entidad, y afectación a los derechos de los involucrados. Denuncias por incidentes en uso de documentación que custodia los archivos de la CGR con carácter confidencial.', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:25:09', '2025-03-25 00:25:09'),
	(50, 118, 14, 'Resolución jefatural N° 386.2002-INEI, que aprueba la Directiva N°016-2002-INEI/DTNP Normas técnicas para el almacenamiento y Respaldo de la Información procesada por las entidades de la Administración Pública.', 'Tomar las medidas necesarias para proteger y salvaguardar la integridad de los respaldos de la información', 'Mantenimiento preventivo de hardware y software, pruebas semestrales de recuperación de los respaldos\r\nPR-TI-06 Procedimiento "Respaldo y Restauración de Información"', 'No se logra recuperar la información requerida', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:35:57', '2025-03-25 00:35:57'),
	(51, 118, 14, 'Contrato de servicio de correo electrónico (solución de respaldo)', 'Tomar las medidas necesarias para proteger y salvaguardar la integridad de los respaldos de la información de los correos electrónicos', 'Reportar al proveedor mediante ticket para que tome acciones de corrección y hacer seguimiento hasta la corrección.', 'No se logra recuperar la información requerida en un tiempo adecuado', NULL, 'pendiente', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-25 00:36:57', '2025-03-25 00:36:57');

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
  `ouo_cod_padre` varchar(50) DEFAULT NULL,
  `ouo_sigla` varchar(50) DEFAULT NULL,
  `nivel_jerarquico` int(11) NOT NULL,
  `doc_vigencia_alta` varchar(255) DEFAULT NULL,
  `fecha_vigencia_inicio` date NOT NULL,
  `doc_vigencia_baja` varchar(255) DEFAULT NULL,
  `fecha_vigencia_fin` date DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ouo_ouo_padre_foreign` (`ouo_padre`),
  CONSTRAINT `ouo_ouo_padre_foreign` FOREIGN KEY (`ouo_padre`) REFERENCES `ouos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.ouos: ~98 rows (aproximadamente)
REPLACE INTO `ouos` (`id`, `ouo_nombre`, `ouo_codigo`, `ouo_padre`, `ouo_cod_padre`, `ouo_sigla`, `nivel_jerarquico`, `doc_vigencia_alta`, `fecha_vigencia_inicio`, `doc_vigencia_baja`, `fecha_vigencia_fin`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
	(1, 'Despacho del Contralor', 'D100', NULL, NULL, 'DC', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(2, 'Tribunal Superior de Responsabilidades Administrativas', 'E300', 1, 'D100', 'TSRA', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(3, 'Órgano de Auditoría Interna', 'D200', 1, 'D100', 'AI', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(4, 'Procuraduría Pública', 'D900', 1, 'D100', 'PP', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(5, 'Oficina de Integridad Institucional', 'A260', 1, 'D100', 'OINT', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(6, 'Oficina de Gestión de la Potestad Administrativa Sancionadora', 'E200', 1, 'D100', 'OGPAS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(7, 'Órgano Instructor', 'E210', 1, 'D100', 'OINS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(8, 'Órgano Sancionador', 'E220', 1, 'D100', 'OSAN', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(9, 'Dirección General Escuela Nacional de Control', 'D400', 1, 'D100', 'DGENC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(10, 'Subdirección de Posgrado y Servicios Académicos', 'D403', 9, 'D400', 'ENCP', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(11, 'Secretaría General', 'D300', 1, 'D100', 'SGE', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(12, 'Oficina de Seguridad y Defensa Nacional', 'D531', 11, 'D300', 'SEG', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(13, 'Gerencia de Administración', 'C200', 11, 'D300', 'GAD', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(14, 'Subgerencia de Abastecimiento', 'D530', 13, 'C200', 'ABAS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(15, 'Subgerencia de Gestión Documentaria', 'D320', 13, 'C200', 'DOC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(16, 'Unidad de Contabilidad', 'D521', 13, 'C200', 'CONT', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(17, 'Unidad de Tesorería', 'D522', 13, 'C200', 'TESO', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(18, 'Unidad de Gestión Administrativa de Sociedades de Auditoria', 'L510', 13, 'C200', 'GSOA', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(19, 'Gerencia de Capital Humano', 'D550', 11, 'D300', 'GCH', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(20, 'Subgerencia de Políticas y Desarrollo Humano', 'D517', 19, 'D550', 'POLDEH', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(21, 'Subgerencia de Personal y Compensaciones', 'D510', 19, 'D550', 'PER', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(22, 'Subgerencia de Bienestar y Relaciones Laborales', 'D511', 19, 'D550', 'BRL', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(23, 'Gerencia de Tecnologías de la Información y Gobierno Digital', 'D600', 11, 'D300', 'GTIGD', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(24, 'Subgerencia de Operaciones y Plataforma Tecnológica', 'D602', 23, 'D600', 'OPTEC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(25, 'Subgerencia de Sistemas de Información y Analítica de Datos', 'D610', 23, 'D600', 'SINFA', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(26, 'Unidad de Atención Digital y Soporte Técnico ', 'D604', 23, 'D600', 'ADST', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(27, 'Gerencia de Comunicaciones', 'C401', 11, 'D300', 'GCOM', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(28, 'Subgerencia de Comunicación y Medios Digitales', 'C402', 27, 'C401', 'SCMD', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:39'),
	(29, 'Subgerencia de Imagen y Relaciones Corporativas', 'D310', 27, 'C401', 'IRC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(30, 'Unidad de Transparencia y Acceso a la Información Pública', 'A261', 27, 'C401', 'UTAIP', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(31, 'Gerencia de Asesoría Jurídica y Normatividad en Control Gubernamental', 'D700', 11, 'D300', 'GJNC', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(32, 'Subgerencia de Asesoría Jurídica', 'D710', 31, 'D700', 'AJ', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(33, 'Subgerencia de Normatividad en Control Gubernamental', 'C312', 31, 'D700', 'NORM', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(34, 'Gerencia de Modernización y Planeamiento', 'L527', 11, 'D300', 'GMPL', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(35, 'Subgerencia de Planeamiento, Presupuesto y Programación de Inversiones', 'L520', 34, 'L527', 'PLPREPI', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(36, 'Subgerencia de Modernización', 'C321', 34, 'L527', 'MODER', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(37, 'Subgerencia de Gestión de Riesgos', 'C326', 34, 'L527', 'GERI', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(38, 'Subgerencia de Seguimiento y Evaluación del Sistema Nacional de Control', 'L590', 34, 'L527', 'SESNC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(39, 'Gerencia de Relaciones Institucionales', 'C381', 11, 'D300', 'GRI', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(40, 'Subgerencia de Coordinación Parlamentaria', 'C380', 39, 'C381', 'CPAR', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(41, 'Subgerencia de Coordinación Institucional Nacional', 'C382', 39, 'C381', 'CIN', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(42, 'Subgerencia de Cooperación y Asuntos Internacionales', 'D800', 39, 'C381', 'CASI', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(43, 'Gerencia de Gestión de Proyectos', 'D500', 11, 'D300', 'GPROY', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(44, 'Subgerencia de Infraestructura', 'D502', 43, 'D500', 'INFRA', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(45, 'Subgerencia de Gestión de Inversiones', 'C322', 43, 'D500', 'SGIN', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(46, 'Unidad de Formulación de Inversiones', 'C325', 43, 'D500', 'UFI', 4, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(47, 'Vicecontraloría de Control Gubernamental', 'L100', 1, 'D100', 'VCG', 1, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(48, 'Oficina de Aseguramiento de la Calidad de Servicios de Control', 'L157', 47, 'L100', 'ACAL', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(49, 'Oficina de Control Especializado', 'L153', 47, 'L100', 'OCE', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(50, 'Oficina de Supervisión Técnica de Auditoría Financiera', 'L154', 47, 'L100', 'OSTAF', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(51, 'Gerencia de Prevención y Control Social', 'C601', 47, 'L100', 'GPCS', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(52, 'Subgerencia de Prevención', 'C370', 51, 'C601', 'PREV', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(53, 'Subgerencia de Participación Ciudadana y Control Social', 'C600', 51, 'C601', 'PCCS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(54, 'Gerencia de Gestión y Fiscalización de Declaraciones Juradas', 'C124', 47, 'L100', 'GFDJ', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(55, 'Subgerencia de Gestión de Declaraciones Juradas', 'C122', 54, 'C124', 'GDJ', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(56, 'Subgerencia de Fiscalización', 'L540', 54, 'C124', 'FIS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(57, 'Gerencia de Control Político Institucional y Económico', 'L301', 47, 'L100', 'GPOIN', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(58, 'Subgerencia de Control del Sector Seguridad Interna y Externa', 'L340', 57, 'L301', 'SIE  ', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(59, 'Subgerencia de Control del Sector Justicia, Político y Electoral', 'L352', 57, 'L301', 'JUSPE', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(60, 'Subgerencia de Control del Sector Social y Cultura', 'L315', 57, 'L301', 'SOCC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(61, 'Subgerencia de Control del Sector Económico y Financiero', 'L320', 57, 'L301', 'ECOFI', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(62, 'Subgerencia de Control del Sector Productivo y Trabajo', 'L330', 57, 'L301', 'PROT', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(63, 'Gerencia de Control de Servicios Públicos Básicos', 'L303', 47, 'L100', 'GCSPB', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(64, 'Subgerencia de Control del Sector Transportes y Comunicaciones', 'L331', 63, 'L303', 'TRACO', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(65, 'Subgerencia de Control del Sector Vivienda, Construcción y Saneamiento', 'L336', 63, 'L303', 'VICOS', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(66, 'Subgerencia de Control del Sector Agricultura y Ambiente', 'L332', 63, 'L303', 'AGR', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(67, 'Subgerencia de Control del Sector Educación', 'L351', 63, 'L303', 'EDUC', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(68, 'Subgerencia de Control del Sector Salud', 'L316', 63, 'L303', 'SALUD', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(69, 'Gerencia de Control de Megaproyectos', 'L304', 47, 'L100', 'GCMEGA', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(70, 'Subgerencia de Control de Megaproyectos', 'L334', 69, 'L304', 'MPROY', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(71, 'Subgerencia de Control de Asociaciones Público-Privadas y Obras por Impuestos', 'C920', 69, 'L304', 'APP', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(72, 'Subgerencia de Control Previo de Adicionales de Obra y Supervisión de Obra', 'L556', 69, 'L304', 'SCP', 3, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(73, 'Gerencia Regional de Control de Lima Metropolitana', 'L401', 47, 'L100', 'GRLIM', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(74, 'Gerencia Regional de Control del Callao', 'C824', 47, 'L100', 'GRCLL', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(75, 'Gerencia Regional de Control de Lima Provincias', 'C823', 47, 'L100', 'GRLP', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(76, 'Gerencia Regional de Control de Ancash', 'L425', 47, 'L100', 'GRAN', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(77, 'Gerencia Regional de Control de Ica', 'L445', 47, 'L100', 'GRIC', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(78, 'Gerencia Regional de Control de Loreto', 'L440', 47, 'L100', 'GRLO', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(79, 'Gerencia Regional de Control de Lambayeque', 'L430', 47, 'L100', '   GRLA', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(80, 'Gerencia Regional de Control de Tumbes', 'L422', 47, 'L100', 'GRTB', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(81, 'Gerencia Regional de Control de Piura', 'L420', 47, 'L100', 'GRPI', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(82, 'Gerencia Regional de Control de La Libertad', 'L495', 47, 'L100', 'GRLIB', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(83, 'Gerencia Regional de Control de Cajamarca', 'L435', 47, 'L100', 'GRCA', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(84, 'Gerencia Regional de Control de San Martín', 'L450', 47, 'L100', 'GRSM', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(85, 'Gerencia Regional de Control de Amazonas', 'L452', 47, 'L100', 'GRAM', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(86, 'Gerencia Regional de Control de Junín', 'L460', 47, 'L100', 'GRJU', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(87, 'Gerencia Regional de Control de Ayacucho', 'L490', 47, 'L100', 'GRAY', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(88, 'Gerencia Regional de Control de Huánuco', 'L465', 47, 'L100', 'GRHC', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(89, 'Gerencia Regional de Control de Huancavelica', 'L446', 47, 'L100', 'GRHV', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(90, 'Gerencia Regional de Control de Ucayali', 'L466', 47, 'L100', 'GRUC', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(91, 'Gerencia Regional de Control de Pasco', 'L467', 47, 'L100', 'GRPA', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(92, 'Gerencia Regional de Control de Arequipa', 'L470', 47, 'L100', 'GRAR', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(93, 'Gerencia Regional de Control de Cusco', 'L480', 47, 'L100', 'GRCU', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(94, 'Gerencia Regional de Control de Puno', 'L455', 47, 'L100', 'GRPU', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(95, 'Gerencia Regional de Control de Tacna', 'L475', 47, 'L100', 'GRTA', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(96, 'Gerencia Regional de Control de Moquegua', 'L476', 47, 'L100', 'GRMQ', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(97, 'Gerencia Regional de Control de Apurímac', 'L485', 47, 'L100', 'GRAP', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40'),
	(98, 'Gerencia Regional De Control de Madre de Dios', 'L482', 47, 'L100', 'GRMD', 2, '274-2025-CG', '2025-09-08', NULL, NULL, 1, NULL, '2026-01-19 21:36:39', '2026-01-19 21:36:40');

-- Volcando estructura para tabla kallpaq.ouo_user
CREATE TABLE IF NOT EXISTS `ouo_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ouo_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_in_ouo` enum('titular','encargado','facilitador','colaborador') DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ouo_user_ouo_id_user_id_unique` (`ouo_id`,`user_id`),
  KEY `ouo_user_user_id_foreign` (`user_id`),
  CONSTRAINT `ouo_user_ouo_id_foreign` FOREIGN KEY (`ouo_id`) REFERENCES `ouos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ouo_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.ouo_user: ~6 rows (aproximadamente)
REPLACE INTO `ouo_user` (`id`, `ouo_id`, `user_id`, `role_in_ouo`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 85, 1, '', 1, NULL, NULL, NULL),
	(3, 85, 8, '', 1, NULL, NULL, NULL),
	(4, 85, 3, '', 1, NULL, '2026-01-18 18:38:41', NULL),
	(5, 85, 5, '', 1, NULL, NULL, NULL),
	(6, 82, 14, 'facilitador', 1, NULL, '2026-01-18 22:08:13', NULL),
	(7, 5, 16, 'facilitador', 1, NULL, NULL, NULL),
	(8, 5, 15, 'facilitador', 1, NULL, '2026-01-19 22:06:21', NULL),
	(9, 13, 17, 'facilitador', 1, NULL, '2026-01-19 22:17:19', NULL),
	(10, 13, 18, 'facilitador', 1, NULL, '2026-01-19 22:17:15', NULL),
	(11, 13, 19, 'facilitador', 1, NULL, '2026-01-19 22:18:43', NULL),
	(12, 13, 20, 'facilitador', 1, NULL, '2026-01-19 22:18:39', NULL),
	(13, 13, 21, 'facilitador', 1, NULL, '2026-01-19 22:18:36', NULL),
	(14, 13, 22, 'facilitador', 1, NULL, '2026-01-19 22:18:32', NULL),
	(15, 48, 14, 'facilitador', 1, NULL, '2026-01-19 22:27:34', NULL),
	(16, 48, 23, 'facilitador', 1, NULL, '2026-01-19 22:27:30', NULL),
	(17, 12, 24, 'facilitador', 1, NULL, '2026-01-19 22:31:45', NULL),
	(18, 12, 25, 'facilitador', 1, NULL, '2026-01-19 22:31:49', NULL),
	(19, 4, 26, 'facilitador', 1, NULL, '2026-01-19 22:40:21', NULL),
	(20, 4, 27, 'facilitador', 1, NULL, '2026-01-19 22:40:17', NULL),
	(21, 14, 28, 'facilitador', 1, NULL, '2026-01-19 23:04:30', NULL),
	(22, 14, 29, 'facilitador', 1, NULL, '2026-01-19 23:04:30', NULL),
	(23, 14, 30, 'facilitador', 1, NULL, '2026-01-19 23:04:30', NULL),
	(24, 14, 31, 'facilitador', 1, NULL, '2026-01-19 23:04:30', NULL),
	(25, 14, 32, 'facilitador', 1, NULL, '2026-01-19 23:04:30', NULL),
	(26, 24, 40, 'colaborador', 1, NULL, NULL, NULL),
	(27, 19, 38, 'facilitador', 1, NULL, NULL, NULL),
	(28, 12, 35, 'facilitador', 1, NULL, NULL, NULL),
	(29, 20, 37, 'facilitador', 1, NULL, NULL, NULL),
	(30, 20, 36, 'facilitador', 1, NULL, NULL, NULL),
	(31, 25, 33, 'facilitador', 1, NULL, NULL, NULL),
	(32, 24, 34, 'facilitador', 1, NULL, NULL, NULL);

-- Volcando estructura para tabla kallpaq.ouo_user_movimientos
CREATE TABLE IF NOT EXISTS `ouo_user_movimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ouo_user_id` bigint(20) unsigned NOT NULL,
  `cambiado_por` bigint(20) unsigned NOT NULL,
  `tipo_anterior` varchar(255) DEFAULT NULL,
  `tipo_nuevo` varchar(255) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ouo_user_movimientos_ouo_user_id_foreign` (`ouo_user_id`),
  KEY `ouo_user_movimientos_cambiado_por_foreign` (`cambiado_por`),
  CONSTRAINT `ouo_user_movimientos_cambiado_por_foreign` FOREIGN KEY (`cambiado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `ouo_user_movimientos_ouo_user_id_foreign` FOREIGN KEY (`ouo_user_id`) REFERENCES `ouo_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.ouo_user_movimientos: ~5 rows (aproximadamente)
REPLACE INTO `ouo_user_movimientos` (`id`, `ouo_user_id`, `cambiado_por`, `tipo_anterior`, `tipo_nuevo`, `motivo`, `fecha_cambio`) VALUES
	(1, 4, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-18 13:36:12'),
	(2, 4, 1, NULL, 'colaborador', 'Asignación de usuario a OUO restaurada y actualizada', '2026-01-18 13:38:41'),
	(3, 5, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-18 13:44:07'),
	(4, 6, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-18 17:08:08'),
	(5, 7, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:02:09'),
	(6, 8, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:06:12'),
	(7, 9, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:16:52'),
	(8, 10, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:17:06'),
	(9, 11, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:17:33'),
	(10, 12, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:17:49'),
	(11, 13, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:18:02'),
	(12, 14, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:18:18'),
	(13, 15, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:27:05'),
	(14, 16, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:27:19'),
	(15, 17, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:31:24'),
	(16, 18, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:31:36'),
	(17, 19, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:39:29'),
	(18, 20, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:40:10'),
	(19, 21, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:55:39'),
	(20, 22, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:55:54'),
	(21, 23, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:56:06'),
	(22, 24, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:56:17'),
	(23, 25, 1, NULL, 'colaborador', 'Asignación inicial de usuario a OUO', '2026-01-19 17:56:33'),
	(24, 21, 1, 'colaborador', 'facilitador', 'Actualización masiva de rol/estado', '2026-01-19 18:04:30'),
	(25, 22, 1, 'colaborador', 'facilitador', 'Actualización masiva de rol/estado', '2026-01-19 18:04:30'),
	(26, 23, 1, 'colaborador', 'facilitador', 'Actualización masiva de rol/estado', '2026-01-19 18:04:30'),
	(27, 24, 1, 'colaborador', 'facilitador', 'Actualización masiva de rol/estado', '2026-01-19 18:04:30'),
	(28, 25, 1, 'colaborador', 'facilitador', 'Actualización masiva de rol/estado', '2026-01-19 18:04:30'),
	(29, 26, 1, NULL, 'colaborador', 'Asignación masiva inicial', '2026-01-21 12:21:18'),
	(30, 27, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:27:22'),
	(31, 28, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:28:44'),
	(32, 29, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:38:52'),
	(33, 30, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:38:52'),
	(34, 31, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:42:08'),
	(35, 32, 1, NULL, 'facilitador', 'Asignación masiva inicial', '2026-01-21 12:45:24');

-- Volcando estructura para tabla kallpaq.partes_interesadas
CREATE TABLE IF NOT EXISTS `partes_interesadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pi_nombre` varchar(255) NOT NULL,
  `pi_tipo` enum('interna','externa','cliente','proveedor','regulador') NOT NULL DEFAULT 'interna',
  `pi_nivel_influencia` enum('bajo','medio','alto') DEFAULT NULL,
  `pi_nivel_interes` enum('bajo','medio','alto') DEFAULT NULL,
  `pi_cuadrante` varchar(5) DEFAULT NULL,
  `pi_valoracion` varchar(255) DEFAULT NULL,
  `pi_descripcion` text DEFAULT NULL,
  `pi_activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
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
REPLACE INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('manuelperez@contraloria.gob.pe', '$2y$10$2VDUCJGTA.3qngMNge5dR.zc8xBgVush4hygEMwmaeS2m9xU4Z0oi', '2026-01-20 19:56:09');

-- Volcando estructura para tabla kallpaq.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.permissions: ~57 rows (aproximadamente)
REPLACE INTO `permissions` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'menu.requerimientos', 'Acceso al módulo requerimientos', 'web', '2023-08-25 20:55:30', '2026-01-18 14:42:07'),
	(2, 'menu.mejora', 'Acceso al módulo mejora', 'web', '2023-08-25 20:55:30', '2026-01-18 14:42:07'),
	(3, 'menu.obligaciones', 'Acceso al módulo obligaciones', 'web', '2023-08-25 20:55:30', '2026-01-18 14:42:07'),
	(4, 'menu.riesgos', 'Acceso al módulo riesgos', 'web', '2023-08-25 20:55:30', '2026-01-18 14:42:07'),
	(5, 'menu.procesos', 'Acceso al módulo procesos', 'web', '2026-01-17 00:07:53', '2026-01-18 14:42:07'),
	(6, 'menu.continuidad', 'Acceso al módulo continuidad', 'web', '2026-01-17 00:09:48', '2026-01-18 14:42:07'),
	(7, 'menu.satisfaccion', 'Acceso al módulo satisfaccion', 'web', '2026-01-17 00:09:48', '2026-01-18 14:42:07'),
	(8, 'menu.auditoria', 'Acceso al módulo auditoria', 'web', '2026-01-17 00:09:49', '2026-01-18 14:42:07'),
	(9, 'menu.innovacion', 'Acceso al módulo innovacion', 'web', '2026-01-17 00:15:13', '2026-01-18 14:42:07'),
	(10, 'menu.direccion', 'Acceso al módulo direccion', 'web', '2026-01-17 00:10:52', '2026-01-18 14:42:07'),
	(11, 'menu.administracion', 'Acceso al módulo administracion', 'web', '2026-01-17 00:10:53', '2026-01-18 14:42:07'),
	(13, 'menu.documentacion.inventario', 'Ver Inventario de Procesos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(14, 'menu.documentacion.mapa', 'Ver Mapa de Procesos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(15, 'menu.documentacion.documentos', 'Ver Listado de documentos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(16, 'menu.requerimientos.bandeja', 'Ver Bandeja de Requerimientos (Admin)', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(17, 'menu.requerimientos.crear', 'Ver menú Crear Requerimiento', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(18, 'menu.requerimientos.mis_requerimientos', 'Ver menú Mis Requerimientos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(19, 'menu.requerimientos.mis_asignados', 'Ver menú Mis Req. Asignados', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(20, 'menu.requerimientos.dashboard', 'Ver Dashboard Requerimientos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(21, 'menu.procesos.inventario', 'Ver Gestión del Inventario', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(22, 'menu.procesos.listado', 'Ver Listado de Procesos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(23, 'menu.procesos.documentos', 'Ver Listado de Documentos (Gestión)', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(24, 'menu.procesos.lmde', 'Ver Listado de Doc Externos (LMDE)', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(25, 'menu.procesos.indicadores', 'Ver Listado de Indicadores', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(26, 'menu.procesos.partes', 'Ver Partes Interesadas', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(27, 'menu.procesos.dashboard', 'Ver Dashboard Procesos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(28, 'menu.mejora.bandeja', 'Ver Bandeja de SMP', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(29, 'menu.mejora.mis_solicitudes', 'Ver Mis Solicitudes de Mejora', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(30, 'menu.mejora.mis_asignados', 'Ver Mis SMP Asignados', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(31, 'menu.mejora.dashboard', 'Ver Dashboard de Mejora', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(32, 'menu.obligaciones.bandeja', 'Ver Bandeja de Obligaciones', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(33, 'menu.obligaciones.mis_obligaciones', 'Ver Mis Obligaciones', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(34, 'menu.obligaciones.seguimiento', 'Ver Seguimiento de Acciones', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(35, 'menu.obligaciones.dashboard', 'Ver Dashboard Obligaciones', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(36, 'menu.riesgos.bandeja', 'Ver Bandeja de Riesgos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(37, 'menu.riesgos.mis_asignados', 'Ver Mis Riesgos Asignados', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(38, 'menu.riesgos.eficacia', 'Ver Verificar Eficacia Riesgos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(39, 'menu.riesgos.dashboard', 'Ver Dashboard Riesgos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(40, 'menu.auditoria.programa', 'Ver Programa Anual', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(41, 'menu.auditoria.auditores', 'Ver Listado de Auditores', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(42, 'menu.auditoria.normas', 'Ver Normas Auditables', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(43, 'menu.continuidad.planes', 'Ver Planes de Continuidad', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(44, 'menu.continuidad.escenarios', 'Ver Escenarios de Riesgo', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(45, 'menu.continuidad.activos', 'Ver Activos Críticos', 'web', '2026-01-18 14:25:15', '2026-01-18 14:25:15'),
	(46, 'menu.continuidad.pruebas', 'Ver Pruebas y Ejercicios', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(47, 'menu.continuidad.dashboard', 'Ver Dashboard Continuidad', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(48, 'menu.satisfaccion.encuestas', 'Ver Encuestas de Satisfacción', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(49, 'menu.satisfaccion.sugerencias', 'Ver Consolidado Sugerencias', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(50, 'menu.satisfaccion.salidas_nc', 'Ver Salidas No Conformes', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(51, 'menu.satisfaccion.reporte', 'Ver Reporte Trimestral', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(52, 'menu.direccion.revision', 'Ver Revisión por la Dirección', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(53, 'menu.administracion.usuarios', 'Ver Gestionar Usuarios', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(54, 'menu.administracion.roles', 'Ver Gestionar Roles', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(55, 'menu.administracion.asignacion', 'Ver Gestionar OUO', 'web', '2026-01-18 14:25:16', '2026-01-18 17:35:41'),
	(56, 'menu.administracion.configuracion', 'Ver Configuración General', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(57, 'menu.administracion.dashboard', 'Ver Dashboard Administración', 'web', '2026-01-18 14:25:16', '2026-01-18 14:25:16'),
	(58, 'menu.documentacion', 'Acceso al módulo Documentación', 'web', '2026-01-18 14:42:07', '2026-01-18 14:42:07');

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

-- Volcando estructura para tabla kallpaq.planes_continuidad
CREATE TABLE IF NOT EXISTS `planes_continuidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `objetivo` text NOT NULL,
  `tipo_plan` enum('bcp','drp','irp','crmp') NOT NULL,
  `escenario_id` bigint(20) unsigned DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `responsable_id` bigint(20) unsigned NOT NULL,
  `alcance` text DEFAULT NULL,
  `equipo_respuesta` text DEFAULT NULL,
  `procedimientos_activacion` text DEFAULT NULL,
  `procedimientos_recuperacion` text DEFAULT NULL,
  `recursos_necesarios` text DEFAULT NULL,
  `comunicacion_crisis` text DEFAULT NULL,
  `documento_path` varchar(255) DEFAULT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `fecha_aprobacion` date DEFAULT NULL,
  `proxima_revision` date DEFAULT NULL,
  `estado` enum('borrador','en_revision','aprobado','obsoleto') NOT NULL DEFAULT 'borrador',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `planes_continuidad_codigo_unique` (`codigo`),
  KEY `planes_continuidad_escenario_id_foreign` (`escenario_id`),
  KEY `planes_continuidad_proceso_id_foreign` (`proceso_id`),
  KEY `planes_continuidad_responsable_id_foreign` (`responsable_id`),
  KEY `planes_continuidad_created_by_foreign` (`created_by`),
  CONSTRAINT `planes_continuidad_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `planes_continuidad_escenario_id_foreign` FOREIGN KEY (`escenario_id`) REFERENCES `escenarios_continuidad` (`id`) ON DELETE SET NULL,
  CONSTRAINT `planes_continuidad_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `planes_continuidad_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.planes_continuidad: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.planificacion_pei
CREATE TABLE IF NOT EXISTS `planificacion_pei` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pp_cod` varchar(255) NOT NULL,
  `pp_nombre` varchar(255) NOT NULL,
  `pp_alcance` varchar(255) NOT NULL,
  `pp_documento_aprueba` varchar(255) NOT NULL,
  `pp_fecha_aprueba` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.planificacion_pei: ~4 rows (aproximadamente)
REPLACE INTO `planificacion_pei` (`id`, `pp_cod`, `pp_nombre`, `pp_alcance`, `pp_documento_aprueba`, `pp_fecha_aprueba`, `created_at`, `updated_at`) VALUES
	(1, 'OEI.01', 'Mejorar la cobertura y calidad de los servicios de control gubernamental en las entidades públicas sujetas al Sistema Nacional de Control', '2025 -2030', 'RC-258-2025-CG.pdf', '2026-06-27 23:56:05', '2025-02-17 19:25:17', NULL),
	(2, 'OEI.02', 'Promover la transparencia e integridad en la gestión pública y la participación activa de la ciudadanía en el control gubernamental', '2025 -2030', 'RC-258-2025-CG.pdf', '2026-06-27 23:56:05', '2025-02-17 19:25:17', NULL),
	(3, 'OEI.03', 'Modernizar la gestión y cultura institucional', '2025 -2030', 'RC-258-2025-CG.pdf', '2026-06-27 23:56:05', '2025-02-17 19:25:17', NULL),
	(4, 'OEI.04', 'Optimizar los procesos de soporte del control gubernamental mediante la Escuela', '2025 -2030', 'RC-258-2025-CG.pdf', '2026-06-27 23:56:05', '2025-02-17 19:25:17', NULL);

-- Volcando estructura para tabla kallpaq.planificacion_sig
CREATE TABLE IF NOT EXISTS `planificacion_sig` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `objetivo_sig_cod` varchar(255) NOT NULL,
  `sistema` enum('sgas','sgc','sgsi','sgco','sgcm') NOT NULL,
  `objetivo_sig_nombre` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.planificacion_sig: ~96 rows (aproximadamente)
REPLACE INTO `planificacion_sig` (`id`, `objetivo_sig_cod`, `sistema`, `objetivo_sig_nombre`, `created_at`, `updated_at`) VALUES
	(1, 'SGAS-01', 'sgas', 'Garantizar una cultura de integridad y ética en la entidad que permita que los colaboradores y las partes interesadas pertinentes de la Contraloría General de la República rechacen actos de soborno.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(2, 'SGAS-02', 'sgas', 'Asegurar una efectiva gestión de denuncias contra actos de soborno en la Contraloría General de la República.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(3, 'SGAS-03', 'sgas', 'Promover la identificación y el tratamiento de riesgos de soborno a nivel estratégico y táctico.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(4, 'SGAS-04', 'sgas', 'Detectar y sancionar los actos de soborno en los que participen colaboradores de la Contraloría General de la República.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(5, 'SGAS-05', 'sgas', 'Garantizar el acceso oportuno de la función de cumplimiento antisoborno a la Alta Dirección y las instancias pertinentes, para la adecuada implementación y mantenimiento del Sistema de Gestión Antisoborno de la CGR.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(6, 'SGAS-06', 'sgas', 'Asegurar la eficacia del Sistema de Gestión Antisoborno implementado en la Contraloría General de la República.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(7, 'SGCM-01', 'sgcm', 'Propiciar una cultura de compliance en el ámbito interno y externo, a través del fortalecimiento de capacidades, conocimientos, toma de conciencia a todo el personal en materias relacionadas al cumplimiento.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(8, 'SGCM-02', 'sgcm', 'Supervisar la implementación efectiva y el mantenimiento continuo del Sistema de Gestión de Cumplimiento de la Contraloría General de la República (CGR) por parte la función de cumplimiento.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(9, 'SGCM-03', 'sgcm', 'Establecer procesos efectivos para la gestión de denuncias contra actos que vulneren nuestra política de gestión de compliance, así como cualquier incumplimiento de nuestras obligaciones o compromisos.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(10, 'SGCM-04', 'sgcm', 'Detectar y sancionar los actos que vulneren nuestro Sistema de Gestión de Compliance en los que participe el personal de la Contraloría General de la República.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(11, 'SGCM-05', 'sgcm', 'Asegurar la eficacia del Sistema de Gestión de Compliance implementado en la Contraloría General de la República.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(12, 'SGSI-01', 'sgsi', 'Asegurar la confidencialidad, integridad y disponibilidad de la información, mediante la gestión de vulnerabilidades técnicas identificadas en los sistemas de información.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(13, 'SGSI-02', 'sgsi', 'Asegurar el aprovisionamiento de los recursos necesarios para establecer, implementar, mantener y mejorar de manera continua el sistema de gestión de seguridad de la información y gestionar los riesgos de seguridad de la información mediante el diseño, implementación y seguimiento de los planes de tratamiento', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(14, 'SGSI-03', 'sgsi', 'Mejorar continuamente la eficacia del sistema de gestión de seguridad de la información concientizando y/o capacitando al personal de la institución en promoción de una cultura de seguridad en la CGR.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(15, 'SGSI-04', 'sgsi', 'Garantizar el cumplimiento de los requisitos legales y regulatorios, aplicables a la CGR, pertinentes para la seguridad de la información, mediante la actualización y seguimiento a la implementación de estos.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(16, 'SGSI-05', 'sgsi', 'Mantener la continuidad operativa de la infraestructura tecnológica de la institución, mediante la implementación de controles de seguridad tecnológica que permitan la resiliencia organizacional ante una interrupción prolongada.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(17, 'SGCO-01', 'sgco', 'Mantener la certificación obtenida mediante el cumplimiento de los requisitos legales, normativos y reglamentarios aplicables, con el fin de garantizar la mejora continua del SGCOE.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(18, 'SGCO-02', 'sgco', 'Garantizar una cultura de calidad que involucre actividades de responsabilidad social y la supervisión regular de la satisfacción de las partes interesadas pertinentes.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(19, 'SGCO-03', 'sgco', 'Promover la investigación a través de recursos tanto internos como externos por parte de la Escuela Nacional de Control.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(20, 'SGCO-04', 'sgco', 'Garantizar la gestión adecuada de la propiedad intelectual teniendo en cuenta los requisitos legales y reglamentarios aplicables.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(21, 'SGC-01', 'sgc', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(22, 'SGC-02', 'sgc', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(23, 'SGC-03', 'sgc', 'Promover la participación ciudadana en el control social y la formación en valores de integridad.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(24, 'SGC-04', 'sgc', 'Fortalecer la gestión del Sistema Nacional de Control.', '2025-12-18 23:58:49', '2025-12-18 23:58:49'),
	(25, 'SGAS-01', 'sgas', 'Garantizar una cultura de integridad y ética en la entidad que permita que los colaboradores y las partes interesadas pertinentes de la Contraloría General de la República rechacen actos de soborno.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(26, 'SGAS-02', 'sgas', 'Asegurar una efectiva gestión de denuncias contra actos de soborno en la Contraloría General de la República.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(27, 'SGAS-03', 'sgas', 'Promover la identificación y el tratamiento de riesgos de soborno a nivel estratégico y táctico.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(28, 'SGAS-04', 'sgas', 'Detectar y sancionar los actos de soborno en los que participen colaboradores de la Contraloría General de la República.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(29, 'SGAS-05', 'sgas', 'Garantizar el acceso oportuno de la función de cumplimiento antisoborno a la Alta Dirección y las instancias pertinentes, para la adecuada implementación y mantenimiento del Sistema de Gestión Antisoborno de la CGR.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(30, 'SGAS-06', 'sgas', 'Asegurar la eficacia del Sistema de Gestión Antisoborno implementado en la Contraloría General de la República.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(31, 'SGCM-01', 'sgcm', 'Propiciar una cultura de compliance en el ámbito interno y externo, a través del fortalecimiento de capacidades, conocimientos, toma de conciencia a todo el personal en materias relacionadas al cumplimiento.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(32, 'SGCM-02', 'sgcm', 'Supervisar la implementación efectiva y el mantenimiento continuo del Sistema de Gestión de Cumplimiento de la Contraloría General de la República (CGR) por parte la función de cumplimiento.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(33, 'SGCM-03', 'sgcm', 'Establecer procesos efectivos para la gestión de denuncias contra actos que vulneren nuestra política de gestión de compliance, así como cualquier incumplimiento de nuestras obligaciones o compromisos.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(34, 'SGCM-04', 'sgcm', 'Detectar y sancionar los actos que vulneren nuestro Sistema de Gestión de Compliance en los que participe el personal de la Contraloría General de la República.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(35, 'SGCM-05', 'sgcm', 'Asegurar la eficacia del Sistema de Gestión de Compliance implementado en la Contraloría General de la República.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(36, 'SGSI-01', 'sgsi', 'Asegurar la confidencialidad, integridad y disponibilidad de la información, mediante la gestión de vulnerabilidades técnicas identificadas en los sistemas de información.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(37, 'SGSI-02', 'sgsi', 'Asegurar el aprovisionamiento de los recursos necesarios para establecer, implementar, mantener y mejorar de manera continua el sistema de gestión de seguridad de la información y gestionar los riesgos de seguridad de la información mediante el diseño, implementación y seguimiento de los planes de tratamiento.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(38, 'SGSI-03', 'sgsi', 'Mejorar continuamente la eficacia del sistema de gestión de seguridad de la información concientizando y/o capacitando al personal de la institución en promoción de una cultura de seguridad en la CGR.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(39, 'SGSI-04', 'sgsi', 'Garantizar el cumplimiento de los requisitos legales y regulatorios, aplicables a la CGR, pertinentes para la seguridad de la información, mediante la actualización y seguimiento a la implementación de estos.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(40, 'SGSI-05', 'sgsi', 'Mantener la continuidad operativa de la infraestructura tecnológica de la institución, mediante la implementación de controles de seguridad tecnológica que permitan la resiliencia organizacional ante una interrupción prolongada.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(41, 'SGCOE-01', '', 'Mantener la certificación obtenida mediante el cumplimiento de los requisitos legales, normativos y reglamentarios aplicables, con el fin de garantizar la mejora continua del SGCOE.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(42, 'SGCOE-02', '', 'Garantizar una cultura de calidad que involucre actividades de responsabilidad social y la supervisión regular de la satisfacción de las partes interesadas pertinentes.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(43, 'SGCOE-03', '', 'Promover la investigación a través de recursos tanto internos como externos por parte de la Escuela Nacional de Control.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(44, 'SGCOE-04', '', 'Garantizar la gestión adecuada de la propiedad intelectual teniendo en cuenta los requisitos legales y reglamentarios aplicables.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(45, 'SGC-01', 'sgc', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(46, 'SGC-02', 'sgc', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(47, 'SGC-03', 'sgc', 'Promover la participación ciudadana en el control social y la formación en valores de integridad.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(48, 'SGC-04', 'sgc', 'Fortalecer la gestión del Sistema Nacional de Control.', '2026-01-19 15:09:49', '2026-01-19 15:09:49'),
	(49, 'SGAS-01', 'sgas', 'Garantizar una cultura de integridad y ética en la entidad que permita que los colaboradores y las partes interesadas pertinentes de la Contraloría General de la República rechacen actos de soborno.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(50, 'SGAS-02', 'sgas', 'Asegurar una efectiva gestión de denuncias contra actos de soborno en la Contraloría General de la República.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(51, 'SGAS-03', 'sgas', 'Promover la identificación y el tratamiento de riesgos de soborno a nivel estratégico y táctico.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(52, 'SGAS-04', 'sgas', 'Detectar y sancionar los actos de soborno en los que participen colaboradores de la Contraloría General de la República.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(53, 'SGAS-05', 'sgas', 'Garantizar el acceso oportuno de la función de cumplimiento antisoborno a la Alta Dirección y las instancias pertinentes, para la adecuada implementación y mantenimiento del Sistema de Gestión Antisoborno de la CGR.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(54, 'SGAS-06', 'sgas', 'Asegurar la eficacia del Sistema de Gestión Antisoborno implementado en la Contraloría General de la República.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(55, 'SGCM-01', 'sgcm', 'Propiciar una cultura de compliance en el ámbito interno y externo, a través del fortalecimiento de capacidades, conocimientos, toma de conciencia a todo el personal en materias relacionadas al cumplimiento.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(56, 'SGCM-02', 'sgcm', 'Supervisar la implementación efectiva y el mantenimiento continuo del Sistema de Gestión de Cumplimiento de la Contraloría General de la República (CGR) por parte la función de cumplimiento.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(57, 'SGCM-03', 'sgcm', 'Establecer procesos efectivos para la gestión de denuncias contra actos que vulneren nuestra política de gestión de compliance, así como cualquier incumplimiento de nuestras obligaciones o compromisos.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(58, 'SGCM-04', 'sgcm', 'Detectar y sancionar los actos que vulneren nuestro Sistema de Gestión de Compliance en los que participe el personal de la Contraloría General de la República.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(59, 'SGCM-05', 'sgcm', 'Asegurar la eficacia del Sistema de Gestión de Compliance implementado en la Contraloría General de la República.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(60, 'SGSI-01', 'sgsi', 'Asegurar la confidencialidad, integridad y disponibilidad de la información, mediante la gestión de vulnerabilidades técnicas identificadas en los sistemas de información.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(61, 'SGSI-02', 'sgsi', 'Asegurar el aprovisionamiento de los recursos necesarios para establecer, implementar, mantener y mejorar de manera continua el sistema de gestión de seguridad de la información y gestionar los riesgos de seguridad de la información mediante el diseño, implementación y seguimiento de los planes de tratamiento.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(62, 'SGSI-03', 'sgsi', 'Mejorar continuamente la eficacia del sistema de gestión de seguridad de la información concientizando y/o capacitando al personal de la institución en promoción de una cultura de seguridad en la CGR.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(63, 'SGSI-04', 'sgsi', 'Garantizar el cumplimiento de los requisitos legales y regulatorios, aplicables a la CGR, pertinentes para la seguridad de la información, mediante la actualización y seguimiento a la implementación de estos.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(64, 'SGSI-05', 'sgsi', 'Mantener la continuidad operativa de la infraestructura tecnológica de la institución, mediante la implementación de controles de seguridad tecnológica que permitan la resiliencia organizacional ante una interrupción prolongada.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(65, 'SGCOE-01', '', 'Mantener la certificación obtenida mediante el cumplimiento de los requisitos legales, normativos y reglamentarios aplicables, con el fin de garantizar la mejora continua del SGCOE.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(66, 'SGCOE-02', '', 'Garantizar una cultura de calidad que involucre actividades de responsabilidad social y la supervisión regular de la satisfacción de las partes interesadas pertinentes.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(67, 'SGCOE-03', '', 'Promover la investigación a través de recursos tanto internos como externos por parte de la Escuela Nacional de Control.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(68, 'SGCOE-04', '', 'Garantizar la gestión adecuada de la propiedad intelectual teniendo en cuenta los requisitos legales y reglamentarios aplicables.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(69, 'SGC-01', 'sgc', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(70, 'SGC-02', 'sgc', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(71, 'SGC-03', 'sgc', 'Promover la participación ciudadana en el control social y la formación en valores de integridad.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(72, 'SGC-04', 'sgc', 'Fortalecer la gestión del Sistema Nacional de Control.', '2026-01-19 15:09:53', '2026-01-19 15:09:53'),
	(73, 'SGAS-01', 'sgas', 'Garantizar una cultura de integridad y ética en la entidad que permita que los colaboradores y las partes interesadas pertinentes de la Contraloría General de la República rechacen actos de soborno.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(74, 'SGAS-02', 'sgas', 'Asegurar una efectiva gestión de denuncias contra actos de soborno en la Contraloría General de la República.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(75, 'SGAS-03', 'sgas', 'Promover la identificación y el tratamiento de riesgos de soborno a nivel estratégico y táctico.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(76, 'SGAS-04', 'sgas', 'Detectar y sancionar los actos de soborno en los que participen colaboradores de la Contraloría General de la República.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(77, 'SGAS-05', 'sgas', 'Garantizar el acceso oportuno de la función de cumplimiento antisoborno a la Alta Dirección y las instancias pertinentes, para la adecuada implementación y mantenimiento del Sistema de Gestión Antisoborno de la CGR.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(78, 'SGAS-06', 'sgas', 'Asegurar la eficacia del Sistema de Gestión Antisoborno implementado en la Contraloría General de la República.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(79, 'SGCM-01', 'sgcm', 'Propiciar una cultura de compliance en el ámbito interno y externo, a través del fortalecimiento de capacidades, conocimientos, toma de conciencia a todo el personal en materias relacionadas al cumplimiento.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(80, 'SGCM-02', 'sgcm', 'Supervisar la implementación efectiva y el mantenimiento continuo del Sistema de Gestión de Cumplimiento de la Contraloría General de la República (CGR) por parte la función de cumplimiento.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(81, 'SGCM-03', 'sgcm', 'Establecer procesos efectivos para la gestión de denuncias contra actos que vulneren nuestra política de gestión de compliance, así como cualquier incumplimiento de nuestras obligaciones o compromisos.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(82, 'SGCM-04', 'sgcm', 'Detectar y sancionar los actos que vulneren nuestro Sistema de Gestión de Compliance en los que participe el personal de la Contraloría General de la República.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(83, 'SGCM-05', 'sgcm', 'Asegurar la eficacia del Sistema de Gestión de Compliance implementado en la Contraloría General de la República.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(84, 'SGSI-01', 'sgsi', 'Asegurar la confidencialidad, integridad y disponibilidad de la información, mediante la gestión de vulnerabilidades técnicas identificadas en los sistemas de información.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(85, 'SGSI-02', 'sgsi', 'Asegurar el aprovisionamiento de los recursos necesarios para establecer, implementar, mantener y mejorar de manera continua el sistema de gestión de seguridad de la información y gestionar los riesgos de seguridad de la información mediante el diseño, implementación y seguimiento de los planes de tratamiento.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(86, 'SGSI-03', 'sgsi', 'Mejorar continuamente la eficacia del sistema de gestión de seguridad de la información concientizando y/o capacitando al personal de la institución en promoción de una cultura de seguridad en la CGR.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(87, 'SGSI-04', 'sgsi', 'Garantizar el cumplimiento de los requisitos legales y regulatorios, aplicables a la CGR, pertinentes para la seguridad de la información, mediante la actualización y seguimiento a la implementación de estos.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(88, 'SGSI-05', 'sgsi', 'Mantener la continuidad operativa de la infraestructura tecnológica de la institución, mediante la implementación de controles de seguridad tecnológica que permitan la resiliencia organizacional ante una interrupción prolongada.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(89, 'SGCOE-01', '', 'Mantener la certificación obtenida mediante el cumplimiento de los requisitos legales, normativos y reglamentarios aplicables, con el fin de garantizar la mejora continua del SGCOE.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(90, 'SGCOE-02', '', 'Garantizar una cultura de calidad que involucre actividades de responsabilidad social y la supervisión regular de la satisfacción de las partes interesadas pertinentes.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(91, 'SGCOE-03', '', 'Promover la investigación a través de recursos tanto internos como externos por parte de la Escuela Nacional de Control.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(92, 'SGCOE-04', '', 'Garantizar la gestión adecuada de la propiedad intelectual teniendo en cuenta los requisitos legales y reglamentarios aplicables.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(93, 'SGC-01', 'sgc', 'Apoyar la gestión eficiente y eficaz de los recursos públicos en beneficio de la población.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(94, 'SGC-02', 'sgc', 'Contribuir a la reducción de la inconducta funcional y la corrupción en las entidades públicas.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(95, 'SGC-03', 'sgc', 'Promover la participación ciudadana en el control social y la formación en valores de integridad.', '2026-01-19 15:10:03', '2026-01-19 15:10:03'),
	(96, 'SGC-04', 'sgc', 'Fortalecer la gestión del Sistema Nacional de Control.', '2026-01-19 15:10:03', '2026-01-19 15:10:03');

-- Volcando estructura para tabla kallpaq.procesos
CREATE TABLE IF NOT EXISTS `procesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cod_proceso` varchar(255) NOT NULL,
  `proceso_sigla` varchar(255) DEFAULT NULL,
  `proceso_nombre` varchar(255) NOT NULL,
  `proceso_producto` varchar(255) NOT NULL,
  `proceso_objetivo` text DEFAULT NULL,
  `proceso_tipo` enum('Misional','Estratégico','Apoyo') NOT NULL,
  `cod_proceso_padre` bigint(20) unsigned DEFAULT NULL,
  `proceso_nivel` varchar(255) DEFAULT NULL,
  `planificacion_pei_id` bigint(20) unsigned DEFAULT NULL,
  `proceso_estado` tinyint(4) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.procesos: ~199 rows (aproximadamente)
REPLACE INTO `procesos` (`id`, `cod_proceso`, `proceso_sigla`, `proceso_nombre`, `proceso_producto`, `proceso_objetivo`, `proceso_tipo`, `cod_proceso_padre`, `proceso_nivel`, `planificacion_pei_id`, `proceso_estado`, `sgc`, `sgas`, `sgcm`, `sgsi`, `sgco`, `inactivated_at`, `created_at`, `updated_at`) VALUES
	(1, 'PM01', 'GPCS', 'GESTIÓN DE LA PREVENCIÓN Y CONTROL SOCIAL', 'Evaluaciones de transparencia, mecanismos de participación ciudadana y campañas de promoción de integridad.', 'Consolidar mecanismos de prevención, control social, participación ciudadana y seguimiento de integridad y transparencia.', 'Misional', NULL, '0', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(2, 'PM02', 'GSCG', 'GESTIÓN DE LOS SERVICIOS DE CONTROL GUBERNAMENTAL', 'Informes de control gubernamental, informes de servicios relacionados e informes de seguimiento.', 'Garantizar legalidad y eficiencia mediante servicios de control previo, simultáneo y posterior, y seguimiento de recomendaciones.', 'Misional', NULL, '0', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(3, 'PM03', 'GPSA', 'GESTIÓN DE LA POTESTAD SANCIONADORA', 'Pronunciamientos sobre sanciones y registro de sanciones firmes.', 'Ejercer la determinación y sanción de responsabilidad administrativa funcional e infracciones al control gubernamental.', 'Misional', NULL, '0', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(4, 'PM04', 'GSAC', 'GESTIÓN DE LOS SERVICIOS ACADÉMICOS', 'Formación académica, acreditación de profesionales y difusión de conocimiento especializado.', 'Planificar y ejecutar programas de formación especializada en control y gestión pública a través de la Escuela Nacional de Control.', 'Misional', NULL, '0', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(5, 'PE01', 'GEST', 'GESTIÓN ESTRATÉGICA', 'Estrategia institucional y planes operativos.', 'Planificar, seguir y evaluar el desempeño institucional y la estructura organizacional para cumplir los fines del control.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(6, 'PE02', 'GMII', 'GESTIÓN DE LA MEJORA E INNOVACIÓN INSTITUCIONAL', 'Implementación de mejora e innovación institucional.', 'Administrar sistemas de gestión (calidad, riesgos, cumplimiento) e impulsar la innovación y mejora continua.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(7, 'PE03', 'GINV', 'GESTIÓN DE LA INVERSIÓN', 'Ejecución de proyectos e iniciativas de inversión.', 'Programar y ejecutar proyectos de inversión institucional alineados a los objetivos estratégicos y normativa vigente.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(8, 'PE04', 'GCOM', 'GESTIÓN DE LA COMUNICACIÓN', 'Comunicación institucional y relacionamiento corporativo.', 'Fortalecer la imagen y reputación institucional mediante estrategias de comunicación interna y externa.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(9, 'PE05', 'GRIN', 'GESTIÓN DE LAS RELACIONES INTERINSTITUCIONALES', 'Fortalecimiento de relaciones interinstitucionales.', 'Coordinar políticas de relacionamiento con entidades públicas, privadas e internacionales para consolidar alianzas.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(10, 'PE06', 'GNOR', 'GESTIÓN NORMATIVA', 'Normativa institucional y documentos normativos.', 'Formular y actualizar el marco normativo interno y las iniciativas legislativas que regulan la institución.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(11, 'PE07', 'AEPI', 'ATENCIÓN A LAS ENTIDADES Y PARTES INTERESADAS', 'Provisión de información y orientación institucional.', 'Gestionar solicitudes, consultas y reclamos de entidades y ciudadanos para promover la transparencia.', 'Estratégico', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(12, 'PA01', 'GCHU', 'GESTIÓN DEL CAPITAL HUMANO', 'Servicios de talento humano institucional.', 'Administrar y desarrollar integralmente al personal bajo principios de mérito, bienestar y seguridad.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(13, 'PA02', 'GADO', 'GESTIÓN DE ACTIVOS DOCUMENTARIOS', 'Servicios documentarios y archivísticos.', 'Organizar y conservar el acervo documentario asegurando trazabilidad, digitalización y custodia.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(14, 'PA03', 'GABA', 'GESTIÓN DE ABASTECIMIENTO', 'Provisión institucional de bienes y servicios.', 'Gestionar contrataciones, bienes, servicios patrimoniales y sociedades de auditoría para el funcionamiento institucional.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(15, 'PA04', 'GFIN', 'GESTIÓN FINANCIERA', 'Programación y ejecución presupuestal y financiera.', 'Ejecutar y controlar los recursos financieros y presupuestales asegurando la rendición de cuentas.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(16, 'PA05', 'GTIC', 'GESTIÓN DE TECNOLOGÍAS DE LA INFORMACIÓN Y COMUNICACIONES', 'Servicios de tecnología de la información y comunicación.', 'Mantener y desarrollar la infraestructura tecnológica y soluciones informáticas para la transformación digital.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(17, 'PA06', 'AJUR', 'ASESORÍA JURÍDICA', 'Opiniones y orientaciones jurídicas institucionales.', 'Brindar soporte legal especializado mediante opiniones e informes para fortalecer la seguridad jurídica.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(18, 'PA07', 'GRDJ', 'GESTIÓN DE LA REPRESENTACIÓN Y DEFENSA JURÍDICA', 'Servicios de defensa y representación jurídica.', 'Conducir la defensa de los intereses institucionales en procesos judiciales, arbitrales y otros.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(19, 'PA08', 'GSEG', 'GESTIÓN DE LA SEGURIDAD', 'Servicios de seguridad y protección institucional.', 'Garantizar la seguridad física, prevención de riesgos de desastres y continuidad operativa institucional.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(20, 'PA09', 'GSAE', 'GESTIÓN DE LOS SERVICIOS ADMINISTRATIVOS DE LA ESCUELA NACIONAL DE CONTROL', 'Servicios administrativos de la Escuela Nacional de Control.', 'Brindar apoyo administrativo y recursos educativos (biblioteca) para la formación especializada.', 'Apoyo', NULL, '0', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 20:20:29', '2025-12-18 20:20:29'),
	(21, 'PM01.01', 'GPREV', 'GESTIÓN DE LA PREVENCIÓN', 'Informes de cumplimiento de SCI y reportes de evaluación selectiva.', 'Contribuir a la implementación de sólidos sistemas de control interno.', 'Misional', 1, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(22, 'PM01.02', 'GPPCC', 'GESTIÓN DE LA PROMOCIÓN DE LA PARTICIPACIÓN CIUDADANA EN EL CONTROL SOCIAL', 'Informes de vigilancia ciudadana y reportes de resultados.', 'Conseguir la participación activa de la ciudadanía en el control social.', 'Misional', 1, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(23, 'PM02.01', 'PLASC', 'PLANEAMIENTO DE LOS SERVICIOS DE CONTROL', 'Plan Nacional de Control y servicios de control programados.', 'Garantizar que los servicios de control se prioricen y programen adecuadamente.', 'Misional', 2, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(24, 'PM02.02', 'RSCOV', 'REALIZACIÓN DE SERVICIOS DE CONTROL PREVIO', 'Informes de control previo y resoluciones de gerencia.', 'Evaluar oportunamente solicitudes de adicionales de obra y operaciones de APP.', 'Misional', 2, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(25, 'PM02.03', 'RSCSM', 'REALIZACIÓN DE SERVICIOS DE CONTROL SIMULTANEO', 'Informes de visita de control y control concurrente.', 'Identificar situaciones adversas en procesos en curso.', 'Misional', 2, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(26, 'PM02.04', 'RSCOP', 'REALIZACIÓN DE SERVICIOS DE CONTROL POSTERIOR', 'Informes de Auditoría de Cumplimiento y Desempeño.', 'Determinar si los actos de gestión cumplen con la normativa.', 'Misional', 2, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(27, 'PM02.05', 'RSRAE', 'REALIZACIÓN DE SERVICIOS RELACIONADOS ANÁLISIS Y DE EVALUACIÓN', 'Informes de fiscalización de DDJJ y reportes de Vaso de Leche.', 'Fiscalizar declaraciones juradas y realizar análisis consolidados.', 'Misional', 2, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(28, 'PM02.06', 'STREV', 'SUPERVISIÓN TÉCNICA Y REVISIÓN POSTERIOR DE LOS SERVICIOS DE CONTROL', 'Reportes de supervisión técnica e informes reformulados.', 'Asegurar que los servicios de control cumplan con estándares de calidad.', 'Misional', 2, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(29, 'PM02.07', 'SEIRP', 'SEGUIMIENTO Y EVALUACIÓN A LA IMPLEMENTACIÓN DE LAS RECOMENDACIONES', 'Estado de las situaciones adversas y seguimiento.', 'Verificar la efectiva implementación de las recomendaciones.', 'Misional', 2, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(30, 'PM03.01', 'GSADM', 'GESTIÓN DE SANCIONES ADMINISTRATIVAS', 'Resoluciones de sanción en primera y segunda instancia.', 'Determinar la existencia de infracciones e imponer sanciones.', 'Misional', 3, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(31, 'PM03.02', 'GPSIC', 'GESTIÓN DEL PROCEDIMIENTO SANCIONADOR POR INFRACCIÓN AL EJERCICIO DEL CONTROL GUBERNAMENTAL', 'Resoluciones de sanción por infracción al control.', 'Sancionar conductas que obstaculicen el ejercicio del control.', 'Misional', 3, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(32, 'PM04.01', 'DDCUR', 'DISEÑO Y DESARROLLO CURRICULAR DE PROGRAMAS ACADÉMICOS', 'Planes de estudio actualizados y mallas curriculares.', 'Garantizar la calidad y pertinencia de los programas académicos.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(33, 'PM04.02', 'PAPAC', 'PROGRAMACIÓN DE ACTIVIDADES DE POSGRADO Y ACADÉMICAS', 'Cronograma de actividades y base de datos de docentes.', 'Garantizar una oferta académica ordenada y oportuna.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(34, 'PM04.03', 'GADMA', 'GESTIÓN DE LA ADMISIÓN Y MATRÍCULA', 'Lista de ingresantes y nómina de matrícula.', 'Garantizar el acceso ordenado y transparente a programas formativos.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(35, 'PM04.04', 'RAPAC', 'REALIZACIÓN DE LAS ACTIVIDADES DE POSGRADO Y ACADÉMICAS', 'Registros de calificaciones y evaluaciones docentes.', 'Garantizar la calidad en la impartición de contenidos académicos.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(36, 'PM04.05', 'GETGR', 'GESTIÓN DEL EGRESADO LA TITULACIÓN Y EL GRADO', 'Diplomas de grado o título y certificados.', 'Formalizar la obtención del grado académico o título profesional.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(37, 'PM04.06', 'INVAC', 'INVESTIGACION ACADÉMICA', 'Artículos científicos y publicaciones indexadas.', 'Promover y difundir la investigación académica especializada.', 'Misional', 4, '1', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(38, 'PE01.01', 'PLEST', 'PLANEAMIENTO ESTRATÉGICO', 'Plan Estratégico Institucional (PEI).', 'Definir componentes de la estrategia institucional a mediano plazo.', 'Estratégico', 5, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(39, 'PE01.02', 'PLOPE', 'PLANEAMIENTO OPERATIVO', 'Plan Operativo Institucional (POI).', 'Articular la programación y seguimiento del POI.', 'Estratégico', 5, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(40, 'PE01.03', 'DIORG', 'DISEÑO ORGANIZACIONAL', 'Estructura orgánica y ROF.', 'Garantizar una organización eficiente y alineada.', 'Estratégico', 5, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(41, 'PE01.04', 'GCOPE', 'GESTIÓN DE LA CONTINUIDAD OPERATIVA', 'Plan de Continuidad Operativa.', 'Asegurar la capacidad de respuesta ante emergencias.', 'Estratégico', 5, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(42, 'PE01.05', 'CONIN', 'CONTROL INSTITUCIONAL', 'Informes de servicios de control interno.', 'Coadyuvar al correcto funcionamiento institucional.', 'Estratégico', 5, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(43, 'PE01.06', 'ABESC', 'ADMINISTRACIÓN DE LA BASE DE ENTIDADES SUJETAS A CONTROL', 'Registro de entidades actualizado.', 'Administrar el universo de entidades sujetas a control.', 'Estratégico', 5, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(44, 'PE02.01', 'ASGES', 'ADMINISTRACIÓN DE LOS SISTEMAS DE GESTIÓN', 'Manual del SIG y reportes de gestión.', 'Asegurar la efectividad de los sistemas de gestión.', 'Estratégico', 6, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(45, 'PE02.02', 'GPROC', 'GESTIÓN DE PROCESOS', 'Mapa de procesos e inventario.', 'Optimizar procesos para garantizar eficiencia.', 'Estratégico', 6, '1', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(46, 'PE02.03', 'GCALI', 'GESTIÓN DE LA CALIDAD', 'Informes de satisfacción y reportes de calidad.', 'Mejorar la calidad de productos y servicios.', 'Estratégico', 6, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(47, 'PE02.04', 'GRIES', 'GESTIÓN DE RIESGOS', 'Matriz Integral de Riesgos y Oportunidades.', 'Gestionar sistemáticamente los riesgos institucionales.', 'Estratégico', 6, '1', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(48, 'PE02.05', 'GANSO', 'GESTIÓN ANTISOBORNO', 'Política y matriz de riesgos antisoborno.', 'Prevenir y gestionar riesgos de soborno.', 'Estratégico', 6, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(49, 'PE02.06', 'GCOMP', 'GESTIÓN DE COMPLIANCE', 'Matriz de obligaciones de cumplimiento.', 'Garantizar el cumplimiento normativo.', 'Estratégico', 6, '1', NULL, NULL, 0, 0, 1, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(50, 'PE02.07', 'GSEGI', 'GESTIÓN DE LA SEGURIDAD DE LA INFORMACIÓN', 'Políticas de seguridad de la información.', 'Salvaguardar los activos de información.', 'Estratégico', 6, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(51, 'PE02.08', 'GCONO', 'GESTIÓN DEL CONOCIMIENTO', 'Repositorio de lecciones aprendidas.', 'Fomentar el aprendizaje colectivo y preservar conocimiento.', 'Estratégico', 6, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(52, 'PE02.09', 'GSIMA', 'GESTIÓN DE LA SIMPLIFICACIÓN ADMINISTRATIVA', 'TUPA y TUSNE aprobados.', 'Reducir trámites y requisitos administrativos.', 'Estratégico', 6, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(53, 'PE02.10', 'GCOIN', 'GESTIÓN DEL CONTROL INTERNO', 'Reportes de seguimiento del Plan del SCI.', 'Optimizar eficiencia y ética de operaciones de la CGR.', 'Estratégico', 6, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(54, 'PE02.11', 'GINTE', 'GESTIÓN DE LA INTEGRIDAD INSTITUCIONAL', 'Programa de Integridad Institucional.', 'Garantizar una cultura de integridad y ética.', 'Estratégico', 6, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(55, 'PE03.01', 'PINVE', 'PROGRAMACIÓN DE LAS INVERSIONES', 'Programa Multianual de Inversiones (PMI).', 'Garantizar que las inversiones cierren brechas de infraestructura.', 'Estratégico', 7, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(56, 'PE03.02', 'FEECP', 'FORMULACIÓN EVALUACIÓN EJECUCIÓN Y CIERRE DE PROYECTOS', 'Expedientes técnicos y liquidaciones.', 'Garantizar que proyectos se formulen y ejecuten eficientemente.', 'Estratégico', 7, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(57, 'PE03.03', 'SEINV', 'SEGUIMIENTO Y EVALUACIÓN DE LAS INVERSIONES', 'Informes mensuales de seguimiento.', 'Mejorar la eficacia de la inversión con acciones correctivas.', 'Estratégico', 7, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(58, 'PE04.01', 'PLCOM', 'PLANIFICACIÓN DE LA COMUNICACIÓN', 'Plan de comunicación corporativa.', 'Asegurar comunicación interna y externa alineada.', 'Estratégico', 8, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(59, 'PE04.02', 'GCOMC', 'GESTIÓN DE LA COMUNICACIÓN INSTITUCIONAL Y RELACIONAMIENTO CORPORATIVO', 'Boletines y reportes de eventos.', 'Fortalecer imagen institucional y confianza.', 'Estratégico', 8, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(60, 'PE04.03', 'GCOMX', 'GESTIÓN DE LA COMUNICACIÓN EXTERNA', 'Notas de prensa y redes sociales.', 'Garantizar comunicación transparente hacia la ciudadanía.', 'Estratégico', 8, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(61, 'PE05.01', 'DPERI', 'DISEÑO DE POLÍTICAS Y ESTRATEGIAS DE RELACIONAMIENTO INSTITUCIONAL', 'Políticas y planes de relacionamiento.', 'Definir políticas de relacionamiento nacional e internacional.', 'Estratégico', 9, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(62, 'PE05.02', 'GRNAC', 'GESTIÓN DEL RELACIONAMIENTO NACIONAL', 'Convenios y reportes parlamentarios.', 'Fortalecer relación con entidades y Congreso.', 'Estratégico', 9, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(63, 'PE05.03', 'GRCIN', 'GESTIÓN DEL RELACIONAMIENTO Y COOPERACIÓN INTERNACIONAL', 'Instrumentos de cooperación internacional.', 'Promover cooperación técnica internacional.', 'Estratégico', 9, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(64, 'PE06.01', 'GINLE', 'GESTIÓN DE INICIATIVAS LEGISLATIVAS', 'Proyectos de ley e iniciativas.', 'Mejorar marco normativo mediante propuestas de ley.', 'Estratégico', 10, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(65, 'PE06.02', 'GDNOR', 'GESTIÓN DE DOCUMENTOS NORMATIVOS', 'Directivas y Reglamentos aprobados.', 'Proporcionar marco legal para servicios de control.', 'Estratégico', 10, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(66, 'PE06.03', 'GDNSA', 'GESTIÓN DE DOCUMENTOS NORMATIVOS DE LOS SERVICIOS ACADÉMICOS', 'Reglamentos y guías académicas.', 'Contar con marco normativo educativo transparente.', 'Estratégico', 10, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(67, 'PE06.04', 'GDASG', 'GESTIÓN DE DOCUMENTOS EN EL ALCANCE DEL SIG', 'Lista Maestra de Documentos del SIG.', 'Garantizar actualización de documentación de procesos.', 'Estratégico', 10, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(68, 'PE07.01', 'ASINF', 'ATENCIÓN DE SOLICITUDES DE INFORMACIÓN', 'Información entregada a ciudadanos.', 'Atender pedidos de información pública y Congreso.', 'Estratégico', 11, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(69, 'PE07.02', 'ACSOP', 'ATENCIÓN DE CONSULTAS Y SOLICITUDES DE OPINIÓN', 'Opiniones legales externas.', 'Garantizar opiniones jurídicas oportunas y confiables.', 'Estratégico', 11, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(70, 'PE07.03', 'ESELG', 'EVALUACION Y SEGUIMIENTO DE ENCARGOS LEGALES', 'Reportes de seguimiento legal.', 'Cumplir con encargos dispuestos en leyes.', 'Estratégico', 11, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(71, 'PE07.04', 'OATCI', 'ORIENTACIÓN Y ATENCIÓN AL CIUDADANO', 'Registros de atención ciudadana.', 'Facilitar acceso de ciudadanía a trámites y servicios.', 'Estratégico', 11, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(72, 'PE07.05', 'ACSPA', 'ATENCIÓN DE CONSULTAS DE LOS SERVICIOS DE POSGRADO Y ACADMÉMICOS', 'Requerimientos académicos atendidos.', 'Absolver consultas de estudiantes y egresados.', 'Estratégico', 11, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(73, 'PE07.06', 'AQREC', 'ATENCIÓN DE QUEJAS Y RECLAMOS', 'Resoluciones de reclamos.', 'Garantizar atención eficiente a reclamos y quejas PAS.', 'Estratégico', 11, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(74, 'PA01.01', 'PPRHH', 'PLANIFICACIÓN DE POLÍTICAS DE RECURSOS HUMANOS', 'Políticas de personal y presupuestos.', 'Establecer marco estratégico de gestión del talento.', 'Apoyo', 12, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(75, 'PA01.02', 'ODTRA', 'ORGANIZACIÓN Y DISTRIBUCIÓN DEL TRABAJO', 'Manuales de funciones y perfiles.', 'Optimizar estructura mediante diseño de puestos.', 'Apoyo', 12, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(76, 'PA01.03', 'GINCO', 'GESTIÓN DE LA INCORPORACIÓN', 'Contratos e inducciones.', 'Incorporar personal idóneo mediante concursos.', 'Apoyo', 12, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(77, 'PA01.04', 'ADPER', 'ADMINISTRACIÓN DE PERSONAL', 'Legajos y registros de asistencia.', 'Administrar legajos y asistencia del personal.', 'Apoyo', 12, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(78, 'PA01.05', 'GJPOCI', 'GESTIÓN DEL JEFE Y PERSONAL DEL OCI', 'Resoluciones de designación.', 'Asegurar competencia técnica e independencia del OCI.', 'Apoyo', 12, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(79, 'PA01.06', 'PDPER', 'PROCESO DISCIPLINARIO DE PERSONAL', 'Resoluciones disciplinarias.', 'Garantizar correcta aplicación de sanciones.', 'Apoyo', 12, '1', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(80, 'PA01.07', 'GREND', 'GESTIÓN DEL RENDIMIENTO', 'Evaluaciones de desempeño.', 'Optimizar rendimiento mediante evaluación objetiva.', 'Apoyo', 12, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(81, 'PA01.08', 'GCPEN', 'GESTIÓN DE LA COMPENSACIÓN Y PENSIONES', 'Planillas y cálculos de pensiones.', 'Gestionar remuneraciones y beneficios eficientemente.', 'Apoyo', 12, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(82, 'PA01.09', 'GDCAP', 'GESTIÓN DEL DESARROLLO Y CAPACITACIÓN', 'Certificados de capacitación.', 'Fortalecer competencias mediante programas de formación.', 'Apoyo', 12, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(83, 'PA01.10', 'GRHSO', 'GESTIÓN DE RELACIONES HUMANAS Y SOCIALES', 'Plan de SST y convenios.', 'Promover bienestar y seguridad laboral.', 'Apoyo', 12, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(84, 'PA02.01', 'PADOC', 'PLANIFICACIÓN DEL ACTIVO DOCUMENTARIO', 'Programas de control archivístico.', 'Asegurar gestión integral de documentos.', 'Apoyo', 13, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(85, 'PA02.02', 'RDOCU', 'RECEPCIÓN DE DOCUMENTOS', 'Cargos y registros de ingreso.', 'Asegurar registro y derivación de documentos.', 'Apoyo', 13, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(86, 'PA02.03', 'GDSRE', 'CLASIFICACIÓN RECLASIFICACIÓN Y DESCLASIFICACIÓN DOCUMENTOS SECRETOS', 'Resoluciones de clasificación.', 'Administrar acceso a documentos clasificados.', 'Apoyo', 13, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(87, 'PA02.04', 'GMENS', 'GESTIÓN DE MENSAJERÍA', 'Cédulas de notificación electrónica.', 'Garantizar entrega segura de documentos y eCasilla.', 'Apoyo', 13, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(88, 'PA02.05', 'DIGDO', 'DIGITALIZACIÓN DE DOCUMENTOS', 'Microformas con valor legal.', 'Optimizar acceso mediante digitalización legal.', 'Apoyo', 13, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(89, 'PA02.06', 'GDARC', 'GESTIÓN DE DOCUMENTOS DEL ARCHIVO', 'Inventarios y archivos organizados.', 'Garantizar preservación de documentos custodiados.', 'Apoyo', 13, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(90, 'PA02.07', 'AFCDO', 'AUTENTICACIÓN DE FIRMAS Y CERTIFICACIÓN DE DOCUMENTOS', 'Documentos autenticados.', 'Brindar seguridad jurídica validando firmas.', 'Apoyo', 13, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(91, 'PA03.01', 'ECMNE', 'ELABORACIÓN DEL CUADRO MULTIANUAL DE NECESIDADES', 'Cuadro Multianual de Necesidades.', 'Asegurar registro oportuno de necesidades.', 'Apoyo', 14, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(92, 'PA03.02', 'CONBS', 'CONTRATACIÓN DE BIENES Y SERVICIOS', 'Órdenes de compra y contratos.', 'Asegurar contratación eficiente conforme a ley.', 'Apoyo', 14, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(93, 'PA03.03', 'GBPAT', 'GESTIÓN DE BIENES PATRIMONIALES', 'Inventarios y altas/bajas.', 'Garantizar administración de bienes institucionales.', 'Apoyo', 14, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(94, 'PA03.04', 'GALMA', 'GESTIÓN DE ALMACÉN', 'Kárdex y registros de salida.', 'Asegurar conservación y distribución de existencias.', 'Apoyo', 14, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(95, 'PA03.05', 'ASGEN', 'ADMINISTRACIÓN DE SERVICIOS GENERALES', 'Informes de mantenimiento.', 'Garantizar mantenimiento de infraestructura.', 'Apoyo', 14, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(96, 'PA03.06', 'GSAUD', 'GESTIÓN DE SOCIEDADES DE AUDITORIA', 'Contratos de sociedades de auditoría.', 'Administrar registro y contratación de sociedades.', 'Apoyo', 14, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(97, 'PA04.01', 'PMFAP', 'PROGRAMACIÓN MULTIANUAL FORMULACIÓN Y APROBACIÓN DEL PRESUPUESTO', 'PIA aprobado.', 'Garantizar disponibilidad de créditos presupuestarios.', 'Apoyo', 15, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(98, 'PA04.02', 'EJEGA', 'EJECUCIÓN DEL GASTO', 'Expedientes pagados y rendiciones.', 'Brindar atención oportuna a pagos y viáticos.', 'Apoyo', 15, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(99, 'PA04.03', 'EVPBU', 'EVALUACIÓN PRESUPUESTAL', 'Reportes de evaluación anual.', 'Monitorear ejecución para mejorar eficiencia.', 'Apoyo', 15, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(100, 'PA04.04', 'GCONT', 'GESTIÓN CONTABLE', 'Estados financieros y conciliaciones.', 'Asegurar confiabilidad de información financiera.', 'Apoyo', 15, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(101, 'PA05.01', 'PGTIC', 'PLANIFICACIÓN Y GOBERNANZA DE TIC', 'Estrategia TIC institucional.', 'Definir estrategias de recursos tecnológicos.', 'Apoyo', 16, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(102, 'PA05.02', 'ITICS', 'IMPLEMENTACIÓN DE TECNOLOGÍAS TIC', 'Sistemas de información.', 'Desarrollar soluciones informáticas alineadas.', 'Apoyo', 16, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(103, 'PA05.03', 'OMTIC', 'OPERACIÓN Y MANTENIMIENTO DE TIC', 'Backups y mantenimiento técnico.', 'Garantizar disponibilidad de infraestructura.', 'Apoyo', 16, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(104, 'PA06.01', 'GDPLG', 'GESTIÓN Y DIFUSIÓN DE PRODUCTOS DE INTERÉS LEGAL', 'Alertas y reportes legales.', 'Facilitar acceso a información legal relevante.', 'Apoyo', 17, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(105, 'PA06.02', 'ACIJL', 'ABSOLUCIÓN DE CONSULTAS INTERNAS DE CARÁCTER JURÍDICO', 'Opiniones jurídicas internas.', 'Garantizar opiniones legales internas confiables.', 'Apoyo', 17, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(106, 'PA07.01', 'GARBI', 'GESTIÓN DE LOS PROCESOS ARBITRALES DE LA CGR', 'Resultados arbitrales.', 'Defender intereses institucionales en arbitraje.', 'Apoyo', 18, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(107, 'PA07.02', 'GJUDI', 'GESTIÓN DE LOS PROCESOS JUDICIALES DE LA CGR', 'Estado de procesos judiciales.', 'Conducir defensa legal en procesos judiciales.', 'Apoyo', 18, '1', NULL, NULL, 0, 0, 1, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(108, 'PA08.01', 'GPRDE', 'GESTIÓN DE PREVENCIÓN DE RIESGOS DE DESASTRES', 'Planes de seguridad.', 'Minimizar impacto de desastres y continuidad.', 'Apoyo', 19, '1', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(109, 'PA08.02', 'OSEGU', 'OPERACIÓN DE LA GESTIÓN DE LA SEGURIDAD', 'Reportes de vigilancia.', 'Preservar seguridad física de instalaciones.', 'Apoyo', 19, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(110, 'PA08.03', 'APINI', 'ANALISIS Y PROCESAMIENTO DE INFORMACIÓN DE INTERES', 'Alertas socio-ambientales.', 'Contribuir al desarrollo de servicios de control.', 'Apoyo', 19, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(111, 'PA08.04', 'DAIND', 'DESARROLLO DE ACTIVIDADES INDAGATORIAS', 'Hojas de actividades indagatorias.', 'Determinar vulnerabilidades de seguridad e imagen.', 'Apoyo', 19, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(112, 'PA09.01', 'GSBIB', 'GESTION DE LOS SERVICIOS DE BIBLIOTECA', 'Inventario bibliográfico.', 'Proporcionar servicios bibliotecarios especializados.', 'Apoyo', 20, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(113, 'PA09.02', 'ARMED', 'ATENCIÓN DE REQUERIMIENTOS DE MATERIAL EDUCATIVO', 'Material educativo impreso.', 'Garantizar entrega de material académico impreso.', 'Apoyo', 20, '1', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 21:42:49', '2025-12-18 21:42:49'),
	(114, 'PM01.01.01', 'ISCI', 'ADMINISTRACIÓN Y EVALUACIÓN DEL SCI', 'Informe de cumplimiento de implementación de SCI.', 'Contribuir a la implementación de sólidos sistemas de control interno.', 'Misional', 21, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(115, 'PM01.01.02', 'ROBP', 'GESTIÓN DEL REGISTRO DE AVANCE DE OBRAS PÚBLICAS', 'Informe de verificación de obras públicas e INFOBRAS.', 'Asegurar el control eficiente y transparente del avance de obras públicas.', 'Misional', 21, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(116, 'PM01.01.03', 'TGENT', 'EVALUACIÓN DE LA TRANSFERENCIA DE GESTIÓN', 'Informe consolidado de evaluación selectiva.', 'Promover la continuidad de los servicios públicos en cambios de autoridades.', 'Misional', 21, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(117, 'PM01.01.04', 'RCENT', 'EVALUACIÓN DE LA RENDICIÓN DE CUENTAS', 'Informe de evaluación de rendición de cuentas.', 'Fortalecer la transparencia sobre el cumplimiento de metas y resultados.', 'Misional', 21, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(118, 'PM01.01.05', 'DDJJ', 'GESTIÓN DE LAS DECLARACIONES JURADAS', 'Reportes de cumplimiento de presentación de DDJJ.', 'Garantizar la transparencia y el control patrimonial de funcionarios.', 'Misional', 21, '2', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(119, 'PM01.01.06', 'PVLVE', 'VERIFICACIÓN DE CUENTA DEL VASO DE LECHE', 'Informe de resultados de verificación PVL.', 'Asegurar la consistencia de información del PVL para intervenciones.', 'Misional', 21, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(120, 'PM01.01.07', 'SIRFP', 'REGISTRO DE FUNCIONARIOS QUE MANEJAN FONDOS', 'Reportes de registro de obligados SIREC.', 'Promover la transparencia en la administración financiera del sector público.', 'Misional', 21, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(121, 'PM01.01.08', 'RCONC', 'CONTROL DE CONTRATOS DE CONSULTORÍA', 'Plataforma de consulta ciudadana actualizada.', 'Contribuir con la transparencia en las contrataciones de consultoría.', 'Misional', 21, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(122, 'PM01.01.09', 'BSREG', 'EVALUACIÓN DE BALANCE SEMESTRAL REGIDORES/CONSEJEROS', 'Informe de cumplimiento del Balance Semestral.', 'Transparentar el uso de recursos destinados a la fiscalización local.', 'Misional', 21, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(123, 'PM01.01.10', 'PSCIC', 'PROMOCIÓN Y FORTALECIMIENTO DEL SCI', 'Reporte de eventos y contenidos elaborados.', 'Sensibilizar sobre el SCI como mecanismo preventivo de corrupción.', 'Misional', 21, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(124, 'PM01.02.01', 'GDENU', 'GESTIÓN DE DENUNCIAS', 'Reportes de denuncias ingresadas y admitidas.', 'Garantizar la reserva y atención oportuna de denuncias ciudadanas.', 'Misional', 22, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(125, 'PM01.02.02', 'SVCIU', 'SISTEMAS DE VIGILANCIA CIUDADANA', 'Informes de vigilancia ciudadana.', 'Coadyuvar al control gubernamental mediante participación activa.', 'Misional', 22, '2', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(126, 'PM02.02.01', 'EPAOB', 'EVALUACIÓN DE PRESTACIONES ADICIONALES DE OBRA', 'Informe de control previo de adicionales.', 'Evaluar la solicitud de autorización previa para adicionales de obra.', 'Misional', 24, '2', NULL, NULL, 1, 1, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(127, 'PM02.02.02', 'RAAOB', 'EVALUACIÓN DE RECURSOS DE APELACIÓN DE PRESTACIONES ADICIONALES DE OBRA ', 'Informe de evaluación de recurso de apelación.', 'Asegurar transparencia en impugnaciones sobre adicionales de obra.', 'Misional', 24, '2', NULL, NULL, 1, 1, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(128, 'PM02.02.03', 'EPASU', 'EVALUACIÓN DE PRESTACIONES ADICIONALES DE SUPERVISIÓN DE OBRA', 'Informe de control previo de supervisión.', 'Evaluar autorización previa para adicionales de supervisión de obra.', 'Misional', 24, '2', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(129, 'PM02.02.04', 'RAASU', 'EVALUACIÓN DE RECURSOS DE APELACIÓN DE PRESTACIONES  DE ADICIONALES DE SUPERVISIÓN DE OBRA', 'Informe de evaluación de apelación.', 'Garantizar impugnación correcta de adicionales de supervisión.', 'Misional', 24, '2', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(130, 'PM02.02.05', 'EAPOX', 'EVALUACIÓN DE SOLICITUDES DE EMISIÓN DE INFORME PREVIO A LAS OPERACIONES DE ASOCIACIONES PÚBLICO PRIVADAS Y OBRAS POR IMPUESTOS', 'Informe previo en APP y OxI.', 'Emitir informes previos en operaciones de APP y Obras por Impuestos.', 'Misional', 24, '2', NULL, NULL, 1, 0, 0, 0, 1, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(131, 'PM02.02.06', 'EENDE', 'EVALUACIÓN DE ENDEUDAMIENTO PÚBLICO', 'Informe previo de endeudamiento.', 'Evaluar operaciones que comprometan el crédito financiero del Estado.', 'Misional', 24, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(132, 'PM02.02.07', 'OCSEC', 'OPINIÓN PREVIA COMPRAS SECRETAS MILITARES', 'Opinión previa sobre compras secretas.', 'Asegurar legalidad en compras secretas por seguridad nacional.', 'Misional', 24, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(133, 'PM02.03.01', 'VCON', 'VISITA DE CONTROL', 'Informe de Visita de Control.', 'Identificar situaciones adversas en el momento de ejecución.', 'Misional', 25, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(134, 'PM02.03.02', 'OOFIC', 'ORIENTACIÓN DE OFICIO', 'Informe de Orientación de Oficio.', 'Revisión documental para alertar situaciones adversas oportunamente.', 'Misional', 25, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(135, 'PM02.03.03', 'CCONC', 'CONTROL CONCURRENTE', 'Informe de hito de control.', 'Acompañamiento sistemático a hitos de control en curso.', 'Misional', 25, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(136, 'PM02.03.04', 'OCSIM', 'OPERATIVO DE CONTROL SIMULTÁNEO', 'Informe de operativo de control.', 'Intervención masiva sobre un mismo hito en múltiples entidades.', 'Misional', 25, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(137, 'PM02.03.05', 'CPORA', 'CONTROL A OBRAS PÚBLICAS PARALIZADAS', 'Informe de control preventivo.', 'Contribuir a la reactivación de obras paralizadas reduciendo riesgos.', 'Misional', 25, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(138, 'PM02.04.01', 'ACUMP', 'AUDITORÍA DE CUMPLIMIENTO', 'Informe de Auditoría de Cumplimiento.', 'Determinar si actos de gestión cumplen con la normativa.', 'Misional', 26, '2', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(139, 'PM02.04.02', 'ADESE', 'AUDITORÍA DE DESEMPEÑO', 'Informe de Auditoría de Desempeño.', 'Generar recomendaciones para mejorar eficacia y economía pública.', 'Misional', 26, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(140, 'PM02.04.03', 'AFINA', 'AUDITORÍA FINANCIERA', 'Dictamen Financiero y Presupuestario.', 'Expresar opinión sobre razonabilidad de estados financieros.', 'Misional', 26, '2', NULL, NULL, 1, 1, 1, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(141, 'PM02.04.04', 'ACGUR', 'AUDITORÍA DE LA CUENTA GENERAL DE LA REPÚBLICA', 'Informe de auditoría de Cuenta General.', 'Garantizar integridad de la rendición de cuentas del Estado.', 'Misional', 26, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(142, 'PM02.04.05', 'SCEHI', 'CONTROL ESPECÍFICO A HECHOS IRREGULARES', 'Informe de control específico.', 'Verificar irregularidades y determinar responsabilidades.', 'Misional', 26, '2', NULL, NULL, 1, 0, 1, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(143, 'PM02.04.06', 'AOPO', 'ACCIÓN DE OFICIO POSTERIOR', 'Informe de acción de oficio posterior.', 'Alertar sobre hechos con indicio de irregularidad evidenciados.', 'Misional', 26, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(144, 'PM02.04.07', 'AFORE', 'AUDITORÍA FORENSE', 'Informe de Auditoría Forense.', 'Identificar presunta responsabilidad penal por enriquecimiento.', 'Misional', 26, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(145, 'PM02.04.08', 'AUDTI', 'AUDITORÍA A LAS TECNOLOGÍAS DE INFORMACIÓN', 'Informe de Auditoría de TI.', 'Confirmar que sistemas y controles de TI operan de forma segura.', 'Misional', 26, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(146, 'PM02.05.01', 'FFSUP', 'FISCALIZACIÓN DE FUNCIONARIOS PÚBLICOS', 'Informe de fiscalización de DDJJ.', 'Detectar incremento patrimonial no justificado e incompatibilidades.', 'Misional', 27, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(147, 'PM02.05.02', 'AEGVL', 'EVALUACIÓN DEL GASTO PROGRAMA VASO DE LECHE', 'Informe consolidado anual PVL.', 'Contribuir a la mejora de gestión del PVL.', 'Misional', 27, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(148, 'PM02.05.03', 'STOCB', 'SUSPENSIÓN TEMPORAL DE CUENTAS BANCARIAS', 'Hoja informativa de solicitud de suspensión.', 'Salvaguardar fondos públicos ante riesgos de uso inadecuado.', 'Misional', 27, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(149, 'PM02.05.04', 'EICCG', 'ELABORACIÓN DE INFORME CONSOLIDADO DE CONTROL', 'Informe consolidado temático.', 'Exponer resultados consolidados sobre materias específicas.', 'Misional', 27, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(150, 'PM02.06.01', 'STRSC', 'SUPERVISIÓN TÉCNICA DE SERVICIOS DE CONTROL', 'Reporte de resultados de supervisión.', 'Alertar aspectos que afecten la calidad durante el servicio.', 'Misional', 28, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(151, 'PM02.06.02', 'ACALS', 'ASEGURAMIENTO DE LA CALIDAD', 'Informe de aseguramiento de calidad.', 'Garantizar productos que cumplan estándares internacionales.', 'Misional', 28, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(152, 'PM02.06.03', 'ROICO', 'REVISIÓN DE OFICIO DE INFORMES DE CONTROL', 'Hoja informativa de revisión de oficio.', 'Verificar cumplimiento normativo para reformulación excepcional.', 'Misional', 28, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(153, 'PM02.06.04', 'REICO', 'REFORMULACIÓN DE INFORMES DE CONTROL', 'Informe de control reformulado.', 'Corregir deficiencias en informes para preservar debido proceso.', 'Misional', 28, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(154, 'PM03.01.01', 'PASP1', 'PAS EN PRIMERA INSTANCIA', 'Resoluciones de inicio y determinación.', 'Identificar infracciones funcionales proponiendo sanciones.', 'Misional', 30, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(155, 'PM03.01.02', 'PASP2', 'PAS EN SEGUNDA INSTANCIA', 'Resoluciones de Tribunal (TSRA).', 'Resolver recursos de apelación en última instancia.', 'Misional', 30, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(156, 'PM03.01.03', 'GCSAN', 'GESTIÓN PARA EL CUMPLIMIENTO DE SANCIONES', 'Inscripción en RNSSC y publicación.', 'Asegurar inscripción de sanciones firmes en el RNSSC.', 'Misional', 30, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(157, 'PM04.01.01', 'DACUR', 'DISEÑO Y ACTUALIZACIÓN DE CURRÍCULO', 'Diseño de planes de estudio.', 'Garantizar pertinencia de programas académicos y asignaturas.', 'Misional', 32, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(158, 'PM04.01.02', 'DRCUP', 'DISEÑO Y REDISEÑO DE CURSOS Y PROGRAMAS', 'Nuevos cursos y programas diseñados.', 'Actualizar oferta académica formativa de la Escuela.', 'Misional', 32, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(159, 'PM04.03.01', 'AMPOS', 'ADMISIÓN Y MATRÍCULA DE POSGRADO', 'Lista de ingresantes posgrado.', 'Garantizar acceso ordenado a Maestrías y Especialidades.', 'Misional', 34, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(160, 'PM04.03.02', 'IAACA', 'INSCRIPCIÓN EN ACTIVIDADES ACADÉMICAS', 'Lista de inscritos confirmados.', 'Garantizar la oportuna inscripción a formación continua.', 'Misional', 34, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(161, 'PM04.04.01', 'INIES', 'INDUCCIÓN Y NIVELACIÓN A ESTUDIANTES', 'Plan de nivelación implementado.', 'Facilitar integración y homogeneizar conocimientos básicos.', 'Misional', 35, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(162, 'PM04.04.02', 'DCPAC', 'DESARROLLO DE CLASES POSGRADO/ACADÉMICAS', 'Registros de calificaciones.', 'Impartir contenidos académicos y acompañar progreso.', 'Misional', 35, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(163, 'PM04.04.03', 'ECACA', 'EVALUACIÓN DE LA CALIDAD ACADÉMICA', 'Informe de desempeño docente.', 'Asegurar excelencia educativa evaluando desempeño docente.', 'Misional', 35, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(164, 'PM04.05.01', 'OCEGR', 'OBTENCIÓN DE CONDICIÓN DE EGRESADO', 'Constancia de egresado.', 'Verificar cumplimiento de requisitos para trámite de grado.', 'Misional', 36, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(165, 'PM04.05.02', 'OGTIT', 'OBTENCIÓN DEL GRADO O TÍTULO', 'Acta de sustentación.', 'Evaluar trabajos de investigación mediante sustentación.', 'Misional', 36, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(166, 'PM04.05.03', 'EGTIT', 'EMISIÓN DE GRADO O TÍTULO', 'Diplomas emitidos e inscritos.', 'Formalizar reconocimiento oficial ante SUNEDU.', 'Misional', 36, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(167, 'PE01.03.01', 'DECGR', 'DISEÑO ESTRUCTURA ORGANIZACIONAL CGR', 'ROF y Manuales de Operaciones.', 'Garantizar organización eficiente alineada a objetivos.', 'Estratégico', 40, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(168, 'PE01.03.02', 'DEOCI', 'DISEÑO ESTRUCTURA ORGANIZACIONAL OCI', 'OCI en funcionamiento.', 'Establecer estructuras de OCI según nivel de riesgo.', 'Estratégico', 40, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(169, 'PE05.02.01', 'GREIP', 'RELACIONAMIENTO ENTIDADES PÚBLICAS/PRIVADAS', 'Convenios de cooperación nacional.', 'Fortalecer relaciones para objetivos de la CGR.', 'Estratégico', 62, '2', NULL, NULL, 0, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(170, 'PE05.02.02', 'GRCON', 'RELACIONAMIENTO CON EL CONGRESO', 'Presentaciones y compromisos.', 'Fortalecer relación mediante representación parlamentaria.', 'Estratégico', 62, '2', NULL, NULL, 0, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(171, 'PE07.01.01', 'AAIPU', 'ACCESO A LA INFORMACIÓN PÚBLICA', 'Entrega de información pública.', 'Garantizar entrega eficiente de información a ciudadanos.', 'Estratégico', 68, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(172, 'PE07.01.02', 'ARICO', 'REQUERIMIENTOS DE INFORMACIÓN DEL CONGRESO', 'Respuestas al Congreso.', 'Atender oportunamente solicitudes de fiscalización.', 'Estratégico', 68, '2', NULL, NULL, 0, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(173, 'PE07.01.03', 'ASIEP', 'INFORMACIÓN A ENTIDADES E INST. PRIVADAS', 'Solicitudes de información atendidas.', 'Fortalecer relacionamiento atendiendo consultas.', 'Estratégico', 68, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(174, 'PE07.06.01', 'ARLRE', 'ATENCIÓN DE RECLAMOS LIBRO RECLAMACIONES', 'Resolución de reclamos.', 'Atender eficientemente reclamos para mejora continua.', 'Estratégico', 73, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(175, 'PE07.06.02', 'AQDTP', 'QUEJAS POR DEFECTO TRAMITACIÓN PAS', 'Resoluciones de quejas procedimentales.', 'Salvaguardar derecho al debido proceso en el PAS.', 'Estratégico', 73, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(176, 'PA01.06.01', 'EDCPC', 'EVALUACIÓN DE DENUNCIAS SOBRE PRESUNTOS ACTOS DE CORRUPCIÓN CONTRA PERSONAL CON VÍNCULO CON LA CGR', 'Informe de evaluación de denuncia interna.', 'Determinar procedencia de denuncias contra personal propio.', 'Apoyo', 79, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(177, 'PA01.06.02', 'EDOCI', 'DENUNCIAS CONTRA JEFES Y PERSONAL OCI', 'Informe de admisión o archivo.', 'Preservar legitimidad resolviendo quejas contra OCI.', 'Apoyo', 79, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(178, 'PA01.06.03', 'GPADI', 'GESTIÓN PROCEDIMIENTO DISCIPLINARIO (PAD)', 'Resoluciones de sanción PAD.', 'Garantizar aplicación justa de sanciones al personal.', 'Apoyo', 79, '2', NULL, NULL, 1, 1, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(179, 'PA01.10.01', 'SSOTR', 'SEGURIDAD Y SALUD EN EL TRABAJO', 'Plan y Programa anual de SST.', 'Prevenir accidentes laborales y promover cultura preventiva.', 'Apoyo', 83, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(180, 'PA01.10.02', 'RLICO', 'RELACIONES LABORALES INDIVIDUALES/COLECTIVAS', 'Convenios colectivos registrados.', 'Mantener relaciones justas fomentando diálogo sindical.', 'Apoyo', 83, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(181, 'PA01.10.03', 'CCLIO', 'CULTURA Y CLIMA ORGANIZACIONAL', 'Plan de Clima y Cultura.', 'Fortalecer sentido de pertenencia y ambiente positivo.', 'Apoyo', 83, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(182, 'PA01.10.04', 'BSOCI', 'BIENESTAR SOCIAL', 'Programas de bienestar y apoyo.', 'Propiciar condiciones que mejoren calidad de vida.', 'Apoyo', 83, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(183, 'PA01.10.05', 'GSEGU', 'GESTIÓN DE SEGUROS', 'Pólizas activas (SCTR/EPS).', 'Proteger a trabajadores ante contingencias de salud.', 'Apoyo', 83, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(184, 'PA02.04.01', 'SMENS', 'SERVICIO DE MENSAJERÍA', 'Cargos de entrega de mensajería.', 'Garantizar entrega segura de documentos institucionales.', 'Apoyo', 87, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(185, 'PA02.04.02', 'NELEC', 'NOTIFICACIONES ELECTRÓNICAS', 'Cédulas de notificación electrónica.', 'Garantizar integridad y sello de tiempo vía eCasilla.', 'Apoyo', 87, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(186, 'PA04.02.01', 'DCFPP', 'DISPONIBILIDAD Y CERTIFICACIÓN PRESUPUESTAL', 'Certificaciones presupuestales.', 'Asegurar asignación de créditos para operaciones.', 'Apoyo', 98, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(187, 'PA04.02.02', 'TFACG', 'TRANSFERENCIAS A FAVOR DE LA CGR', 'Presupuesto incorporado.', 'Actualizar presupuesto según transferencia de recursos.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(188, 'PA04.02.03', 'MPRES', 'MODIFICACIÓN PRESUPUESTARIA', 'Resoluciones de notas de modificación.', 'Asegurar correcta redistribución de créditos.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(189, 'PA04.02.04', 'EINGR', 'EJECUCIÓN DE INGRESOS', 'Recibos de ingreso conciliados.', 'Centralizar fondos recaudados en la Cuenta del Tesoro.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(190, 'PA04.02.05', 'EGASO', 'EJECUCIÓN DEL GASTO', 'Expedientes de pago girados.', 'Brindar atención oportuna a los pagos girados.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(191, 'PA04.02.06', 'GVIAT', 'GESTIÓN DE VIÁTICOS', 'Viáticos y rendiciones aprobadas.', 'Gestionar otorgamiento y rendición de viáticos.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(192, 'PA04.02.07', 'GFCCH', 'GESTIÓN DE CAJA CHICA', 'Reembolsos de caja chica.', 'Asignar recursos inmediatos para gastos menores.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(193, 'PA04.02.08', 'GANTI', 'GESTIÓN DE ANTICIPOS', 'Anticipos desembolsados.', 'Atender requerimientos y rendición de anticipos.', 'Apoyo', 98, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(194, 'PA05.02.01', 'DAIDC', 'ARQUITECTURA INFORMÁTICA Y DE DATOS', 'Diseño de infraestructura tecnológica.', 'Garantizar interoperabilidad de sistemas tecnológicos.', 'Apoyo', 102, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(195, 'PA05.02.02', 'DSOLU', 'DESARROLLO DE SOLUCIONES TI', 'Aplicaciones y sistemas implementados.', 'Implementar herramientas tecnológicas institucionales.', 'Apoyo', 102, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(196, 'PA05.03.01', 'RINFO', 'RESPALDO DE INFORMACIÓN (BACKUP)', 'Copias de seguridad ejecutadas.', 'Asegurar integridad y continuidad de datos digitales.', 'Apoyo', 103, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(197, 'PA05.03.02', 'ARRII', 'REQUERIMIENTOS DE RECURSOS INFORMÁTICOS', 'Recursos TI habilitados.', 'Asegurar atención de solicitudes de usuarios finales.', 'Apoyo', 103, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(198, 'PA05.03.03', 'SCSTI', 'SEGUIMIENTO Y CONTROL DE SERVICIOS TI', 'Reportes de desempeño TI.', 'Asegurar calidad y continuidad de servicios tecnológicos.', 'Apoyo', 103, '2', NULL, NULL, 0, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21'),
	(199, 'PA05.03.04', 'MPCAC', 'MANTENIMIENTO DE ACTIVOS INFORMÁTICOS', 'Informes de mantenimiento técnico.', 'Prolongar vida útil de activos y evitar fallos.', 'Apoyo', 103, '2', NULL, NULL, 1, 0, 0, 0, 0, NULL, '2025-12-18 23:16:21', '2025-12-18 23:16:21');

-- Volcando estructura para tabla kallpaq.procesos_ouo
CREATE TABLE IF NOT EXISTS `procesos_ouo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_proceso` bigint(20) unsigned NOT NULL,
  `id_ouo` bigint(20) unsigned NOT NULL,
  `propietario` int(11) DEFAULT 0,
  `delegado` int(11) DEFAULT 0,
  `ejecutor` int(11) DEFAULT 0,
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
  CONSTRAINT `procesos_ouo_ibfk_2` FOREIGN KEY (`id_ouo`) REFERENCES `ouos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=328 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.procesos_ouo: ~3 rows (aproximadamente)
REPLACE INTO `procesos_ouo` (`id`, `id_proceso`, `id_ouo`, `propietario`, `delegado`, `ejecutor`, `sgc`, `sgas`, `sgcm`, `sgsi`, `sgco`, `created_at`, `updated_at`) VALUES
	(302, 48, 5, 1, 0, 0, 1, 1, 1, 0, 0, NULL, '2026-01-19 21:53:30'),
	(303, 54, 5, 1, 0, 0, 1, 1, 1, 0, 0, NULL, '2026-01-19 21:53:30'),
	(304, 176, 5, 1, 0, 0, 1, 1, 0, 0, 0, NULL, '2026-01-19 21:53:55'),
	(305, 96, 13, 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2026-01-19 22:09:35'),
	(306, 189, 13, 1, 0, 0, 0, 1, 1, 0, 0, NULL, '2026-01-19 22:09:49'),
	(307, 190, 13, 1, 0, 0, 0, 1, 1, 0, 0, NULL, '2026-01-19 22:09:42'),
	(308, 192, 13, 1, 0, 0, 0, 1, 1, 0, 0, NULL, '2026-01-19 22:09:46'),
	(309, 151, 48, 1, 0, 0, 1, 1, 0, 0, 0, NULL, '2026-01-19 22:21:17'),
	(310, 152, 48, 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2026-01-19 22:21:21'),
	(311, 109, 12, 1, 0, 0, 0, 0, 0, 1, 0, NULL, '2026-01-21 17:28:13'),
	(312, 110, 12, 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2026-01-21 17:28:13'),
	(313, 111, 12, 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2026-01-21 17:28:13'),
	(314, 106, 4, 1, 0, 0, 0, 0, 0, 0, 0, NULL, '2026-01-19 22:36:51'),
	(315, 107, 4, 1, 0, 0, 0, 0, 1, 0, 0, NULL, '2026-01-19 22:36:46'),
	(316, 95, 14, 0, 0, 0, 1, 0, 0, 0, 0, NULL, NULL),
	(317, 94, 14, 0, 0, 0, 1, 1, 0, 0, 0, NULL, NULL),
	(318, 93, 14, 0, 0, 0, 1, 1, 1, 0, 0, NULL, NULL),
	(319, 92, 14, 0, 0, 0, 1, 1, 1, 0, 0, NULL, NULL),
	(320, 91, 14, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL),
	(321, 198, 23, 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL),
	(322, 178, 19, 1, 0, 0, 0, 1, 1, 1, 0, NULL, NULL),
	(323, 76, 20, 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL),
	(324, 82, 20, 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL),
	(325, 195, 25, 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL),
	(326, 50, 25, 1, 0, 0, 0, 0, 0, 1, 0, NULL, NULL),
	(327, 196, 24, 1, 0, 0, 1, 1, 1, 1, 0, NULL, '2026-01-21 17:43:22');

-- Volcando estructura para tabla kallpaq.proceso_expectativa
CREATE TABLE IF NOT EXISTS `proceso_expectativa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expectativa_id` bigint(20) unsigned NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proceso_expectativa_expectativa_id_foreign` (`expectativa_id`),
  KEY `proceso_expectativa_proceso_id_foreign` (`proceso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.proceso_expectativa: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.programa_auditoria
CREATE TABLE IF NOT EXISTS `programa_auditoria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pa_version` varchar(255) NOT NULL,
  `pa_anio` varchar(255) NOT NULL,
  `pa_recursos` text DEFAULT NULL,
  `pa_fecha_aprobacion` date NOT NULL,
  `pa_estado` varchar(255) NOT NULL DEFAULT 'Borrador',
  `pa_objetivo_general` text DEFAULT NULL,
  `pa_alcance` text DEFAULT NULL,
  `pa_metodos` text DEFAULT NULL,
  `pa_criterios` text DEFAULT NULL,
  `avance` decimal(8,2) DEFAULT 0.00,
  `pa_descripcion` text DEFAULT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pa_riesgos` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.programa_auditoria: ~1 rows (aproximadamente)
REPLACE INTO `programa_auditoria` (`id`, `pa_version`, `pa_anio`, `pa_recursos`, `pa_fecha_aprobacion`, `pa_estado`, `pa_objetivo_general`, `pa_alcance`, `pa_metodos`, `pa_criterios`, `avance`, `pa_descripcion`, `archivo_pdf`, `fecha_publicacion`, `created_at`, `updated_at`, `pa_riesgos`) VALUES
	(1, '01', '2026', '20000 soles', '2026-01-16', 'Borrador', 'Programa Anual 2026', 'Incluye todas la Auditorias del Sistema de gestión', 'Entrevistas, muestreo, otros', 'Normas ISO, Procedimientos', 0.00, NULL, NULL, NULL, '2026-01-16 19:06:24', '2026-01-16 19:06:24', NULL);

-- Volcando estructura para tabla kallpaq.pruebas_continuidad
CREATE TABLE IF NOT EXISTS `pruebas_continuidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `plan_id` bigint(20) unsigned NOT NULL,
  `tipo_prueba` enum('documental','walkthrough','simulacion','funcional','ejercicio_total') NOT NULL,
  `fecha_programada` date NOT NULL,
  `fecha_ejecucion` date DEFAULT NULL,
  `objetivo` text NOT NULL,
  `alcance` text DEFAULT NULL,
  `participantes` text DEFAULT NULL,
  `escenario_prueba` text DEFAULT NULL,
  `estado` enum('programada','en_ejecucion','completada','cancelada','postergada') NOT NULL DEFAULT 'programada',
  `resultados` text DEFAULT NULL,
  `hallazgos` text DEFAULT NULL,
  `lecciones_aprendidas` text DEFAULT NULL,
  `acciones_mejora` text DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL COMMENT '1-5',
  `informe_path` varchar(255) DEFAULT NULL,
  `responsable_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pruebas_continuidad_codigo_unique` (`codigo`),
  KEY `pruebas_continuidad_plan_id_foreign` (`plan_id`),
  KEY `pruebas_continuidad_responsable_id_foreign` (`responsable_id`),
  KEY `pruebas_continuidad_created_by_foreign` (`created_by`),
  CONSTRAINT `pruebas_continuidad_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pruebas_continuidad_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `planes_continuidad` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pruebas_continuidad_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.pruebas_continuidad: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.radar_normativo
CREATE TABLE IF NOT EXISTS `radar_normativo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `numero_norma` varchar(255) DEFAULT NULL,
  `organismo_emisor` varchar(255) DEFAULT NULL,
  `url_fuente` varchar(500) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL,
  `resumen_ia` text DEFAULT NULL,
  `texto_completo` longtext DEFAULT NULL,
  `nivel_relevancia` enum('Alta','Media','Baja') NOT NULL DEFAULT 'Media',
  `estado` enum('Pendiente','En Análisis','Aplicable','No Aplicable') NOT NULL DEFAULT 'Pendiente',
  `obligacion_principal` text DEFAULT NULL,
  `analisis_humano` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.radar_normativo: ~39 rows (aproximadamente)
REPLACE INTO `radar_normativo` (`id`, `titulo`, `numero_norma`, `organismo_emisor`, `url_fuente`, `fecha_publicacion`, `resumen_ia`, `texto_completo`, `nivel_relevancia`, `estado`, `obligacion_principal`, `analisis_humano`, `created_at`, `updated_at`) VALUES
	(125, 'RESOLUCION JEFATURAL N° 000076-2025-OEDI/JEF', 'RESOLUCION JEFATURAL N° 000076-2025-OEDI/JEF', 'PRESIDENCIA DEL CONSEJO DE MINISTROS', 'https://busquedas.elperuano.pe/dispositivo/NL/2462549-1', '2025-11-28', 'Establecen plazo máximo para que el sector funcional responsable coordine con el OEDI la verificación efectuada por este último sobre el cumplimiento de normas técnicas políticas sectoriales y documentos de gestión equivalentes de los expedientes técnicos o documentos equivalentes elaborados o asistidos por el OEDI; y dictan otras disposiciones', 'Establecen plazo máximo para que el sector funcional responsable coordine con el OEDI la verificación efectuada por este último sobre el cumplimiento de normas técnicas políticas sectoriales y documentos de gestión equivalentes de los expedientes técnicos o documentos equivalentes elaborados o asistidos por el OEDI; y dictan otras disposiciones', 'Baja', 'Pendiente', 'Establecen plazo máximo para que el sector funcional responsable coordine con el OEDI la verificación efectuada por este último sobre el cumplimiento de normas técnicas políticas sectoriales y documentos de gestión equivalentes de los expedientes técnicos o documentos equivalentes elaborados o asistidos por el OEDI; y dictan otras disposiciones', NULL, '2025-11-28 16:31:30', '2025-11-28 16:31:30'),
	(126, 'RESOLUCION MINISTERIAL N° 384-2025-MINEM/DM', 'RESOLUCION MINISTERIAL N° 384-2025-MINEM/DM', 'ENERGIA Y MINAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2460382-1', '2025-11-28', 'Califican como fuerza mayor determinados eventos que afectaron la ejecución del proyecto Central Solar Fotovoltaica Lupi y aprueban primera modificación de la concesión definitiva y del Contrato de Concesión N° 605-2023', 'Califican como fuerza mayor determinados eventos que afectaron la ejecución del proyecto Central Solar Fotovoltaica Lupi y aprueban primera modificación de la concesión definitiva y del Contrato de Concesión N° 605-2023', 'Baja', 'Pendiente', 'Califican como fuerza mayor determinados eventos que afectaron la ejecución del proyecto Central Solar Fotovoltaica Lupi y aprueban primera modificación de la concesión definitiva y del Contrato de Concesión N° 605-2023', NULL, '2025-11-28 16:31:31', '2025-11-28 16:31:31'),
	(127, 'DECRETO SUPREMO N° 053-2025-RE', 'DECRETO SUPREMO N° 053-2025-RE', 'RELACIONES EXTERIORES', 'https://busquedas.elperuano.pe/dispositivo/NL/2463489-1', '2025-11-28', 'Decreto Supremo que modifica el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Decreto Supremo que modifica el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Baja', 'Pendiente', 'Decreto Supremo que modifica el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', NULL, '2025-11-28 16:31:31', '2025-11-28 16:31:31'),
	(128, 'DECRETO SUPREMO N° 020-2025-SA', 'DECRETO SUPREMO N° 020-2025-SA', 'SALUD', 'https://busquedas.elperuano.pe/dispositivo/NL/2463489-2', '2025-11-28', 'Decreto Supremo que modifica el Reglamento de la Ley N° 30024 Ley que crea el Registro Nacional de Historias Clínicas Electrónicas aprobado por Decreto Supremo N° 009-2017-SA', 'Decreto Supremo que modifica el Reglamento de la Ley N° 30024 Ley que crea el Registro Nacional de Historias Clínicas Electrónicas aprobado por Decreto Supremo N° 009-2017-SA', 'Baja', 'Pendiente', 'Decreto Supremo que modifica el Reglamento de la Ley N° 30024 Ley que crea el Registro Nacional de Historias Clínicas Electrónicas aprobado por Decreto Supremo N° 009-2017-SA', NULL, '2025-11-28 16:31:31', '2025-11-28 16:31:31'),
	(129, 'RESOLUCION N° D000230-2025-MIDAGRI-SERFOR-DE', 'RESOLUCION N° D000230-2025-MIDAGRI-SERFOR-DE', 'DESARROLLO AGRARIO Y RIEGO', 'https://busquedas.elperuano.pe/dispositivo/NL/2463115-1', '2025-11-28', 'Aprueban otorgamiento de subvenciones a favor de tres entidades ejecutoras que vienen ejecutando sus Planes de Negocio Forestal para el Hito 2', 'Aprueban otorgamiento de subvenciones a favor de tres entidades ejecutoras que vienen ejecutando sus Planes de Negocio Forestal para el Hito 2', 'Baja', 'Pendiente', 'Aprueban otorgamiento de subvenciones a favor de tres entidades ejecutoras que vienen ejecutando sus Planes de Negocio Forestal para el Hito 2', NULL, '2025-11-28 17:06:29', '2025-11-28 17:06:29'),
	(130, 'DECRETO SUPREMO N° 279-2025-EF', 'DECRETO SUPREMO N° 279-2025-EF', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2464529-1', '2025-12-02', 'Decreto Supremo que aprueba la nueva escala remunerativa para los trabajadores contratados bajo el régimen laboral del Decreto Legislativo N° 728 que laboran en el Ministerio de Educación', 'Decreto Supremo que aprueba la nueva escala remunerativa para los trabajadores contratados bajo el régimen laboral del Decreto Legislativo N° 728 que laboran en el Ministerio de Educación', 'Baja', 'Pendiente', 'Decreto Supremo que aprueba la nueva escala remunerativa para los trabajadores contratados bajo el régimen laboral del Decreto Legislativo N° 728 que laboran en el Ministerio de Educación', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(131, 'DECRETO SUPREMO N° 280-2025-EF', 'DECRETO SUPREMO N° 280-2025-EF', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2464529-2', '2025-12-02', 'Decreto Supremo que autoriza Transferencias de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de la Reserva de Contingencia y del Ministerio de Vivienda Construcción y Saneamiento', 'Decreto Supremo que autoriza Transferencias de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de la Reserva de Contingencia y del Ministerio de Vivienda Construcción y Saneamiento', 'Baja', 'Pendiente', 'Decreto Supremo que autoriza Transferencias de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de la Reserva de Contingencia y del Ministerio de Vivienda Construcción y Saneamiento', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(132, 'DECRETO SUPREMO N° 281-2025-EF', 'DECRETO SUPREMO N° 281-2025-EF', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2464529-3', '2025-12-02', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversas universidades públicas', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversas universidades públicas', 'Baja', 'Pendiente', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversas universidades públicas', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(133, 'RESOLUCION DIRECTORAL N° 0019-2025-EF/50.01', 'RESOLUCION DIRECTORAL N° 0019-2025-EF/50.01', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2464389-1', '2025-12-02', 'Aprueban resultados finales del cumplimiento de las metas del Tramo III del Año 2024 del Programa de Incentivos a la Mejora de la Gestión Municipal', 'Aprueban resultados finales del cumplimiento de las metas del Tramo III del Año 2024 del Programa de Incentivos a la Mejora de la Gestión Municipal', 'Baja', 'Pendiente', 'Aprueban resultados finales del cumplimiento de las metas del Tramo III del Año 2024 del Programa de Incentivos a la Mejora de la Gestión Municipal', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(134, 'DECRETO SUPREMO N° 054-2025-RE', 'DECRETO SUPREMO N° 054-2025-RE', 'RELACIONES EXTERIORES', 'https://busquedas.elperuano.pe/dispositivo/NL/2464529-4', '2025-12-02', 'Decreto Supremo que autoriza al Instituto Nacional de Innovación Agraria a efectuar el pago de la cuota internacional no contemplada en el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Decreto Supremo que autoriza al Instituto Nacional de Innovación Agraria a efectuar el pago de la cuota internacional no contemplada en el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Baja', 'Pendiente', 'Decreto Supremo que autoriza al Instituto Nacional de Innovación Agraria a efectuar el pago de la cuota internacional no contemplada en el Anexo B: Cuotas Internacionales para el Año Fiscal 2025 de la Ley N° 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(135, 'RESOLUCION JEFATURAL N° 000156-2025-SIS/J', 'RESOLUCION JEFATURAL N° 000156-2025-SIS/J', 'SEGURO INTEGRAL DE SALUD', 'https://busquedas.elperuano.pe/dispositivo/NL/2464487-1', '2025-12-02', 'Aprueban Transferencia Financiera del Seguro Integral de Salud a favor de Unidades Ejecutoras para financiar las prestaciones de salud', 'Aprueban Transferencia Financiera del Seguro Integral de Salud a favor de Unidades Ejecutoras para financiar las prestaciones de salud', 'Media', 'Pendiente', 'Aprueban Transferencia Financiera del Seguro Integral de Salud a favor de Unidades Ejecutoras para financiar las prestaciones de salud', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(136, 'RESOLUCION N° 3652-2025-MP-FN', 'RESOLUCION N° 3652-2025-MP-FN', 'MINISTERIO PUBLICO', 'https://busquedas.elperuano.pe/dispositivo/NL/2464384-2', '2025-12-02', 'Amplían competencia funcional de la Fiscalía Suprema de Familia para que en adición a sus funciones conozca recursos de elevación de actuados provenientes de la Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos y de la Segunda Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos', 'Amplían competencia funcional de la Fiscalía Suprema de Familia para que en adición a sus funciones conozca recursos de elevación de actuados provenientes de la Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos y de la Segunda Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos', 'Baja', 'Pendiente', 'Amplían competencia funcional de la Fiscalía Suprema de Familia para que en adición a sus funciones conozca recursos de elevación de actuados provenientes de la Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos y de la Segunda Fiscalía Suprema Especializada en Delitos Cometidos por Funcionarios Públicos', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(137, 'ORDENANZA N° 737-MSB y Acuerdo N° 393', 'ORDENANZA N° 737-MSB y Acuerdo N° 393', 'MUNICIPALIDAD DE SAN BORJA', 'https://epdoc2.elperuano.pe/EpPo/VistaNLSE.asp?Referencias=MjQ2MTQxMi0xMjAyNTEyMDI=', '2025-12-02', 'Ordenanza que prorroga para el Ejercicio 2026 el régimen tributario de los arbitrios municipales de limpieza pública (barrido de calles y recolección de residuos sólidos) parques y jardines y seguridad ciudadana aprobado para el Ejercicio 2025', 'Ordenanza que prorroga para el Ejercicio 2026 el régimen tributario de los arbitrios municipales de limpieza pública (barrido de calles y recolección de residuos sólidos) parques y jardines y seguridad ciudadana aprobado para el Ejercicio 2025', 'Media', 'Pendiente', 'Ordenanza que prorroga para el Ejercicio 2026 el régimen tributario de los arbitrios municipales de limpieza pública (barrido de calles y recolección de residuos sólidos) parques y jardines y seguridad ciudadana aprobado para el Ejercicio 2025', NULL, '2025-12-02 15:05:40', '2025-12-02 15:05:40'),
	(138, 'LEY N° 32513, N° 32514 y N° 32515', 'LEY N° 32513, N° 32514 y N° 32515', 'CONGRESO DE LA REPUBLICA', 'https://epdoc2.elperuano.pe/EpPo/VistaNLSE.asp?Referencias=MjQ2NTUwMS0xMjAyNTEyMDQ=', '2025-12-04', 'Ley de Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Equilibrio Financiero del Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Endeudamiento del Sector Público para el Año Fiscal 2026', 'Ley de Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Equilibrio Financiero del Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Endeudamiento del Sector Público para el Año Fiscal 2026', 'Baja', 'Pendiente', 'Ley de Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Equilibrio Financiero del Presupuesto del Sector Público para el Año Fiscal 2026 Ley de Endeudamiento del Sector Público para el Año Fiscal 2026', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(139, 'DECRETO SUPREMO N° 284-2025-EF', 'DECRETO SUPREMO N° 284-2025-EF', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2465494-1', '2025-12-04', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversos Pliegos del Gobierno Nacional y de los Gobiernos Regionales', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversos Pliegos del Gobierno Nacional y de los Gobiernos Regionales', 'Baja', 'Pendiente', 'Decreto Supremo que autoriza Transferencia de Partidas en el Presupuesto del Sector Público para el Año Fiscal 2025 a favor de diversos Pliegos del Gobierno Nacional y de los Gobiernos Regionales', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(140, 'RESOLUCION VICE MINISTERIAL N° 140-2025-MINEDU', 'RESOLUCION VICE MINISTERIAL N° 140-2025-MINEDU', 'EDUCACION', 'https://busquedas.elperuano.pe/dispositivo/NL/2465183-1', '2025-12-04', 'Aprueban la norma técnica denominada Disposiciones que regulan la transferencia y escalamiento de las buenas prácticas de gestión educativa', 'Aprueban la norma técnica denominada Disposiciones que regulan la transferencia y escalamiento de las buenas prácticas de gestión educativa', 'Baja', 'Pendiente', 'Aprueban la norma técnica denominada Disposiciones que regulan la transferencia y escalamiento de las buenas prácticas de gestión educativa', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(141, 'Aprueban transferencia financiera a favor de la Contraloría General de la República destinada al pago de sociedad auditora que realizará la auditor...', 'RESOLUCION N° 149-IGP/2025', 'INSTITUTO GEOFISICO DEL PERU', 'https://busquedas.elperuano.pe/dispositivo/NL/2464785-1', '2025-12-04', 'Aprueban transferencia financiera a favor de la Contraloría General de la República destinada al pago de sociedad auditora que realizará la auditoría financiera gubernamental del período 2025', 'Aprueban transferencia financiera a favor de la Contraloría General de la República destinada al pago de sociedad auditora que realizará la auditoría financiera gubernamental del período 2025', 'Baja', 'Pendiente', 'Aprueban transferencia financiera a favor de la Contraloría General de la República destinada al pago de sociedad auditora que realizará la auditoría financiera gubernamental del período 2025', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(142, 'RESOLUCION N° D000083-2025-OECE-PRE', 'RESOLUCION N° D000083-2025-OECE-PRE', 'ORGANISMO ESPECIALIZADO PARA LAS CONTRATACIONES PÚBLICAS EFICIENTES', 'https://busquedas.elperuano.pe/dispositivo/NL/2465064-1', '2025-12-04', 'Formalizan la aprobación de la Directiva N° 018-2025-OECE-CD Directiva del registro de las valorizaciones de obra a través del Sistema Electrónico de Contrataciones del Estado – SEACE', 'Formalizan la aprobación de la Directiva N° 018-2025-OECE-CD Directiva del registro de las valorizaciones de obra a través del Sistema Electrónico de Contrataciones del Estado – SEACE', 'Baja', 'Pendiente', 'Formalizan la aprobación de la Directiva N° 018-2025-OECE-CD Directiva del registro de las valorizaciones de obra a través del Sistema Electrónico de Contrataciones del Estado – SEACE', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(143, 'RESOLUCION N° D000084-2025-OECE-PRE', 'RESOLUCION N° D000084-2025-OECE-PRE', 'ORGANISMO ESPECIALIZADO PARA LAS CONTRATACIONES PÚBLICAS EFICIENTES', 'https://busquedas.elperuano.pe/dispositivo/NL/2465067-1', '2025-12-04', 'Aprueban publicación en la sede digital de la OECE de proyecto de Directiva sobre la Ficha Única del Proveedor – FUP su anexo y exposición de motivos así como el proyecto de Resolución que la aprueba', 'Aprueban publicación en la sede digital de la OECE de proyecto de Directiva sobre la Ficha Única del Proveedor – FUP su anexo y exposición de motivos así como el proyecto de Resolución que la aprueba', 'Baja', 'Pendiente', 'Aprueban publicación en la sede digital de la OECE de proyecto de Directiva sobre la Ficha Única del Proveedor – FUP su anexo y exposición de motivos así como el proyecto de Resolución que la aprueba', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(144, 'Declaran de Interés y Prioridad Regional la Educación en Seguridad y Salud en el Trabajo y Normas Sociolaborales', 'ORDENANZA N° 017-2025-GRA/CR', 'GOBIERNO REGIONAL DE AYACUCHO', 'https://busquedas.elperuano.pe/dispositivo/NL/2461966-1', '2025-12-04', 'Declaran de Interés y Prioridad Regional la Educación en Seguridad y Salud en el Trabajo y Normas Sociolaborales', 'Declaran de Interés y Prioridad Regional la Educación en Seguridad y Salud en el Trabajo y Normas Sociolaborales', 'Alta', 'Pendiente', 'Declaran de Interés y Prioridad Regional la Educación en Seguridad y Salud en el Trabajo y Normas Sociolaborales', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(145, 'ORDENANZA N° 000018-2025-GRP/GR PUNO', 'ORDENANZA N° 000018-2025-GRP/GR PUNO', 'GOBIERNO REGIONAL DE PUNO', 'https://busquedas.elperuano.pe/dispositivo/NL/2456988-1', '2025-12-04', 'Ordenanza Regional que declara de interés regional la regulación del régimen de intangibilidad protección conservación defensa y mantenimiento de las áreas verdes de uso público en la región Puno', 'Ordenanza Regional que declara de interés regional la regulación del régimen de intangibilidad protección conservación defensa y mantenimiento de las áreas verdes de uso público en la región Puno', 'Baja', 'Pendiente', 'Ordenanza Regional que declara de interés regional la regulación del régimen de intangibilidad protección conservación defensa y mantenimiento de las áreas verdes de uso público en la región Puno', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(146, 'Ordenanza que aprueba la creación del Consejo Consultivo y Participativo de Niñas Niños y Adolescentes del distrito de Puente Piedra - CCONNA - PP', 'ORDENANZA N° 478-MDPP', 'MUNICIPALIDAD DE PUENTE PIEDRA', 'https://busquedas.elperuano.pe/dispositivo/NL/2463149-1', '2025-12-04', 'Ordenanza que aprueba la creación del Consejo Consultivo y Participativo de Niñas Niños y Adolescentes del distrito de Puente Piedra - CCONNA - PP', 'Ordenanza que aprueba la creación del Consejo Consultivo y Participativo de Niñas Niños y Adolescentes del distrito de Puente Piedra - CCONNA - PP', 'Baja', 'Pendiente', 'Ordenanza que aprueba la creación del Consejo Consultivo y Participativo de Niñas Niños y Adolescentes del distrito de Puente Piedra - CCONNA - PP', NULL, '2025-12-04 17:11:35', '2025-12-04 17:11:35'),
	(147, 'RESOLUCION JEFATURAL N° 000190-2025-ANIN/JEF', 'RESOLUCION JEFATURAL N° 000190-2025-ANIN/JEF', 'PRESIDENCIA DEL CONSEJO DE MINISTROS', 'https://busquedas.elperuano.pe/dispositivo/NL/2465774-1', '2025-12-05', 'Aprueban ejecución de la expropiación del inmueble afectado por la ejecución del Proyecto: Creación del servicio de protección contra inundaciones en la quebrada de San Idelfonso en los Distritos de El Porvenir Trujillo y Víctor Larco Herrera de la Provincia de Trujillo - Departamento de La Libertad identificado con CUI N° 2446345 y el valor de la Tasación', 'Aprueban ejecución de la expropiación del inmueble afectado por la ejecución del Proyecto: Creación del servicio de protección contra inundaciones en la quebrada de San Idelfonso en los Distritos de El Porvenir Trujillo y Víctor Larco Herrera de la Provincia de Trujillo - Departamento de La Libertad identificado con CUI N° 2446345 y el valor de la Tasación', 'Baja', 'Pendiente', 'Aprueban ejecución de la expropiación del inmueble afectado por la ejecución del Proyecto: Creación del servicio de protección contra inundaciones en la quebrada de San Idelfonso en los Distritos de El Porvenir Trujillo y Víctor Larco Herrera de la Provincia de Trujillo - Departamento de La Libertad identificado con CUI N° 2446345 y el valor de la Tasación', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(148, 'RESOLUCION MINISTERIAL N° 000335-2025-MC', 'RESOLUCION MINISTERIAL N° 000335-2025-MC', 'CULTURA', 'https://busquedas.elperuano.pe/dispositivo/NL/2465678-1', '2025-12-05', 'Disponen publicación del proyecto de Decreto Supremo que aprueba el Reglamento de Participación Ciudadana en Gestión Ambiental del Sector Cultura en el marco del Sistema Nacional de Evaluación de Impacto Ambiental (SEIA)', 'Disponen publicación del proyecto de Decreto Supremo que aprueba el Reglamento de Participación Ciudadana en Gestión Ambiental del Sector Cultura en el marco del Sistema Nacional de Evaluación de Impacto Ambiental (SEIA)', 'Baja', 'Pendiente', 'Disponen publicación del proyecto de Decreto Supremo que aprueba el Reglamento de Participación Ciudadana en Gestión Ambiental del Sector Cultura en el marco del Sistema Nacional de Evaluación de Impacto Ambiental (SEIA)', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(149, 'RESOLUCION MINISTERIAL N° 000334-2025-MC', 'RESOLUCION MINISTERIAL N° 000334-2025-MC', 'CULTURA', 'https://busquedas.elperuano.pe/dispositivo/NL/2465549-1', '2025-12-05', 'Disponen la publicación de proyecto de Decreto Supremo que aprueba el Reglamento de Gestión Ambiental del Sector Cultura y su exposición de motivos', 'Disponen la publicación de proyecto de Decreto Supremo que aprueba el Reglamento de Gestión Ambiental del Sector Cultura y su exposición de motivos', 'Baja', 'Pendiente', 'Disponen la publicación de proyecto de Decreto Supremo que aprueba el Reglamento de Gestión Ambiental del Sector Cultura y su exposición de motivos', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(150, 'DECRETO SUPREMO N° 286-2025-EF', 'DECRETO SUPREMO N° 286-2025-EF', 'ECONOMIA Y FINANZAS', 'https://busquedas.elperuano.pe/dispositivo/NL/2465988-1', '2025-12-05', 'Decreto Supremo que modifica el Decreto Supremo N° 268-2019-EF que aprueba las listas de insumos químicos productos y sus subproductos o derivados que son objeto de control y definen los bienes fiscalizados considerados de uso doméstico y artesanal conforme lo establecido en los artículos 5 y 16 del Decreto Legislativo N° 1126 para incorporar el Bisulfato de Sodio en el Anexo N° 1 que contiene la lista de insumos químicos productos subproductos y derivados sujetos al registro control y fiscalización', 'Decreto Supremo que modifica el Decreto Supremo N° 268-2019-EF que aprueba las listas de insumos químicos productos y sus subproductos o derivados que son objeto de control y definen los bienes fiscalizados considerados de uso doméstico y artesanal conforme lo establecido en los artículos 5 y 16 del Decreto Legislativo N° 1126 para incorporar el Bisulfato de Sodio en el Anexo N° 1 que contiene la lista de insumos químicos productos subproductos y derivados sujetos al registro control y fiscalización', 'Baja', 'Pendiente', 'Decreto Supremo que modifica el Decreto Supremo N° 268-2019-EF que aprueba las listas de insumos químicos productos y sus subproductos o derivados que son objeto de control y definen los bienes fiscalizados considerados de uso doméstico y artesanal conforme lo establecido en los artículos 5 y 16 del Decreto Legislativo N° 1126 para incorporar el Bisulfato de Sodio en el Anexo N° 1 que contiene la lista de insumos químicos productos subproductos y derivados sujetos al registro control y fiscalización', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(151, 'DECRETO SUPREMO N° 020-2025-MINEDU', 'DECRETO SUPREMO N° 020-2025-MINEDU', 'EDUCACION', 'https://busquedas.elperuano.pe/dispositivo/NL/2465988-2', '2025-12-05', 'Decreto Supremo que modifica el Anexo A: Subvenciones para Personas Jurídicas para el Año Fiscal 2025 de la Ley Nº 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Decreto Supremo que modifica el Anexo A: Subvenciones para Personas Jurídicas para el Año Fiscal 2025 de la Ley Nº 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', 'Baja', 'Pendiente', 'Decreto Supremo que modifica el Anexo A: Subvenciones para Personas Jurídicas para el Año Fiscal 2025 de la Ley Nº 32185 Ley de Presupuesto del Sector Público para el Año Fiscal 2025', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(152, 'Fijan Margen de Reserva Rotante para la Regulación Primaria de Frecuencia del Sistema Eléctrico Interconectado Nacional para el periodo de avenida y...', 'RESOLUCION N° 171-2025-OS/CD', 'ORGANISMO SUPERVISOR DE LA INVERSIÓN EN ENERGÍA Y MINERÍA', 'https://busquedas.elperuano.pe/dispositivo/NL/2465815-1', '2025-12-05', 'Fijan Margen de Reserva Rotante para la Regulación Primaria de Frecuencia del Sistema Eléctrico Interconectado Nacional para el periodo de avenida y para el periodo de estiaje del año 2026', 'Fijan Margen de Reserva Rotante para la Regulación Primaria de Frecuencia del Sistema Eléctrico Interconectado Nacional para el periodo de avenida y para el periodo de estiaje del año 2026', 'Baja', 'Pendiente', 'Fijan Margen de Reserva Rotante para la Regulación Primaria de Frecuencia del Sistema Eléctrico Interconectado Nacional para el periodo de avenida y para el periodo de estiaje del año 2026', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(153, 'RESOLUCION N° 158-2025-OS/PRES', 'RESOLUCION N° 158-2025-OS/PRES', 'ORGANISMO SUPERVISOR DE LA INVERSIÓN EN ENERGÍA Y MINERÍA', 'https://busquedas.elperuano.pe/dispositivo/NL/2465967-1', '2025-12-05', 'Autorizan publicar para comentarios el proyecto de Resolución de Consejo Directivo que modifica los artículos 19 20 y 21 del Reglamento del Registro de Hidrocarburos de Osinergmin aprobado por Resolución de Consejo Directivo N° 150-2024-OS/CD relativo a los supuestos de suspensión y cancelación de inscripción en el Registro de Hidrocarburos entre otras disposiciones', 'Autorizan publicar para comentarios el proyecto de Resolución de Consejo Directivo que modifica los artículos 19 20 y 21 del Reglamento del Registro de Hidrocarburos de Osinergmin aprobado por Resolución de Consejo Directivo N° 150-2024-OS/CD relativo a los supuestos de suspensión y cancelación de inscripción en el Registro de Hidrocarburos entre otras disposiciones', 'Baja', 'Pendiente', 'Autorizan publicar para comentarios el proyecto de Resolución de Consejo Directivo que modifica los artículos 19 20 y 21 del Reglamento del Registro de Hidrocarburos de Osinergmin aprobado por Resolución de Consejo Directivo N° 150-2024-OS/CD relativo a los supuestos de suspensión y cancelación de inscripción en el Registro de Hidrocarburos entre otras disposiciones', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(154, 'RESOLUCION N° 159-2025-OS/PRES', 'RESOLUCION N° 159-2025-OS/PRES', 'ORGANISMO SUPERVISOR DE LA INVERSIÓN EN ENERGÍA Y MINERÍA', 'https://busquedas.elperuano.pe/dispositivo/NL/2465970-1', '2025-12-05', 'Resolución de Presidencia del Consejo Directivo que autoriza la publicación para comentarios de la Norma Procedimiento Integrado para la Fiscalización de la Operatividad y Seguridad de las Instalaciones de Distribución Eléctrica', 'Resolución de Presidencia del Consejo Directivo que autoriza la publicación para comentarios de la Norma Procedimiento Integrado para la Fiscalización de la Operatividad y Seguridad de las Instalaciones de Distribución Eléctrica', 'Media', 'Pendiente', 'Resolución de Presidencia del Consejo Directivo que autoriza la publicación para comentarios de la Norma Procedimiento Integrado para la Fiscalización de la Operatividad y Seguridad de las Instalaciones de Distribución Eléctrica', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(155, 'ORDENANZA N° 522/MDSM y Acuerdo N° 408', 'ORDENANZA N° 522/MDSM y Acuerdo N° 408', 'MUNICIPALIDAD DE SAN MIGUEL', 'https://epdoc2.elperuano.pe/EpPo/VistaNLSE.asp?Referencias=MjQ2MzA4Ny0xMjAyNTEyMDU=', '2025-12-05', 'Ordenanza que aprueba el Régimen Tributario de los Arbitrios Municipales de Barrido de Calles Recolección de Residuos Sólidos Mantenimiento de Parques y Jardines Públicos y Serenazgo del año 2026', 'Ordenanza que aprueba el Régimen Tributario de los Arbitrios Municipales de Barrido de Calles Recolección de Residuos Sólidos Mantenimiento de Parques y Jardines Públicos y Serenazgo del año 2026', 'Baja', 'Pendiente', 'Ordenanza que aprueba el Régimen Tributario de los Arbitrios Municipales de Barrido de Calles Recolección de Residuos Sólidos Mantenimiento de Parques y Jardines Públicos y Serenazgo del año 2026', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(156, 'Ordenanza Municipal que aprueba el Texto Único de Procedimientos Administrativos (TUPA) de la Municipalidad Provincial de Atalaya', 'ORDENANZA N° 028-2025-MPA', 'MUNICIPALIDAD PROVINCIAL DE ATALAYA', 'https://busquedas.elperuano.pe/dispositivo/NL/2463870-1', '2025-12-05', 'Ordenanza Municipal que aprueba el Texto Único de Procedimientos Administrativos (TUPA) de la Municipalidad Provincial de Atalaya', 'Ordenanza Municipal que aprueba el Texto Único de Procedimientos Administrativos (TUPA) de la Municipalidad Provincial de Atalaya', 'Baja', 'Pendiente', 'Ordenanza Municipal que aprueba el Texto Único de Procedimientos Administrativos (TUPA) de la Municipalidad Provincial de Atalaya', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(157, 'Aprueban Texto Único de Procedimientos Administrativos - Tupa de la Municipalidad Distrital de Supe', 'ORDENANZA N° 008-2022-MDS', 'MUNICIPALIDAD DISTRITAL DE SUPE', 'https://busquedas.elperuano.pe/dispositivo/NL/2464156-1', '2025-12-05', 'Aprueban Texto Único de Procedimientos Administrativos - Tupa de la Municipalidad Distrital de Supe', 'Aprueban Texto Único de Procedimientos Administrativos - Tupa de la Municipalidad Distrital de Supe', 'Baja', 'Pendiente', 'Aprueban Texto Único de Procedimientos Administrativos - Tupa de la Municipalidad Distrital de Supe', NULL, '2025-12-05 20:54:18', '2025-12-05 20:54:18'),
	(158, 'Declaran infundado recurso de apelación interpuesto por alcalde de la Municipalidad Distrital de Ocoyo y confirman la Res. Nº 00361-2025-JEE-HVCA/JN...', 'RESOLUCION N° 0738-2025-JNE', 'JURADO NACIONAL DE ELECCIONES', 'https://busquedas.elperuano.pe/dispositivo/NL/2466888-1', '2025-12-11', 'Declaran infundado recurso de apelación interpuesto por alcalde de la Municipalidad Distrital de Ocoyo y confirman la Res. Nº 00361-2025-JEE-HVCA/JNE que dispuso imponer sanción de amonestación y multa por vulnerar las normas de publicidad estatal en el marco de las Elecciones Generales 2026', 'Declaran infundado recurso de apelación interpuesto por alcalde de la Municipalidad Distrital de Ocoyo y confirman la Res. Nº 00361-2025-JEE-HVCA/JNE que dispuso imponer sanción de amonestación y multa por vulnerar las normas de publicidad estatal en el marco de las Elecciones Generales 2026', 'Media', 'Pendiente', 'Declaran infundado recurso de apelación interpuesto por alcalde de la Municipalidad Distrital de Ocoyo y confirman la Res.', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28'),
	(159, 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00228-2025-JEE-PASC/JNE...', 'RESOLUCION N° 0734-2025-JNE', 'JURADO NACIONAL DE ELECCIONES', 'https://busquedas.elperuano.pe/dispositivo/NL/2466589-1', '2025-12-11', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00228-2025-JEE-PASC/JNE emitida por el Jurado Electoral Especial de Pasco que dispuso imponer la sanción de amonestación pública y multa por vulnerar el Reglamento sobre Propaganda Electoral Publicidad Estatal y Neutralidad en Periodo Electoral en el marco de las Elecciones Generales 2026', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00228-2025-JEE-PASC/JNE emitida por el Jurado Electoral Especial de Pasco que dispuso imponer la sanción de amonestación pública y multa por vulnerar el Reglamento sobre Propaganda Electoral Publicidad Estatal y Neutralidad en Periodo Electoral en el marco de las Elecciones Generales 2026', 'Media', 'Pendiente', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res.', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28'),
	(160, 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00231-2025-JEE-PASC/JNE...', 'RESOLUCION N° 0735-2025-JNE', 'JURADO NACIONAL DE ELECCIONES', 'https://busquedas.elperuano.pe/dispositivo/NL/2466591-1', '2025-12-11', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00231-2025-JEE-PASC/JNE emitida por el Jurado Electoral Especial de Pasco que dispuso imponer la sanción de amonestación pública y multa por vulnerar el Reglamento sobre Propaganda Electoral Publicidad Estatal y Neutralidad en Periodo Electoral en el marco de las Elecciones Generales 2026', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res. Nº 00231-2025-JEE-PASC/JNE emitida por el Jurado Electoral Especial de Pasco que dispuso imponer la sanción de amonestación pública y multa por vulnerar el Reglamento sobre Propaganda Electoral Publicidad Estatal y Neutralidad en Periodo Electoral en el marco de las Elecciones Generales 2026', 'Media', 'Pendiente', 'Declaran infundado recurso de apelación interpuesto por el gobernador del Gobierno Regional de Pasco; y confirman la Res.', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28'),
	(161, 'Declaran infundado recurso de apelación interpuesto por el gerente municipal de la Municipalidad Provincial de Tayacaja departamento de Huancavelica;...', 'RESOLUCION N° 0740-2025-JNE', 'JURADO NACIONAL DE ELECCIONES', 'https://busquedas.elperuano.pe/dispositivo/NL/2466607-1', '2025-12-11', 'Declaran infundado recurso de apelación interpuesto por el gerente municipal de la Municipalidad Provincial de Tayacaja departamento de Huancavelica; y confirman la Resolución Nº 00406-2025-JEE-HVCA/JNE emitida por el Jurado Electoral Especial de Huancavelica', 'Declaran infundado recurso de apelación interpuesto por el gerente municipal de la Municipalidad Provincial de Tayacaja departamento de Huancavelica; y confirman la Resolución Nº 00406-2025-JEE-HVCA/JNE emitida por el Jurado Electoral Especial de Huancavelica', 'Baja', 'Pendiente', 'Declaran infundado recurso de apelación interpuesto por el gerente municipal de la Municipalidad Provincial de Tayacaja departamento de Huancavelica; y confirman la Resolución Nº 00406-2025-JEE-HVCA/JNE emitida por el Jurado Electoral Especial de Huancavelica', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28'),
	(162, 'Confirman resolución emitida por el Jurado Electoral Especial de Coronel Portillo que dispuso imponer sanción de amonestación y multa a alcalde de ...', 'RESOLUCION N° 0732-2025-JNE', 'JURADO NACIONAL DE ELECCIONES', 'https://busquedas.elperuano.pe/dispositivo/NL/2466615-1', '2025-12-11', 'Confirman resolución emitida por el Jurado Electoral Especial de Coronel Portillo que dispuso imponer sanción de amonestación y multa a alcalde de la Municipalidad Distrital de Irazola provincia de Padre Abad departamento de Ucayali por vulnerar las normas de publicidad estatal', 'Confirman resolución emitida por el Jurado Electoral Especial de Coronel Portillo que dispuso imponer sanción de amonestación y multa a alcalde de la Municipalidad Distrital de Irazola provincia de Padre Abad departamento de Ucayali por vulnerar las normas de publicidad estatal', 'Media', 'Pendiente', 'Confirman resolución emitida por el Jurado Electoral Especial de Coronel Portillo que dispuso imponer sanción de amonestación y multa a alcalde de la Municipalidad Distrital de Irazola provincia de Padre Abad departamento de Ucayali por vulnerar las normas de publicidad estatal', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28'),
	(163, 'Aprueban primera inscripción de dominio de inmueble de la municipalidad ante la Superintendencia Nacional de los Registros Públicos', 'ACUERDO N° 094-2025-MDC/A.', 'MUNICIPALIDAD DISTRITAL DE COLAN', 'https://busquedas.elperuano.pe/dispositivo/NL/2462707-1', '2025-12-11', 'Aprueban primera inscripción de dominio de inmueble de la municipalidad ante la Superintendencia Nacional de los Registros Públicos', 'Aprueban primera inscripción de dominio de inmueble de la municipalidad ante la Superintendencia Nacional de los Registros Públicos', 'Baja', 'Pendiente', 'Aprueban primera inscripción de dominio de inmueble de la municipalidad ante la Superintendencia Nacional de los Registros Públicos', NULL, '2025-12-11 16:16:28', '2025-12-11 16:16:28');

-- Volcando estructura para tabla kallpaq.reporte_satisfaccions
CREATE TABLE IF NOT EXISTS `reporte_satisfaccions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anio` int(11) NOT NULL,
  `trimestre` int(11) NOT NULL,
  `fecha_generacion` date NOT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `resumen_encuestas` text DEFAULT NULL,
  `resumen_sugerencias` text DEFAULT NULL,
  `reclamos` text DEFAULT NULL,
  `resumen_snc` text DEFAULT NULL,
  `oportunidades_mejora` text DEFAULT NULL,
  `conclusiones` text DEFAULT NULL,
  `archivo_path` varchar(255) DEFAULT NULL,
  `estado` enum('borrador','generado','firmado') NOT NULL DEFAULT 'borrador',
  `archivo_firmado` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reporte_satisfaccions_proceso_id_foreign` (`proceso_id`),
  KEY `reporte_satisfaccions_user_id_foreign` (`user_id`),
  CONSTRAINT `reporte_satisfaccions_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`),
  CONSTRAINT `reporte_satisfaccions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.reporte_satisfaccions: ~1 rows (aproximadamente)
REPLACE INTO `reporte_satisfaccions` (`id`, `anio`, `trimestre`, `fecha_generacion`, `proceso_id`, `resumen_encuestas`, `resumen_sugerencias`, `reclamos`, `resumen_snc`, `oportunidades_mejora`, `conclusiones`, `archivo_path`, `estado`, `archivo_firmado`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, 2025, 4, '2026-01-19', 151, 'Se consideró los resultados de la medición correspondiente al periodo Trimestral del año 2025. Se obtuvo un índice de satisfacción (Score Global) de 4.2% y un NPS de 89.00. La medición se realizó sobre una muestra de 120 encuestados. Los resultados por conductor fueron: Oportunidad: 4.00, Trato: 4.00, Claridad: 4.00, Seguridad: 4.00, Calidad: 5.00.', 'Durante el periodo 4 del año 2025, se recibieron un total de 1 sugerencias. El estado actual de las mismas es: abierta (1). Del total recibido, 0 han sido analizadas y cuentan con tratamiento definido.', 'No se registraron reclamos en este periodo.', 'Se identificaron 1 salidas no conformes en el periodo. De las cuales, 0 se encuentran cerradas y 1 permanecen en proceso de tratamiento/cierre.', '- Implementar un plan de seguimiento para la sugerencia abierta, asegurando que se analice y se le dé tratamiento definido en un plazo máximo de 30 días, con el fin de mejorar la percepción del servicio.\n- Realizar capacitaciones adicionales al personal en las áreas de Oportunidad, Trato y Claridad, dado que estas dimensiones obtuvieron un puntaje de 4.00, lo que indica que hay margen para mejorar la experiencia del usuario.\n- Establecer un sistema de monitoreo y análisis de las Salidas No Conformes (SNC) para identificar las causas raíz y prevenir su recurrencia, asegurando que se cierren en un plazo definido y se implementen acciones correctivas efectivas.', 'El índice de satisfacción global de 4.2% y un NPS de 89.00 reflejan un desempeño positivo en la percepción del servicio, aunque el puntaje de satisfacción en las dimensiones de Oportunidad, Trato y Claridad sugiere que existen áreas de mejora que deben ser atendidas. La ausencia de reclamos durante el trimestre es un indicador favorable, sin embargo, la única sugerencia abierta y la SNC en proceso de tratamiento resaltan la necesidad de una gestión más proactiva en la atención al cliente.\nA pesar de los desafíos, el alto NPS indica que los usuarios valoran positivamente la calidad del servicio ofrecido. Esto sugiere que, si bien hay aspectos que requieren atención, la base de satisfacción es sólida. La implementación de acciones correctivas y de mejora continua será fundamental para mantener y elevar estos estándares en el futuro.', 'reportes_satisfaccion/n9k3GMH4FFLpfz0BnIvIfvPKgP1miRru4fLQZVyG.pdf', 'firmado', NULL, 14, '2026-01-19 17:01:14', '2026-01-19 20:10:08');

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
  CONSTRAINT `requerimientos_facilitador_id` FOREIGN KEY (`facilitador_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimientos: ~18 rows (aproximadamente)
REPLACE INTO `requerimientos` (`id`, `proceso_id`, `user_asigna_id`, `facilitador_id`, `especialista_id`, `asunto`, `descripcion`, `justificacion`, `comentario`, `estado`, `prioridad`, `complejidad`, `ruta_archivo_desistimacion`, `ruta_archivo_requerimiento`, `fecha_limite`, `fecha_asignacion`, `fecha_inicio`, `fecha_fin`, `updated_at`, `created_at`) VALUES
	(1, 162, 207, 1, 1, 'Revisión y optimización del Proceso de Logística', 'Realizar una revisión integral del proceso PR-102 para identificar cuellos de botella y proponer mejoras en el flujo de aprobación.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', '', 'atendido', 'muy alta', 'alta', NULL, 'documentos/ejemplo_requerimiento.pdf', '2026-04-03 00:21:00', '2025-11-05 19:03:33', '2025-11-04 00:21:00', '2025-11-05 19:24:02', '2025-11-05 19:24:02', '2025-10-13 02:33:10'),
	(2, 58, 12, 198, 2, 'Elaboración de Manual de Perfiles de Puesto (MPP)', 'Se solicita la elaboración de la Directiva MA-942 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Resolución N° 055-2025-CGR.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', NULL, 'atendido', 'media', 'alta', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-06-12 14:49:39', '2025-05-29 04:20:59', '2025-06-09 03:03:45', '2025-07-15 00:24:08', '2025-05-26 16:00:37', '2025-05-26 16:00:37'),
	(3, 208, 57, 207, 7, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva PR-286 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Ley N° 30512.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', 'Documento ha sido desestimado por parte del solicitante', 'desestimado', 'media', 'muy alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-07-06 18:42:17', '2025-07-06 18:42:17'),
	(4, 261, 202, 189, 4, 'Revisión y optimización del Proceso de Logística', 'Actualizar el procedimiento FO-951 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'atendido', 'baja', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-06 14:05:31', '2025-09-03 21:30:26', '2025-09-28 06:58:59', '2025-10-09 07:02:02', '2025-09-02 08:56:56', '2025-09-02 08:56:56'),
	(5, 84, 29, 187, 2, 'Elaboración de nueva Directiva de Gestión Documental', 'Incluir el indicador "Porcentaje de satisfacción del usuario" en la Ficha de Proceso de MA-694, con su respectiva fórmula de cálculo.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', 'Se desestimará', 'desestimado', 'alta', 'media', 'requerimientos/5/deestimacion/4PZQ0fPsz8ML7l6ChPYhnwj2Bz9j3HEWFu5SHqN5.pdf', 'documentos/ejemplo_requerimiento.pdf', '2025-01-23 00:20:34', '2025-11-04 00:49:27', '2025-11-04 00:20:34', '2025-11-05 22:16:47', '2025-11-05 22:16:47', '2025-09-12 05:15:56'),
	(6, 130, 54, 210, 2, 'Creación de formato de registro para Control de Calidad', 'Elaborar el Manual de Perfiles de Puesto para las 3 nuevas posiciones creadas en el área de Planificación y Presupuesto.', 'Alineamiento con los objetivos estratégicos del Plan Operativo Institucional (POI) 2026.', NULL, 'asignado', 'muy alta', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2026-03-04 22:37:31', '2025-12-05 21:26:37', '2025-11-04 22:37:31', NULL, '2025-12-05 21:26:37', '2025-06-14 23:48:44'),
	(7, 101, 37, 1, 1, 'Inclusión de nuevo indicador en el Proceso de Atención al Ciudadano', 'Elaborar el Manual de Perfiles de Puesto para las 3 nuevas posiciones creadas en el área de Planificación y Presupuesto.', 'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.', NULL, 'desestimado', 'media', 'baja', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-08-09 17:16:50', '2025-08-09 17:16:50'),
	(8, 14, 203, 1, 2, 'Inclusión de nuevo indicador en el Proceso de Atención al Ciudadano', 'Actualizar el procedimiento FO-885 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'asignado', 'muy alta', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-04-03 00:23:42', '2025-11-04 00:30:49', '2025-11-04 00:23:42', NULL, '2025-11-04 00:30:49', '2025-10-06 22:58:32'),
	(9, 119, 194, 11, 5, 'Actualización del Manual de Procedimientos (MAPRO)', 'Actualizar el procedimiento FO-495 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'atendido', 'media', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-29 05:43:32', '2025-09-03 23:49:32', '2025-09-18 18:46:33', '2025-10-17 09:36:20', '2025-06-11 11:09:35', '2025-06-11 11:09:35'),
	(10, 252, 16, 1, 1, 'Revisión y optimización del Proceso de Logística', 'Incluir el indicador "Porcentaje de satisfacción del usuario" en la Ficha de Proceso de MA-731, con su respectiva fórmula de cálculo.', 'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.', NULL, 'asignado', 'baja', 'media', NULL, '[{"path":"requerimientos\\/10\\/signed_requerimiento\\/CTvQ04RIIKtChOjKTImrX9WDXiGFZzBkwg3nBHgq.pdf","name":"requerimiento-10.pdf"}]', '2026-01-09 17:37:41', '2025-11-10 17:37:41', '2025-11-10 17:37:41', NULL, '2025-11-10 17:37:41', '2025-08-23 03:02:00'),
	(11, 153, 48, 40, 5, 'Actualización del Manual de Procedimientos (MAPRO)', 'Modificar la Ficha de Proceso FO-874 para añadir las nuevas actividades de control interno detectadas en la última auditoría.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'atendido', 'muy alta', 'baja', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-01 04:02:35', '2025-08-22 21:48:17', '2025-10-07 07:24:16', '2025-10-07 08:09:57', '2025-06-19 08:17:59', '2025-06-19 08:17:59'),
	(12, 112, 200, 1, 7, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva MA-933 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Resolución N° 055-2025-CGR.', 'Alineamiento con los objetivos estratégicos del Plan Operativo Institucional (POI) 2026.', NULL, 'atendido', 'muy alta', 'media', NULL, 'documentos/ejemplo_requerimiento.pdf', '2025-09-03 01:10:36', '2025-08-29 03:58:36', '2025-09-19 01:19:10', '2025-10-31 21:34:58', '2025-07-03 19:34:54', '2025-07-03 19:34:54'),
	(13, 153, 68, 211, 1, 'Actualización del Manual de Procedimientos (MAPRO)', 'Se solicita la elaboración de la Directiva MA-910 que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa Ley N° 30512.', 'Necesidad de mejora de la eficiencia operativa y reducción de costos en el área solicitante.', NULL, 'desestimado', 'media', 'alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-09-26 07:39:39', '2025-09-26 07:39:39'),
	(14, 137, 36, 187, 1, 'Actualización del Manual de Procedimientos (MAPRO)', 'Actualizar el procedimiento MA-471 "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.', 'Necesidad de mejora de la eficiencia operativa y reducción de costos en el área solicitante.', NULL, 'desestimado', 'baja', 'alta', 'documentos/ejemplo_desistimacion.pdf', 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-10-27 15:10:04', '2025-10-27 15:10:04'),
	(15, 96, 1, 191, NULL, 'Actualización del Manual de Procedimientos (MAPRO)', 'Modificar la Ficha de Proceso MA-562 para añadir las nuevas actividades de control interno detectadas en la última auditoría.', 'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.', NULL, 'creado', 'muy alta', 'muy alta', NULL, 'documentos/ejemplo_requerimiento.pdf', NULL, NULL, NULL, NULL, '2025-10-23 00:15:58', '2025-10-23 00:15:58'),
	(16, 3, NULL, 1, NULL, 'sadasd', 'dsaddasdsad', 'dsadsadasddsaddsad', NULL, 'creado', NULL, 'media', NULL, '[{"path":"requerimientos\\/16\\/signed_requerimiento\\/kcC8NvCNhIZbrIuJRXSRdhv2mRs3hgoXMw4ydQJy.pdf","name":"Ficha de Requerimiento - RQ - 016.pdf"},{"path":"requerimientos\\/16\\/signed_requerimiento\\/Biu1IlOcrj1zAT22HTUHNRURP8odESn0PHM2mVxe.pdf","name":"Hazle+una+pregunta+al+asistente+y+analiza+su+fuente.pdf"},{"path":"requerimientos\\/16\\/signed_requerimiento\\/a77VtKtV9pImlbEoXVUq56Y9shAnah4SMwLik1M7.pdf","name":"Listado+de+Prompts+Resumen+Ejecutivo+en+NotebookLM.pdf"}]', NULL, NULL, NULL, NULL, '2025-11-10 17:46:06', '2025-11-07 00:59:10'),
	(17, 4, NULL, 1, 1, 'cxzc', 'czxcxc', 'czxcxzc', NULL, 'asignado', NULL, 'baja', NULL, NULL, '2026-02-18 00:21:15', '2026-01-19 00:21:15', '2026-01-19 00:21:15', NULL, '2026-01-19 00:21:15', '2025-11-20 17:42:16'),
	(18, 151, NULL, 14, NULL, 'Nuevo', 'Ejemplo 02', 'justifcacion', NULL, 'aprobado', NULL, 'baja', NULL, '[{"path":"requerimientos\\/18\\/signed_requerimiento\\/C8yjp22D7EI6ovROMyLYEHIAhBjVScsuUw39jyly.pdf","name":"requerimiento_18.pdf"},{"path":"requerimientos\\/18\\/signed_requerimiento\\/dfLzPeqNmBpT8NL1PtpEJBpGgX7koczoojkZvDGG.pdf","name":"CERTIFICADO_DE_APROBACIN.pdf"}]', NULL, NULL, NULL, NULL, '2026-01-19 01:25:49', '2026-01-19 01:11:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_avances: ~6 rows (aproximadamente)
REPLACE INTO `requerimiento_avances` (`id`, `requerimiento_id`, `levantamiento`, `comentario_levantamiento`, `contexto`, `comentario_contexto`, `caracterizacion`, `comentario_caracterizacion`, `formatos`, `comentario_formatos`, `revision_interna`, `comentario_revision_interna`, `revision_tecnica`, `comentario_revision_tecnica`, `firma`, `comentario_firma`, `publicacion`, `comentario_publicacion`, `ruta_evidencias`, `avance_registrado`, `created_at`, `updated_at`) VALUES
	(6, 5, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 1.00, '2025-11-04 00:20:34', '2025-11-04 00:20:34'),
	(7, 1, 1, 'dsadsad', 1, 'nuevo avance', 1, NULL, 1, NULL, 1, NULL, 1, NULL, 1, NULL, 1, NULL, '[{"name":"MEMORANDO-000365-2025-SCP.pdf","path":"requerimientos\\/1\\/evidencias\\/JhodXUmVhWXFJzZXV9IpR1Pi1eWEAMoGWEV1r45r.pdf","url":"http:\\/\\/localhost\\/storage\\/requerimientos\\/1\\/evidencias\\/JhodXUmVhWXFJzZXV9IpR1Pi1eWEAMoGWEV1r45r.pdf"},{"name":"01Taller _ Inter37001.docx","path":"requerimientos\\/1\\/evidencias\\/hhz3mXt3Tu28JQoYQhl2SCEXTWCXM5kBnCoAnWA6.docx","url":"http:\\/\\/localhost\\/storage\\/requerimientos\\/1\\/evidencias\\/hhz3mXt3Tu28JQoYQhl2SCEXTWCXM5kBnCoAnWA6.docx"},{"name":"Memo._Circ._00125.pdf","path":"requerimientos\\/1\\/evidencias\\/Xny2OteQ0kgnpxAs3Rrz6OZaN7CNyoHOcbb63utJ.pdf","url":"http:\\/\\/127.0.0.1:8000\\/storage\\/requerimientos\\/1\\/evidencias\\/Xny2OteQ0kgnpxAs3Rrz6OZaN7CNyoHOcbb63utJ.pdf"}]', 100.00, '2025-11-04 00:21:00', '2025-11-05 19:23:50'),
	(8, 8, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, '[{"name":"MEMORANDO ABAS.docx","path":"requerimientos\\/8\\/evidencias\\/USXXL9UJaDu4kbEWkhrNc023ejf0J1ry9yKNP3RV.docx","url":"http:\\/\\/127.0.0.1:8000\\/storage\\/requerimientos\\/8\\/evidencias\\/USXXL9UJaDu4kbEWkhrNc023ejf0J1ry9yKNP3RV.docx"}]', 1.00, '2025-11-04 00:23:42', '2025-11-05 19:51:34'),
	(9, 6, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 2.00, '2025-11-04 22:03:12', '2025-11-04 22:37:31'),
	(13, 10, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 2.00, '2025-11-10 17:37:41', '2025-11-10 17:37:41'),
	(14, 17, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, 2.00, '2026-01-19 00:21:15', '2026-01-19 00:21:15');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_evaluaciones: ~6 rows (aproximadamente)
REPLACE INTO `requerimiento_evaluaciones` (`id`, `requerimiento_id`, `num_actividades`, `num_areas`, `num_requisitos`, `nivel_documentacion`, `impacto_requerimiento`, `complejidad_valor`, `complejidad_nivel`, `fecha_evaluacion`, `created_at`, `updated_at`) VALUES
	(2, 5, 1, 2, 2, 3, 4, 12, 'media', '2025-11-03', '2025-11-03 23:57:45', '2025-11-04 00:00:52'),
	(3, 6, 1, 2, 3, 3, 3, 12, 'media', '2025-11-04', '2025-11-04 22:31:54', '2025-11-04 22:31:54'),
	(4, 16, 1, 1, 2, 3, 4, 11, 'media', '2025-12-01', '2025-11-07 19:10:05', '2025-12-01 21:05:17'),
	(5, 10, 1, 1, 2, 2, 4, 10, 'media', '2025-11-10', '2025-11-10 17:24:27', '2025-11-10 17:37:25'),
	(6, 17, 1, 1, 2, 2, 2, 8, 'baja', '2026-01-18', '2025-11-20 17:42:36', '2026-01-19 00:20:45'),
	(7, 18, 1, 1, 1, 1, 1, 5, 'baja', '2026-01-18', '2026-01-19 01:11:48', '2026-01-19 01:20:40');

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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.requerimiento_movimientos: ~55 rows (aproximadamente)
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
	(51, 16, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 11 y nivel: media', 1, '2025-11-10 17:46:06', '2025-11-10 17:46:06'),
	(52, 6, 'asignado', 'Asignación del requerimiento al especialista ID: 2', 1, '2025-12-05 21:26:37', '2025-12-05 21:26:37'),
	(53, 17, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 8 y nivel: baja', 1, '2026-01-19 00:12:15', '2026-01-19 00:12:15'),
	(54, 17, 'evaluado', 'Evaluación de complejidad registrada con puntaje: 8 y nivel: baja', 1, '2026-01-19 00:20:45', '2026-01-19 00:20:45'),
	(55, 17, 'asignado', 'Asignación del requerimiento al especialista ID: 1', 1, '2026-01-19 00:21:15', '2026-01-19 00:21:15');

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

-- Volcando estructura para tabla kallpaq.revisiones_direccion
CREATE TABLE IF NOT EXISTS `revisiones_direccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `fecha_programada` date NOT NULL,
  `fecha_reunion` date DEFAULT NULL,
  `periodo` varchar(20) NOT NULL,
  `anio` year(4) NOT NULL,
  `participantes` text DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('programada','en_preparacion','realizada','cancelada') NOT NULL DEFAULT 'programada',
  `sistemas_gestion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sistemas_gestion`)),
  `acta_path` varchar(255) DEFAULT NULL,
  `responsable_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `revisiones_direccion_codigo_unique` (`codigo`),
  KEY `revisiones_direccion_responsable_id_foreign` (`responsable_id`),
  KEY `revisiones_direccion_created_by_foreign` (`created_by`),
  CONSTRAINT `revisiones_direccion_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `revisiones_direccion_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.revisiones_direccion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.revision_compromisos
CREATE TABLE IF NOT EXISTS `revision_compromisos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `revision_id` bigint(20) unsigned NOT NULL,
  `salida_id` bigint(20) unsigned DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `responsable_id` bigint(20) unsigned NOT NULL,
  `fecha_limite` date NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','completado','vencido','cancelado') NOT NULL DEFAULT 'pendiente',
  `sistemas_gestion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sistemas_gestion`)),
  `recursos_necesarios` text DEFAULT NULL,
  `evidencia_path` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `avance` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_compromisos_revision_id_foreign` (`revision_id`),
  KEY `revision_compromisos_salida_id_foreign` (`salida_id`),
  KEY `revision_compromisos_responsable_id_foreign` (`responsable_id`),
  CONSTRAINT `revision_compromisos_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`),
  CONSTRAINT `revision_compromisos_revision_id_foreign` FOREIGN KEY (`revision_id`) REFERENCES `revisiones_direccion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `revision_compromisos_salida_id_foreign` FOREIGN KEY (`salida_id`) REFERENCES `revision_salidas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.revision_compromisos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.revision_compromiso_seguimientos
CREATE TABLE IF NOT EXISTS `revision_compromiso_seguimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compromiso_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `comentario` text NOT NULL,
  `avance_anterior` int(11) DEFAULT NULL,
  `avance_nuevo` int(11) NOT NULL,
  `estado_anterior` varchar(255) DEFAULT NULL,
  `estado_nuevo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_compromiso_seguimientos_compromiso_id_foreign` (`compromiso_id`),
  KEY `revision_compromiso_seguimientos_user_id_foreign` (`user_id`),
  CONSTRAINT `revision_compromiso_seguimientos_compromiso_id_foreign` FOREIGN KEY (`compromiso_id`) REFERENCES `revision_compromisos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `revision_compromiso_seguimientos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.revision_compromiso_seguimientos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.revision_entradas
CREATE TABLE IF NOT EXISTS `revision_entradas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `revision_id` bigint(20) unsigned NOT NULL,
  `tipo_entrada` enum('estado_acciones_anteriores','cambios_contexto_externo','cambios_contexto_interno','retroalimentacion_partes_interesadas','desempeno_procesos','conformidad_productos_servicios','no_conformidades_acciones_correctivas','resultados_auditorias','desempeno_proveedores','adecuacion_recursos','eficacia_acciones_riesgos','oportunidades_mejora','satisfaccion_cliente','cumplimiento_objetivos','otros') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `datos_soporte` text DEFAULT NULL,
  `conclusion` text DEFAULT NULL,
  `estado` enum('pendiente','revisado','aprobado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_entradas_revision_id_foreign` (`revision_id`),
  CONSTRAINT `revision_entradas_revision_id_foreign` FOREIGN KEY (`revision_id`) REFERENCES `revisiones_direccion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.revision_entradas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.revision_salidas
CREATE TABLE IF NOT EXISTS `revision_salidas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `revision_id` bigint(20) unsigned NOT NULL,
  `tipo_salida` enum('decision_mejora','necesidad_cambio_sgc','necesidad_recursos','actualizacion_riesgos','actualizacion_objetivos','otros') NOT NULL,
  `descripcion` text NOT NULL,
  `justificacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_salidas_revision_id_foreign` (`revision_id`),
  CONSTRAINT `revision_salidas_revision_id_foreign` FOREIGN KEY (`revision_id`) REFERENCES `revisiones_direccion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.revision_salidas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla kallpaq.riesgos
CREATE TABLE IF NOT EXISTS `riesgos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_cod` varchar(255) DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  `especialista_id` bigint(20) unsigned DEFAULT 0,
  `riesgo_nombre` text NOT NULL,
  `riesgo_tipo` enum('Riesgo','Oportunidad') DEFAULT NULL,
  `factor_id` bigint(20) unsigned DEFAULT NULL,
  `riesgo_controles` text DEFAULT NULL,
  `riesgo_consecuencia` text DEFAULT NULL,
  `riesgo_probabilidad` int(11) NOT NULL,
  `riesgo_impacto` int(11) NOT NULL,
  `riesgo_valor` int(11) NOT NULL,
  `riesgo_nivel` enum('bajo','medio','alto','muy alto') DEFAULT NULL,
  `riesgo_matriz` enum('estrategica','tactica') DEFAULT NULL,
  `riesgo_tratamiento` enum('reducir','aceptar','compartir','aprovechar') DEFAULT NULL,
  `riesgo_estado` enum('proyecto','aprobado','en proceso','concluido','evaluado','cerrado') NOT NULL,
  `riesgo_fecha_valoracion_rr` date DEFAULT NULL,
  `riesgo_probabilidad_rr` int(11) DEFAULT NULL,
  `riesgo_impacto_rr` int(11) DEFAULT NULL,
  `riesgo_valor_rr` int(11) DEFAULT NULL,
  `riesgo_nivel_rr` enum('bajo','medio','alto','muy alto') DEFAULT NULL,
  `riesgo_estado_rr` enum('con eficacia','sin eficacia') DEFAULT NULL,
  `riesgo_ciclo` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `riesgos_riesgo_cod_unique` (`riesgo_cod`),
  KEY `riesgos_proceso_cod_foreign` (`proceso_id`),
  KEY `riesgos_factor_cod_foreign` (`factor_id`),
  CONSTRAINT `riesgos_factor_cod_foreign` FOREIGN KEY (`factor_id`) REFERENCES `factores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgos: ~51 rows (aproximadamente)
REPLACE INTO `riesgos` (`id`, `riesgo_cod`, `proceso_id`, `especialista_id`, `riesgo_nombre`, `riesgo_tipo`, `factor_id`, `riesgo_controles`, `riesgo_consecuencia`, `riesgo_probabilidad`, `riesgo_impacto`, `riesgo_valor`, `riesgo_nivel`, `riesgo_matriz`, `riesgo_tratamiento`, `riesgo_estado`, `riesgo_fecha_valoracion_rr`, `riesgo_probabilidad_rr`, `riesgo_impacto_rr`, `riesgo_valor_rr`, `riesgo_nivel_rr`, `riesgo_estado_rr`, `riesgo_ciclo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, NULL, 105, 1, 'Resistencia al cambio en la implementación de mejoras, debido a la falta de respaldo activo y compromiso por parte de la alta dirección y de los OUO en la programación y ejecución de las auditorías internas.', 'Riesgo', 4, 'Sin controlessASAS', '- Opción 1: Disminución en la eficacia de los procesos, lo que puede llevar a la no conformidad en los productos o servicios ofrecidos, afectando la satisfacción del cliente y la reputación de la organización.  \n- Opción 2: Aumento en los costos operativos debido a la falta de mejoras continuas, lo que puede resultar en ineficiencias y pérdida de competitividad en el mercado.', 4, 4, 16, 'bajo', 'estrategica', 'aceptar', 'proyecto', '2025-12-05', 6, 6, 36, 'medio', 'sin eficacia', 2, NULL, '2025-12-05 19:28:57', NULL),
	(23, NULL, 164, 0, 'Rendición de viáticos fuera del plazo establecido debido a la no priorización de estas actividades por parte del Comisionado.', 'Riesgo', 4, NULL, '- Opción 1: Retrasos en la disponibilidad de fondos para actividades operativas, lo que puede afectar la ejecución de proyectos y la continuidad del servicio.  \n- Opción 2: Pérdida de confianza por parte de los colaboradores y stakeholders, lo que puede resultar en una disminución de la motivación y el compromiso hacia la organización.', 6, 6, 36, 'medio', 'estrategica', NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 00:19:15', '2025-12-02 21:09:34', NULL),
	(24, NULL, 164, 0, 'Incumplimiento de plazos para la solicitud de viáticos debido al desconocimiento del procedimiento y directiva de viáticos por parte de los comisionados', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 00:21:50', '2025-03-05 18:28:48', NULL),
	(25, NULL, 164, 1, 'No atender los requerimientos de viáticos al exterior debido a la demora en la firma de la resolución administrativa.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 00:39:54', '2025-12-02 19:55:14', NULL),
	(26, NULL, 164, 0, 'Que no se autorice el requerimiento de anticipo, debido a que no se cuenta con la opinión favorable de ABAS, a la tardía generación de requerimiento de anticipo y a la falta de disponibilidad presupuestal.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 00:51:32', '2025-02-28 00:51:32', NULL),
	(30, NULL, 166, 0, 'Rendición de cuentas de anticipo fuera de plazo, debido a la no priorización de las\nactividades por parte del colaborador OUO.', 'Riesgo', 4, NULL, NULL, 6, 6, 36, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 18:47:50', '2025-12-02 21:14:02', NULL),
	(31, NULL, 166, 0, 'Otorgar anticipos por un monto mayor al establecido debido a desconocimiento de la normativa relacionada a Anticipos.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 19:00:07', '2025-02-28 19:00:07', NULL),
	(32, NULL, 166, 0, 'Rendiciones de anticipos no conformes a los criterios establecidos, debido a la falta de capacitación del colaborador OUO sobre la normativa de SUNAT.', 'Riesgo', 4, NULL, '- Opción 1: Retrasos en la aprobación de rendiciones, lo que puede afectar la disponibilidad de recursos financieros y la planificación de proyectos.  \n- Opción 2: Sanciones o multas por parte de SUNAT, lo que podría impactar negativamente en la reputación de la organización y en su capacidad para operar de manera eficiente.', 6, 6, 36, 'medio', 'tactica', 'reducir', 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 19:49:22', '2025-12-02 21:33:18', NULL),
	(33, NULL, 165, 0, 'Apertura de Caja Chica de manera oportuna', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 20:40:32', '2025-02-28 20:40:32', NULL),
	(34, NULL, 165, 0, 'Atención de gastos por caja chica que no cumplen requisitos establecidos, debido a la aplicación inadecuada de la norma o desconocimiento de la norma de parte de los colaboradores.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 20:44:46', '2025-02-28 20:44:46', NULL),
	(35, NULL, 165, 0, 'Desembolso de la caja chica fuera del plazo, debido a demora en el levantamiento de las observaciones, problemas con los aplicativos del sistema de caja chica, SGD o SIAF.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 20:47:25', '2025-02-28 20:47:25', NULL),
	(36, NULL, 165, 0, 'Liquidación final de caja chica fuera del plazo establecido, debido a demora en el levantamiento de las observaciones, demora en el depósito T6, problemas con los aplicativos: SIGA- Caja Chica, SGD, SIAF.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 20:50:16', '2025-02-28 20:50:16', NULL),
	(37, NULL, 92, 0, 'Acta de Conciliación de Bienes y Suministros que ha sido validado o firmado y que cuente con información inconsistente (errores de información no detectada), a causa de errores en la revisión.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 21:10:23', '2025-02-28 21:10:23', NULL),
	(38, NULL, 92, 0, 'Acta de Conciliación del Marco Legal y Ejecución del Presupuesto que ha sido validado o firmado y que cuente con información inconsistente (errores de información) no detectada, a causa de errores en la revisión.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 21:11:32', '2025-02-28 21:11:32', NULL),
	(39, NULL, 92, 0, 'Información inconsistente no detectada en el Acta de Conciliación de Cuentas Corrientes y Cuenta Única del Tesoro Púbico (validada o firmada), a causa de errores en la revisión.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 21:11:59', '2025-02-28 21:11:59', NULL),
	(40, NULL, 92, 0, 'Presentación de los estados financieros y presupuestarios fuera de los plazos establecidos, por información tardía por parte de los órganos o unidades orgánicas que conforman el pliego CGR, así como por deficiencia del módulo cliente y web del aplicativo SIAFSP y del SIAF - Módulo contable.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-28 21:12:43', '2025-02-28 21:12:43', NULL),
	(42, NULL, 106, 0, 'Perdida de la certificación por no cumplir con los requisitos de la norma internacional.', 'Riesgo', 4, 'sin controles', NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-05 19:21:19', '2025-03-13 23:35:26', NULL),
	(43, NULL, 107, 0, 'No cumplir con los lineamientos establecidos por no contar con el soporte documentario, falta de compromiso de la Alta dirección o unidades orgánicas.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-05 19:25:30', '2025-03-24 21:17:17', NULL),
	(44, NULL, 113, 0, 'No cumplir con los lineamientos establecidos en la Norma Técnica a “Implementación de la Gestión por Procesos en las Entidades de la Administración Pública” por no contar con el marco normativo interno actualizado.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-05 20:41:44', '2025-03-12 22:28:59', NULL),
	(45, NULL, 114, 0, 'Perdida de la certificación por no cumplir con los requisitos de la norma internacional', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-05 21:05:00', '2025-03-05 21:05:00', NULL),
	(47, NULL, 117, 0, 'Sobrerregulación de los procesos debido a una visión centrada en los aspectos funcionales, sin considerar de manera integral el proceso en su totalidad.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-05 23:03:32', '2025-03-26 15:56:11', NULL),
	(48, NULL, 219, 0, 'Posible presentación de casos de inelegibilidad de gastos, debido al incumplimiento de procedimiento, instancias, responsabilidades y normas que se debe seguir para la ejecución del proyecto.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-06 14:09:50', '2025-03-06 14:09:50', NULL),
	(49, NULL, 219, 0, 'Posible suspensión en los desembolsos del prestamo BID, debido al incumplimiento de acuerdos contractuales.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-06 14:10:09', '2025-03-06 14:10:09', NULL),
	(50, NULL, 219, 0, 'Posibilidad de no obtener la "no objeción" por parte del banco, debido al incumplimiento de la política para la adquisición de bienes y obras financiados por el BID.', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-06 14:10:26', '2025-03-24 22:29:31', NULL),
	(51, NULL, 220, 0, 'Posibilidad de no obtener la "no objeción" por parte del banco, debido al incumplimiento de la pólitica de selección y contratación de consultores financiados por el BID', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-06 14:13:22', '2025-03-24 22:29:00', NULL),
	(53, NULL, 219, 0, 'Posible afectación en el cumplimiento de los objetivos de los proyectos de inversión debido a modificaciones normativas sobre inversiones y políticas de gobierno, desactualizada o cambios en la normativa para la fase de ejecución de proyectos de inversión, insuficiente cultura en la gestión de proyectos, cambios en la estructura orgánica de la CGR o nuevos roles y funciones asignados al personal.', 'Riesgo', 4, NULL, NULL, 8, 8, 64, 'alto', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-06 14:46:41', '2025-03-06 14:46:41', NULL),
	(58, NULL, 30, 0, 'Incumplimiento de los Objetivos Estratégicos Institucionales', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:35:17', '2025-03-24 21:35:17', NULL),
	(59, NULL, 32, 0, 'La Subgerencia de Abastecimiento no pueda iniciar oportunamente el desarrollo del Cuadro de Necesidades', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:42:36', '2025-03-24 21:42:36', NULL),
	(60, NULL, 32, 0, 'Aprobación del Plan Operativo Institucional del siguiente año, fuera del plazo establecido', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:43:08', '2025-03-24 21:43:08', NULL),
	(61, NULL, 32, 0, 'Incumplimiento de elabora el Seguimiento del Plan Operativo dentro de los plazos establecidos', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:43:24', '2025-03-24 21:43:24', NULL),
	(62, NULL, 32, 0, 'Incumplimiento de elaborar la Evaluación del Plan Operativo Institucional a más tardar el siguiente mes de concluido el trimestre', 'Riesgo', 4, NULL, NULL, 4, 4, 16, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:43:49', '2025-03-24 21:43:49', NULL),
	(63, NULL, 109, 0, 'Los colaboradores podrían incumplir la Política y Objetivos Antisoborno al no considerar los controles establecidos en los riesgos o procesos dentro del alcance del certificado del Sistema de Gestión Antisoborno.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:47:56', '2025-03-24 21:48:51', NULL),
	(64, NULL, 109, 0, 'Los colaboradores podrían incumplir los requisitos de la norma ISO 37001:2016, al no considerar los controles establecidos en los riesgos o procesos dentro del alcance del certificado del Sistema de Gestión Antisoborno.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 21:48:27', '2025-03-24 21:48:27', NULL),
	(65, NULL, 40, 0, 'La Entidad podría incumplir con la Política Nacional de Integridad al no contar con el compromiso o interés de Alta Dirección y funcionarios que garantice una gestión efectiva y sostenible de la integridad pública.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 22:40:52', '2025-03-24 22:40:52', NULL),
	(66, NULL, 40, 0, 'La ejecución de las actividades del Programa de integridad podría incumplirse por falta de compromiso de los responsables de los Órganos o Unidades Orgánicas involucradas en la sostenibilidad de la implementación del modelo de integridad', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 22:41:13', '2025-03-24 22:41:13', NULL),
	(67, NULL, 276, 0, 'La gestión de las denuncias podría ser realizada fuera del plazo establecido en la normativa.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:08:56', '2025-03-24 23:08:56', NULL),
	(68, NULL, 276, 0, 'En el marco de la gestión de denuncias, podría vulnerarse la identidad del denunciante y/o la materia de la denuncia y/o las actuaciones derivadas de la misma.', 'Riesgo', 4, NULL, NULL, 6, 8, 48, 'alto', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:09:49', '2025-03-24 23:09:49', NULL),
	(69, NULL, 276, 0, 'Especialista de denuncias podría evaluar la solicitud de medida de protección por el denunciante incumpliendo las normas establecidas.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:10:12', '2025-03-24 23:11:15', NULL),
	(70, NULL, 276, 0, 'El denunciante podría verse afectado en el ejercicio de los derechos personales y/o laborales, debido a que no se dé cumplimiento a la medida de protección otorgada.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:10:42', '2025-03-24 23:10:42', NULL),
	(71, NULL, 81, 0, 'No elaborar y/o actualizar la normativa que regule los procesos archivísticos a causa de no haber determinado la responsabilidad y prioridad de tal obligación.', 'Riesgo', 4, NULL, NULL, 6, 8, 48, 'alto', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:26:08', '2025-03-24 23:26:08', NULL),
	(73, NULL, 81, 0, 'Proporcionar información confidencial a los usuarios por no contar con controles de seguridad establecidos (documentados).', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:29:32', '2025-03-24 23:29:32', NULL),
	(74, NULL, 81, 0, 'Archivo de documentos con series documentales combinadas a causa de escasos mecanismos de difusión y socialización con las unidades de organización.', 'Riesgo', 4, NULL, NULL, 8, 4, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:30:18', '2025-03-24 23:30:41', NULL),
	(75, NULL, 81, 0, 'Solicitudes de eliminación rechazadas debido a desconocimiento de procedimiento vigente.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:30:57', '2025-03-24 23:30:57', NULL),
	(76, NULL, 81, 0, 'La degradación de soportes físicos y digitales puede comprometer la integridad y accesibilidad de la información, afectando la eficiencia operativa y la seguridad de la información en la organización.', 'Riesgo', 4, NULL, NULL, 6, 8, 48, 'alto', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-24 23:34:53', '2025-03-24 23:34:53', NULL),
	(77, NULL, 78, 0, 'Posible incumplimiento de los criterios, actividades y roles asignados en los procedimientos, TUPA y directiva de "Gestión documental", debido a la poca experiencia o desconocimiento por parte del responsable de la actividad correspondiente a los presentes procedimientos.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:25:50', '2025-03-25 00:25:50', NULL),
	(78, NULL, 78, 0, 'Posible incumplimiento de los requisitos de recepción y errores en las derivaciones de documentos (clasificados como reservados, secretos y confidenciales), debido a la poca experiencia del responsable del proceso.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:26:17', '2025-03-25 00:26:17', NULL),
	(79, NULL, 78, 0, 'Posible incumplimiento de los criterios establecidos en el manual de atención a la ciudadanía, debido al desconocimiento o poca experiencia por parte del responsable de la actividad.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:27:19', '2025-03-25 00:27:19', NULL),
	(80, NULL, 78, 0, 'Demora por una inadecuada verificación de los requisitos e incorrecto registro de la presentación de la declaración jurada, debido al desconocimiento o errores involuntarios por parte del responsable de la actividad.', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:27:55', '2025-03-25 00:27:55', NULL),
	(81, NULL, 78, 0, 'Posible soborno hacia personal de Gestión Documentaria para proporcionar indebidamente información a favor de terceros para fines personales, debido a la falta de ética del personal que realiza la gestión de recepción de documentos.', 'Riesgo', 4, NULL, NULL, 4, 8, 32, 'medio', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:29:06', '2025-03-25 00:29:06', NULL),
	(82, NULL, 118, 0, 'Indisponibilidad de la información respaldada por una falla de software o hardware', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:37:13', '2025-03-25 00:37:13', NULL),
	(83, NULL, 118, 0, 'Demora en el resguardo y recuperación de la información del correo electrónico respaldada por el proveedor debido que los recursos e infraestructura no están bajo el control de CGR', 'Riesgo', 4, NULL, NULL, 4, 6, 24, 'bajo', NULL, NULL, 'proyecto', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-03-25 00:37:28', '2025-03-25 00:37:28', NULL);

-- Volcando estructura para tabla kallpaq.riesgo_acciones
CREATE TABLE IF NOT EXISTS `riesgo_acciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_id` bigint(20) unsigned NOT NULL,
  `ra_descripcion` text NOT NULL,
  `ra_comentario` longtext DEFAULT NULL,
  `ra_fecha_inicio` date DEFAULT NULL,
  `ra_fecha_fin_planificada` date DEFAULT NULL,
  `ra_fecha_fin_reprogramada` date DEFAULT NULL,
  `ra_fecha_fin_cancelada` date DEFAULT NULL,
  `ra_fecha_fin_real` date DEFAULT NULL,
  `ra_justificacion` text DEFAULT NULL,
  `ra_evidencia` text DEFAULT NULL,
  `ra_responsable` varchar(255) DEFAULT NULL,
  `ra_responsable_correo` varchar(255) DEFAULT NULL,
  `ra_estado` enum('programada','desestimada','en proceso','implementada') DEFAULT NULL,
  `ra_ciclo` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riesgo_acciones_riesgo_cod_foreign` (`riesgo_id`) USING BTREE,
  CONSTRAINT `riesgo_acciones_riesgo_cod_foreign` FOREIGN KEY (`riesgo_id`) REFERENCES `riesgos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgo_acciones: ~6 rows (aproximadamente)
REPLACE INTO `riesgo_acciones` (`id`, `riesgo_id`, `ra_descripcion`, `ra_comentario`, `ra_fecha_inicio`, `ra_fecha_fin_planificada`, `ra_fecha_fin_reprogramada`, `ra_fecha_fin_cancelada`, `ra_fecha_fin_real`, `ra_justificacion`, `ra_evidencia`, `ra_responsable`, `ra_responsable_correo`, `ra_estado`, `ra_ciclo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'DSADSADSADSADSDSDAS', 'cxzcxcxczxcvcvcxvcxvcvcxvcx', '2025-12-01', '2025-12-19', '2025-12-31', NULL, '2025-12-04', 'RWERWER', NULL, 'dsadsad', 'dsadsad@dsasd.com', 'implementada', 1, '2025-12-01 21:38:50', '2025-12-04 16:26:49', NULL),
	(2, 1, 'Nueva accion 2 (actualizado)', 'dsdsadsddaas', '2025-12-10', '2025-12-09', '2025-12-02', NULL, '2025-12-04', 'sasasas', NULL, 'juan almeyda', 'jalmeyda1403@gmail.com', 'implementada', 1, '2025-12-02 16:48:58', '2025-12-04 16:30:34', NULL),
	(3, 1, 'Acciones 3', 'sasasssasasasassas', '2025-12-02', '2025-12-31', '2026-01-09', NULL, '2025-12-04', 'wsasaS', NULL, 'Juan Almeyda', 'jalmeyda1403@gmail.com', 'implementada', 1, '2025-12-02 17:11:29', '2025-12-04 16:32:40', NULL),
	(4, 32, 'dsadsadsad', NULL, '2025-12-02', '2025-12-31', '2026-01-08', NULL, NULL, 'ddadsadsadads', NULL, 'adsdasdsad', 'jalmeyda1403@gmail.com', 'programada', 1, '2025-12-02 21:33:50', '2025-12-02 21:35:09', NULL),
	(5, 1, 'Riesgo nuevo', 'dsadsd', '2025-12-03', '2025-12-25', NULL, NULL, NULL, NULL, NULL, 'dsadad', 'jalmeyda1403@gmail.com', 'desestimada', 1, '2025-12-02 22:43:22', '2025-12-04 16:56:44', NULL),
	(6, 1, 'ddsadsad', NULL, '2025-12-02', '2025-12-26', NULL, NULL, '2025-12-04', NULL, NULL, 'dsadasd', 'jalmeyda1403@gmail.com', 'implementada', 2, '2025-12-02 22:47:17', '2025-12-04 16:31:27', NULL);

-- Volcando estructura para tabla kallpaq.riesgo_acciones_reprogramaciones
CREATE TABLE IF NOT EXISTS `riesgo_acciones_reprogramaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_accion_id` bigint(20) unsigned NOT NULL,
  `rar_fecha_anterior` date NOT NULL,
  `rar_fecha_nueva` date NOT NULL,
  `rar_justificacion` text NOT NULL,
  `rar_evidencia` varchar(255) DEFAULT NULL,
  `rar_estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `rar_aprobado_por` bigint(20) unsigned DEFAULT NULL,
  `rar_fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `rar_comentario_aprobacion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riesgo_acciones_reprogramaciones_riesgo_accion_id_foreign` (`riesgo_accion_id`),
  KEY `riesgo_acciones_reprogramaciones_rar_aprobado_por_foreign` (`rar_aprobado_por`),
  CONSTRAINT `riesgo_acciones_reprogramaciones_rar_aprobado_por_foreign` FOREIGN KEY (`rar_aprobado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riesgo_acciones_reprogramaciones_riesgo_accion_id_foreign` FOREIGN KEY (`riesgo_accion_id`) REFERENCES `riesgo_acciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgo_acciones_reprogramaciones: ~4 rows (aproximadamente)
REPLACE INTO `riesgo_acciones_reprogramaciones` (`id`, `riesgo_accion_id`, `rar_fecha_anterior`, `rar_fecha_nueva`, `rar_justificacion`, `rar_evidencia`, `rar_estado`, `rar_aprobado_por`, `rar_fecha_aprobacion`, `rar_comentario_aprobacion`, `created_at`, `updated_at`) VALUES
	(1, 2, '2025-12-09', '2025-12-02', 'sasasas', NULL, 'aprobado', 1, '2025-12-02 17:09:40', 'aprobado por ultima vez', '2025-12-02 16:49:28', '2025-12-02 17:09:40'),
	(2, 3, '2025-12-31', '2026-01-09', 'wsasaS', NULL, 'aprobado', 1, '2025-12-02 17:12:26', 'aprobado por ultima vez', '2025-12-02 17:12:06', '2025-12-02 17:12:26'),
	(3, 4, '2025-12-31', '2026-01-08', 'ddadsadsadads', NULL, 'aprobado', 1, '2025-12-02 21:35:09', 'sccxzcc', '2025-12-02 21:34:17', '2025-12-02 21:35:09'),
	(4, 5, '2025-12-25', '2025-12-31', 'sasa', NULL, 'pendiente', NULL, NULL, NULL, '2025-12-04 14:32:18', '2025-12-04 14:32:18');

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

-- Volcando estructura para tabla kallpaq.riesgo_revisions
CREATE TABLE IF NOT EXISTS `riesgo_revisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riesgo_id` bigint(20) unsigned NOT NULL,
  `rr_fecha` date NOT NULL,
  `rr_responsable_id` bigint(20) unsigned NOT NULL,
  `rr_resultado` enum('con eficacia','sin eficacia') NOT NULL,
  `rr_comentario` text DEFAULT NULL,
  `rr_ciclo` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riesgo_revisions_riesgo_id_foreign` (`riesgo_id`),
  KEY `riesgo_revisions_rr_responsable_id_foreign` (`rr_responsable_id`),
  CONSTRAINT `riesgo_revisions_riesgo_id_foreign` FOREIGN KEY (`riesgo_id`) REFERENCES `riesgos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riesgo_revisions_rr_responsable_id_foreign` FOREIGN KEY (`rr_responsable_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.riesgo_revisions: ~1 rows (aproximadamente)
REPLACE INTO `riesgo_revisions` (`id`, `riesgo_id`, `rr_fecha`, `rr_responsable_id`, `rr_resultado`, `rr_comentario`, `rr_ciclo`, `created_at`, `updated_at`) VALUES
	(11, 1, '2025-12-05', 1, 'sin eficacia', 'sasasas', 1, '2025-12-05 19:20:05', '2025-12-05 19:20:05');

-- Volcando estructura para tabla kallpaq.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.roles: ~5 rows (aproximadamente)
REPLACE INTO `roles` (`id`, `name`, `guard_name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'admin', 'web', 'Administración de Tablas Maestras y configuraciones del Sistema.', '2023-08-25 20:55:30', '2023-08-25 20:55:30', NULL),
	(2, 'especialista', 'web', 'Especialista del SIG, puede tener permisos de los modulos de Indicadores, Riesgos, Hallazgos o Procesos.', '2023-08-25 20:55:30', '2023-08-25 20:55:30', NULL),
	(3, 'auditor', 'web', 'Tiene acceso a la vista de facilitador del proceso y se puede habilitar los diferentes módulos del SIG.', '2023-08-25 20:55:30', '2023-08-25 20:55:30', NULL),
	(4, 'facilitador', 'web', 'Tienes acceso para el registro y seguimiento de los componentes del sistema integrado de gestión relacionados con su proceso', '2023-08-25 20:55:30', '2026-01-16 22:52:11', NULL),
	(5, 'propietario', 'web', 'Tienes acceso de sólo lectura a los reportes y dashboards del SIG de acuerdo a los procesos de su propiedad.', '2026-01-16 21:21:42', '2026-01-16 21:21:44', NULL);

-- Volcando estructura para tabla kallpaq.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.role_has_permissions: ~68 rows (aproximadamente)
REPLACE INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(1, 2),
	(1, 4),
	(2, 1),
	(2, 2),
	(2, 4),
	(3, 1),
	(4, 1),
	(4, 2),
	(5, 1),
	(5, 2),
	(7, 1),
	(7, 4),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(11, 2),
	(13, 1),
	(14, 1),
	(15, 1),
	(16, 1),
	(17, 1),
	(17, 4),
	(18, 1),
	(18, 4),
	(19, 1),
	(20, 1),
	(21, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(24, 4),
	(25, 1),
	(25, 4),
	(26, 1),
	(27, 1),
	(28, 1),
	(29, 1),
	(29, 4),
	(30, 1),
	(31, 1),
	(32, 1),
	(33, 1),
	(34, 1),
	(35, 1),
	(36, 1),
	(37, 1),
	(38, 1),
	(39, 1),
	(40, 1),
	(41, 1),
	(42, 1),
	(48, 1),
	(48, 4),
	(49, 1),
	(49, 4),
	(50, 1),
	(50, 4),
	(51, 1),
	(51, 4),
	(52, 1),
	(53, 1),
	(54, 1),
	(55, 1),
	(56, 1),
	(57, 1),
	(58, 1);

-- Volcando estructura para tabla kallpaq.salidas_no_conformes
CREATE TABLE IF NOT EXISTS `salidas_no_conformes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `snc_descripcion` text NOT NULL COMMENT 'Descripción de la no conformidad',
  `snc_cantidad_afectada` decimal(10,2) NOT NULL COMMENT 'Cantidad de unidades afectadas',
  `snc_fecha_deteccion` date NOT NULL COMMENT 'Fecha de detección',
  `snc_responsable` varchar(255) NOT NULL,
  `snc_origen` enum('cliente','auditoría interna','auditoría externa','otro') NOT NULL COMMENT 'Origen de la detección',
  `snc_clasificacion` enum('crítica','mayor','menor') NOT NULL COMMENT 'Clasificación por severidad',
  `snc_tratamiento` enum('corrección','concesion','reclasificación','rechazo','retención','disposición') DEFAULT NULL COMMENT 'Tipo de tratamiento aplicado',
  `snc_descripcion_tratamiento` text DEFAULT NULL COMMENT 'Descripción detallada del tratamiento',
  `snc_fecha_tratamiento` date DEFAULT NULL COMMENT 'Fecha de aplicación del tratamiento',
  `snc_costo_estimado` decimal(10,2) DEFAULT NULL COMMENT 'Costo estimado del tratamiento',
  `snc_estado` enum('registrada','en tratamiento','tratada') NOT NULL DEFAULT 'registrada' COMMENT 'Estado actual',
  `snc_requiere_accion_correctiva` tinyint(1) DEFAULT 0 COMMENT 'Indica si requiere acción correctiva',
  `snc_fecha_cierre` date DEFAULT NULL COMMENT 'Fecha de cierre',
  `snc_observaciones` text DEFAULT NULL COMMENT 'Observaciones generales',
  `snc_archivos` text DEFAULT NULL COMMENT 'Ruta a archivo de evidencia',
  `snc_evidencias` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `proceso_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salidas_no_conformes_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `salidas_no_conformes_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.salidas_no_conformes: ~3 rows (aproximadamente)
REPLACE INTO `salidas_no_conformes` (`id`, `snc_descripcion`, `snc_cantidad_afectada`, `snc_fecha_deteccion`, `snc_responsable`, `snc_origen`, `snc_clasificacion`, `snc_tratamiento`, `snc_descripcion_tratamiento`, `snc_fecha_tratamiento`, `snc_costo_estimado`, `snc_estado`, `snc_requiere_accion_correctiva`, `snc_fecha_cierre`, `snc_observaciones`, `snc_archivos`, `snc_evidencias`, `created_at`, `updated_at`, `proceso_id`) VALUES
	(1, 'Prueba de descripción de salida no conforme.', 10.00, '2025-11-24', 'Juan almeyda', 'auditoría interna', 'mayor', '', NULL, NULL, NULL, 'registrada', 0, NULL, NULL, NULL, NULL, '2025-11-24 19:50:15', '2025-11-25 00:08:20', NULL),
	(5, 'Durante el proceso de licitación para la construcción de un nuevo puente, ocurre lo siguiente:\r\nIncumplimiento de Plazos: El Comité recibe y registra una propuesta de un proveedor 10 minutos después de la hora límite oficial de cierre. (El requisito es que debe estar "antes de la hora límite").\r\nIncumplimiento de Composición: El informe final de adjudicación está firmado por solo dos funcionarios, en lugar de los tres requeridos por el Reglamento.', 1.00, '2025-11-24', 'Juan Almeyda', 'cliente', 'crítica', 'corrección', 'csasad', '2025-11-18', 2121.00, 'registrada', 0, '2025-11-25', 'dsad', '[{"path":"snc\\/5\\/w0WPsOnES34RgnyiZ1LcReHvX9LMwfVXYx7bT29G.pdf","name":"Informe Tecnico de Encuesta DJ 2025-III[F].pdf"},{"path":"snc\\/5\\/fcN5JYTEQoRJvXPruS0c0Qhla1cx6ZdMyY7l75Le.xlsx","name":"Procesamiento Informe DJ.xlsx"}]', '[{"path":"snc\\/5\\/3xo1LLIQT6cSCto4783pXkcL27DUL7usAT5hwQa3.docx","name":"Informe Tecnico de Encuesta DJ 2025-III.docx"}]', '2025-11-24 23:07:29', '2025-11-26 18:29:10', NULL),
	(6, 'Se identificarion registros erroneos', 5.00, '2025-11-19', 'carlos', 'auditoría interna', 'crítica', NULL, NULL, NULL, NULL, 'registrada', 0, NULL, NULL, NULL, NULL, '2026-01-19 16:56:53', '2026-01-19 16:56:53', 151);

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

-- Volcando estructura para tabla kallpaq.sugerencias
CREATE TABLE IF NOT EXISTS `sugerencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sugerencia_clasificacion` varchar(255) NOT NULL,
  `sugerencia_detalle` text NOT NULL,
  `sugerencia_fecha_ingreso` date NOT NULL,
  `sugerencia_procedencia` varchar(255) NOT NULL,
  `sugerencia_analisis` text DEFAULT NULL,
  `sugerencia_viabilidad` varchar(255) DEFAULT NULL,
  `sugerencia_tratamiento` text DEFAULT NULL,
  `sugerencia_observacion` text DEFAULT NULL,
  `sugerencia_fecha_observacion` date DEFAULT NULL,
  `sugerencia_fecha_cierre` date DEFAULT NULL,
  `sugerencia_estado` enum('abierta','en progreso','concluida','observada','cerrada') DEFAULT NULL,
  `sugerencia_fecha_fin_prog` date DEFAULT NULL,
  `sugerencia_fecha_fin_real` date DEFAULT NULL,
  `proceso_id` bigint(20) unsigned NOT NULL,
  `sugerencia_evidencias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sugerencias_proceso_id_foreign` (`proceso_id`),
  CONSTRAINT `sugerencias_proceso_id_foreign` FOREIGN KEY (`proceso_id`) REFERENCES `procesos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.sugerencias: ~1 rows (aproximadamente)
REPLACE INTO `sugerencias` (`id`, `sugerencia_clasificacion`, `sugerencia_detalle`, `sugerencia_fecha_ingreso`, `sugerencia_procedencia`, `sugerencia_analisis`, `sugerencia_viabilidad`, `sugerencia_tratamiento`, `sugerencia_observacion`, `sugerencia_fecha_observacion`, `sugerencia_fecha_cierre`, `sugerencia_estado`, `sugerencia_fecha_fin_prog`, `sugerencia_fecha_fin_real`, `proceso_id`, `sugerencia_evidencias`, `created_at`, `updated_at`) VALUES
	(2, 'AC', 'Mejorar la atención con el cliente', '2025-12-19', 'virtual', NULL, NULL, NULL, NULL, NULL, NULL, 'abierta', NULL, NULL, 151, NULL, '2026-01-19 16:55:32', '2026-01-19 16:59:08');

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
  `td_sigla` varchar(255) NOT NULL,
  `td_nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_documentos_sigla_unique` (`td_sigla`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.tipo_documentos: ~12 rows (aproximadamente)
REPLACE INTO `tipo_documentos` (`id`, `td_sigla`, `td_nombre`, `estado`, `inactive_at`, `created_at`, `updated_at`) VALUES
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
  `user_cod_personal` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_iniciales` varchar(255) DEFAULT NULL,
  `user_foto_url` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_user_cod_personal_unique` (`user_cod_personal`),
  UNIQUE KEY `users_user_iniciales_unique` (`user_iniciales`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla kallpaq.users: ~40 rows (aproximadamente)
REPLACE INTO `users` (`id`, `name`, `email`, `user_cod_personal`, `email_verified_at`, `password`, `user_iniciales`, `user_foto_url`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Juan Manuel Almeyda Requejo', 'jalmeyda@contraloria.gob.pe', '17618', '2023-08-30 15:54:20', '$2y$10$OQbzKSWuWyoAZfKry7w3d.4dSbl.YxYSY/X95TXRjINq.608sUJby', 'JMAR', 'photo/1768608079_696ad14f6e4f7.png', 'qHbiBmzNW1aG0Lx12cO0N8C9mrZAOxStspdhvdp4eVPEH4fpy7d7Of85ACI0', '2023-05-26 23:01:48', '2026-01-20 20:17:25'),
	(2, 'Manuel Perez Effus', 'manuelperez@contraloria.gob.pe', '64415', '2023-08-25 19:29:29', '$2y$10$wb1s0IQ6oIR7r/4mXxg/0uTBKptsXG05I62.sIvKz3.rCnCgqT/6q', 'MPE', NULL, NULL, '2023-08-26 00:28:38', '2026-01-19 22:12:17'),
	(3, 'Angel Arturo Bendezu Cardenas\r\n', 'abendezuc@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(4, 'Maria Isabel Hiyo Huapaya\r\n', 'mhiyo@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(5, 'Ana Elsa Gonzales Napaico\r\n', 'agonzalesn@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(6, 'Gatsby Loayza Parraga\r\n', 'gloayza@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(7, 'Gustavo Adolfo Villanueva Salvador\r\n', 'gvillanuevas@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(8, 'Elias Martin Tresierra Paz\r\n', 'etresierra@contraloria.gob.pe', NULL, '2023-08-25 19:29:29', '$2a$12$UaFP2VL90DuMIzK4uhCpAOfKV4GEzhNe.IMh7iI3EI0huFVEEpN5y', NULL, NULL, NULL, '2023-08-26 00:28:38', '2023-08-26 00:28:38'),
	(9, 'Angel Benny Cordova Filio', 'acordovaf@contraloria.gob.pe', NULL, NULL, '$2y$10$s51D/dM8cAeoDHW8JSekuODBWFBfyZUzlaKQvsKatz2O5UTWWuoCW', 'ABCF', NULL, NULL, '2026-01-18 21:46:35', '2026-01-18 21:46:35'),
	(10, 'Luis Carlos Echeverria Tamayo', 'lecheverria@contraloria.gob.pe', NULL, NULL, '$2y$10$UxJU2AHPal8/sjV/QRyZOuXXvaW7LfThjIjCCHTMw6fbKnkdXDtze', 'LCET', NULL, NULL, '2026-01-18 21:50:18', '2026-01-18 21:50:18'),
	(11, 'Roberto Ruben Soldevilla Garcia', 'rsoldevilla@contraloria.gob.pe', NULL, NULL, '$2y$10$QnayjRSkqtBXZmnmQyDZ7.mgvmm5g.MOETrxKLU8ao1GJfH2ibtNu', 'RRSG', NULL, NULL, '2026-01-18 21:54:33', '2026-01-18 21:54:33'),
	(12, 'David Ricardo Pinto Yoshinari', 'dpinto@contraloria.gob.pe', NULL, NULL, '$2y$10$qDuSM12j75fu7XqpOBLlWO4rCfcxaUsJWjyibNHaJdMVJkl6wvbV2', 'DRPY', NULL, NULL, '2026-01-18 21:55:35', '2026-01-18 21:55:35'),
	(13, 'Luis Alberto Marini Lopez', 'lmarini@contraloria.gob.pe', NULL, NULL, '$2y$10$ZJYFidO1j3BBLlnvGM0koe4BnIu8mq9PTdcAcfcVOiCPMqtVwsB7.', 'LAML', NULL, NULL, '2026-01-18 21:56:19', '2026-01-18 21:56:19'),
	(14, 'Natali Camacho Bartra', 'ncamacho@contraloria.gob.pe', '17586', NULL, '$2y$10$DvfNUKnZ6EG/IBgb7Jzmc.DBhj.LTZlvpVF29r9VzGyeN2ohJjWmG', 'NCB', NULL, NULL, '2026-01-18 22:03:15', '2026-01-18 22:03:15'),
	(15, 'Mónica Jeanette Isique Pérez', 'misique@contraloria.gob.pe', '10519', NULL, '$2y$10$ZyHwJJrAVC7zZnM39rllse8JI1g7OuTqUYpn4kDk0f7FbXw/7oCee', 'MJIP', NULL, NULL, '2026-01-19 21:58:13', '2026-01-19 21:58:13'),
	(16, 'Yvette Ormeño Luna', 'yormeno@contraloria.gob.pe', '65730', NULL, '$2y$10$ftDQamrU7VPIRBsdHBTcw.6qoF1gnxuDoMBNeD0KcALC1WzMMPYWS', 'YOL', NULL, NULL, '2026-01-19 21:58:46', '2026-01-19 21:58:46'),
	(17, 'Mario Lorenzo Salinas Cornejo', 'msalinas@contraloria.gob.pe', '14176', NULL, '$2y$10$DAGrectOSh4UlBZgt2NDceRxB8dvgU/jK3RI1aGmjAwl86Sxl53KW', 'MLSC', NULL, NULL, '2026-01-19 22:10:53', '2026-01-19 22:10:53'),
	(18, 'Jhonny Alfredo Cava Guerra', 'jcava@contraloria.gob.pe', '20026', NULL, '$2y$10$ExnMzr63tQnGWajudFOBUuz1P61zP.LtOx2i7t8lZszfY6htiteB2', 'JACG', NULL, NULL, '2026-01-19 22:11:26', '2026-01-19 22:11:26'),
	(19, 'Joice Fabiola Ponce Cámara', 'joiceponce@contraloria.gob.pe', '64361', NULL, '$2y$10$pbD/VyneLz03/N2quD2Iw.X9GX.LOvIvWpVSIPWZx1EGCEcua..Ii', 'JFPC', NULL, NULL, '2026-01-19 22:11:51', '2026-01-19 22:11:51'),
	(20, 'Jacinto Jorge Paz Ortiz', 'jpaz@contraloria.gob.pe', '18936', NULL, '$2y$10$X5zl9hsO5kvJKLrrMJOpfOiLJ77wkCzuWRxHPe2oTEYkzfaLharqO', 'JJPO', NULL, NULL, '2026-01-19 22:13:23', '2026-01-19 22:13:23'),
	(21, 'Julio Miguel Juárez Torres', 'jjuarez@contraloria.gob.pe', '71008', NULL, '$2y$10$hj0A/8vaf6svnWadND9jOu/f7yQgjUGbBJrAm0NTCrT8cEYiF1HXq', 'JMJT', NULL, NULL, '2026-01-19 22:14:05', '2026-01-19 22:14:05'),
	(22, 'Violeta Marina Vivanco Puente', 'vvivancop@contraloria.gob.pe', '60781', NULL, '$2y$10$xI.3wbX9F9.HCaiaIBj0..7QcCmYcgt5z88GbCvIiVb3XTv/kELdu', 'VMVP', NULL, NULL, '2026-01-19 22:14:33', '2026-01-19 22:14:33'),
	(23, 'Juan Ricardo Torres Torres Prado', 'jtorres@contraloria.gob.pe', '63473', NULL, '$2y$10$ePAgOibBmzMeUwjrw1CzYePb1H.bzlI5eBHl7MSIb9Nmsq1dEgeu2', 'JRTT', NULL, NULL, '2026-01-19 22:26:35', '2026-01-19 22:26:35'),
	(24, 'Joel Enrique Mercado Rojas', 'jmercador@contraloria.gob.pe', '17642', NULL, '$2y$10$xLu9RT1bQipMpFKRb92FE.TBGManUcDMGc7yFEk21T2mTfN53vmSa', 'JEMR', NULL, NULL, '2026-01-19 22:29:56', '2026-01-19 22:29:56'),
	(25, 'Angie Reategui Reategui', 'areateguir@contraloria.gob.pe', '18209', NULL, '$2y$10$bdxVo7vFXYHbC53Jo3s4p.s66lnrxre03/ARGBv.Nr.0KjqCYRdNq', 'ARR', NULL, NULL, '2026-01-19 22:30:21', '2026-01-19 22:30:21'),
	(26, 'Obdulia Isabel Camacho Tavera', 'ocamachot@contraloria.gob.pe', '19407', NULL, '$2y$10$ASfig2sWnBtqhvM8TVmgNOgfpq6EuFjw7crUm5yPrfbwlthTH2.QG', 'OICT', NULL, NULL, '2026-01-19 22:37:35', '2026-01-19 22:37:35'),
	(27, 'Hector Hildeck Maldonado Montalvo', 'hmaldonado@contraloria.gob.pe', '12939', NULL, '$2y$10$harA8WYWHboS.bkIxkybSOrZq39AoT.il9osIF6Z28VlRkQ7GP0Fq', 'HHMM', NULL, NULL, '2026-01-19 22:38:07', '2026-01-19 22:38:07'),
	(28, 'Pedro Luis Vera Barriga', 'pvera@cvontraloria.gob.pe', '64617', NULL, '$2y$10$ChB2pTf0WNXUVMirMRtvbuNbuRFWRNdM5JVZV5.vYoW.xz.XbSlLW', 'PLVB', NULL, NULL, '2026-01-19 22:50:57', '2026-01-19 22:53:48'),
	(29, 'Yndira Sidilia Orellana Tutaya', 'yorellana@contraloria.gob.pe', '18302', NULL, '$2y$10$ChB2pTf0WNXUVMirMRtvbuNbuRFWRNdM5JVZV5.vYoW.xz.XbSlLW', 'YSOT', NULL, NULL, '2026-01-19 22:50:57', '2026-01-19 22:54:06'),
	(30, 'Anna Cecilia Espinoza Panty', 'aespinozap@contraloria.gob.pe', '62243', NULL, '$2y$10$ChB2pTf0WNXUVMirMRtvbuNbuRFWRNdM5JVZV5.vYoW.xz.XbSlLW', 'ACEP', NULL, NULL, '2026-01-19 22:50:57', '2026-01-19 22:54:21'),
	(31, 'Rosa Violeta Quispitongo Vásquez', 'rquispitongo@contraloria.gob.pe', '61096', NULL, '$2y$10$ChB2pTf0WNXUVMirMRtvbuNbuRFWRNdM5JVZV5.vYoW.xz.XbSlLW', 'RVQV', NULL, NULL, '2026-01-19 22:50:57', '2026-01-19 22:54:36'),
	(32, 'Cesar Acosta Valeriano', 'cacosta@contraloria.gob.pe', '61042', NULL, '$2y$10$ChB2pTf0WNXUVMirMRtvbuNbuRFWRNdM5JVZV5.vYoW.xz.XbSlLW', 'CAV', NULL, NULL, '2026-01-19 22:50:57', '2026-01-19 22:53:16'),
	(33, 'Wilder Alex Inga Lopez', 'winga@contraloria.gob.pe', '66389', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'WAIL', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(34, 'Rómulo Bolesman Paoli', 'rbosleman@contraloria.gob.pe', '60239', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'RBP', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(35, 'Henry Paz Melendez', 'hpaz@contraloria.gob.pe', '65398', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'HPM', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(36, 'Jaime Fernando Ramirez Salinas', 'jramirezs@contraloria.gob.pe', '20119', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'JFRS', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(37, 'Vilma Violeta Carrillo Paredes', 'vcarrillo@contraloria.gob.pe', '64489', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'VVCP', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(38, 'Gliria Glenda Rivero Cornejo', 'grivero@contraloria.gob.pe', '62589', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'GGRC', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(39, 'Jose Alexander Avila Zamudio', 'javila@contraloria.gob.pe', '18839', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'JAAZ', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(40, 'Serapio Victor Moreno Vega', 'vmoreno@contraloria.gob.pe', '70146', NULL, '$2y$10$Mo882598gf50zydzb3pmg.lI5DYLKaG2M/DulqzaGnFsF8C0/z48u', 'SVMV', NULL, NULL, '2026-01-21 17:14:46', '2026-01-21 17:14:46'),
	(41, 'Dagmar Kirey Salazar Frias', 'dsalazarf@contraloria.gob.pe', '18435', NULL, '$2y$10$t8kxoullMyMK6QKSsXyx0eE1PnIkZcfXqoaygw3X7pvbxWJ6M6ymO', 'DKSF', NULL, NULL, '2026-01-21 21:49:12', '2026-01-21 21:49:12');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
