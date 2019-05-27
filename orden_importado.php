<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "producto";
$productos = "";

$query = "select DISTINCT 
     Proveedor.id_proveedor, Proveedor.proveedor 
     FROM 
      Categoria, Proveedor, ItemComprar, Item, Unidad 
     WHERE (
      (ItemComprar.id_item = Item.id_item) AND 
      (Item.id_proveedor = Proveedor.id_proveedor) AND
      (Categoria.id_categoria = Item.id_categoria) AND
      (Unidad.id_unidad = Categoria.id_unidad_visual) AND
      (Proveedor.id_pais != 1)
     )";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
 $proveedor = $proveedor . "<a onclick=\"show_items($row[0])\">$row[1]</a><br />\n";
}

$var = array(
  "proveedor" => $proveedor, 
  "focus" => $focus);

eval_html('orden_nacional.html', $var);
