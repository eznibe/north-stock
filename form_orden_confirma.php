<?php

include 'main.php';
include 'dbutils.php';

session_start();

$valid_user = $_SESSION['valid_user'];

$id_orden = $_POST['id_orden'];

$formname = $_POST['formname'];

// leer valores de cantidad por tipo arribo de los input de la tabla 	// NOTA: no se esta usando mas este mapa
$cant_filas = $_POST['rows'];
$table_map = array();
for ($i = 0; $i < $cant_filas; $i++) {
	$table_map[$i][0] = $_POST['id_orden_item'.$i];
	$table_map[$i][1] = $_POST['cantidad'.$i];
}

$fecha = date("Y-m-d");

$mensaje = "";
$focus = "forms[0].pais";

db_connect();
$pdo = get_db_connection();

$query = "UPDATE
	orden
  SET
	id_status = 1 ,
	fecha = now()
  WHERE (
	(id_orden = $id_orden)
  )";
$result = $pdo->query($query);

// logueo orden confirmada (4)
log_trans($valid_user, 4, 0, 0, $fecha, $id_orden);

$query = "SELECT
        id_item,
        cantidad
  FROM
        ordenitem
  WHERE
        id_orden = $id_orden";

$result = $pdo->query($query);
$items = array();
$cantidades = array();
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 array_push($items, array($row[0],$row[1]));
}

// update items cant en transito
foreach ($items as $item)
{
 $cantidad_factor = (get_factor_unidades($item[0])) * $item[1];

 // log para debug de items que quedan con transito negativo
 log_stock_transito_negativo($valid_user, $item[0], $id_orden, get_stock_transito($item[0]), (get_stock_transito($item[0]) + $cantidad_factor), $item[1], $item[1], 'auto-confirma-orden');

 $query = "UPDATE
        item
  SET
        item.stock_transito = item.stock_transito + $cantidad_factor
  WHERE (
        (item.id_item = $item[0])
  )";
 $result = $pdo->query($query);
}

$var = array(
  "id_orden" => $id_orden
 );
eval_html('orden_confirma_fin.html', $var);

?>
