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
else if(isset($_GET['guardarFacturaAR'])) {
	$value = guardarFacturaAR($_GET['id_orden'], $_GET['factura_AR']);
}
else if(isset($_GET['guardarProveedorAR'])) {
	$value = guardarProveedorAR($_GET['id_orden'], $_GET['proveedor_AR']);
}

//return JSON array
exit(json_encode($value));

function guardarDespacho($id_orden, $despacho) {

  $obj->success = true;

  $query = "UPDATE orden SET despacho = '$despacho' WHERE id_orden = $id_orden";
	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarNrFactura($id_orden, $nr_factura) {

  $obj->success = true;

  $query = "UPDATE orden SET nr_factura = '$nr_factura' WHERE id_orden = $id_orden";
	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarFacturaAR($id_orden, $factura_AR) {

  $obj->success = true;

  $query = "UPDATE orden SET factura_AR = '$factura_AR' WHERE id_orden = $id_orden";
	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarProveedorAR($id_orden, $proveedor_AR) {

  $obj->success = true;

  $query = "UPDATE orden SET proveedor_AR = '$proveedor_AR' WHERE id_orden = $id_orden";
	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

?>
