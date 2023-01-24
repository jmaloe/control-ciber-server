/*Autor: Jesus Malo Escobar
 email: dic.malo@gmail.com
 Cel. 9621332427
*/

CREATE DATABASE dbmycontrol CHARACTER SET utf8 COLLATE utf8_general_ci;
USE dbmycontrol;

/*CREACION DE TABLAS PARA EL ACCESO A USUARIOS*/
CREATE TABLE usuarios(
	idUser SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user varchar(25) NOT NULL,
	password TINYTEXT NOT NULL,
	email TINYTEXT NOT NULL,
	nombre TINYTEXT NOT NULL,	
	vigente TINYINT(1) DEFAULT 1,
	creado_por SMALLINT UNSIGNED,
	fechaCaptura TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=INNODB;

insert into usuarios(user,password,email,nombre,vigente) values('jesus.malo','c9acd907f21fa81033a64809fd73e991','dic.malo@gmail.com','Jesús Malo Escobar',1);

CREATE TABLE roles(
	idRol SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nombreRol VARCHAR(25) NOT NULL
)ENGINE=INNODB;

insert into roles(nombreRol) values('Superusuario'),('Administrador'),('Gestor');

CREATE TABLE recursos(
	idRecurso SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nombreRecurso TINYTEXT NOT NULL	
)ENGINE=INNODB;

insert into recursos(nombreRecurso) values('FRegistro'),('FAdministracion'),('FAdminPermisos'),('FAdminRoles'),('FAdminUsuarios'),('FAdminRecursos'),('FReportes');

CREATE TABLE permisos_rol(
	idPermiso SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	idRol SMALLINT UNSIGNED NOT NULL,
	idRecurso SMALLINT UNSIGNED NOT NULL,
	lectura TINYINT(1) NOT NULL,
	escritura TINYINT(1) NOT NULL,
	actualizacion TINYINT(1) NOT NULL,
	eliminacion TINYINT(1) NOT NULL,
	FOREIGN KEY (idRol) REFERENCES roles(idRol) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (idRecurso) REFERENCES recursos(idRecurso) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

insert into permisos_rol(idRol,idRecurso,lectura,escritura,actualizacion,eliminacion) values(1,1,1,1,1,1),(1,2,1,1,1,1),(1,3,1,1,1,1),(1,4,1,1,1,1),(1,5,1,1,1,1),(1,6,1,1,1,1),(1,7,1,1,1,1);

CREATE TABLE rol_del_usuario(
	cns_rol_usuario SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	idRol SMALLINT UNSIGNED,
	idUser SMALLINT UNSIGNED,
	FOREIGN KEY (idRol) REFERENCES roles(idRol) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (idUser) REFERENCES usuarios(idUser) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

insert into rol_del_usuario(idRol,idUser) values(1,1);

CREATE TABLE CategoriaEquipo(
	id_cat TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	descripcion varchar(15),
	icon varchar(20),
	clase varchar(20) not null default 'btn-primary'
)ENGINE=INNODB;

INSERT INTO CategoriaEquipo(descripcion,icon) values('PC','01pc.svg'),('Laptop','02laptop.svg'),('Smartphone','03smartphone.svg'),('Impresora','04printer.svg');	

CREATE TABLE Equipo(
	id_equipo SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_cat TINYINT UNSIGNED,
	serial VARCHAR(10),
	tag VARCHAR(15),
	fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	fechaDeBaja DATE,
	FOREIGN KEY(id_cat) REFERENCES CategoriaEquipo(id_cat) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

CREATE TABLE Producto(
	id_producto SMALLINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nombre TINYTEXT,
	descripcion TINYTEXT,
	precio_compra DECIMAL(6,2),
	precio_venta DECIMAL(6,2)
)ENGINE=INNODB;

CREATE TABLE Venta(
	no_venta INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_equipo SMALLINT UNSIGNED,
	fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	hora_inicio TIME,
	hora_termino TIME,
	total DECIMAL(6,2),
	idUser SMALLINT UNSIGNED,
	openorclosed BIT(1) DEFAULT 0,
	nota TINYTEXT,
	pago_anticipado BIT(1) DEFAULT 0,
	FOREIGN KEY(id_equipo) REFERENCES Equipo(id_equipo) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(idUser) REFERENCES usuarios(idUser) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

CREATE TABLE DetalleDeVenta(
	cnsdv INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	no_venta INTEGER UNSIGNED,
	id_producto SMALLINT UNSIGNED,
	cantidad DECIMAL(5,2),
	precio_unitario DECIMAL(6,2),
	total DECIMAL(6,2),
	FOREIGN KEY(no_venta) REFERENCES Venta(no_venta) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(id_producto) REFERENCES Producto(id_producto) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

CREATE TABLE Compra(
	cns_compra INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	fechaCompra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	total DECIMAL(6,2),
	idUser SMALLINT UNSIGNED,
	FOREIGN KEY(idUser) REFERENCES usuarios(idUser) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;

CREATE TABLE DetalleDeCompra(
	cnsdc INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	cns_compra INTEGER UNSIGNED,
	id_producto SMALLINT UNSIGNED,
	cantidad SMALLINT UNSIGNED,
	precio_compra DECIMAL(6,2),
	total DECIMAL(6,2),
	FOREIGN KEY(cns_compra) REFERENCES Compra(cns_compra) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(id_producto) REFERENCES Producto(id_producto) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=INNODB;