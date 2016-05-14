create database test_pdo_postgre;
drop table if exists users;
create table users (
	id serial not null primary key,
	name varchar(45) not null,
	password char(32) not null,
	active boolean not null default '0'
);