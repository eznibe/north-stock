<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();


$query = "alter table categoria add column reservado int default 0";
$result = mysql_query($query);

echo 'Ejecutado.';

?>
