<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_proveedor = $_GET['id_proveedor'];

$query = "SELECT
	categoria.categoria, proveedor.proveedor, itemcomprar.cantidad, unidad.unidad
	FROM
	categoria, proveedor, itemcomprar, item, unidad
	WHERE (
	(itemcomprar.id_item = item.id_item) AND
	(item.id_proveedor = proveedor.id_proveedor) AND
	(item.id_proveedor = $id_proveedor) AND
	(categoria.id_categoria = item.id_categoria) AND
	(unidad.id_unidad = categoria.id_unidad_visual)
	)";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $item = $item . "$row[0] - $row[1] - <input type=\"text\" size=\"5\" name=\"pcategoria\" value=\"$row[2]\" id=\"pcategoria\" class=\"obligatorio\"> ($row[3]) <br />\n";
}

$var = array("item" => $item,
	"rows" => $aux);
eval_html('producto_itemcomprar.html', $var);
