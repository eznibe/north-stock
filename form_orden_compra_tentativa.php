<?php

include 'main.php';
include 'dbutils.php';

session_start();

$valid_user = $_SESSION['valid_user'];

$formname = $_POST['formname'];

$id_proveedor = $_POST['id_proveedor'];

$mensaje = "";

$fecha = date("Y-m-d");

db_connect();

// Genera una unica orden de compra para el proveedor seleccionado
$proveedor = $id_proveedor;

// Update para proveedor
$query =  "UPDATE itemcomprar JOIN item
	ON itemcomprar.id_item = item.id_item
	SET itemcomprar.tentativo = false WHERE item.id_proveedor = $id_proveedor AND itemcomprar.tentativo = true";
$result = $pdo->query($query);

// logueo comprar confirmada (9)
log_trans($valid_user, 9, 0, 0, $fecha);

$var = "";
eval_html('orden_compra_tentativa_fin.html', $var);

?>

