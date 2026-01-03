<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_categoria = $_GET['id_categoria'];

$query = "SELECT
	categoria.id_categoria,
	categoria.categoria,
	categoria.reservado
  FROM
	categoria
  WHERE
	categoria.id_categoria = $id_categoria";

$result = $pdo->query($query);

$row = $result->fetch(PDO::FETCH_NUM);
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
