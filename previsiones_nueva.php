<?php

include_once 'main.php';
include_once 'dbutils.php';

session_start();

db_connect();

$mensaje = "";

// insertar una prevision nueva
$query = "INSERT INTO prevision () VALUES ()";
$result = mysql_query($query);

$query = "SELECT p.id_prevision
	FROM prevision p
  ORDER BY p.id_prevision desc
  LIMIT 1";

$result = mysql_query($query);
$row = mysql_fetch_array($result);

$id_prevision = $row[0];

$var = array(
  "id_prevision" => $id_prevision
);

eval_html("prevision_ver.php", $var);

?>
