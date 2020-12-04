<?php

include 'main.php';
include 'dbutils.php';

// session_start();

db_connect();

$id_prevision = $_GET['id_prevision'];

// Todos los items 
//
$query = "SELECT i.id_item, concat(c.categoria, ' - ', pro.proveedor), 
  round(coalesce(i.precio_fob, i.precio_ref), 2) as precio,
  case when pro.id_pais = 1 then 'AR$' when pro.id_pais > 1 then 'US$' end as moneda,
  CONCAT(u.unidad,'(',i.factor_unidades,')'),
  i.stock_disponible,
  i.stock_transito
FROM item i 
JOIN proveedor pro on i.id_proveedor = pro.id_proveedor
JOIN categoria c on i.id_categoria = c.id_categoria
JOIN unidad u on u.id_unidad = c.id_unidad_visual
order by c.categoria, pro.proveedor";

$result = mysql_query($query);

$items = "";
while ($row = mysql_fetch_array($result))
{
  $items = $items . "<option value=\"$row[0],$row[2],$row[3],$row[4],$row[5],$row[6]\">$row[1]</option>";
}

$focus = "forms[0].cantidad";

$var = array("focus" => $focus,
        "id_prevision" => $id_prevision,
        "items" => $items,
        "submitto" => "prevision_ver.php");


eval_html('prevision_item_nuevo.html', $var);

?>
