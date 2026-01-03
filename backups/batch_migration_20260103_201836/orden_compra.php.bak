<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_itemcomprar = $_POST['id_itemcomprar'];
$cantidad = $_POST['cantidad'];

$formname = $_POST['formname'];

//echo "Datos: $id_itemcomprar, $cantidad, $formname<p>";

$mensaje = "";
$focus = "producto";

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


if ($formname == "orden_comprar_update_cant") update_cantidad($id_itemcomprar, $cantidad);

//Busco los item a comprar a proveedores extranjeros
//
$query = "SELECT
	itemcomprar.id_itemcomprar,
	concat(categoria.categoria, \" - \", proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_fob,
	(itemcomprar.cantidad * item.precio_fob)
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais <> 'ARGENTINA') )
  )
  ORDER BY
	categoria.categoria";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_cant($row[0]);\">$row[2]</a></td>
	<td>$row[3]</td>
	<td class=\"centrado\">$row[4]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">US$</td>
   </tr>\n";

}

//Busco los item a comprar a proveedores argentinos
//
$query = "SELECT
	itemcomprar.id_itemcomprar,
	concat(categoria.categoria, \" - \", proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_ref,
	(itemcomprar.cantidad * item.precio_ref)
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais = 'ARGENTINA') )
  )
  ORDER BY
	categoria.categoria";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_cant($row[0]);\">$row[2]</a></td>
	<td>$row[3]</td>
	<td class=\"centrado\">$row[4]</td>
	<td class=\"centrado\">$row[5]</td>
	<td class=\"centrado\">AR$</td>
   </tr>\n";

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
        (item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais <> 'ARGENTINA') )
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
        (item.id_proveedor IN (SELECT id_proveedor FROM proveedor, pais WHERE proveedor.id_pais = pais.id_pais AND pais.pais = 'ARGENTINA') )
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
  "focus" => $focus);

eval_html('orden_compra.html', $var);

?>
