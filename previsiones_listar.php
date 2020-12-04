<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "producto";

$query = "SELECT p.id_prevision, coalesce(p.numero_orden, p.id_prevision), coalesce(DATE_FORMAT(p.fecha_entrega, '%d-%m-%Y'), '-') AS fecha_entrega, SUM(CASE WHEN pi.id_item is not null THEN 1 ELSE 0 END) as items, count(*) 
		FROM prevision p LEFT JOIN previsionitem pi on p.id_prevision = pi.id_prevision
		WHERE p.fecha_descarga is null
  		GROUP BY p.id_prevision
  		ORDER BY p.fecha_entrega, p.numero_orden, p.id_prevision";

$previsiones = "";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result))
{
 $previsiones = $previsiones .  "<tr class=\"provlistrow\"> 
	<td><a class=\"list\" href=\"prevision_ver.php?id_prevision=$row[0]\">$row[1]</a></td>
	<td>$row[2]</td>
	<td>$row[3]</td>
    </tr>\n";
}

$var = array(
  "previsiones" => $previsiones, 
  "focus" => $focus);

eval_html('previsiones_listar.html', $var);

?>
