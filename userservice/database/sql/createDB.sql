create database userservicedb;
use userservicedb;
create table users(
	user_id INT(10) AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	password VARCHAR(15) NOT NULL,
	PRIMARY KEY (user_id)
);

create table authorities(
	authority_id INT(10) AUTO_INCREMENT,
	authority VARCHAR(50) NOT NULL,
	PRIMARY KEY(authority_id)
);

create table user_authority(
	user_id INT(10) NOT NULL,
	authority_id INT(10) NOT NULL,
	PRIMARY KEY(user_id, authority_id),
	FOREIGN KEY(user_id) REFERENCES users(user_id),
	FOREIGN KEY(authority_id) REFERENCES authorities(authority_id)
);

INSERT INTO `userservicedb`.`authorities` (`authority`) VALUES ('admin'), ('user'), ('moderator');