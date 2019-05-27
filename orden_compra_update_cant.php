<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_proveedor = $_GET['id_proveedor'];
$id_itemcomprar = $_GET['id_itemcomprar'];

$query = "SELECT
        ItemComprar.id_itemcomprar,
        Categoria.categoria, 
	Proveedor.proveedor,
        ItemComprar.cantidad,
        Unidad.unidad,
        Item.precio_fob,
        (ItemComprar.cantidad * Item.precio_fob),
	Item.factor_unidades,
	Categoria.id_categoria
  FROM
      Categoria, Proveedor, ItemComprar, Item, Unidad
  WHERE (
	(ItemComprar.id_itemcomprar = $id_itemcomprar) AND
        (Item.id_item = ItemComprar.id_item) AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Item.id_unidad_compra)
  )
  ORDER BY
        Categoria.categoria";

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
