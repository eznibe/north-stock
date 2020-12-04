<?php
// Detalle orden en transito

include_once 'main.php';
include_once 'dbutils.php';

session_start();

db_connect();

if ( isset($_GET['id_orden']) ) $id_orden = $_GET['id_orden'];
else $id_orden = $_POST['id_orden'];

if ( isset($_GET['incluir_completos']) ) $incluir_completos = $_GET['incluir_completos'];
else $incluir_completos = false;



$mensaje = "";
$focus = "forms[0].dia";
$cant_filas=0;

if (isset($_POST['formname']) && $_POST['formname'] == "orden_update")
{
	$id_orden_item = $_POST['id_orden_item'];
	$cantidad = $_POST['cantidad'];
	$precio = $_POST['precio'];

	update_orden($id_orden, $id_orden_item, $cantidad, $precio);
	//  Actualiza el valor del item gral., no solo en la orden
	update_precio_item($id_orden_item, $precio);
}

if(obtener_tipo_proveedor($id_orden) == "EXTRANJERO")
{
	  $query = "SELECT
		DATE_FORMAT(Orden.fecha, '%d-%m-%Y') AS fech,
		Proveedor.proveedor,
		OrdenItem.id_orden_item,
		Categoria.categoria,
		OrdenItem.cantidad,
		CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
		OrdenItem.precio_fob,
		(OrdenItem.cantidad_pendiente * OrdenItem.precio_fob),
		Item.codigo_proveedor,
		OrdenItem.cantidad_pendiente,
		OrdenItem.id_item,
		TipoEnvio.tipo_envio
	  FROM
	      Orden, Categoria, Proveedor, OrdenItem, Item, Unidad, TipoEnvio
	  WHERE (
		(Orden.id_orden = $id_orden) AND
		(OrdenItem.id_orden = Orden.id_orden) AND
		(Item.id_item = OrdenItem.id_item) AND
		(Categoria.id_categoria = Item.id_categoria) AND
		(Proveedor.id_proveedor = Item.id_proveedor) AND
		(Unidad.id_unidad = Item.id_unidad_compra) AND
		(OrdenItem.id_tipo_envio = TipoEnvio.id_tipo_envio) ";
		if(!$incluir_completos){ // no incluir esta condicion si quiero ver la orden con los items ya arribados completamente
			$query .= " AND (OrdenItem.cantidad_pendiente > 0) ";
		}
  	$query .= ")
			  ORDER BY
				Categoria.categoria, TipoEnvio.id_tipo_envio";

	$orden="";
	$result = mysql_query($query);

	while ($row = mysql_fetch_array($result))
	{
	 $header = "$row[1] $row[0] / $id_orden";
	 $orden = $orden . "<tr class=\"provlistrow\" id='item$cant_filas' name='item$cant_filas' value='$row[2]'>

		<td class=\"centrado\"><a class=\"list\" onclick=\"update_orden($row[2]);\">$row[3]</a></td>
		<td class=\"centrado\">$row[8]</td>
		<td class=\"centrado\">$row[4]</td>
		<td class=\"centrado\">$row[9] <input type='hidden' id='cant_pend$cant_filas' value='$row[9]' /></td>
		<td class=\"centrado\">
				<div style='width=70px;'><input onblur='validar_valor_ingresado($cant_filas);' size='5' type='text' id='cant_arribada$cant_filas' name='cant_arribada$cant_filas' value='$row[9]'/></div>
				<input type='hidden' value='$row[2]'  name='orden_item$cant_filas' id='orden_item$cant_filas'>
				<input type='hidden' value='$row[10]' name='item$cant_filas' id='item$cant_filas'>
		</td>
		<td class=\"centrado\">$row[11]</td>
		<td class=\"centrado\">$row[5]</td>
		<td class=\"centrado\">$row[6]</td>
		<td class=\"centrado\">$row[7]</td>
		<td class=\"centrado\">US$</td>
	   </tr>\n";

	 $cant_filas = $cant_filas+1;
	}

	$query = "SELECT
	        sum((OrdenItem.cantidad_pendiente * OrdenItem.precio_fob))
	  FROM
	      OrdenItem
	  WHERE (
	        (OrdenItem.id_orden = $id_orden)
	  )";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		$total = $row[0];
		$total_dolar = $total;
		$total_pesos = $total * obtener_precio_dolar_orden($id_orden);
}

else
{
//Es Proveedor Argentino
//
	$query = "SELECT
	DATE_FORMAT(Orden.fecha, '%d-%m-%Y') AS fech,
	Proveedor.proveedor,
	OrdenItem.id_orden_item,
	Categoria.categoria,
	OrdenItem.cantidad,
	CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
	OrdenItem.precio_ref,
	(OrdenItem.cantidad_pendiente * OrdenItem.precio_ref),
	Item.codigo_proveedor,
	OrdenItem.cantidad_pendiente,
	OrdenItem.id_item,
	TipoEnvio.tipo_envio
  FROM
      Orden, Categoria, Proveedor, OrdenItem, Item, Unidad, TipoEnvio
  WHERE (
	(Orden.id_orden = $id_orden) AND
	(OrdenItem.id_orden = Orden.id_orden) AND
	(Item.id_item = OrdenItem.id_item) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor) AND
	(Unidad.id_unidad = Item.id_unidad_compra) AND
	(OrdenItem.id_tipo_envio = TipoEnvio.id_tipo_envio) ";
	if(!$incluir_completos){ // no incluir esta condicion si quiero ver la orden con los items ya arribados completamente
		$query .= " AND (OrdenItem.cantidad_pendiente > 0) ";
	}
  $query .= ")
		  ORDER BY
			Categoria.categoria, TipoEnvio.id_tipo_envio";

	$result = mysql_query($query);

	while ($row = mysql_fetch_array($result))
	{
	 $header = "$row[1] $row[0] / $id_orden";
	 $orden = $orden . "<tr class=\"provlistrow\" value='$row[2]'>
		<td class=\"centrado\"><a class=\"list\" onclick=\"update_orden($row[2]);\">$row[3]</a></td>
		<td class=\"centrado\">$row[8]</td>
		<td class=\"centrado\">$row[4]</td>
		<td class=\"centrado\">$row[9] <input type='hidden' id='cant_pend$cant_filas' value='$row[9]' /></td>
		<td class=\"centrado\">
				<div style='width=70px;'><input onblur='validar_valor_ingresado($cant_filas);' size='5' type='text' id='cant_arribada$cant_filas' name='cant_arribada$cant_filas' value='$row[9]'/></div>
				<input type='hidden' value='$row[2]'  name='orden_item$cant_filas' id='orden_item$cant_filas'>
				<input type='hidden' value='$row[10]' name='item$cant_filas' id='item$cant_filas'>
		</td>
		<td class=\"centrado\">$row[11]</td>
		<td class=\"centrado\">$row[5]</td>
		<td class=\"centrado\">$row[6]</td>
		<td class=\"centrado\">$row[7]</td>
		<td class=\"centrado\">AR$</td>
	   </tr>\n";

	 $cant_filas = $cant_filas+1;
	}

	  $query = "SELECT
	        sum((OrdenItem.cantidad_pendiente * OrdenItem.precio_ref))
	  FROM
	      OrdenItem
	  WHERE (
	        (OrdenItem.id_orden = $id_orden)
	  )";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$total = $row[0];

	if(obtener_precio_dolar_orden($id_orden)=="" || obtener_precio_dolar_orden($id_orden)== 0)
		$total_dolar = 0;
	else
		$total_dolar = $total / obtener_precio_dolar_orden($id_orden);
	$total_pesos = $total;
}

totales_dos_decimales($total_dolar,$total_pesos);

$cotiz_dolar = obtener_precio_dolar_orden($id_orden);
$cotiz_fecha = obtener_fecha_orden($id_orden);

$diaopc = opciones_dia();
$mesopc = opciones_mes();
$anoopc = opciones_ano();

$date = date("F j, Y, g:i a");

$var = array(
  "header" => $header,
  "orden" => $orden,
  "id_orden" => $id_orden,
  "total_dolar" => $total_dolar,
  "total_pesos" => $total_pesos,
  "cotiz_dolar" => $cotiz_dolar,
  "cotiz_fecha" => $cotiz_fecha,
  "dia" => $diaopc,
  "mes" => $mesopc,
  "ano" => $anoopc,
  "focus" => $focus,
  "cant_filas" => $cant_filas,
  "date" => $date,
  "descripcion" => orden_descripcion($id_orden),
	"despacho" => orden_despacho($id_orden),
	"nr_factura" => orden_nr_factura($id_orden),
  "mensaje" => $mensaje);

//eval_html('orden_ver_arribo.html', $var);
eval_html('orden_ver_arribo_ajax.php', $var);


//FUNCIONES
function orden_descripcion($id_orden)
{
	$query = "SELECT descripcion FROM Orden WHERE Orden.id_orden = $id_orden";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function orden_despacho($id_orden)
{
	$query = "SELECT despacho FROM Orden WHERE Orden.id_orden = $id_orden";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function orden_nr_factura($id_orden)
{
	$query = "SELECT nr_factura FROM Orden WHERE Orden.id_orden = $id_orden";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

/**
 *
 */
function update_orden($id_orden, $id_orden_item, $cantidad, $precio)
{
 $cantidad_anterior = get_cantidad_pendiente_comprar($id_orden_item);
 $id_item = get_ordenitem_id_item($id_orden_item);
 $stock_transito_actual = get_stock_transito($id_item);
 $factor_unidades = get_factor_unidades($id_item);
 $nuevo_stock = $stock_transito_actual + (($cantidad - $cantidad_anterior) * $factor_unidades);
 set_stock_transito($id_item, $nuevo_stock);

 // DEBUG intended -> por ahora logueo siempre
 //if($nuevo_stock < 0) {
 	log_stock_transito_negativo($_SESSION['valid_user'], $id_item, $id_orden, $stock_transito_actual, $nuevo_stock, $cantidad_anterior, $cantidad, 'manual');
 //}

 if ( ($cantidad == 0) or ($cantidad == "") )
 {
  $query = "SELECT id_orden, id_item FROM OrdenItem WHERE id_orden_item = $id_orden_item";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);

  $query = "DELETE FROM OrdenItem WHERE id_orden_item = $id_orden_item";

  // logueo item borrado de la orden (8)
  log_trans($_SESSION['valid_user'], 8, $row[1], 0, date("Y-m-d"), $row[0]);
 }
 else
 {
	  if ($precio == "") $precio="NULL";

	  // update cantidad pendiente del item de la orden y update del precio
	  if(obtener_tipo_proveedor_por_orden_item($id_orden_item) == "EXTRANJERO")
	  {
	  	$query = "UPDATE
	        OrdenItem
	   		SET ";
	   	if($cantidad_anterior != 0){
	   		$query .= "cantidad = (cantidad + ($cantidad - cantidad_pendiente)),";
	   	}
	    	$query .= "
		aereo_pendiente = (aereo_pendiente + ($cantidad - cantidad_pendiente)),
		cantidad_pendiente = $cantidad,
	        precio_fob = $precio
	   	WHERE
	        id_orden_item = $id_orden_item";
	  }
	  else
	  {
	  	$query = "UPDATE
	        OrdenItem
	   		SET ";
	   	if($cantidad_anterior != 0){
	   		$query .= "cantidad = (cantidad + ($cantidad - cantidad_pendiente)),";
	   	}
	    	$query .= "
		aereo_pendiente = (aereo_pendiente + ($cantidad - cantidad_pendiente)),
		cantidad_pendiente = $cantidad,
	        precio_ref = $precio
	   	WHERE
	        id_orden_item = $id_orden_item";
	  }

	  // restar del stock la cantidad ingresada, solo si la cantidad pendiente anterior era 0 (es cancelar arribo)
	  if($cantidad_anterior == 0) {

		// logueo transaccion de recupero -> agrego movimiento negativo para representarlo reduccion de stock disp.
	  	log_trans($_SESSION['valid_user'], 1, $id_item, $cantidad * (-1), date("Y-m-d"), $id_orden);

	  	$stock_query = "UPDATE Item SET stock_disponible = stock_disponible - ($cantidad * $factor_unidades) WHERE id_item = $id_item";
	  	var_dump($stock_query);
	  	$result = mysql_query($stock_query);
	  }
 }
 $result = mysql_query($query);

 // mantener siempre el status de la orden en 1 (en transito) -> si la orden estaba cerrada la vuelva a poner en transito
 $query = "UPDATE Orden SET id_status = 1 WHERE id_orden = $id_orden";
 $result = mysql_query($query);
}


function update_precio_item($id_orden_item, $precio)
{

 $id_item = get_id_item_por_orden_item($id_orden_item);

 // update cantidad pendiente del item de la orden y update del precio
 if(obtener_tipo_proveedor_por_orden_item($id_orden_item) == "EXTRANJERO")
 {
 	$precio_fob = $precio;

 	//Calculo los precios nac y ref a partir del precio_fob ingresado si es un item de proveedor extranjero
	//
  	$id_categoria  = obtener_categoria($id_item);
  	$precio_nac = $precio_fob + ($precio_fob * porcentaje_impuesto_categoria($id_categoria)/100);
  	$precio_ref = $precio_nac * precio_dolar();


  	$query = "UPDATE Item SET
				precio_fob = $precio_fob,
				precio_nac = $precio_nac,
				precio_ref = $precio_ref
			  WHERE
				Item.id_item = $id_item";
 }
 else
 {
  	$precio_ref = $precio;

  	$query = "UPDATE Item SET
				precio_ref = $precio_ref
			  WHERE
				Item.id_item = $id_item";
 }

 $result = mysql_query($query);
}

function obtener_categoria($id_item)
{
	$query = "SELECT id_categoria FROM Item WHERE id_item=$id_item";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}

function porcentaje_impuesto_categoria($id_categoria)
{
	$query = "SELECT porc_impuesto FROM Categoria WHERE id_categoria=$id_categoria";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}

function precio_dolar()
{
	$query = "SELECT precio_dolar FROM DolarHoy WHERE id_dolar=(SELECT MAX(id_dolar) FROM dolarHoy)";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}


/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado
 * a partir del id_orden
 */
function obtener_tipo_proveedor($id_orden){
	$query = "SELECT pais FROM Pais pais, Proveedor proveedor, Item item, OrdenItem ordenitem
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = item.id_proveedor and
				ordenitem.id_orden = $id_orden and
				ordenitem.id_item = item.id_item";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
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
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

function get_id_item_por_orden_item($id_orden_item){
	$query = "SELECT id_item
				FROM ordenitem
				WHERE id_orden_item = $id_orden_item";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function obtener_precio_dolar_orden($id_orden)
{
	$query = "SELECT cotizacion_dolar FROM Orden WHERE id_orden = $id_orden";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function obtener_fecha_orden($id_orden)
{
	$query = "SELECT fecha FROM Orden WHERE id_orden = $id_orden";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
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


// retorna lo que qeuda por comprar del item en la orden
function get_cantidad_pendiente_comprar($id_orden_item)
{
 $query = "SELECT OrdenItem.cantidad_pendiente
        FROM OrdenItem
        WHERE OrdenItem.id_orden_item = $id_orden_item";
 $result = mysql_query($query);
 $row = mysql_fetch_array($result);
 return $row[0];
}

?>
