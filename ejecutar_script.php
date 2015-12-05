<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();


$query = "update log set fecha = '2015-01-05' where id_log = 174283";
$result = mysql_query($query);

$query = "update log set fecha = '2015-01-05' where id_log = 174286";
$result = mysql_query($query);

$query = "update log set fecha = '2015-01-05' where id_log = 174471";
$result = mysql_query($query);

$query = "update log set fecha = '2015-01-21' where id_log = 175667";
$result = mysql_query($query);

$query = "update log set fecha = '2015-03-31' where id_log = 178191";
$result = mysql_query($query);

$query = "update log set fecha = '2015-03-31' where id_log = 178192";
$result = mysql_query($query);

echo 'Ejecutado.';

?>
