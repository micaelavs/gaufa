/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.5.16-MariaDB : Database - xxxx_gaufa
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `metadata` */
DROP TABLE IF EXISTS `estados`;

CREATE TABLE `estados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `estados` (`nombre`) VALUES ('ALTA');
INSERT INTO `estados` (`nombre`) VALUES ('BAJA');
INSERT INTO `estados` (`nombre`) VALUES ('CANCELADA');
INSERT INTO `estados` (`nombre`) VALUES ('PENDIENTE APROBACION');
INSERT INTO `estados` (`nombre`) VALUES ('PENDIENTE ASIGNACION');
INSERT INTO `estados` (`nombre`) VALUES ('APROBADA');
INSERT INTO `estados` (`nombre`) VALUES ('ASIGNADA');
INSERT INTO `estados` (`nombre`) VALUES ('RECHAZADA');

DROP TABLE IF EXISTS `puestos`;

CREATE TABLE `puestos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `id_estado` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `puestos`(`id`,`nombre`,`created_at`,`updated_at`,`id_estado`) values 
(1,'Abogado','2023-04-27 10:39:15','2023-04-27 10:39:15',1),
(2,'Contador','2023-04-27 10:39:18','2023-04-27 10:39:18',1),
(3,'Programador','2023-04-27 10:39:21','2023-04-27 10:39:21',1);

DROP TABLE IF EXISTS `loggers`;

CREATE TABLE `loggers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(255) DEFAULT NULL,
  `record_id` bigint(20) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `user` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

ALTER TABLE loggers ADD INDEX (created_at);
ALTER TABLE loggers DROP PRIMARY KEY, ADD PRIMARY KEY (id, created_at);
ALTER TABLE loggers 
PARTITION BY RANGE (YEAR(created_at))
(
    PARTITION p2022 VALUES LESS THAN (2023),
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p2027 VALUES LESS THAN (2028),
    PARTITION p2028 VALUES LESS THAN (2029),
    PARTITION p2029 VALUES LESS THAN (2030),
    PARTITION p2030 VALUES LESS THAN MAXVALUE
);

DROP TABLE IF EXISTS `peticion_usuarios`;

CREATE TABLE `peticion_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `id_puesto` int(10) NOT NULL,
  `id_area` int(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_estado` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(2) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `roles` */

INSERT INTO `roles`(`id`,`nombre`,`descripcion`,`estado`,`created_at`,`updated_at`) values 
(1,'Administrador',NULL,1,'2023-04-12 14:03:58','2023-04-12 14:03:58'),
(2,'Operaciones',NULL,1,'2023-04-12 14:03:58','2023-04-12 14:03:58'),
(3,'RR HH',NULL,1,'2023-04-12 14:03:58','2023-04-12 14:03:58');

DROP TABLE IF EXISTS `enrutado`;

CREATE TABLE `enrutado` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `permisos` text DEFAULT NULL,
  `estado` tinyint(2) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;