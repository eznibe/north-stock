<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_item = $_POST['id_item'];
$cantidad = $_POST['cantidad'];
$formname = $_POST['formname'];
$id_orden = $_POST['id_orden'];
$tipoenvio = $_POST['tipoenvio'];
$id_proveedor = $_POST['id_proveedor'];

if(!isset($tipoenvio)) {
 $tipoenvio = "NULL";
}

if(isset($id_orden) && !empty($id_orden)) {
	// se inserta el item directo en la orden ya existente seleccionada
	if(es_proveedor_nacional($id_proveedor, 'ARGENTINA'))
	{
	  $query = "INSERT INTO ordenitem (id_orden, id_item, cantidad,	cantidad_pendiente, precio_ref,	moneda, id_tipo_envio)
		    SELECT $id_orden, $id_item, $cantidad, $cantidad, item.precio_ref, 'AR$', $tipoenvio  FROM  item  WHERE item.id_item = $id_item";
	}
	else
	{
	  //proveedor extranjero
	  $query = "INSERT INTO ordenitem (id_orden, id_item, cantidad, cantidad_pendiente, precio_fob, moneda, id_tipo_envio)
		    SELECT $id_orden, $id_item, $cantidad, $cantidad, item.precio_fob, 'US$', $tipoenvio  FROM  item  WHERE item.id_item = $id_item";
	}
}
else {
	// se inserta el item en la lista de items a comprar
	$query = "INSERT INTO itemcomprar (id_item, cantidad, id_tipo_envio, cantidad_pendiente) VALUES ($id_item, $cantidad, $tipoenvio, $cantidad)";
}

$result = mysql_query($query);

$var = "";
eval_html('window_close.html', $var);


/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado,
 * a partir del id_proveedor pasado como parametro
 */
function obtener_tipo_proveedor($id_proveedor){
	$query = "SELECT pais FROM pais, proveedor
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = $id_proveedor";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}
?>
