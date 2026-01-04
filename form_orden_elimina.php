<?php

include 'main.php';
include 'dbutils.php';

session_start();

$valid_user = $_SESSION['valid_user'];

$id_orden = $_POST['id_orden'];

$formname = $_POST['formname'];

$valid_user = $_SESSION['valid_user'];
$fecha = date("Y-m-d");

$mensaje = "";
$focus = "forms[0].pais";

db_connect();
$pdo = get_db_connection();

// Verifico en status de la orden. Si posee stock_transito (id_status == 1) modificarlo
if (get_orden_status($id_orden) == 1)
{
 $query = "SELECT
        id_item,
        cantidad,
        cantidad_pendiente
  FROM
        ordenitem
  WHERE
        id_orden = $id_orden";

 $result = $pdo->query($query);
 $items = array();
 $cantidades = array();
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  array_push($items, array($row[0],$row[2]));
 }

 foreach ($items as $item)
 {
  $cantidad_factor = (get_factor_unidades($item[0])) * $item[1];
  
  // log para debug de items que quedan con transito negativo
  log_stock_transito_negativo($valid_user, $item[0], $id_orden, get_stock_transito($item[0]), (get_stock_transito($item[0])-$cantidad_factor), 0, 0, 'auto-elimina-orden');

  $query = "UPDATE item
	SET
	item.stock_transito = item.stock_transito - $cantidad_factor
	WHERE (
        (item.id_item = $item[0])
   )";
  $result = $pdo->query($query);
 }
}

// En todos los casos la paso a status 5
$query = "UPDATE
        orden
  SET
        id_status = 5
  WHERE (
        (id_orden = $id_orden)
  )";

$result = $pdo->query($query);

log_trans($valid_user, 5, 0, 0, $fecha, $id_orden);

$var = array(
  "id_orden" => $id_orden
 );
eval_html('orden_elimina_fin.html', $var);

?>

