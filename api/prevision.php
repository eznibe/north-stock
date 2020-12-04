<?php

require_once('../main.php');
require_once('../dbutils.php');

session_start();

db_connect();


if(isset($_GET['allItems'])) {
	$value = allItems();
}
else if(isset($_GET['guardarPrevision'])) {
	$value = guardarPrevision($_GET['id_prevision'], $_GET['numero_orden'], $_GET['cliente'], $_GET['fecha_entrega'], $_GET['descripcion']);
}

//return JSON array
exit(json_encode($value));

function allItems() {

  $obj->success = true;

  $query = "UPDATE Orden SET despacho = '$despacho' WHERE id_orden = $id_orden";
	if(!mysql_query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarPrevision($id_prevision, $numero_orden, $cliente, $fecha_entrega, $descripcion) {

  $obj = new stdClass();
  $obj->success = true;

  $numero_orden = isset($numero_orden) ? "'$numero_orden'" : 'null';
  $cliente = isset($cliente) ? "'$cliente'" : 'null';
  $descripcion = isset($descripcion) ? "'$descripcion'" : 'null';
  $fecha_entrega = isset($fecha_entrega) ? "'$fecha_entrega'" : 'null';

  $query = "UPDATE prevision 
    SET 
      numero_orden = $numero_orden,
      cliente = $cliente, 
      descripcion = $descripcion,
      fecha_entrega = $fecha_entrega
    WHERE id_prevision = $id_prevision";

  echo $query;

	if(!mysql_query($query)) {
    $obj->success = false;
  }

  return $obj;
}

?>
