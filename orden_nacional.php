<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "producto";
$productos = "";

$query = "select DISTINCT 
     proveedor.id_proveedor, proveedor.proveedor 
     FROM 
      categoria, proveedor, itemcomprar, item, unidad 
     WHERE (
      (itemcomprar.id_item = item.id_item) AND 
      (item.id_proveedor = proveedor.id_proveedor) AND
      (categoria.id_categoria = item.id_categoria) AND
      (unidad.id_unidad = categoria.id_unidad_visual) AND
      (proveedor.id_pais = 1)
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
