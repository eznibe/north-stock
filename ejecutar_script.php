<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();


$query = "update item set stock_transito = 0 where id_item = 5129";
$result = mysql_query($query);

echo 'Ejecutado.';

?>
