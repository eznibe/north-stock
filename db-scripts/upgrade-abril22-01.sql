alter table itemcomprar add column cantidad_pendiente int;

update itemcomprar set cantidad_pendiente = cantidad;

alter table itemcomprar add column tentativo bool default true;