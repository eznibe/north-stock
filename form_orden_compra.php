<?php

include 'main.php';
include 'dbutils.php';

session_start();

$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].pais";

db_connect();

$query = "SELECT
	DISTINCT Item.id_proveedor
  FROM
	 Item,
	ItemComprar
  WHERE (
	(Item.id_item = ItemComprar.id_item)
  )";
$result = mysql_query($query);
$proveedores = array();
while ($row = mysql_fetch_array($result))
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
	Orden
	(fecha,
	cotizacion_dolar,
	id_status)
  VALUES
	(NULL, $cotiz_dolar, 0)";
 $result = mysql_query($query);
 $result = mysql_query("SELECT LAST_INSERT_ID()");
 $last_id = mysql_fetch_array($result);

//Inserto los items de la nueva orden segun el tipo de proveedor en dolares o pesos
if(obtener_tipo_proveedor($proveedor) == "EXTRANJERO")
{
 $query = "INSERT INTO
	OrdenItem

	(id_orden,
	id_item,
	cantidad,
	cantidad_pendiente,
	precio_fob,
	moneda)
  SELECT
	\"$last_id[0]\",
	ItemComprar.id_item,
	ItemComprar.cantidad,
	ItemComprar.cantidad,
	Item.precio_fob,
	'US$'
  FROM
	ItemComprar,
	Item
  WHERE (
	(Item.id_item = ItemComprar.id_item) AND
	(Item.id_proveedor = $proveedor)
  )";
}
else
{
 //Proveedor argentino
 //
 $query = "INSERT INTO
	OrdenItem

	(id_orden,
	id_item,
	cantidad,
	cantidad_pendiente,
	precio_ref,
	moneda)
  SELECT
	\"$last_id[0]\",
	ItemComprar.id_item,
	ItemComprar.cantidad,
	ItemComprar.cantidad,
	Item.precio_ref,
	'AR$'
  FROM
	ItemComprar,
	Item
  WHERE (
	(Item.id_item = ItemComprar.id_item) AND
	(Item.id_proveedor = $proveedor)
  )";
}

$result = mysql_query($query);


// Genero archivo para download de datos de compra
 $query_1 = "SELECT
	ItemComprar.id_item,
	Proveedor.proveedor,
	Item.codigo_proveedor,
	ItemComprar.cantidad,
	Unidad.unidad,
	DATE_FORMAT(NOW(), \"%d-%m-%Y\"),
	Categoria.categoria
  FROM
	ItemComprar,
	Proveedor,
	Item,
	Categoria,
	Unidad
  WHERE (
	(Item.id_item = ItemComprar.id_item) AND
	(Proveedor.id_proveedor = Item.id_proveedor) AND
	(Item.id_proveedor = $proveedor) AND
	(Item.id_categoria = Categoria.id_categoria) AND
	(Unidad.id_unidad = Item.id_unidad_compra)
  )";
 $result_1 = mysql_query($query_1);
 $row_1 = mysql_fetch_array($result_1);
 $pedido = $pedido . "\n\n\nPedido para $row_1[1] $row_1[5] Orden No $last_id[0]\n\n";

 if($row_1[2]=="")
 	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

 $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n"; 

 while ($row_1 = mysql_fetch_array($result_1))
 {
  if($row_1[2]=="")
  	$row_1[2] = obtener_descripcion_categoria($row_1[0]);

  $pedido = $pedido . str_pad($row_1[2],30) . str_pad($row_1[6],40) . str_pad($row_1[3],10) . "$row_1[4]\n";
 }

}

$query = "DELETE FROM ItemComprar";
$result = mysql_query($query);

$handle = fopen("pedido.txt", "w");
fwrite($handle,$pedido);
fclose($handle);

$var = "";
eval_html('orden_compra_fin.html', $var);

/**
 * Descripcion de la categoria a la cual pertenece el item pasado como parametro
 */
function obtener_descripcion_categoria($id_item)
{
	$query_2 = "SELECT categoria
  				FROM Categoria, Item
  				WHERE (Item.id_item=$id_item AND
  					   Item.id_categoria = Categoria.id_categoria)";

	$result_2 = mysql_query($query_2);
	$row_2 = mysql_fetch_array($result_2);
	//Devuelvo la descripcion de la categoria
	return $row_2[0];
}

function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar from DolarHoy where id_dolar=(SELECT max(id_dolar) FROM DolarHoy)";

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

