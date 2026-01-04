<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();
$pdo = get_db_connection();

$id_categoria = $_GET['id_categoria'];

$query = "SELECT 
	categoria.categoria, 
	categoria.stock_minimo, 
	SUM(item.stock_disponible), 
	(SUM(item.stock_disponible)-categoria.stock_minimo), 
	unidad.unidad,
	SUM(item.stock_transito),
	(SUM(item.stock_disponible)+SUM(item.stock_transito)-categoria.stock_minimo),
	categoria.pos_arancelaria
  FROM 
	item, categoria, unidad 
  WHERE (
	(item.id_categoria = categoria.id_categoria) AND 
	(categoria.id_categoria = $id_categoria) AND 
	(unidad.id_unidad = categoria.id_unidad_visual)
  ) 
  GROUP BY 
	item.id_categoria 
  ORDER BY 
	categoria.categoria";
$result = $pdo->query($query);


$row = $result->fetch(PDO::FETCH_NUM);

if ($row[3] < 0) $row[3] = "<em>$row[3]</em>";
if ($row[6] < 0) $row[6] = "<em>$row[6]</em>";

$unidad = "<em>" . strtoupper($row[4]) . "</em>";
$producto = htmlspecialchars(stripslashes($row[0]));
$header = "<tr class=\"provlistrow\"><td>$producto</td><td>$row[2]</td><td>$row[1]</td><td>$row[3]</td><td>$row[5]</td><td>$row[6]</td><td>$unidad</td><td>$row[7]</td></tr>\n";

$query = "SELECT 
	proveedor.proveedor, 
	item.codigo_proveedor,
	item.stock_disponible, 
	item.precio_fob, 
	item.precio_nac, 
	item.id_item,
	item.stock_transito,
	item.precio_ref,
	item.oculto_fob,
	item.oculto_nac 
  FROM 
	item, 
	proveedor  
  WHERE (
	(item.id_categoria = $id_categoria) AND 
	(proveedor.id_proveedor = item.id_proveedor)
  ) 
  ORDER BY 
	proveedor.proveedor";
$result = $pdo->query($query);


$aux = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
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
