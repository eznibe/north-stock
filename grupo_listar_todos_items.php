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
	Categoria.categoria,
	Item.id_categoria,
	Item.id_proveedor,
	Proveedor.proveedor,
	Item.stock_disponible,
	Categoria.stock_minimo,
	Item.stock_disponible - Categoria.stock_minimo,
	Unidad.unidad,
	Item.stock_transito,
	Item.stock_disponible + Item.stock_transito - Categoria.stock_minimo - (coalesce(sum(pi.cantidad), 0)),
	Item.codigo_proveedor,
	Item.id_item,
	round(Item.precio_fob, 2),
	round(Item.precio_nac, 2),
	round(Item.precio_ref, 2),
	Categoria.reservado,
  coalesce(sum(pi.cantidad), 0) as prevision
  FROM
    Item  
    JOIN Categoria on Item.id_categoria = Categoria.id_categoria
    JOIN Unidad on Unidad.id_unidad = Categoria.id_unidad_visual
    JOIN Proveedor on Proveedor.id_proveedor = Item.id_proveedor
    LEFT JOIN previsionitem pi on pi.id_item = Item.id_item
    LEFT JOIN prevision p on p.id_prevision = pi.id_prevision
  WHERE
	  Categoria.id_grupo = $id_grupo
  GROUP BY 
    Item.id_item
  ORDER BY
	  Categoria.categoria";
$result = mysql_query($query);

$aux = "";
while ($row = mysql_fetch_array($result))
{
 if ($row[6] < 0) $row[6] = "<em>$row[6]</em>";
 if ($row[9] < 0) $row[9] = "<em>$row[9]</em>";
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[11]);\">$row[0]</a></td>
      <td bgcolor=#D4D4D4>$row[4]</td><td bgcolor=#D4D4D4>$row[5]</td><td>$row[6]</td><td>$row[8]</td>
      <td>$row[16]</td>
      <td title='Reservado: $row[15]'>$row[9]</td> <td bgcolor=#D4D4D4>$row[12]</td><td bgcolor=#D4D4D4>$row[13]</td><td bgcolor=#D4D4D4>$row[14]</td> <td>$row[7]</td><td>$row[3]</td><td>$row[10]</td></tr>\n";
}

$grupo = obtener_grupo($id_grupo);
$titulo = "Listado total de items del grupo $grupo";

$var = array("rows" => $aux,
			 "titulo" => $titulo);
eval_html('producto_listar_todos_items.html', $var);
