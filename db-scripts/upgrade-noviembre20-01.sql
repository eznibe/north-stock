
create table prevision
(
   id_prevision int key AUTO_INCREMENT,
   numero_orden varchar(64),
   cliente varchar(1024),
   fecha_entrega date,
   descripcion varchar(1024),
   fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP,
   fecha_descarga datetime,
   usuario_descarga varchar(64)
)
engine=MyISAM
;


create table previsionitem
(
   id_prevision_item int key AUTO_INCREMENT,
   id_prevision int not null,
   id_item int not null,
   cantidad int,
   precio_prevision float,
   moneda char(3),
   fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP
)
engine=MyISAM
;

alter table log add column id_prevision integer;