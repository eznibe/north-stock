<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_proveedor = $_GET['id_proveedor'];

$query = "SELECT
	Categoria.categoria, Proveedor.proveedor, ItemComprar.cantidad, Unidad.unidad
	FROM
	Categoria, Proveedor, ItemComprar, Item, Unidad
	WHERE (
	(ItemComprar.id_item = Item.id_item) AND
	(Item.id_proveedor = Proveedor.id_proveedor) AND
	(Item.id_proveedor = $id_proveedor) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Unidad.id_unidad = Categoria.id_unidad_visual)
	)";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $item = $item . "$row[0] - $row[1] - <input type=\"text\" size=\"5\" name=\"pcategoria\" value=\"$row[2]\" id=\"pcategoria\" class=\"obligatorio\"> ($row[3]) <br />\n";
}

$var = array("item" => $item,
	"rows" => $aux);
eval_html('producto_ItemComprar.html', $var);
