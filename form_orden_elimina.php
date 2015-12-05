<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_orden = $_POST['id_orden'];

$formname = $_POST['formname'];

$valid_user = $_SESSION['valid_user'];
$fecha = date("Y-m-d");

$mensaje = "";
$focus = "forms[0].pais";

db_connect();

// Verifico en status de la orden. Si posee stock_transito (id_status == 1) modificarlo
if (get_orden_status($id_orden) == 1)
{
 $query = "SELECT
        id_item,
        cantidad,
        cantidad_pendiente
  FROM
        OrdenItem
  WHERE
        id_orden = $id_orden";

 $result = mysql_query($query);
 $items = array();
 $cantidades = array();
 while ($row = mysql_fetch_array($result))
 {
  array_push($items, array($row[0],$row[2]));
 }

 foreach ($items as $item)
 {
  $cantidad_factor = (get_factor_unidades($item[0])) * $item[1];
  $query = "UPDATE Item
	SET
	Item.stock_transito = Item.stock_transito - $cantidad_factor
	WHERE (
        (Item.id_item = $item[0])
   )";
  $result = mysql_query($query);
 }
}

// En todos los casos la paso a status 5
$query = "UPDATE
        Orden
  SET
        id_status = 5
  WHERE (
        (id_orden = $id_orden)
  )";

$result = mysql_query($query);

log_trans($valid_user, 5, 0, 0, $fecha, $id_orden);

$var = array(
  "id_orden" => $id_orden
 );
eval_html('orden_elimina_fin.html', $var);

?>

