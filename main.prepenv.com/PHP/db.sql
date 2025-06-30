-- --------------------------------------------------------
-- Host:                         193.203.166.205
-- Server version:               10.11.10-MariaDB-log - MariaDB Server
-- Server OS:                    Linux
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for u124132715_parking_db
CREATE DATABASE IF NOT EXISTS `u124132715_parking_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `u124132715_parking_db`;

-- Dumping structure for table u124132715_parking_db.configuracion_lineas
CREATE TABLE IF NOT EXISTS `configuracion_lineas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_linea` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `parking_id` int(11) NOT NULL,
  `hora_inicio_preferida` time DEFAULT NULL,
  `hora_fin_preferida` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parking_id_numero_linea` (`parking_id`,`numero_linea`),
  CONSTRAINT `configuracion_lineas_ibfk_1` FOREIGN KEY (`parking_id`) REFERENCES `parking_lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.configuracion_sistema
CREATE TABLE IF NOT EXISTS `configuracion_sistema` (
  `config_key` varchar(50) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  `parking_id` int(11) NOT NULL,
  PRIMARY KEY (`config_key`,`parking_id`),
  KEY `parking_id` (`parking_id`),
  CONSTRAINT `configuracion_sistema_ibfk_1` FOREIGN KEY (`parking_id`) REFERENCES `parking_lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.espacios_fuera_de_servicio
CREATE TABLE IF NOT EXISTS `espacios_fuera_de_servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_linea` int(11) NOT NULL,
  `numero_espacio` int(11) NOT NULL,
  `parking_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parking_id_linea_espacio` (`parking_id`,`numero_linea`,`numero_espacio`),
  CONSTRAINT `espacios_fuera_de_servicio_ibfk_1` FOREIGN KEY (`parking_id`) REFERENCES `parking_lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.line_time_preferences
CREATE TABLE IF NOT EXISTS `line_time_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `line_config_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `line_config_id` (`line_config_id`),
  CONSTRAINT `line_time_preferences_ibfk_1` FOREIGN KEY (`line_config_id`) REFERENCES `configuracion_lineas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.parking_lots
CREATE TABLE IF NOT EXISTS `parking_lots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `access_code` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_code` (`access_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.phone_verifications
CREATE TABLE IF NOT EXISTS `phone_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(20) NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.registros_estacionamiento
CREATE TABLE IF NOT EXISTS `registros_estacionamiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `fecha_hora_entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_hora_salida` datetime DEFAULT NULL,
  `linea_asignada` int(11) DEFAULT NULL,
  `espacio_asignado` int(11) DEFAULT NULL,
  `estado` enum('registrado','asignado','bloqueado','expirado') NOT NULL,
  `parking_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `parking_id` (`parking_id`),
  CONSTRAINT `registros_estacionamiento_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `registros_estacionamiento_ibfk_2` FOREIGN KEY (`parking_id`) REFERENCES `parking_lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.user_parking_access
CREATE TABLE IF NOT EXISTS `user_parking_access` (
  `user_id` int(11) NOT NULL,
  `parking_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`parking_id`),
  KEY `parking_id` (`parking_id`),
  CONSTRAINT `user_parking_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_parking_access_ibfk_2` FOREIGN KEY (`parking_id`) REFERENCES `parking_lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telefono` (`telefono`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_parking_db.vehiculos_usuario
CREATE TABLE IF NOT EXISTS `vehiculos_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `alias` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_usuario_matricula` (`id_usuario`,`matricula`),
  CONSTRAINT `vehiculos_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
