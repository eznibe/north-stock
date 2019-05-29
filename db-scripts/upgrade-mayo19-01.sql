
alter table categoria add column pos_arancelaria varchar(128);

alter table orden add column nr_factura varchar(64);

create table inflacion (id_inflacion int not null auto_increment, anio int, mes int, valor decimal, primary key (id_inflacion)) engine=MyISAM;

insert into inflacion (anio, mes,valor) values (2001, 13, 0);
insert into inflacion (anio, mes,valor) values (2002, 13, 0);
insert into inflacion (anio, mes,valor) values (2003, 13, 0);
insert into inflacion (anio, mes,valor) values (2004, 13, 0);
insert into inflacion (anio, mes,valor) values (2005, 13, 0);
insert into inflacion (anio, mes,valor) values (2006, 13, 0);
insert into inflacion (anio, mes,valor) values (2007, 13, 0);
insert into inflacion (anio, mes,valor) values (2008, 13, 0);
insert into inflacion (anio, mes,valor) values (2009, 13, 0);
insert into inflacion (anio, mes,valor) values (2010, 13, 0);
insert into inflacion (anio, mes,valor) values (2011, 13, 0);
insert into inflacion (anio, mes,valor) values (2012, 13, 0);
insert into inflacion (anio, mes,valor) values (2013, 13, 0);
insert into inflacion (anio, mes,valor) values (2014, 13, 0);
insert into inflacion (anio, mes,valor) values (2015, 13, 0);
insert into inflacion (anio, mes,valor) values (2016, 13, 0);
insert into inflacion (anio, mes,valor) values (2017, 13, 25);
insert into inflacion (anio, mes,valor) values (2018, 13, 45);
insert into inflacion (anio, mes,valor) values (2019, 13, 40);