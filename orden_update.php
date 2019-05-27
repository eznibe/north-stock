<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_orden_item = $_GET['id_orden_item'];

if(obtener_tipo_proveedor($id_orden_item) == "EXTRANJERO")
{
	//Datos de proveedor extranjero
	//
	$query = "SELECT
        OrdenItem.id_orden_item,
        Categoria.categoria,
		Proveedor.proveedor,
        OrdenItem.cantidad,
        Unidad.unidad,
        OrdenItem.precio_fob,
        (OrdenItem.cantidad * OrdenItem.precio_fob),
	OrdenItem.id_orden,
	Item.factor_unidades,
	Categoria.id_categoria
  FROM
      Categoria, Proveedor, OrdenItem, Item, Unidad
  WHERE (
	(OrdenItem.id_orden_item = $id_orden_item) AND
        (Item.id_item = OrdenItem.id_item) AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Item.id_unidad_compra)
  )
  ORDER BY
        Categoria.categoria";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$moneda = "US$";
}

else
{
	//Datos de proveedor argentino
	//
	$query = "SELECT
        OrdenItem.id_orden_item,
        Categoria.categoria,
		Proveedor.proveedor,
        OrdenItem.cantidad,
        Unidad.unidad,
        OrdenItem.precio_ref,
        (OrdenItem.cantidad * OrdenItem.precio_ref),
	OrdenItem.id_orden,
	Item.factor_unidades,
	Categoria.id_categoria
  FROM
      Categoria, Proveedor, OrdenItem, Item, Unidad
  WHERE (
	(OrdenItem.id_orden_item = $id_orden_item) AND
        (Item.id_item = OrdenItem.id_item) AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Item.id_unidad_compra)
  )
  ORDER BY
        Categoria.categoria";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$moneda = "AR$";
}


$focus = "forms[0].cantidad";

$categoria = $row[1];
$proveedor = $row[2];
$cantidad = $row[3];
$precio_fob = $row[5];

if ($row[8] != 1)
{
 $unidad = "<em>" . strtoupper($row[4]) . "</em> [" . $row[8] . " " . get_unidad_descarga($row[9]) . "]";
}
else
{
 $unidad = "<em>" . strtoupper($row[4]) . "</em>";
}
$id_orden_item = $row[0];
$id_orden = $row[7];

$var = array("focus" => $focus,
        "categoria" => $categoria,
        "proveedor" => $proveedor,
        "cantidad" => $cantidad,
        "unidad" => $unidad,
        "precio" => $precio_fob,
        "moneda" => $moneda,
        "id_orden_item" => $id_orden_item,
        "submitto" => "orden_ver.php",
        "id_orden" => $id_orden);


eval_html('orden_update.html', $var);


/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado
 * a partir del id_orden_item
 */
function obtener_tipo_proveedor($id_orden_item){
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

?>
