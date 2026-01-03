<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_orden_item = $_GET['id_orden_item'];

if(obtener_tipo_proveedor($id_orden_item) == "EXTRANJERO")
{
  $query = "SELECT
        ordenitem.id_orden_item,
        categoria.categoria,
		proveedor.proveedor,
        ordenitem.cantidad,
        unidad.unidad,
        ordenitem.precio_fob,
        (ordenitem.cantidad * ordenitem.precio_fob),
		ordenitem.id_orden,
		item.factor_unidades,
		categoria.id_categoria,
		ordenitem.cantidad_pendiente
  FROM
      categoria, Proveedor, ordenitem, item, Unidad
  WHERE (
	(ordenitem.id_orden_item = $id_orden_item) AND
        (item.id_item = ordenitem.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (Proveedor.id_proveedor = item.id_proveedor) AND
        (Unidad.id_unidad = item.id_unidad_compra)
  )
  ORDER BY
        categoria.categoria";

	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);

	$moneda = "US$";
}
else
{
	$query = "SELECT
        ordenitem.id_orden_item,
        categoria.categoria,
		proveedor.proveedor,
        ordenitem.cantidad,
        unidad.unidad,
        ordenitem.precio_ref,
        (ordenitem.cantidad * ordenitem.precio_ref),
		ordenitem.id_orden,
		item.factor_unidades,
		categoria.id_categoria,
		ordenitem.cantidad_pendiente
  FROM
      categoria, Proveedor, ordenitem, item, Unidad
  WHERE (
	(ordenitem.id_orden_item = $id_orden_item) AND
        (item.id_item = ordenitem.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (Proveedor.id_proveedor = item.id_proveedor) AND
        (Unidad.id_unidad = item.id_unidad_compra)
  )
  ORDER BY
        categoria.categoria";

	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);

	$moneda = "AR$";
}


$focus = "forms[0].cantidad";

$categoria = $row[1];
$proveedor = $row[2];
$cantidad = $row[10];
$precio = $row[5];
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
        "precio" => $precio,
        "moneda" => $moneda,
        "id_orden_item" => $id_orden_item,
        "submitto" => "orden_ver_arribo.php",
        "id_orden" => $id_orden);


eval_html('orden_update.html', $var);


/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado
 * a partir del id_orden_item
 */
function obtener_tipo_proveedor($id_orden_item){
	$query = "SELECT pais.pais FROM Pais pais, Proveedor proveedor, item item, ordenitem ordenitem
		  WHERE pais.id_pais = proveedor.id_pais and
				proveedor.id_proveedor = item.id_proveedor and
				ordenitem.id_orden_item = $id_orden_item and
				ordenitem.id_item = item.id_item";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

?>
