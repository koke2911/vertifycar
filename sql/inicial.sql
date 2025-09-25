
create database certifycar;
use certifycar;

CREATE TABLE usuarios (
  id INT(10) NOT NULL AUTO_INCREMENT,
  rut VARCHAR(255) NOT NULL,
  clave VARCHAR(255) DEFAULT NULL,
  nombre VARCHAR(255) NOT NULL,
  apellidos VARCHAR(255) NOT NULL,
  fecha_nacimiento DATE NOT NULL,
  contacto VARCHAR(255) DEFAULT NULL,
  email VARCHAR(255) NOT NULL,
  tipo VARCHAR(255) DEFAULT NULL,
  estado INT(10) DEFAULT 0,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL,  
  PRIMARY KEY (id)
);

INSERT INTO usuarios (rut, clave, nombre, apellidos, fecha_nacimiento, contacto, email, tipo, estado, fecha_creacion)
VALUES ('17525457-9', 'c0e21b77a35c69aaf01cb8bb7a3f3194', 'Victor', 'Martinez Zamora', '1991-11-29', '975143052', 'koke1592@gmail.com', '1', 1, '1991-11-21 00:00:00');



update usuarios set clave='c0e21b77a35c69aaf01cb8bb7a3f3194' where rut='17525457-9';

create table tipos_usuario (
    id INT(10) NOT NULL AUTO_INCREMENT,
    glosa VARCHAR(100),
    estado int(2),
    PRIMARY KEY (id)
);

insert into tipos_usuario (glosa,estado)values('Administrador',1);
insert into tipos_usuario (glosa,estado)values('Mecanico',1);
insert into tipos_usuario (glosa,estado)values('Supervisor',1);
insert into tipos_usuario (glosa,estado)values('Secretaria',1);