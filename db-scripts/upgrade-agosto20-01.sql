
create table logprecios
(
   id_item int not null,
   precio_fob real, 
   precio_nac real, 
   precio_ref real, 
   stock_anterior real,
   stock_disponible real,
   insertado timestamp
)
engine=MyISAM
;

insert into logprecios (id_item, precio_fob, precio_nac, precio_ref, insertado) 
select id_item, precio_fob, precio_nac, precio_ref, '2000-01-01'
FROM item i join categoria c on i.id_categoria = c.id_categoria;