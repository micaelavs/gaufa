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