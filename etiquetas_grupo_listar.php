<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$id_grupo = $_GET['id_grupo'];

$query = "SELECT DISTINCT 
	Item.id_categoria, 
	Categoria.categoria
  FROM 
	Item, Categoria 
  WHERE (
	(Item.id_categoria = Categoria.id_categoria) AND
	(Categoria.id_grupo = $id_grupo)
        ) 
  ORDER BY 
	Categoria.categoria";
$result = mysql_query($query);
$categorias = array();
while ($row = mysql_fetch_array($result))
{
 array_push($categorias, $row[0]);
}

foreach($categorias as $categoria)
{
 $query = "SELECT
	Item.id_item,
	Categoria.categoria,
	Proveedor.proveedor
  FROM
	Item,
	Categoria,
	Proveedor
  WHERE (
	(Item.id_categoria = $categoria) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor)
	)
  ORDER BY
	Proveedor.proveedor";
 $result = mysql_query($query);
 $row = mysql_fetch_array($result);
 $listado = $listado . "<tr><td colspan=\"2\" class=\"list2\">$row[1]</td></tr>\n<tr><td>&nbsp;</td><td><a href=\"form_impresion_etiquetas.php?id_item=$row[0]\" target=\"impresion_etiquetas\" onclick=\"open_print()\" class=\"list\">$row[2]</a></td></tr>\n";
 while($row = mysql_fetch_array($result))
 {
  $listado = $listado . "<tr><td>&nbsp;</td><td><a href=\"form_impresion_etiquetas.php?id_item=$row[0]\" target=\"impresion_etiquetas\" onclick=\"open_print()\" class=\"list\">$row[2]</a></td></tr>\n";
 }
}


$var = array("listado" => $listado);
eval_html('etiquetas_producto_listar.html', $var);
