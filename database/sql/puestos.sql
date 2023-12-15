DROP TABLE IF EXISTS `puestos`;

CREATE TABLE `puestos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `id_estado` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

insert  into `puestos`(`id`,`nombre`,`created_at`,`updated_at`,`id_estado`) values 
(1,'Abogado','2023-04-27 10:39:15','2023-04-27 10:39:15',1),
(2,'Contador','2023-04-27 10:39:18','2023-04-27 10:39:18',1),
(3,'Programador','2023-04-27 10:39:21','2023-04-27 10:39:21',1);
