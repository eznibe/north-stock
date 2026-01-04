<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$pdo = get_db_connection();

$id_categoria = $_POST['id_categoria'];
$reservado = $_POST['reservado'];

$query = "UPDATE categoria SET reservado = $reservado WHERE id_categoria = $id_categoria";

$result = $pdo->query($query);

$var = "";
eval_html('cerrar_popup.html', $var);

?>
