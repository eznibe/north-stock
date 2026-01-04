<?php

include 'main.php';
include 'dbutils.php';

session_start();

$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].pais";

db_connect();

$query = "SELECT
	DISTINCT item.id_proveedor
  FROM
	 item,
	itemcomprar
  WHERE (
	(item.id_item = itemcomprar.id_item)
  )";
$result = $pdo->query($query);
$proveedores = array();
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 array_push($proveedores, $row[0]);
}


foreach ($proveedores as $proveedor)
{
}

$cotiz_dolar = obtener_precio_dolar();

foreach ($proveedores as $proveedor)
{
// Inserto nueva orden para proveedor
 $query = "INSERT INTO
	orden
	(fecha,
	cotizacion_dolar,
	id_status)
  VALUES
	(NULL, $cotiz_dolar, 0)";
 $result = $pdo->query($query);
 $result = $pdo->query("SELECT LAST_INSERT_ID()");
 $last_id = $result->fetch(PDO::FETCH_NUM);

//Inserto los items de la nueva orden segun el tipo de proveedor en dolares o pesos
if(obtener_tipo_proveedor($proveedor) == "EXTRANJERO")
{
 $query = "INSERT INTO
	ordenitem

	(id_orden,
	id_item,
	cantidad,
	cantidad_pendiente,
	precio_fob,
	moneda)
  SELECT
	\"$last_id[0]\",
	itemcomprar.id_item,
	itemcomprar.cantidad,
	itemcomprar.cantidad,
	item.precio_fob,
	'US$'
  FROM
	itemcomprar,
	item
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(item.id_proveedor = $proveedor)
  )";
}
else
{
 //proveedor argentino
 //
 $query = "INSERT INTO
	ordenitem

	(id_orden,
	id_item,
	cantidad,
	cantidad_pendiente,
	precio_ref,
	moneda)
  SELECT
	\"$last_id[0]\",
	itemcomprar.id_item,
	itemcomprar.cantidad,
	itemcomprar.cantidad,
	item.precio_ref,
	'AR$'
  FROM
	itemcomprar,
	item
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(item.id_proveedor = $proveedor)
  )";
}

$result = $pdo->query($query);


// Genero archivo para download de datos de compra
 $query_1 = "SELECT
	itemcomprar.id_item,
	proveedor.proveedor,
	item.codigo_proveedor,
	itemcomprar.cantidad,
	unidad.unidad,
	DATE_FORMAT(NOW(), \"%d-%m-%Y\"),
	categoria.categoria
  FROM
	itemcomprar,
	proveedor,
	item,
	categoria,
	unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(item.id_proveedor = $proveedor) AND
	(item.id_categoria = categoria.id_categoria) AND
	(unidad.id_unidad = item.id_unidad_compra)
  )";
 $result_1 = $pdo->query($query_1);
 $row_1 = fetch(PDO::FETCH_NUM)($result_1);
 $pedido = $pedido . "\n\n\nPedido para $row_1[1] $row_1[5] orden No $last_id[0]\n\n";

 if($row_1[2]=="")
 	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

 $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n"; 

 while ($row_1 = fetch(PDO::FETCH_NUM)($result_1))
 {
  if($row_1[2]=="")
  	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

  $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n";
 }

}

$query = "DELETE FROM itemcomprar";
$result = $pdo->query($query);

$handle = fopen("pedido.txt", "w");
fwrite($handle,$pedido);
fclose($handle);

$var = "";
eval_html('orden_compra_fin.html', $var);

/**
 * Descripcion de la categoria a la cual pertenece el item pasado como parametro
 */
function obtener_descripcion_categoria($id_item)
{	global $pdo;	$query_2 = "SELECT categoria
  				FROM categoria, item
  				WHERE (item.id_item=$id_item AND
  					   item.id_categoria = categoria.id_categoria)";

	$result_2 = $pdo->query($query_2);
	$row_2 = fetch(PDO::FETCH_NUM)($result_2);
	//Devuelvo la descripcion de la categoria
	return $row_2[0];
}

function obtener_precio_dolar()
{
	global $pdo;
	$query = "SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy)";

	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	//Devuelvo el precio del dolar actual
	return $row[0];
}

/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado,
 * a partir del id_proveedor pasado como parametro
 */
function obtener_tipo_proveedor($id_proveedor){
	global $pdo;
	$query = "SELECT pais FROM pais, Proveedor
		  WHERE pais.id_pais = Proveedor.id_pais and
				proveedor.id_proveedor = $id_proveedor";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

?>

