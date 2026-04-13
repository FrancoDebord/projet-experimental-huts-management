-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.4.7 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.16.0.7229
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage de la structure de table base_airid. exp_huts_daily_observations
CREATE TABLE IF NOT EXISTS `exp_huts_daily_observations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `hut_id` bigint unsigned DEFAULT NULL,
  `observation_date` date NOT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `observed_by` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_daily_observations_session_id_foreign` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_daily_observations : 0 rows

-- Listage de la structure de table base_airid. exp_huts_huts
CREATE TABLE IF NOT EXISTS `exp_huts_huts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned NOT NULL,
  `number` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('available','in_use','damaged','abandoned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_huts_huts_site_id_number_unique` (`site_id`,`number`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_huts : 86 rows
INSERT INTO `exp_huts_huts` (`id`, `site_id`, `number`, `name`, `latitude`, `longitude`, `status`, `image_path`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 2, 1, 'Site 2 Case 1', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-13 09:44:25', NULL),
	(2, 2, 2, 'Site 2 Case 2', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-13 10:14:24', NULL),
	(3, 2, 3, 'Site 2 Case 3', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-13 10:14:24', NULL),
	(4, 2, 4, 'Site 2 Case 4', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(5, 2, 5, 'Site 2 Case 5', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(6, 2, 6, 'Site 2 Case 6', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(7, 2, 7, 'Site 2 Case 7', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(8, 2, 8, 'Site 2 Case 8', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(9, 2, 9, 'Site 2 Case 9', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(10, 2, 10, 'Site 2 Case 10', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(11, 2, 11, 'Site 2 Case 11', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(12, 2, 12, 'Site 2 Case 12', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(13, 2, 13, 'Site 2 Case 13', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(14, 2, 14, 'Site 2 Case 14', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(15, 2, 15, 'Site 2 Case 15', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(16, 2, 16, 'Site 2 Case 16', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(17, 2, 17, 'Site 2 Case 17', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(18, 2, 18, 'Site 2 Case 18', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(19, 2, 19, 'Site 2 Case 19', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(20, 2, 20, 'Site 2 Case 20', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(21, 2, 21, 'Site 2 Case 21', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(22, 2, 22, 'Site 2 Case 22', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(23, 2, 23, 'Site 2 Case 23', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(24, 2, 24, 'Site 2 Case 24', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(25, 2, 25, 'Site 2 Case 25', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(26, 2, 26, 'Site 2 Case 26', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(27, 2, 27, 'Site 2 Case 27', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(28, 2, 28, 'Site 2 Case 28', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(29, 2, 29, 'Site 2 Case 29', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(30, 2, 30, 'Site 2 Case 30', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(31, 2, 31, 'Site 2 Case 31', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(32, 2, 32, 'Site 2 Case 32', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(33, 2, 33, 'Site 2 Case 33', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(34, 2, 34, 'Site 2 Case 34', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(35, 2, 35, 'Site 2 Case 35', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(36, 2, 36, 'Site 2 Case 36', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(37, 2, 37, 'Site 2 Case 37', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(38, 2, 38, 'Site 2 Case 38', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(39, 3, 1, 'Site 3 Case 1', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(40, 3, 2, 'Site 3 Case 2', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(41, 3, 3, 'Site 3 Case 3', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(42, 3, 4, 'Site 3 Case 4', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(43, 3, 5, 'Site 3 Case 5', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(44, 3, 6, 'Site 3 Case 6', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(45, 3, 7, 'Site 3 Case 7', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(46, 3, 8, 'Site 3 Case 8', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(47, 3, 9, 'Site 3 Case 9', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(48, 3, 10, 'Site 3 Case 10', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(49, 3, 11, 'Site 3 Case 11', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(50, 3, 12, 'Site 3 Case 12', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(51, 3, 13, 'Site 3 Case 13', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(52, 3, 14, 'Site 3 Case 14', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(53, 3, 15, 'Site 3 Case 15', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(54, 3, 16, 'Site 3 Case 16', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(55, 3, 17, 'Site 3 Case 17', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(56, 3, 18, 'Site 3 Case 18', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(57, 3, 19, 'Site 3 Case 19', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(58, 3, 20, 'Site 3 Case 20', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(59, 3, 21, 'Site 3 Case 21', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(60, 3, 22, 'Site 3 Case 22', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(61, 3, 23, 'Site 3 Case 23', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(62, 3, 24, 'Site 3 Case 24', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(63, 3, 25, 'Site 3 Case 25', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(64, 3, 26, 'Site 3 Case 26', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(65, 3, 27, 'Site 3 Case 27', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(66, 3, 28, 'Site 3 Case 28', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(67, 3, 29, 'Site 3 Case 29', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(68, 3, 30, 'Site 3 Case 30', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(69, 3, 31, 'Site 3 Case 31', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(70, 3, 32, 'Site 3 Case 32', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(71, 3, 33, 'Site 3 Case 33', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(72, 3, 34, 'Site 3 Case 34', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(73, 3, 35, 'Site 3 Case 35', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(74, 3, 36, 'Site 3 Case 36', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(75, 3, 37, 'Site 3 Case 37', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(76, 3, 38, 'Site 3 Case 38', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(77, 3, 39, 'Site 3 Case 39', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(78, 3, 40, 'Site 3 Case 40', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(79, 3, 41, 'Site 3 Case 41', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(80, 3, 42, 'Site 3 Case 42', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(81, 3, 43, 'Site 3 Case 43', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(82, 3, 44, 'Site 3 Case 44', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(83, 3, 45, 'Site 3 Case 45', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(84, 3, 46, 'Site 3 Case 46', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(85, 3, 47, 'Site 3 Case 47', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(86, 3, 48, 'Site 3 Case 48', NULL, NULL, 'available', NULL, NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL);

-- Listage de la structure de table base_airid. exp_huts_incidents
CREATE TABLE IF NOT EXISTS `exp_huts_incidents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hut_id` bigint unsigned DEFAULT NULL,
  `project_usage_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `incident_date` date NOT NULL,
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `reported_by` bigint unsigned DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_incidents_hut_id_foreign` (`hut_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_incidents : 1 rows
INSERT INTO `exp_huts_incidents` (`id`, `hut_id`, `project_usage_id`, `project_id`, `title`, `description`, `incident_date`, `severity`, `status`, `reported_by`, `resolved_by`, `resolved_at`, `resolution_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, NULL, 40, 'Coupure de courant', 'Less gars n\'ont pas pu dormir là comme prévu le 10/04/2026', '2026-04-10', 'low', 'open', 18, NULL, NULL, 'C\'est résolu après', '2026-04-13 09:20:12', '2026-04-13 09:20:42', NULL);

-- Listage de la structure de table base_airid. exp_huts_notifications
CREATE TABLE IF NOT EXISTS `exp_huts_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_notifications_user_id_read_at_index` (`user_id`,`read_at`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_notifications : 46 rows
INSERT INTO `exp_huts_notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `url`, `read_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(2, 3, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(3, 5, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(4, 18, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', '2026-04-13 10:13:48', '2026-04-10 15:05:42', '2026-04-13 10:13:48'),
	(5, 21, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(6, 24, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(7, 31, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(8, 85, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(9, 96, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 a démarré. 2 case(s) utilisée(s) du 10/04/2026 au 11/04/2026. Durée : 2 jours.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(10, 1, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(11, 3, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(12, 5, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(13, 18, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', '2026-04-13 10:13:48', '2026-04-13 09:20:12', '2026-04-13 10:13:48'),
	(14, 21, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(15, 24, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(16, 31, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(17, 46, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(18, 85, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(19, 96, 'incident_reported', 'Incident : Coupure de courant', 'Incident signalé : "Coupure de courant" dans Site 2 Case 1 (Site 2).', '{"hut_name": "Site 2 Case 1", "severity": "low", "incident_id": 1, "project_code": null}', 'http://127.0.0.1:8001/incidents/1', NULL, '2026-04-13 09:20:12', '2026-04-13 09:20:12'),
	(20, 1, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(21, 3, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(22, 5, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(23, 18, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', '2026-04-13 10:13:48', '2026-04-13 09:44:25', '2026-04-13 10:13:48'),
	(24, 21, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(25, 24, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(26, 31, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(27, 85, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(28, 96, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 1 s\'est terminée le 11/04/2026.', '{"session_id": 1, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25'),
	(29, 1, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(30, 3, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(31, 5, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(32, 18, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', '2026-04-13 10:13:48', '2026-04-13 10:11:03', '2026-04-13 10:13:48'),
	(33, 21, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(34, 24, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(35, 31, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(36, 85, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(37, 96, 'activity_started', 'Démarrage activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 a démarré. 2 case(s) utilisée(s) du 12/04/2026 au 13/04/2026. Durée : 2 jours.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(38, 1, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(39, 3, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(40, 5, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(41, 18, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', '2026-04-13 10:14:30', '2026-04-13 10:14:24', '2026-04-13 10:14:30'),
	(42, 21, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(43, 24, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(44, 31, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(45, 85, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24'),
	(46, 96, 'activity_ended', 'Fin d\'activité — 26-01', 'L\'activité en cases expérimentales du projet 26-01 — Phase 2 s\'est terminée le 13/04/2026.', '{"session_id": 2, "project_code": "26-01"}', 'http://127.0.0.1:8001/projects/40', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24');

-- Listage de la structure de table base_airid. exp_huts_project_usages
CREATE TABLE IF NOT EXISTS `exp_huts_project_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned DEFAULT NULL,
  `hut_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `study_activity_id` bigint unsigned DEFAULT NULL,
  `phase_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_project_usages_hut_id_foreign` (`hut_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_project_usages : 4 rows
INSERT INTO `exp_huts_project_usages` (`id`, `session_id`, `hut_id`, `project_id`, `study_activity_id`, `phase_name`, `date_start`, `date_end`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 40, NULL, 'Phase 1', '2026-04-10', '2026-04-11', 'dd', 18, '2026-04-10 15:05:42', '2026-04-10 15:05:42', NULL),
	(2, 1, 2, 40, NULL, 'Phase 1', '2026-04-10', '2026-04-11', 'dd', 18, '2026-04-10 15:05:42', '2026-04-10 15:05:42', NULL),
	(3, 2, 2, 40, NULL, 'Phase 2', '2026-04-12', '2026-04-13', 'Seconde phase des cases expérimentales', 18, '2026-04-13 10:11:03', '2026-04-13 10:11:03', NULL),
	(4, 2, 3, 40, NULL, 'Phase 2', '2026-04-12', '2026-04-13', 'Seconde phase des cases expérimentales', 18, '2026-04-13 10:11:03', '2026-04-13 10:11:03', NULL);

-- Listage de la structure de table base_airid. exp_huts_push_subscriptions
CREATE TABLE IF NOT EXISTS `exp_huts_push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `endpoint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `p256dh` text COLLATE utf8mb4_unicode_ci,
  `auth` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_push_subscriptions_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_push_subscriptions : 0 rows

-- Listage de la structure de table base_airid. exp_huts_sites
CREATE TABLE IF NOT EXISTS `exp_huts_sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('active','abandoned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_sites : 3 rows
INSERT INTO `exp_huts_sites` (`id`, `name`, `village`, `city`, `image_path`, `latitude`, `longitude`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Site 1', NULL, NULL, NULL, NULL, NULL, 'abandoned', 'Site abandonné — plus en service.', '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(2, 'Site 2', NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL),
	(3, 'Site 3', NULL, NULL, NULL, NULL, NULL, 'active', NULL, '2026-04-10 14:51:14', '2026-04-10 14:51:14', NULL);

-- Listage de la structure de table base_airid. exp_huts_sleeper_assignments
CREATE TABLE IF NOT EXISTS `exp_huts_sleeper_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `hut_id` bigint unsigned NOT NULL,
  `sleeper_id` bigint unsigned NOT NULL,
  `assignment_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session_hut_date` (`session_id`,`hut_id`,`assignment_date`),
  KEY `exp_huts_sleeper_assignments_hut_id_foreign` (`hut_id`),
  KEY `exp_huts_sleeper_assignments_sleeper_id_foreign` (`sleeper_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_sleeper_assignments : 12 rows
INSERT INTO `exp_huts_sleeper_assignments` (`id`, `session_id`, `hut_id`, `sleeper_id`, `assignment_date`, `notes`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, '2026-04-09', NULL, NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(2, 1, 2, 2, '2026-04-09', NULL, NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(3, 1, 1, 1, '2026-04-10', NULL, NULL, '2026-04-10 15:05:42', '2026-04-13 10:06:31'),
	(4, 1, 2, 2, '2026-04-10', NULL, NULL, '2026-04-10 15:05:42', '2026-04-13 10:06:31'),
	(5, 1, 1, 2, '2026-04-11', NULL, NULL, '2026-04-13 09:14:16', '2026-04-13 10:06:31'),
	(6, 1, 2, 1, '2026-04-11', NULL, NULL, '2026-04-13 09:14:16', '2026-04-13 10:06:31'),
	(7, 2, 2, 1, '2026-04-11', NULL, NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(8, 2, 3, 2, '2026-04-11', NULL, NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(9, 2, 2, 2, '2026-04-12', NULL, NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(10, 2, 3, 1, '2026-04-12', NULL, NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03'),
	(11, 2, 2, 1, '2026-04-13', NULL, NULL, '2026-04-13 10:15:07', '2026-04-13 10:15:07'),
	(12, 2, 3, 2, '2026-04-13', NULL, NULL, '2026-04-13 10:15:07', '2026-04-13 10:15:07');

-- Listage de la structure de table base_airid. exp_huts_sleepers
CREATE TABLE IF NOT EXISTS `exp_huts_sleepers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `site_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_huts_sleepers_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_sleepers : 2 rows
INSERT INTO `exp_huts_sleepers` (`id`, `site_id`, `name`, `code`, `gender`, `active`, `notes`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Dormeur 1', '001', 'M', 1, NULL, NULL, '2026-04-10 15:00:05', '2026-04-10 15:00:24'),
	(2, NULL, 'Dormeur 2', '002', 'F', 1, NULL, NULL, '2026-04-10 15:00:18', '2026-04-10 15:00:18');

-- Listage de la structure de table base_airid. exp_huts_state_changes
CREATE TABLE IF NOT EXISTS `exp_huts_state_changes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hut_id` bigint unsigned NOT NULL,
  `previous_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `changed_by` bigint unsigned DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exp_huts_state_changes_hut_id_foreign` (`hut_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_state_changes : 8 rows
INSERT INTO `exp_huts_state_changes` (`id`, `hut_id`, `previous_status`, `new_status`, `reason`, `changed_by`, `changed_at`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'available', 'in_use', 'Affectée au projet 26-01', 18, '2026-04-10 15:05:42', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42', NULL),
	(2, 2, 'available', 'in_use', 'Affectée au projet 26-01', 18, '2026-04-10 15:05:42', NULL, '2026-04-10 15:05:42', '2026-04-10 15:05:42', NULL),
	(3, 1, 'in_use', 'available', 'Fin d\'activité — 26-01', 18, '2026-04-13 09:44:25', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25', NULL),
	(4, 2, 'in_use', 'available', 'Fin d\'activité — 26-01', 18, '2026-04-13 09:44:25', NULL, '2026-04-13 09:44:25', '2026-04-13 09:44:25', NULL),
	(5, 2, 'available', 'in_use', 'Affectée au projet 26-01', 18, '2026-04-13 10:11:03', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03', NULL),
	(6, 3, 'available', 'in_use', 'Affectée au projet 26-01', 18, '2026-04-13 10:11:03', NULL, '2026-04-13 10:11:03', '2026-04-13 10:11:03', NULL),
	(7, 2, 'in_use', 'available', 'Fin d\'activité — 26-01', 18, '2026-04-13 10:14:24', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24', NULL),
	(8, 3, 'in_use', 'available', 'Fin d\'activité — 26-01', 18, '2026-04-13 10:14:24', NULL, '2026-04-13 10:14:24', '2026-04-13 10:14:24', NULL);

-- Listage de la structure de table base_airid. exp_huts_usage_sessions
CREATE TABLE IF NOT EXISTS `exp_huts_usage_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `phase_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('planned','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `notifications_sent_start` tinyint(1) NOT NULL DEFAULT '0',
  `notifications_sent_end` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_usage_sessions : 2 rows
INSERT INTO `exp_huts_usage_sessions` (`id`, `project_id`, `phase_name`, `date_start`, `date_end`, `notes`, `status`, `notifications_sent_start`, `notifications_sent_end`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 40, 'Phase 1', '2026-04-10', '2026-04-11', 'dd', 'completed', 1, 1, 18, NULL, '2026-04-10 15:05:42', '2026-04-13 09:44:25'),
	(2, 40, 'Phase 2', '2026-04-12', '2026-04-13', 'Seconde phase des cases expérimentales', 'completed', 1, 1, 18, NULL, '2026-04-13 10:11:03', '2026-04-13 10:14:24');

-- Listage de la structure de table base_airid. exp_huts_user_prefs
CREATE TABLE IF NOT EXISTS `exp_huts_user_prefs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `push_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `notify_incidents` tinyint(1) NOT NULL DEFAULT '1',
  `notify_activity_start` tinyint(1) NOT NULL DEFAULT '1',
  `notify_activity_end` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_huts_user_prefs_user_id_unique` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table base_airid.exp_huts_user_prefs : 10 rows
INSERT INTO `exp_huts_user_prefs` (`id`, `user_id`, `push_enabled`, `notify_incidents`, `notify_activity_start`, `notify_activity_end`, `created_at`, `updated_at`) VALUES
	(1, 18, 1, 1, 1, 1, '2026-04-10 14:50:00', '2026-04-13 10:23:02'),
	(2, 1, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(3, 3, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(4, 5, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(5, 21, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(6, 24, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(7, 31, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(8, 85, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(9, 96, 0, 1, 1, 1, '2026-04-10 15:05:42', '2026-04-10 15:05:42'),
	(10, 46, 0, 1, 1, 1, '2026-04-13 09:20:12', '2026-04-13 09:20:12');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
