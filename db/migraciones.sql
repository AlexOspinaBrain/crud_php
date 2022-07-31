DROP TABLE IF EXISTS areas;
CREATE TABLE areas
   (
      id int AUTO_INCREMENT,
      nombre varchar(255),
      PRIMARY KEY (ID)
   );
INSERT INTO areas (nombre) VALUES ('Administrativo');
INSERT INTO areas (nombre) VALUES ('Ventas');
INSERT INTO areas (nombre) VALUES ('Informatica');
INSERT INTO areas (nombre) VALUES ('Operaciones');

DROP TABLE IF EXISTS roles;
CREATE TABLE roles
   (
      id int AUTO_INCREMENT,
      nombre varchar(255),
      PRIMARY KEY (ID)
   );

INSERT INTO roles (nombre) VALUES ('Profesional de proyectos - Desarrollador');
INSERT INTO roles (nombre) VALUES ('Gerente estratégico');
INSERT INTO roles (nombre) VALUES ('Auxiliar administrativo');

DROP TABLE IF EXISTS empleados;
CREATE TABLE empleados
   (
      id int AUTO_INCREMENT,
      nombre varchar(255),
      email varchar(255),
      sexo char(1),
      area_id int,
      boletin int,
      descripcion text,
      PRIMARY KEY (ID),
      FOREIGN KEY (area_id) REFERENCES areas(id)
   );

DROP TABLE IF EXISTS empleado_rol;
CREATE TABLE empleado_rol
   (
      id int AUTO_INCREMENT,
      empleado_id int,
      rol_id int,
      PRIMARY KEY (ID),
      FOREIGN KEY (empleado_id) REFERENCES empleados(id),
      FOREIGN KEY (rol_id) REFERENCES roles(id)
   );