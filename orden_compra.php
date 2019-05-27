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
  $query = "DELETE FROM ItemComprar WHERE id_itemcomprar = $id_itemcomprar";
 }
 else
 {
  $query = "UPDATE
 	ItemComprar
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
	ItemComprar.id_itemcomprar,
	concat(Categoria.categoria, \" - \", Proveedor.proveedor),
	ItemComprar.cantidad,
	CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
	Item.precio_fob,
	(ItemComprar.cantidad * Item.precio_fob)
  FROM
      Categoria, Proveedor, ItemComprar, Item, Unidad
  WHERE (
	(Item.id_item = ItemComprar.id_item) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor) AND
	(Unidad.id_unidad = Item.id_unidad_compra) AND
	(Item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais <> 'ARGENTINA') )
  )
  ORDER BY
	Categoria.categoria";

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
	ItemComprar.id_itemcomprar,
	concat(Categoria.categoria, \" - \", Proveedor.proveedor),
	ItemComprar.cantidad,
	CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
	Item.precio_ref,
	(ItemComprar.cantidad * Item.precio_ref)
  FROM
      Categoria, Proveedor, ItemComprar, Item, Unidad
  WHERE (
	(Item.id_item = ItemComprar.id_item) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor) AND
	(Unidad.id_unidad = Item.id_unidad_compra) AND
	(Item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais = 'ARGENTINA') )
  )
  ORDER BY
	Categoria.categoria";

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
        sum((ItemComprar.cantidad * Item.precio_fob)),
        sum((ItemComprar.cantidad * (Item.precio_fob * (SELECT precio_dolar from DolarHoy where id_dolar=(SELECT max(id_dolar) FROM DolarHoy)))))
  FROM
      Categoria, Proveedor, ItemComprar, Item, Unidad
  WHERE (
        (Item.id_item = ItemComprar.id_item) AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Categoria.id_unidad_visual) AND
        (Item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais <> 'ARGENTINA') )
  )";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

$total_dolar_aux = $row[0];
$total_pesos_aux = $row[1];

//Calculo los totales de la compra en pesos y dolares para proveedores argentinos
//
$query = "SELECT
		sum((ItemComprar.cantidad * (Item.precio_ref / (SELECT precio_dolar from DolarHoy where id_dolar=(SELECT max(id_dolar) FROM DolarHoy))))),
        sum((ItemComprar.cantidad * Item.precio_ref))
  FROM
      Categoria, Proveedor, ItemComprar, Item, Unidad
  WHERE (
        (Item.id_item = ItemComprar.id_item) AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Categoria.id_unidad_visual) AND
        (Item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais = 'ARGENTINA') )
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
