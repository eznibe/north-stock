<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

if ( isset($_GET['id_orden']) ) $id_orden = $_GET['id_orden'];
else $id_orden = $_POST['id_orden'];

$formname = isset($_POST['formname']) ? $_POST['formname'] : "";

$mensaje = "";
$focus = "producto";


if ($formname == "orden_update") {
	$id_orden_item = $_POST['id_orden_item'];
	$cantidad = $_POST['cantidad'];
	$precio = $_POST['precio'];

	update_orden($id_orden_item, $cantidad, $precio);
}

if(obtener_tipo_proveedor($id_orden) == "EXTRANJERO")
{
	//proveedor extranjero
	//
	$query = "SELECT
	DATE_FORMAT(orden.fecha, '%d-%m-%Y') AS fech,
	proveedor.proveedor,
	ordenitem.id_orden_item,
	categoria.categoria,
	ordenitem.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	ordenitem.precio_fob,
	(ordenitem.cantidad * ordenitem.precio_fob),
	item.codigo_proveedor,
	orden.descripcion,
	tipoenvio.tipo_envio,
	orden.despacho
  FROM
      orden, categoria, proveedor, ordenitem, item, unidad, tipoenvio
  WHERE (
	(orden.id_orden = $id_orden) AND
	(ordenitem.id_orden = orden.id_orden) AND
	(item.id_item = ordenitem.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(ordenitem.id_tipo_envio = tipoenvio.id_tipo_envio)
  )
  ORDER BY
	categoria.categoria, tipoenvio.id_tipo_envio";

$count=0; $orden="";
$result = $pdo->query($query);

while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $header = "$row[1] $row[0] / $id_orden";
 $orden = $orden . "<tr class=\"provlistrow\">
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_orden($row[2]);\">$row[3]</a></td>
	<td class=\"centrado\">$row[8]</td>
	<td class=\"centrado\">$row[4] <input type='hidden' name='cantidad$count' id='cantidad$count' value='$row[4]'/> <input type='hidden' name='id_orden_item$count' id='id_orden_item$count' value='$row[2]'/></td>
	<td class=\"centrado\">$row[10]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\">$row[7]</td>
	<td class=\"centrado\">US$</td>
   </tr>\n";

 $count++;
}

//Calculo total en dolares
$query = "SELECT
        sum((ordenitem.cantidad * ordenitem.precio_fob))
  FROM
      ordenitem
  WHERE (
        (ordenitem.id_orden = $id_orden)
  )";
$result = $pdo->query($query);
$row = $result->fetch(PDO::FETCH_NUM);

$total = $row[0];
$total_dolar = $total;
$total_pesos = $total * obtener_precio_dolar_orden($id_orden);
}

else
{
	//proveedor argentino
	//
	$query = "SELECT
	DATE_FORMAT(orden.fecha, '%d-%m-%Y') AS fech,
	proveedor.proveedor,
	ordenitem.id_orden_item,
	categoria.categoria,
	ordenitem.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	ordenitem.precio_ref,
	(ordenitem.cantidad * ordenitem.precio_ref),
	item.codigo_proveedor,
	orden.descripcion,
	tipoenvio.tipo_envio,
	orden.despacho
  FROM
      orden, categoria, proveedor, ordenitem, item, unidad, tipoenvio
  WHERE (
	(orden.id_orden = $id_orden) AND
	(ordenitem.id_orden = orden.id_orden) AND
	(item.id_item = ordenitem.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(ordenitem.id_tipo_envio = tipoenvio.id_tipo_envio)
  )
  ORDER BY
	categoria.categoria, tipoenvio.id_tipo_envio";

$count=0;
$result = $pdo->query($query);

while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $header = "$row[1] $row[0] / $id_orden";
 $orden = $orden . "<tr class=\"provlistrow\">
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_orden($row[2]);\">$row[3]</a></td>
	<td class=\"centrado\">$row[8]</td>
	<td class=\"centrado\">$row[4] <input type='hidden' name='cantidad$count' id='cantidad$count' value='$row[4]'/> <input type='hidden' name='id_orden_item$count' id='id_orden_item$count' value='$row[2]'/></td>
	<td class=\"centrado\">$row[10]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\">$row[7]</td>
	<td class=\"centrado\">AR$</td>
   </tr>\n";

 $count++;
}

//Calculo total en pesos
$query = "SELECT
        sum((ordenitem.cantidad * ordenitem.precio_ref))
  FROM
      ordenitem
  WHERE (
        (ordenitem.id_orden = $id_orden)
  )";
$result = $pdo->query($query);
$row = $result->fetch(PDO::FETCH_NUM);

$total = $row[0];
$total_dolar = $total / obtener_precio_dolar_orden($id_orden);
$total_pesos = $total;
}

