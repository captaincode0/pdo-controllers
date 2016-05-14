drop database if exists test_pdo_mysql;
create database if not exists test_pdo_mysql;
use test_pdo_mysql;

create table users(
	id int not null primary key auto_increment,
	email varchar(45) not null,
	password char(32) not null,
	active boolean not null default 0
);