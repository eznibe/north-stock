<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$id_item = $_GET['id_item'];

//$query = "SELECT codigo_barras FROM item WHERE id_item = $id_item";
$query = "SELECT 
	CONCAT('*',codigo_barras,'*'), 
	CONCAT('<br>',categoria,'<br>',proveedor,' (',codigo_proveedor,')') 
	FROM 
	item a LEFT JOIN categoria b ON a.id_categoria=b.id_categoria
	LEFT JOIN proveedor c ON a.id_proveedor=c.id_proveedor
	WHERE id_item = $id_item";
$result = mysql_query($query);


$row = mysql_fetch_array($result);

$codigo_barras = $row[0];
if (ereg("^\*.*\*$", $codigo_barras)) $tipo = "Code 39";
else $tipo = "EAN-13"; 

$descripcion = $row[1];

$var = array("codigo_barras" => $codigo_barras,
	"descripcion" => $descripcion,
	"tipo" => $tipo);
eval_html('print_barcode.html', $var);
