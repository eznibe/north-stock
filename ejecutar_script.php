<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();


$query = "update orden set fecha = fecha_bkp";
$result = mysql_query($query);

echo 'Ejecutado.';

?>
