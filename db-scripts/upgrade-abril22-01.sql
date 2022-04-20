alter table itemcomprar add column cantidad_pendiente int;

update itemcomprar set cantidad_pendiente = cantidad;

alter table itemcomprar add column tentativo bool default true;

insert into tipoenvio values (6, 'Courier2');

alter table tipoenvio add COLUMN orden double;

update tipoenvio set orden = 1 where id_tipo_envio = 1;
update tipoenvio set orden = 2 where id_tipo_envio = 2;
update tipoenvio set orden = 3 where id_tipo_envio = 6;
update tipoenvio set orden = 4 where id_tipo_envio = 3;
update tipoenvio set orden = 5 where id_tipo_envio = 4;
update tipoenvio set orden = 6 where id_tipo_envio = 5;
update tipoenvio set orden = 7 where id_tipo_envio = 0;
