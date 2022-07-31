DROP TABLE IF EXISTS `empleado_rol`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `empleados`;
DROP TABLE IF EXISTS `roles`;

CREATE TABLE `areas`
   (
      `id` int,
      `nombre` varchar(255),
      PRIMARY KEY (ID)
   );

INSERT INTO `areas` (`id`, `nombre`) VALUES
(1, 'Administrativa y Financiera'),
(2, 'Ingeniería'),
(5, 'Desarrollo de Negocio'),
(6, 'Proyectos'),
(7, 'Servicios'),
(8, 'Calidad');


CREATE TABLE `roles`
   (
      `id` int,
      `nombre` varchar(255),
      PRIMARY KEY (`id`)
   );

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Desarrollador'),
(2, 'Analista'),
(3, 'Tester'),
(4, 'Diseñador'),
(5, 'Profesional PMO'),
(6, 'Profesional de servicios'),
(7, 'Auxiliar administrativo'),
(8, 'Codirector');

CREATE TABLE `empleados`
   (
      `id` int AUTO_INCREMENT,
      `nombre` varchar(255),
      `email` varchar(255),
      `sexo` char(1),
      `area_id` int,
      `boletin` int,
      `descripcion` text,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`area_id`) REFERENCES areas(`id`)
   );

CREATE TABLE `empleado_rol`
   (
      `id` int AUTO_INCREMENT,
      `empleado_id` int,
      `rol_id` int,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`empleado_id`) REFERENCES empleados(`id`),
      FOREIGN KEY (`rol_id`) REFERENCES roles(`id`)
   );