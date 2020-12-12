<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_proveedor = $_GET['id_proveedor'];
$id_itemcomprar = $_GET['id_itemcomprar'];

$query = "SELECT
        itemcomprar.id_itemcomprar,
        categoria.categoria, 
	proveedor.proveedor,
        itemcomprar.cantidad,
        unidad.unidad,
        item.precio_fob,
        (itemcomprar.cantidad * item.precio_fob),
	item.factor_unidades,
	categoria.id_categoria
  FROM
      categoria, proveedor, itemcomprar, item, unidad
  WHERE (
	(itemcomprar.id_itemcomprar = $id_itemcomprar) AND
        (item.id_item = itemcomprar.id_item) AND
        (categoria.id_categoria = item.id_categoria) AND
        (proveedor.id_proveedor = item.id_proveedor) AND
        (unidad.id_unidad = item.id_unidad_compra)
  )
  ORDER BY
        categoria.categoria";

$result = mysql_query($query);
$row = mysql_fetch_array($result);


$focus = "forms[0].cantidad";

$categoria = $row[1];
$proveedor = $row[2];
$cantidad = $row[3];
if ($row[7] != 1)
{
 $unidad = "<em>" . strtoupper($row[4]) . "</em> [" . $row[7] . " " . get_unidad_descarga($row[8]) . "]";
}
else
{
 $unidad = "<em>" . strtoupper($row[4]) . "</em>";
}
$id_itemcomprar = $row[0];

$var = array("focus" => $focus,
        "categoria" => $categoria,
        "proveedor" => $proveedor,
        "cantidad" => $cantidad,
        "unidad" => $unidad,
        "id_itemcomprar" => $id_itemcomprar,
        "id_proveedor" => $id_proveedor);


eval_html('orden_compra_update_cant.html', $var);

?>
