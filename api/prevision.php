<?php

require_once('../main.php');
require_once('../dbutils.php');

session_start();

db_connect();


if(isset($_GET['allitems'])) {
	$value = allitems();
}
else if(isset($_GET['guardarPrevision'])) {
	$value = guardarPrevision($_GET['id_prevision'], isset($_GET['numero_orden']) ? $_GET['numero_orden'] : null, $_GET['cliente'], isset($_GET['fecha_entrega']) ? $_GET['fecha_entrega'] : null, isset($_GET['descripcion']) ? $_GET['descripcion'] : null);
}

//return JSON array
exit(json_encode($value));

function allitems() {

  $obj->success = true;

  $query = "UPDATE orden SET despacho = '$despacho' WHERE id_orden = $id_orden";
	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

function guardarPrevision($id_prevision, $numero_orden, $cliente, $fecha_entrega = null, $descripcion = null) {

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

  //echo $query;

	if(!$pdo->query($query)) {
    $obj->success = false;
  }

  return $obj;
}

?>
