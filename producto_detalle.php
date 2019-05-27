<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$id_categoria = $_GET['id_categoria'];

$query = "SELECT 
	Categoria.categoria, 
	Categoria.stock_minimo, 
	SUM(Item.stock_disponible), 
	(SUM(Item.stock_disponible)-Categoria.stock_minimo), 
	Unidad.unidad,
	SUM(Item.stock_transito),
	(SUM(Item.stock_disponible)+SUM(Item.stock_transito)-Categoria.stock_minimo),
	Categoria.pos_arancelaria
  FROM 
	Item, Categoria, Unidad 
  WHERE (
	(Item.id_categoria = Categoria.id_categoria) AND 
	(Categoria.id_categoria = $id_categoria) AND 
	(Unidad.id_unidad = Categoria.id_unidad_visual)
  ) 
  GROUP BY 
	Item.id_categoria 
  ORDER BY 
	Categoria.categoria";
$result = mysql_query($query);


$row = mysql_fetch_array($result);

if ($row[3] < 0) $row[3] = "<em>$row[3]</em>";
if ($row[6] < 0) $row[6] = "<em>$row[6]</em>";

$unidad = "<em>" . strtoupper($row[4]) . "</em>";
$producto = htmlspecialchars(stripslashes($row[0]));
$header = "<tr class=\"provlistrow\"><td>$producto</td><td>$row[2]</td><td>$row[1]</td><td>$row[3]</td><td>$row[5]</td><td>$row[6]</td><td>$unidad</td><td>$row[7]</td></tr>\n";

$query = "SELECT 
	Proveedor.proveedor, 
	Item.codigo_proveedor,
	Item.stock_disponible, 
	Item.precio_fob, 
	Item.precio_nac, 
	Item.id_item,
	Item.stock_transito,
	Item.precio_ref,
	Item.oculto_fob,
	Item.oculto_nac 
  FROM 
	Item, 
	Proveedor  
  WHERE (
	(Item.id_categoria = $id_categoria) AND 
	(Proveedor.id_proveedor = Item.id_proveedor)
  ) 
  ORDER BY 
	Proveedor.proveedor";
$result = mysql_query($query);


$aux = "";
while ($row = mysql_fetch_array($result))
{
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
              <td>$row[1]</td><td>$row[2]</td><td>$row[6]</td><td>$row[3]</td><td>$row[4]</td><td>$row[7]</td><td>$row[8]</td><td>$row[9]</td></tr>\n";
 $aux2 = $aux2 . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
              <td>$row[1]</td><td>$row[2]</td><td>$row[6]</td><td>$row[3]</td><td>$row[4]</td><td>$row[7]</td></tr>\n";
}

if ($_SESSION['user_level'] < 100)
{
 $var = array("header" => $header,
	"rows" => $aux2);
 eval_html('producto_detalle_99.html', $var);
}
else
{
 $var = array("header" => $header,
	"rows" => $aux);
 eval_html('producto_detalle.html', $var);
}
