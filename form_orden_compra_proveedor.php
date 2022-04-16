<?php

include 'main.php';
include 'dbutils.php';

session_start();

$valid_user = $_SESSION['valid_user'];

$formname = $_POST['formname'];

$id_proveedor = $_POST['id_proveedor'];

// leer valores de cantidad arriba de los input de la tabla
$cant_filas = $_POST['rows'];
$table_map = array();
for ($i = 0; $i < $cant_filas; $i++) {

	$table_map[$i][0] = $_POST['id_item'.$i]; // id del item real del la orden
	$table_map[$i][1] = $_POST['cant_arribada'.$i];
	$table_map[$i][2] = $_POST['precio_item'.$i]; // id del item real del la orden
	$table_map[$i][3] = $_POST['tipoenvio'.$i]; // id del item real del la orden
	$table_map[$i][4] = $_POST['id_itemcomprar'.$i]; // id del item real del la orden
	$table_map[$i][5] = $_POST['cant_pendiente'.$i]; // id del item real del la orden
}
// var_dump($table_map);

$mensaje = "";
$focus = "forms[0].pais";

$fecha = date("Y-m-d");

db_connect();

$cotiz_dolar = obtener_precio_dolar();

// Genera una unica orden de compra para el proveedor seleccionado
$proveedor = $id_proveedor;

// Inserto nueva orden para proveedor
 $query =  "INSERT INTO
			orden
				(fecha,
				cotizacion_dolar,
				id_status)
		  	VALUES
				(NULL, $cotiz_dolar, 0)";
 $result = mysql_query($query);
 $result = mysql_query("SELECT LAST_INSERT_ID()");
 $last_id = mysql_fetch_array($result);

// logueo orden creada (7)
log_trans($valid_user, 7, 0, 0, $fecha, $last_id[0]);

//Inserto los items de la nueva orden segun el tipo de proveedor en dolares o pesos
if(obtener_tipo_proveedor($proveedor) == "EXTRANJERO")
{

	foreach ($table_map as $item)
	{
	 $query = "INSERT INTO ordenitem
		(id_orden,
		id_item,
		cantidad,
		cantidad_pendiente,
		precio_fob,
		moneda,
		id_tipo_envio)
	 VALUES
		(
			\"$last_id[0]\",
			$item[0],
			$item[1],
			$item[1],
			$item[2],
			'US$',
			$item[3]
		)";
	 
	if ($item[1] != "0") {
		$result = mysql_query($query);
	}
 }
}
else
{
 //proveedor argentino
 //
 foreach ($table_map as $item) {
	$query = "INSERT INTO ordenitem
		(id_orden,
		id_item,
		cantidad,
		cantidad_pendiente,
		precio_ref,
		moneda,
		id_tipo_envio)
	 VALUES
		(
			\"$last_id[0]\",
			$item[0],
			$item[1],
			$item[1],
			$item[2],
			'AR$',
			$item[3]
		)";
		// var_dump($query);

		if ($item[1] != "0") {
			$result = mysql_query($query);
		}
 }
}

$pedido = "";

// Genero archivo para download de datos de compra
 $query_1 = "SELECT
	ordenitem.id_item,
	proveedor.proveedor,
	item.codigo_proveedor,
	ordenitem.cantidad,
	unidad.unidad,
	DATE_FORMAT(NOW(), \"%d-%m-%Y\"),
	categoria.categoria
  FROM
  ordenitem,
	proveedor,
	item,
	categoria,
	unidad
  WHERE (
	(item.id_item = ordenitem.id_item) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(item.id_categoria = categoria.id_categoria) AND
	(unidad.id_unidad = item.id_unidad_compra)
	AND ordenitem.id_orden = $last_id[0]
  )";
 $result_1 = mysql_query($query_1);
 $row_1 = mysql_fetch_array($result_1);
 $pedido = $pedido . "\n\n\nPedido para $row_1[1] $row_1[5] orden No $last_id[0]\n\n";

 if($row_1[2]=="")
 	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

 $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n"; 

 while ($row_1 = mysql_fetch_array($result_1))
 {
  if($row_1[2]=="")
  	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

  $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n";
 }

//Borrar/Actualizar items a comprar del proveedor
foreach ($table_map as $item) {
	
	$query = "";
	if ($item[5] == $item[1]) {
		$query = "DELETE FROM itemcomprar
	  		WHERE itemcomprar.id_itemcomprar = $item[4]";
	} else {
		$query = "UPDATE itemcomprar
			SET cantidad_pendiente = cantidad_pendiente - $item[1]
			WHERE itemcomprar.id_itemcomprar = $item[4]";
	}
	
	// var_dump($query);

	$result = mysql_query($query);
 }

$handle = fopen("pedido.txt", "w");
fwrite($handle,$pedido);
fclose($handle);

$var = "";
eval_html('orden_compra_proveedor_fin.html', $var);

/**
 * Descripcion de la categoria a la cual pertenece el item pasado como parametro
 */
function obtener_descripcion_categoria($id_item)
{
	$query_2 = "SELECT categoria
  				FROM categoria, item
  				WHERE (item.id_item=$id_item AND
  					   item.id_categoria = categoria.id_categoria)";

	$result_2 = mysql_query($query_2);
	$row_2 = mysql_fetch_array($result_2);
	//Devuelvo la descripcion de la categoria
	return $row_2[0];
}

function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy)";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	//Devuelvo el precio del dolar actual
	return $row[0];
}

/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado,
 * a partir del id_proveedor pasado como parametro
 */
function obtener_tipo_proveedor($id_proveedor){
	$query = "SELECT pais FROM pais, proveedor
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = $id_proveedor";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

?>

