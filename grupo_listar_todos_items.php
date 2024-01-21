<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$id_grupo = $_GET['id_grupo'];

function obtener_grupo($id_grupo)
{
	$query = "SELECT grupo FROM grupo WHERE id_grupo = $id_grupo";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

$query = "SELECT
	categoria.categoria,
	item.id_categoria,
	item.id_proveedor,
	proveedor.proveedor,
	item.stock_disponible,
	categoria.stock_minimo,
	item.stock_disponible - categoria.stock_minimo,
	unidad.unidad,
	item.stock_transito,
	item.stock_disponible + item.stock_transito - categoria.stock_minimo - (coalesce(sum(pi.cantidad), 0)),
	item.codigo_proveedor,
	item.id_item,
	round(item.precio_fob, 2),
	round(item.precio_nac, 2),
	round(item.precio_ref, 2),
	categoria.reservado,
  	coalesce(sum(pi.cantidad), 0) as prevision
  FROM
    item  
    JOIN categoria on item.id_categoria = categoria.id_categoria
    JOIN unidad on unidad.id_unidad = categoria.id_unidad_visual
    JOIN proveedor on proveedor.id_proveedor = item.id_proveedor
    LEFT JOIN previsionitem pi on pi.id_item = item.id_item
    LEFT JOIN prevision p on p.id_prevision = pi.id_prevision
  WHERE
	  categoria.id_grupo = $id_grupo
	  AND p.fecha_descarga is null and (pi.descargado = false or pi.descargado is null)
  GROUP BY 
    item.id_item
  ORDER BY
	  categoria.categoria";
$result = mysql_query($query);

$aux = "";
while ($row = mysql_fetch_array($result))
{
 if ($row[6] < 0) $row[6] = "<em>$row[6]</em>";
 if ($row[9] < 0) $row[9] = "<em>$row[9]</em>";
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[11]);\">$row[0]</a></td>
      <td bgcolor=#D4D4D4>$row[4]</td><td bgcolor=#D4D4D4>$row[5]</td><td>$row[6]</td><td>$row[8]</td>"
      . ($row[16] > 0 ? "<td><a class=\"list\" onclick=\"show_detail_previsiones($row[11]);\">$row[16]</a></td>" : "<td>$row[16]</td>").
      "<td>$row[9]</td> <td bgcolor=#D4D4D4>$row[12]</td><td bgcolor=#D4D4D4>$row[13]</td><td bgcolor=#D4D4D4>$row[14]</td> <td>$row[7]</td><td>$row[3]</td><td>$row[10]</td></tr>\n";
}

$grupo = obtener_grupo($id_grupo);
$titulo = "Listado total de items del grupo $grupo";

$var = array("rows" => $aux,
			 "titulo" => $titulo);
eval_html('producto_listar_todos_items.html', $var);
