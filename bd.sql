DROP DATABASE IF EXISTS sexshop;
CREATE DATABASE sexshop;
USE sexshop;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
	id INT AUTO_INCREMENT,
	nombre VARCHAR(250) NOT NULL,
	apellido VARCHAR(250) NOT NULL,
	estatus INT NOT NULL,
	inicio INT NOT NULL,
	responsable INT NOT NULL,
	id_empresa INT NOT NULL,
	fecha_creacion date NOT NULL,
	PRIMARY KEY (id)
); ALTER TABLE usuarios CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO usuarios (nombre,estatus,inicio,responsable,id_empresa,fecha_creacion) VALUES 
('admin',1,1,1,1,'2021-04-18'),
('modelos',1,1,1,1,'2021-04-18');

DROP TABLE IF EXISTS recorte1;
CREATE TABLE recorte1 (
	id INT AUTO_INCREMENT,
	nombre VARCHAR(250) NOT NULL,
	url VARCHAR(250) NOT NULL,
	recorte VARCHAR(250) NOT NULL,
	fecha_creacion date NOT NULL,
	PRIMARY KEY (id)
); ALTER TABLE recorte1 CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;