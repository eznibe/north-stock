<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$id_grupo = $_GET['id_grupo'];

$query = "SELECT DISTINCT 
	item.id_categoria, 
	categoria.categoria
  FROM 
	item, categoria 
  WHERE (
	(item.id_categoria = categoria.id_categoria) AND
	(categoria.id_grupo = $id_grupo)
        ) 
  ORDER BY 
	categoria.categoria";
$result = mysql_query($query);
$categorias = array();
while ($row = mysql_fetch_array($result))
{
 array_push($categorias, $row[0]);
}

foreach($categorias as $categoria)
{
 $query = "SELECT
	item.id_item,
	categoria.categoria,
	proveedor.proveedor
  FROM
	item,
	categoria,
	proveedor
  WHERE (
	(item.id_categoria = $categoria) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor)
	)
  ORDER BY
	proveedor.proveedor";
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
