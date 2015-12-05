<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "producto";

$query = "SELECT P.id_proveedor, P.proveedor, COUNT(*)
		FROM ItemComprar IC, Proveedor P, Item I
		WHERE I.id_item = IC.id_item
  		  AND I.id_proveedor = P.id_proveedor
  		GROUP BY P.id_proveedor
  		ORDER BY P.proveedor";
 
$result = mysql_query($query);
while ($row = mysql_fetch_array($result))
{
 $orden = $orden .  "<tr class=\"provlistrow\"> 
	<td><a class=\"list\" href=\"orden_compra_proveedor.php?id_proveedor=$row[0]\">$row[1]</a></td>
	<td>$row[2]</td>
    </tr>\n";
}

$var = array(
  "orden" => $orden, 
  "focus" => $focus);

eval_html('orden_compra_listar.html', $var);

?>
