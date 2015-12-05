<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

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
  $query = "DELETE FROM OrdenItem WHERE id_orden_item = $id_orden_item";
 }
 else
 {
  $query = "UPDATE
 	OrdenItem
   SET
 	cantidad = $cantidad,
	precio_fob = $precio_fob
   WHERE
 	id_orden_item = $id_orden_item";
 }
 $result = mysql_query($query);
}


if ($formname == "orden_update") update_orden($id_orden_item, $cantidad, $precio_fob);
#####

$query = "SELECT DISTINCT
	DATE_FORMAT(Orden.fecha, '%d-%m-%Y') AS fech,	
	Orden.id_orden, 
	Proveedor.proveedor,  
	Orden.fecha
  FROM 
	Orden, 
	OrdenItem, 
	Item, 
	Proveedor 
  WHERE ( 
	(Orden.id_status = 0) AND 
	(OrdenItem.id_orden = Orden.id_orden) AND 
	(Item.id_item = OrdenItem.id_item) AND 
	(Proveedor.id_proveedor = Item.id_proveedor) 
  ) 
  ORDER BY fecha, proveedor";

//dump($query);

$result = mysql_query($query);
while ($row = mysql_fetch_array($result))
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
