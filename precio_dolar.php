<?php
	include 'main.php';
	include 'dbutils.php';

	db_connect();

	$datos = obtener_precio_dolar();

	$var = array("precio_dolar" => $datos[0],
				 "fecha_mod" => $datos[1],
				 "mensaje" => "");

	eval_html('precio_dolar.html', $var);


function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar, fecha FROM dolarhoy WHERE id_dolar=(SELECT MAX(id_dolar) FROM dolarhoy)";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row;
}
?>
