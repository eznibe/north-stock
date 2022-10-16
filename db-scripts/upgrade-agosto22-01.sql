
insert into logprecios (id_item, precio_fob, precio_nac, precio_ref, insertado) 
SELECT i.id_item , coalesce(oi.precio_fob, i.precio_fob), i.precio_nac, coalesce(oi.precio_ref, i.precio_ref), o.fecha
FROM ordenitem oi join orden o on oi.id_orden = o.id_orden join item i on i.id_item = oi.id_item
where o.fecha > '2021-01-01'