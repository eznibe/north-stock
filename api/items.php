<?php

require_once('../main.php');
require_once('../dbutils.php');

session_start();

db_connect();


if(isset($_GET['search_categoria'])) {
	$value = search_items_categoria($_GET['search_categoria']);
}

//return JSON array
exit(json_encode($value));

function search_items_categoria($search_categoria) {

  $obj = new stdClass();
  $obj->success = true;

  $query = "SELECT 
    i.id_item, 
    i.stock_disponible,
    i.stock_transito,
    c.categoria, 
    pro.proveedor,
    CONCAT(u.unidad,'(',i.factor_unidades,')') unidad,
    case when pro.id_pais = 1 then 'AR$' when pro.id_pais > 1 then 'US$' end as moneda,
    round(coalesce(i.precio_fob, i.precio_ref), 2) as precio
  FROM item i 
  JOIN categoria c ON i.id_categoria = c.id_categoria
	JOIN proveedor pro ON pro.id_proveedor = i.id_proveedor
  JOIN unidad u ON u.id_unidad = c.id_unidad_visual
  WHERE
	  c.categoria LIKE '%$search_categoria%'
    OR
    i.codigo_barras = '$search_categoria'
  ORDER BY 
    c.categoria";
  
  $result = $pdo->query($query);
	if(!$result) {
    $obj->success = false;
  } else {
    $obj->items = array(); 
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
      array_push($obj->items, $row);
    }
  }

  return $obj;
}

?>
