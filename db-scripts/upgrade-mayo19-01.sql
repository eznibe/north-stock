
alter table categoria add column pos_arancelaria varchar(128);

alter table orden add column nr_factura varchar(64);

create table inflacion (id_inflacion int not null auto_increment, anio int, mes int, valor decimal, primary key (id_inflacion)) engine=MyISAM;

alter table inflacion modify column valor decimal(5,2);

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



create or replace view v_last_orders as
SELECT 
oi.id_item,
SUBSTRING_INDEX(group_concat(oi.precio_fob order by o.fecha desc, o.id_orden desc), ',', 1) as precio_fob,
SUBSTRING_INDEX(group_concat(oi.precio_ref order by o.fecha desc, o.id_orden desc), ',', 1) as precio_ref,
SUBSTRING_INDEX(group_concat(o.id_orden order by o.fecha desc, o.id_orden desc), ',', 1) as id_orden,
SUBSTRING_INDEX(group_concat(o.fecha order by o.fecha desc, o.id_orden desc), ',', 1) as fecha,
SUBSTRING_INDEX(group_concat(o.cotizacion_dolar order by o.fecha desc, o.id_orden desc), ',', 1) as cotizacion_dolar,
SUBSTRING_INDEX(group_concat(o.nr_factura order by o.fecha desc, o.id_orden desc), ',', 1) as nr_factura,
SUBSTRING_INDEX(group_concat(o.despacho order by o.fecha desc, o.id_orden desc), ',', 1) as despacho,
count(*)
FROM ordenitem oi join orden o on o.id_orden=oi.id_orden 
--where oi.id_item = 3488 
group by oi.id_item