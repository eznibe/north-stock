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
	concat(categoria.categoria, \" - \", Proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_fob,
	(itemcomprar.cantidad * item.precio_fob),
	item.codigo_proveedor
  FROM
      categoria, Proveedor, itemcomprar, item, Unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(Proveedor.id_proveedor = item.id_proveedor) AND
	(Unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais <> 'ARGENTINA') ) AND
	proveedor.id_proveedor = $id_proveedor 
	AND itemcomprar.tentativo = true
  )
  ORDER BY
	categoria.categoria";

$result = $pdo->query($query);

while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_cantidad_item($row[0], $id_proveedor);\">$row[2]</a></td>
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
	concat(categoria.categoria, \" - \", Proveedor.proveedor),
	itemcomprar.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
	item.precio_ref,
	(itemcomprar.cantidad * item.precio_ref),
	item.codigo_proveedor
  FROM
      categoria, Proveedor, itemcomprar, item, Unidad
  WHERE (
	(item.id_item = itemcomprar.id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(Proveedor.id_proveedor = item.id_proveedor) AND
	(Unidad.id_unidad = item.id_unidad_compra) AND
	(item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais = 'ARGENTINA') ) AND
	proveedor.id_proveedor = $id_proveedor
	AND itemcomprar.tentativo = true
  )
  ORDER BY
	categoria.categoria";

$result = $pdo->query($query);

while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $compra = $compra . "<tr class=\"provlistrow\">
	<td>$row[1]</td>
	<td class=\"centrado\">$row[6]</td>
	<td class=\"centrado\"><a class=\"list\" onclick=\"update_cantidad_item($row[0], $id_proveedor);\">$row[2]</a></td>
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
      categoria, Proveedor, itemcomprar, item, Unidad
  WHERE (
        (item.id_item = itemcomprar.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (Proveedor.id_proveedor = item.id_proveedor) AND
        (Unidad.id_unidad = categoria.id_unidad_visual) AND
        (item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais <> 'ARGENTINA') ) AND
        proveedor.id_proveedor = $id_proveedor
  )";
$result = $pdo->query($query);
$row = $result->fetch(PDO::FETCH_NUM);

$total_dolar_aux = $row[0];
$total_pesos_aux = $row[1];

//Calculo los totales de la compra en pesos y dolares para proveedores argentinos
//
$query = "SELECT
		sum((itemcomprar.cantidad * (item.precio_ref / (SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy))))),
        sum((itemcomprar.cantidad * item.precio_ref))
  FROM
      categoria, Proveedor, itemcomprar, item, Unidad
  WHERE (
        (item.id_item = itemcomprar.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (Proveedor.id_proveedor = item.id_proveedor) AND
        (Unidad.id_unidad = categoria.id_unidad_visual) AND
        (item.id_proveedor IN (SELECT id_proveedor FROM Proveedor, Pais WHERE Proveedor.id_pais = Pais.id_pais AND Pais.pais = 'ARGENTINA') ) AND
        proveedor.id_proveedor = $id_proveedor
  )";
$result = $pdo->query($query);
$row = $result->fetch(PDO::FETCH_NUM);

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
  "mensaje" => $mensaje);

eval_html('orden_compra_tentativa.html', $var);


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
 $result = $pdo->query($query);
}

?>
