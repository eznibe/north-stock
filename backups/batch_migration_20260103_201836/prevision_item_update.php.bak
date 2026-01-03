<?php

include 'main.php';
include 'dbutils.php';

check_session();
$username = $_SESSION['valid_user'];

db_connect();

$id_prevision_item = $_GET['id_prevision_item'];

//Datos del prevision item
//
$query = "SELECT
		pi.id_prevision_item,
		pi.id_prevision,
		pi.cantidad,
		pro.proveedor,
		c.categoria,
		CONCAT(u.unidad,'(',i.factor_unidades,')'),
		i.codigo_proveedor,
		i.stock_disponible,
		i.stock_transito,
		pi.descargado
	FROM previsionitem pi
		JOIN item i on i.id_item = pi.id_item
		JOIN categoria c on c.id_categoria = i.id_categoria
		JOIN unidad u on u.id_unidad = i.id_unidad_compra
		JOIN proveedor pro on pro.id_proveedor = i.id_proveedor
  WHERE 
		pi.id_prevision_item = $id_prevision_item
	";

$result = mysql_query($query);
$row = mysql_fetch_array($result);

$focus = "forms[0].cantidad";

$cantidad = $row[2];
$proveedor = $row[3];
$categoria = $row[4];
$stock_disponible = $row[7];
$stock_transito = $row[8];
$item_descargado = $row[9];

$unidad = "<em>" . strtoupper($row[5]) . "</em>";
$id_prevision_item = $row[0];
$id_prevision = $row[1];

$var = array("focus" => $focus,
        "categoria" => $categoria,
        "proveedor" => $proveedor,
        "cantidad" => $cantidad,
		"unidad" => $unidad,
		"stock_disponible" => $stock_disponible,
		"stock_transito" => $stock_transito,
		"id_prevision_item" => $id_prevision_item,
		"username" => $username,
		"submitto" => "prevision_ver.php",
		"item_descargado" => $item_descargado,
        "id_prevision" => $id_prevision);


eval_html('prevision_item_update.html', $var);

?>
