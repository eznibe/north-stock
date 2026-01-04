<?php
// para ejecutar scripts

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$pdo = get_db_connection();


$query = "update orden set fecha = fecha_bkp";
$result = $pdo->query($query);

echo 'Ejecutado.';

?>
