
alter table orden add column despacho varchar(128);


alter table orden add column fecha_bkp timestamp;

update orden set fecha_bkp = fecha;

alter table orden modify fecha timestamp default CURRENT_TIMESTAMP;

update orden set fecha = fecha_bkp;

--alter table orden drop column fecha_bkp;
