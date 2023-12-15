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

