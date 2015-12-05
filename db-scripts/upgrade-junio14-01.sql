

create table tipoenvio (id_tipo_envio int, tipo_envio varchar(32)) engine=MyISAM;

insert into tipoenvio values (0, 'Desconocido');
insert into tipoenvio values (1, 'Nacional');
insert into tipoenvio values (2, 'Courier');
insert into tipoenvio values (3, 'Aereo1');
insert into tipoenvio values (4, 'Aereo2');
insert into tipoenvio values (5, 'Maritimo');



alter table ordenitem add column id_tipo_envio int default 0;
alter table itemcomprar add column id_tipo_envio int default 0;


