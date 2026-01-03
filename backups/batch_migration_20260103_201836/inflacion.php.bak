<?php

require_once('../main.php');
require_once('../dbutils.php');

session_start();

db_connect();


if(isset($_GET['obtenerInflacion'])) {
	$value = obtenerInflacion($_GET['anio'], $_GET['mes']);
}
else if(isset($_GET['actualizarInflacion'])) {
	$value = actualizarInflacion($_GET['anio'], $_GET['mes'], $_GET['valor'], $_GET['isNew']);
}

//return JSON array
exit(json_encode($value));

function obtenerInflacion($anio, $mes) {

  $obj->success = true;

  $query = "SELECT valor FROM inflacion WHERE anio = $anio and mes = $mes";
  $result = mysql_query($query);
	if(!$result) {
    $obj->success = false;
  } else {
    if (mysql_num_rows($result) > 0) {
      $row = mysql_fetch_array($result);
      $obj->valor = $row[0];
    }
  }

  return $obj;
}

function actualizarInflacion($anio, $mes, $valor, $isNew) {

  $obj->success = true;

  if (isset($isNew)) {

    $query = "INSERT INTO inflacion (anio, mes, valor) values ($anio, $mes, $valor)";
    if(!mysql_query($query)) {
      $obj->success = false;
    }
  } else {

    $query = "UPDATE inflacion SET valor = $valor WHERE anio = $anio AND mes = $mes";
    if(!mysql_query($query)) {
      $obj->success = false;
    }
  }

  $obj->query = $query;

  return $obj;
}

?>
