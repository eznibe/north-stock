<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$pdo = get_db_connection();

$id_orden_item = $_POST['id_orden_item'];
$cantidad = $_POST['cantidad'];
$precio_fob = $_POST['precio_fob'];

$formname = $_POST['formname'];

$mensaje = "";
$focus = "producto";

function update_orden($id_orden_item, $cantidad, $precio_fob)
{
 if ( ($cantidad == 0) or (cantidad == "") )
 {
  $query = "DELETE FROM ordenitem WHERE id_orden_item = $id_orden_item";
 }
 else
 {
  $query = "UPDATE
 	ordenitem
   SET
 	cantidad = $cantidad,
	precio_fob = $precio_fob
   WHERE
 	id_orden_item = $id_orden_item";
 }
 $result = db_query($query);
}


if ($formname == "orden_update") update_orden($id_orden_item, $cantidad, $precio_fob);
#####

$query = "SELECT DISTINCT
	DATE_FORMAT(orden.fecha, '%d-%m-%Y') AS fech,	
	orden.id_orden, 
	proveedor.proveedor,  
	orden.fecha
  FROM 
	orden, 
	ordenitem, 
	item, 
	proveedor 
  WHERE ( 
	(orden.id_status = 0) AND 
	(ordenitem.id_orden = orden.id_orden) AND 
	(item.id_item = ordenitem.id_item) AND 
	(proveedor.id_proveedor = item.id_proveedor) 
  ) 
  ORDER BY fecha, proveedor";

//dump($query);

$result = $pdo->query($query);
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $orden = $orden .  "<tr class=\"provlistrow\">
	<td>$row[0]</td> 
	<td class=\"centrado\">$row[1]</td> 
	<td><a class=\"list\" href=\"orden_ver.php?id_orden=$row[1]\">$row[2]</a></td>
    </tr>\n";
}

$var = array(
  "orden" => $orden, 
  "focus" => $focus);

eval_html('orden_confirma_listar.html', $var);

?>
