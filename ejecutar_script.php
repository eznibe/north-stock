<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();


$query = "UPDATE Item SET precio_ref = precio_nac * 15 WHERE precio_nac IS NOT NULL AND precio_nac <> 0";
$result = mysql_query($query);

echo 'Ejecutado.';

?>
