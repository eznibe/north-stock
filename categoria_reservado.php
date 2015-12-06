<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_categoria = $_GET['id_categoria'];

$query = "SELECT
	Categoria.id_categoria,
	Categoria.categoria,
	Categoria.reservado
  FROM
	Categoria
  WHERE
	Categoria.id_categoria = $id_categoria";

$result = mysql_query($query);

$row = mysql_fetch_array($result);
$id_categoria = $row[0];
$categoria = $row[1];
$reservado = $row[2];
$focus = 'forms[0].reservado';

$var = array('focus' => $focus,
        'categoria' => $categoria,
        'id_categoria' => $id_categoria,
				'reservado' => $reservado);

eval_html('categoria_reservado.html', $var);

?>
