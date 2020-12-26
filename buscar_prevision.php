<?php

include_once 'main.php';
include_once 'dbutils.php';

session_start();

db_connect();

$mensaje = "";

$numero_orden = $_POST['numero_orden'];

$query = "SELECT p.id_prevision
	FROM prevision p
  WHERE 
    p.numero_orden = '$numero_orden'
    OR
    p.id_prevision = '$numero_orden'
  LIMIT 1";

// dump($query);

$result = mysql_query($query);
$row = mysql_fetch_array($result);

$var = array(
);

if (isset($row[0])) {
  $id_prevision = $row[0];
  eval_html("prevision_ver.php", $var);
}
else {
  $var = array(
    "mensaje" => "Previsión no encontrada."
  );
  eval_html("prevision_accion_fin.html", $var);
}


?>
