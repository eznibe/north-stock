<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

if ( isset($_GET['id_proveedor']) ) $id_proveedor = $_GET['id_proveedor'];
else $id_proveedor = $_POST['id_proveedor'];

$formname = isset($_POST['formname']) ? $_POST['formname'] : "";

//echo "Datos: $id_itemcomprar, $cantidad, $formname<p>";

$mensaje = "";
$focus = "producto";

$compra="";
$cant_filas = 0;
$tipoenvio = tipos_de_envio($id_proveedor);

if ($formname == "orden_comprar_update_cant") 
{
	$id_itemcomprar = $_POST['id_itemcomprar'];
	$cantidad = $_POST['cantidad'];

	update_cantidad($id_itemcomprar, $cantidad);
}

//Busco los item a comprar a proveedores extranjeros
//
$query = "SELECT
	itemcomprar.id_itemcomprar,
	concat(categoria.categoria, \" - \", proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_fob,
	(itemcomprar.cantidad * item.precio_fob),
	item.codigo_proveedor,
	itemcomprar.cantidad_pendiente,
	item.id_item
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais <> 'ARGENTINA') ) AND
	proveedor.id_proveedor = $id_proveedor 
	AND itemcomprar.tentativo = false
  )
  ORDER BY
	categoria.categoria";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\">$row[2]</td>
	<td class=\"centrado\">$row[7] <input type='hidden' id='cant_pend$cant_filas' value='$row[7]' /></td>
	<td class=\"centrado\">
		<div style='width=70px;'><input onblur='validar_valor_ingresado($cant_filas);' size='5' type='text' id='cant_arribada$cant_filas' name='cant_arribada$cant_filas' value='0'/></div>
		<input type='hidden' value='$row[8]' name='id_item$cant_filas' id='id_item$cant_filas'>
		<input type='hidden' value='$row[4]' name='precio_item$cant_filas' id='precio_item$cant_filas'>
		<input type='hidden' value='$row[0]' name='id_itemcomprar$cant_filas' id='id_itemcomprar$cant_filas'>
		<input type='hidden' value='$row[7]' name='cant_pendiente$cant_filas' id='cant_pendiente$cant_filas'>
	</td>
	<td>
		<select name='tipoenvio$cant_filas' id='tipoenvio$cant_filas' class='obligatorio'>$tipoenvio</select> 
	</td>
	<td>$row[3]</td>
	<td class=\"centrado\">$row[4]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">US$</td>
   </tr>\n";

   $cant_filas = $cant_filas + 1;	 
}

//Busco los item a comprar a proveedores argentinos
//
$query = "SELECT
	itemcomprar.id_itemcomprar,
	concat(categoria.categoria, \" - \", proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_ref,
	(itemcomprar.cantidad * item.precio_ref),
	item.codigo_proveedor,
	itemcomprar.cantidad_pendiente,
	item.id_item
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais = 'ARGENTINA') ) AND
	proveedor.id_proveedor = $id_proveedor
	AND itemcomprar.tentativo = false
  )
  ORDER BY
	categoria.categoria";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\">$row[2]</td>
	<td class=\"centrado\">$row[7] <input type='hidden' id='cant_pend$cant_filas' value='$row[7]' /></td>
	<td class=\"centrado\">
		<div style='width=70px;'><input onblur='validar_valor_ingresado($cant_filas);' size='5' type='text' id='cant_arribada$cant_filas' name='cant_arribada$cant_filas' value='0'/></div>
		<input type='hidden' value='$row[8]' name='id_item$cant_filas' id='id_item$cant_filas'>
		<input type='hidden' value='$row[4]' name='precio_item$cant_filas' id='precio_item$cant_filas'>
		<input type='hidden' value='$row[0]' name='id_itemcomprar$cant_filas' id='id_itemcomprar$cant_filas'>
		<input type='hidden' value='$row[7]' name='cant_pendiente$cant_filas' id='cant_pendiente$cant_filas'>
	</td>
	<td>
		<select name='tipoenvio$cant_filas' id='tipoenvio$cant_filas' class='obligatorio'>$tipoenvio</select> 
	</td>
	<td>$row[3]</td>
	<td class=\"centrado\">$row[4]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">AR$</td>
   </tr>\n";

	$cant_filas = $cant_filas + 1;	
}

//Calculo los totales de la compra en pesos y dolares para proveedores extranjeros
//
$query = "SELECT
        sum((itemcomprar.cantidad * item.precio_fob)),
        sum((itemcomprar.cantidad * (item.precio_fob * (SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy)))))
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
        (item.id_item = itemcomprar.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (proveedor.id_proveedor = item.id_proveedor) AND
        (unidad.id_unidad = categoria.id_unidad_visual) AND
        (item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais <> 'ARGENTINA') ) AND
        proveedor.id_proveedor = $id_proveedor
  )";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$total_dolar_aux = $row[0];
$total_pesos_aux = $row[1];

//Calculo los totales de la compra en pesos y dolares para proveedores argentinos
//
$query = "SELECT
		sum((itemcomprar.cantidad * (item.precio_ref / (SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy))))),
        sum((itemcomprar.cantidad * item.precio_ref))
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
        (item.id_item = itemcomprar.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (proveedor.id_proveedor = item.id_proveedor) AND
        (unidad.id_unidad = categoria.id_unidad_visual) AND
        (item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais = 'ARGENTINA') ) AND
        proveedor.id_proveedor = $id_proveedor
  )";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$total_dolar_aux += $row[0];
$total_pesos_aux += $row[1];

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

$var = array(
  "compra" => $compra,
  "total_dolar" => $total_dolar,
  "total_pesos" => $total_pesos,
  "focus" => $focus,
  "id_proveedor" => $id_proveedor,
  "cant_filas" => $cant_filas,
  "mensaje" => $mensaje);

eval_html('orden_compra_proveedor.html', $var);
// eval_html('orden_compra_proveedor_ajax.php', $var);


function update_cantidad($id_itemcomprar, $cantidad)
{
 if ( ($cantidad == 0) or ($cantidad == "") )
 {
  $query = "DELETE FROM itemcomprar WHERE id_itemcomprar = $id_itemcomprar";
 }
 else
 {
  $query = "UPDATE
 	itemcomprar
   SET
 	cantidad = $cantidad
   WHERE
 	id_itemcomprar = $id_itemcomprar";
 }
 $result = mysql_query($query);
}

function tipos_de_envio($id_proveedor) {

	$default = (es_proveedor_nacional($id_proveedor, 'ARGENTINA')) ? '1' : '';

//	$codigo = "<option value=''>Elige un tipo de envio</option>";
	$codigo = "";
	$result = get_tipos_de_envio();

	while ($row = mysql_fetch_array($result))
	{
	      $codigo = $codigo . "<option value='".$row[0]."'". ($row[0]==$default ? 'selected' : '') ."> $row[1] </option>";
	}

	return $codigo;
}

?>
