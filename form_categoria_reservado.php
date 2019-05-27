<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_categoria = $_POST['id_categoria'];
$reservado = $_POST['reservado'];

$query = "UPDATE Categoria SET reservado = $reservado WHERE id_categoria = $id_categoria";

$result = mysql_query($query);

$var = "";
eval_html('cerrar_popup.html', $var);

?>
