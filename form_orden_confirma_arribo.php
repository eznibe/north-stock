<?php

include 'main.php';
include 'dbutils.php';

include 'include/table-extractor.php';

check_session();

$valid_user = $_SESSION['valid_user'];

// leer valores de cantidad arriba de los input de la tabla
$cant_filas = $_POST['rows'];
$table_map = array();
for ($i = 0; $i < $cant_filas; $i++) {

	$table_map[$i][0] = $_POST['orden_item'.$i];
	$table_map[$i][1] = $_POST['cant_arribada'.$i];
	$table_map[$i][2] = $_POST['item'.$i]; // id del item real del la orden
}

//var_dump($table_map);

$id_orden = $_POST['id_orden'];
$dia = $_POST['dia'];
$mes = $_POST['mes'];
$ano = $_POST['ano'];

$fecha = "$ano-$mes-$dia";

$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].pais";

db_connect();


// update stock disponible de cada item de la orden
foreach ($table_map as $item)
{
 $cantidad_factor = (get_factor_unidades($item[2])) * $item[1];
 $query = "UPDATE
	item
  SET
	item.stock_disponible = item.stock_disponible + $cantidad_factor
  WHERE (
	(item.id_item = $item[2])
  )";
 $result = $pdo->query($query);

 if($item[1]!=0) // log cantidad arribada distinta a cero
 	log_trans($valid_user, 1, $item[2], $item[1], $fecha, $id_orden);
}

// update cantidad en transito del item y la cantidad pendiente del item de la orden
foreach ($table_map as $item)
{
 // update item cant en transito
 $cantidad_factor = (get_factor_unidades($item[2])) * $item[1];

 // log para debug de items que quedan con transito negativo
 log_stock_transito_negativo($valid_user, $item[2], $id_orden, get_stock_transito($item[2]), (get_stock_transito($item[2])-$cantidad_factor), get_cantidad_pendiente_comprar($item[0]), $item[1], 'auto');

 $query = "UPDATE
        item
  SET
        item.stock_transito = item.stock_transito - $cantidad_factor
  WHERE (
        (item.id_item = $item[2])
  )";
 $result = $pdo->query($query);

 // update item de la orden (cantidad pendiente)
 $query = "UPDATE
        ordenitem
  SET
        ordenitem.cantidad_pendiente = ordenitem.cantidad_pendiente - $item[1]
  WHERE (
        (ordenitem.id_orden_item = $item[0])
  )";
 $result = $pdo->query($query);
}


//Cambio de estado la orden a arribada (2) si todos los items de la orden estan en 0 (pendiente)
$items_pendientes = obtener_cantidad_items_pendientes($id_orden);
if($items_pendientes == 0){
	update_orden_arribada($id_orden, $fecha, $valid_user);
	$mensaje = "La orden numero $id_orden se ha registrado como arribada. Los items de esta orden se encuentran disponibles.";
}
else
	$mensaje = "La orden numero $id_orden se ha actualizado. Los items arribados se encuentran disponibles.";


$var = array(
  "id_orden" => $id_orden,
  "mensaje" => $mensaje,
  "orden_table" => $table_map
 );
eval_html('orden_arribo_fin.html', $var);


/**
 * Cambiar el estado de la orden a arribada (2)
 */
function update_orden_arribada($id_orden, $fecha, $valid_user)
{
	global $pdo;
	$query = "UPDATE orden  SET  id_status = 2, fecha = '$fecha'
  			  WHERE (id_orden = $id_orden)";

	$result = $pdo->query($query);

	log_trans($valid_user, 6, 0, 0, $fecha, $id_orden);
}

/**
 * Obtine la cantidad de items pendientes de una orden dada
 */
function obtener_cantidad_items_pendientes($id_orden)
{
	global $pdo;
	$query = "SELECT count(*) FROM orden, ordenitem ordenitem
			  WHERE orden.id_orden = $id_orden
				AND orden.id_orden = ordenitem.id_orden
				AND ordenitem.cantidad_pendiente <> 0";

	$result = $pdo->query($query);

	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

// retorna lo que qeuda por comprar del item en la orden
function get_cantidad_pendiente_comprar($id_orden_item)
{
 global $pdo;
 $query = "SELECT ordenitem.cantidad_pendiente
        FROM ordenitem
        WHERE ordenitem.id_orden_item = $id_orden_item";
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}
?>
