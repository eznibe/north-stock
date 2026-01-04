<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$orden = "";
$focus = "producto";

$query = "SELECT P.id_proveedor, P.proveedor, COUNT(*)
		FROM itemcomprar IC, Proveedor P, item I
		WHERE I.id_item = IC.id_item
  		  AND I.id_proveedor = P.id_proveedor
			AND IC.tentativo = true
  		GROUP BY P.id_proveedor
  		ORDER BY P.proveedor";
 
$result = $pdo->query($query);
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $orden = $orden .  "<tr class=\"provlistrow\"> 
	<td><a class=\"list\" href=\"orden_compra_tentativa.php?id_proveedor=$row[0]\">$row[1]</a></td>
	<td>$row[2]</td>
    </tr>\n";
}

$var = array(
  "orden" => $orden, 
  "focus" => $focus);

eval_html('orden_compra_tentativa_listar.html', $var);

?>
