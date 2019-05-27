<?php

require_once('../main.php');
require_once('../dbutils.php');

session_start();

db_connect();


if(isset($_GET['guardarDespacho'])) {
	$value = guardarDespacho($_GET['id_orden'], $_GET['despacho']);
}
else if(isset($_GET['guardarNrFactura'])) {
	$value = guardarNrFactura($_GET['id_orden'], $_GET['nr_factura']);
}

//return JSON array
exit(json_encode($value));

function guardarDespacho($id_orden, $despacho) {

  $obj->success = true;

  $query = "UPDATE Orden SET despacho = '$despacho' WHERE id_orden = $id_orden";
	if(!mysql_query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarNrFactura($id_orden, $nr_factura) {

  $obj->success = true;

  $query = "UPDATE Orden SET nr_factura = '$nr_factura' WHERE id_orden = $id_orden";
	if(!mysql_query($query)) {
    $obj->success = false;
  }

  return $obj;
}

?>
