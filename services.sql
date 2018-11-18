create database services;
use services;
create table users(
	id_user INT(10) AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(15) NOT NULL,
	PRIMARY KEY (id_user)
);

create table authories(
	id_authories INT(10) AUTO_INCREMENT,
	authority VARCHAR(50) NOT NULL,
	PRIMARY KEY(id_authories)
);

create table user_authority(
	id_user INT(10) NOT NULL,
	id_authories INT(10) NOT NULL,
	FOREIGN KEY(id_user) REFERENCES users(id_user),
	FOREIGN KEY(id_authories) REFERENCES authories(id_authories)
);