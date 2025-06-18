-- --------------------------------------------------------
-- Host:                         srv1606.hstgr.io
-- Server version:               10.11.10-MariaDB - MariaDB Server
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


-- Dumping database structure for u124132715_SYP
CREATE DATABASE IF NOT EXISTS `u124132715_DB` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `u124132715_DB`;

-- Dumping structure for table u124132715_SYP.Dishes
CREATE TABLE IF NOT EXISTS `Dishes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Datos`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=347 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.Events
CREATE TABLE IF NOT EXISTS `Events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=386 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.GUESTS
CREATE TABLE IF NOT EXISTS `GUESTS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dumping structure for table u124132715_SYP.FILES
CREATE TABLE IF NOT EXISTS `FILES` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` mediumtext DEFAULT NULL,
  `file_blob` longblob DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;


-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.Log
CREATE TABLE IF NOT EXISTS `Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` timestamp NULL DEFAULT NULL,
  `proceso` varchar(500) DEFAULT NULL,
  `texto` varchar(500) DEFAULT NULL,
  `status` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2744 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.RESTRICTIONS
CREATE TABLE IF NOT EXISTS `RESTRICTIONS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.ROLES
CREATE TABLE IF NOT EXISTS `ROLES` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=779 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.SELECTIONS
CREATE TABLE IF NOT EXISTS `SELECTIONS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Datos`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table u124132715_SYP.Users
CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TS` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
