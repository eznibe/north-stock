
create view v_en_transito_por_item as
SELECT i.id_item, oi.id_tipo_envio, sum(oi.cantidad_pendiente) as pendiente, te.tipo_envio
FROM ordenitem oi join item i on oi.id_item = i.id_item join orden o on o.id_orden = oi.id_orden join tipoenvio te on te.id_tipo_envio = oi.id_tipo_envio
WHERE o.id_status = 1 and oi.cantidad_pendiente > 0
GROUP BY i.id_item, oi.id_tipo_envio
ORDER BY i.id_item, te.orden, oi.id_tipo_envio;

create view v_en_prevision_por_item as
SELECT pi.id_item, sum(pi.cantidad) as cantidad
FROM prevision p
JOIN previsionitem pi on pi.id_prevision = p.id_prevision
where p.fecha_descarga is null and (pi.descargado = false or pi.descargado is null)
group by pi.id_item;


drop view v_export_items;

create view v_export_items as
SELECT i.id_item, i.codigo_barras, c.categoria, g.grupo, IF(p.id_pais = 1, "NAC", "EXT") as tipo_proveedor, 
	p.proveedor,
	i.codigo_proveedor,
	u.unidad,
	i.factor_unidades,
	if(i.precio_fob is null, "", i.precio_fob) as precio_fob, 
	if(i.precio_nac is null, "", i.precio_nac) as precio_nac, 
	if(i.precio_ref is null, "", i.precio_ref) as precio_ref, 
	if (max(lp.insertado) = "2020-01-01", "", max(lp.insertado)) as fecha_ultimo_precio, 
	i.stock_disponible,
	c.stock_minimo,
	round(c.porc_impuesto,2) as porcentaje_impuesto_categoria,
	coalesce(sum(en_prevision.cantidad), 0) as prevision,
	coalesce(itemcomprar.cantidad_pendiente, 0) as compras,
	if(nacional.pendiente is null, 0, nacional.pendiente) as en_transito_nacional,
	if(courier.pendiente is null, 0, courier.pendiente) as en_transito_courier,
	if(courier2.pendiente is null, 0, courier2.pendiente) as en_transito_courier2,
	if(aereo1.pendiente is null, 0, aereo1.pendiente) as en_transito_aereo1,
	if(aereo2.pendiente is null, 0, aereo2.pendiente) as en_transito_aereo2,
	if(maritimo.pendiente is null, 0, maritimo.pendiente) as en_transito_maritimo,
	if(desconocido.pendiente is null, 0, desconocido.pendiente) as en_transito_desconocido
FROM item i join categoria c on c.id_categoria = i.id_categoria join grupo g on g.id_grupo = c.id_grupo join proveedor p on p.id_proveedor = i.id_proveedor join unidad u on u.id_unidad = i.id_unidad_compra
	left join logprecios lp on lp.id_item = i.id_item
	LEFT JOIN itemcomprar on (itemcomprar.id_item = i.id_item and itemcomprar.tentativo = false)  
	LEFT JOIN v_en_prevision_por_item en_prevision on en_prevision.id_item = i.id_item
      left join v_en_transito_por_item nacional on nacional.id_tipo_envio = 1 and nacional.id_item = i.id_item
      left join v_en_transito_por_item courier on courier.id_tipo_envio = 2 and courier.id_item = i.id_item
      left join v_en_transito_por_item courier2 on courier2.id_tipo_envio = 3 and courier2.id_item = i.id_item
      left join v_en_transito_por_item aereo1 on aereo1.id_tipo_envio = 4 and aereo1.id_item = i.id_item
      left join v_en_transito_por_item aereo2 on aereo2.id_tipo_envio = 5 and aereo2.id_item = i.id_item
      left join v_en_transito_por_item maritimo on maritimo.id_tipo_envio = 6 and maritimo.id_item = i.id_item
      left join v_en_transito_por_item desconocido on desconocido.id_tipo_envio = 7 and desconocido.id_item = i.id_item
where 1=1
group by i.id_item
order by g.grupo, c.categoria;