totales_dos_decimales($total_dolar,$total_pesos);

$cotizacion_dolar = obtener_precio_dolar_orden($id_orden);
$cotizacion_fecha = obtener_fecha_orden($id_orden);

$orden_data = obtener_orden($id_orden);

$var = array(
  "header" => $header,
  "orden" => $orden,
  "cant_filas" => $count,
  "id_orden" => $id_orden,
  "total_dolar" => $total_dolar,
  "total_pesos" => $total_pesos,
  "cotiz_dolar" => $cotizacion_dolar,
  "cotiz_fecha" => $cotizacion_fecha,
  "descripcion" => orden_descripcion($id_orden),
  "despacho" => orden_despacho($id_orden),
  "nr_factura" =>  orden_nr_factura($id_orden),
  "factura_AR" => $orden_data['factura_AR'],
  "proveedor_AR" => $orden_data['proveedor_AR'],
  "focus" => $focus);

//eval_html('orden_ver.html', $var);
eval_html('orden_ver_ajax.php', $var);


function orden_descripcion($id_orden)
{
	$query = "SELECT descripcion FROM orden WHERE orden.id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function orden_despacho($id_orden)
{
	$query = "SELECT despacho FROM orden WHERE orden.id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function orden_nr_factura($id_orden)
{
	$query = "SELECT nr_factura FROM orden WHERE orden.id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

/**
 * Actualiza la cantidad (y cant pendiente) y le precio del item de la compra pasado
 * como parametro
 */
function update_orden($id_orden_item, $cantidad, $precio)
{
 if ( ($cantidad == 0) or ($cantidad == "") )
 {
  $query = "SELECT id_orden, id_item FROM ordenitem WHERE id_orden_item = $id_orden_item";
  $result = $pdo->query($query);
  $row = $result->fetch(PDO::FETCH_NUM);

  $query = "DELETE FROM ordenitem WHERE id_orden_item = $id_orden_item";

  // logueo item borrado de la orden (8)
  log_trans($_SESSION['valid_user'], 8, $row[1], 0, date("Y-m-d"), $row[0]);
 }
 else
 {
  if ($precio == "") $precio="NULL";

  if(obtener_tipo_proveedor_por_orden_item($id_orden_item) == "EXTRANJERO")
  {
  	$query = "UPDATE
        ordenitem
  	 SET
        cantidad = $cantidad,
        cantidad_pendiente = $cantidad,
        precio_fob = $precio
   	WHERE
        id_orden_item = $id_orden_item";
  }
  else
  {
  	$query = "UPDATE
        ordenitem
  	 SET
        cantidad = $cantidad,
        cantidad_pendiente = $cantidad,
        precio_ref = $precio
   	WHERE
        id_orden_item = $id_orden_item";
  }

 }
 $result = $pdo->query($query);
}

/**
 * Devuelve si el proveedor es extranjero o nacional para saber como mostrar el listado
 */
function obtener_tipo_proveedor($id_orden){
	$query = "SELECT pais.pais FROM pais pais, proveedor proveedor, item item, ordenitem ordenitem
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = item.id_proveedor and
				ordenitem.id_orden = $id_orden and
				ordenitem.id_item = item.id_item";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);

	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado
 * a partir del id_orden_item
 */
function obtener_tipo_proveedor_por_orden_item($id_orden_item){
	$query = "SELECT pais FROM pais, proveedor, item, ordenitem
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = item.id_proveedor and
				ordenitem.id_orden_item = $id_orden_item and
				ordenitem.id_item = item.id_item";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

function obtener_precio_dolar_orden($id_orden)
{
	$query = "SELECT cotizacion_dolar FROM orden WHERE id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function obtener_fecha_orden($id_orden)
{
	$query = "SELECT fecha FROM orden WHERE id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function obtener_orden($id_orden)
{
	$query = "SELECT * FROM orden WHERE id_orden = $id_orden";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row;
}

function totales_dos_decimales(&$total_dolar,&$total_pesos)
{
	$total_dolar_aux = $total_dolar;
	$total_pesos_aux = $total_pesos;

	$total_dolar=""; $total_pesos="";

//Imprimo totales con dos decimales
//
$tok1 = strtok($total_dolar_aux,".");
$total_dolar = $tok1;
$tok1 = strtok(".\n\t");
if($tok1<>""){
	$tok1 = substr($tok1,0,2);
	$total_dolar .= "," . $tok1;
}
$tok2 = strtok($total_pesos_aux,".");
$total_pesos = $tok2;
$tok2 = strtok(".\n\t");
if($tok2<>""){
	$tok2 = substr($tok2,0,2);
	$total_pesos .= "," . $tok2;
}

}

?>
