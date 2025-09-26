
create database certifycar;
use certifycar;

CREATE TABLE usuarios (
  id INT(10) NOT NULL AUTO_INCREMENT,
  rut VARCHAR(255) NOT NULL,
  clave VARCHAR(255) DEFAULT NULL,
  nombre VARCHAR(255) NOT NULL,
  apellidos VARCHAR(255) NOT NULL, 
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



create table servicios (
    id INT(10) NOT NULL AUTO_INCREMENT,
    id_categoria int(10),
    nombre varchar(200),
    descripcion varchar(500),
    items varchar(1000),
    valor int(10),
    agenda int(1),
    pago int(1),
    estado int(1),
    fecha_crea TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL,
    imagen varchar(200),
    usu_crea int(3),
    PRIMARY KEY (id)
);


create table categorias (
id INT(10) NOT NULL AUTO_INCREMENT,
nombre varchar(100),
estado int(10),
 PRIMARY KEY (id) );
 
insert into categorias (nombre, estado) values('Revision Pre-compra',1);
insert into categorias (nombre, estado) values('Servicio de Gruas',1);
insert into categorias (nombre, estado) values('Instalación Polarizado',1);


CREATE TABLE faq_categorias (
  id INT(10) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 activo, 0 inactivo
  PRIMARY KEY (id)
);

INSERT INTO faq_categorias (id, nombre, estado) VALUES
(1, 'General', 1),
(2, 'Pagos', 1),
(3, 'Cuenta de Usuario', 1),
(4, 'Servicios', 1);


CREATE TABLE faq (
  id INT(10) NOT NULL AUTO_INCREMENT,
  id_categoria INT(10) DEFAULT NULL,
  pregunta VARCHAR(300) NOT NULL,
  respuesta TEXT NOT NULL,
  tags VARCHAR(500),          -- separados por ; (ej: pagos;cuentas;boletas)
  orden INT(10) DEFAULT 0,    -- para ordenar manualmente
  estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 activo, 0 inactivo, 2 eliminado (soft)
  fecha_crea TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  usu_crea INT(10) DEFAULT 0,
  PRIMARY KEY (id),
  KEY (id_categoria)
);



CREATE TABLE multimedia_categorias (
  id INT(10) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1, -- 1 activo, 0 inactivo
  PRIMARY KEY (id)
);

CREATE TABLE multimedia (
  id INT(10) NOT NULL AUTO_INCREMENT,
  id_categoria INT(10) DEFAULT NULL,
  tipo ENUM('imagen','video') NOT NULL,         -- imagen | video
  titulo VARCHAR(200) NOT NULL,
  descripcion VARCHAR(500) DEFAULT NULL,
  tags VARCHAR(500) DEFAULT NULL,               -- separados por ; (ej: evento;autos;2025)
  fuente ENUM('file','url') NOT NULL DEFAULT 'file',  -- archivo subido o URL externa (ej. YouTube)
  archivo VARCHAR(200) DEFAULT NULL,            -- nombre del archivo guardado (si fuente=file)
  url VARCHAR(500) DEFAULT NULL,                -- url externa (si fuente=url)
  estado TINYINT(1) NOT NULL DEFAULT 1,         -- 1 activo, 0 inactivo, 2 eliminado (soft)
  fecha_crea TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  usu_crea INT(10) DEFAULT 0,
  PRIMARY KEY (id),
  KEY (id_categoria)
);




INSERT INTO multimedia_categorias (id, nombre, estado) VALUES
(1, 'Eventos', 1),
(2, 'Galería', 1),
(3, 'Promociones', 1),
(4, 'Testimonios', 1);