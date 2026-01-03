<?php

include 'main.php';
include 'dbutils.php';

// session_start();
check_session();
$username = $_SESSION['valid_user'];

db_connect();

$id_prevision = isset($_GET['id_prevision']) ? $_GET['id_prevision'] : "";
$id_item = isset($_GET['id_item']) ? $_GET['id_item'] : "";
$cantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : "";

// Todos los items 
//
$items = "";
if ($id_item <> "") {
  $items = getitems($id_item);
}

if ($id_item <> "") {
  $item = getitem($id_item);
  $stock_disponible = $item['stock_disponible'];
  $stock_transito = $item['stock_transito'];
  $producto = $item['producto'];
  $unidad = "(<em>" . strtoupper($item["unidad"]) . "</em>)";
}

// Todos los previsiones 
//
$previsiones = getPrevisiones();

$focus = "forms[0].cantidad";

$var = array("focus" => $focus,
        "id_prevision" => $id_prevision <> "" ? $id_prevision : "-1",
        "id_item" => $id_item,
        "known_prevision" => $id_prevision <> "" ? 'true' : 'false',
        "known_item" => $id_item <> "" ? 'true' : 'false',
        "cantidad" => $cantidad,
        "producto" => isset($producto) ? $producto : "",
        "unidad" => isset($unidad) ? $unidad : "",
        "stock_disponible" => isset($stock_disponible) ? $stock_disponible : "-",
        "stock_transito" => isset($stock_transito) ? $stock_transito : "-",
        "items" => $items,
        "previsiones" => $previsiones,
        "username" => $username,
        "submitto" => $id_prevision <> "" ? "prevision_ver.php" : "producto_asignar_prevision.php");

eval_html('prevision_item_nuevo.html', $var);


// Functions

function getPrevisiones() {
  $query = "SELECT 
    id_prevision, coalesce(numero_orden, concat('(', id_prevision, ')'))
  FROM prevision p
  WHERE p.fecha_descarga is null
  ORDER by coalesce(numero_orden, id_prevision)";

  $result = $pdo->query($query);

  $previsiones = "";
  while ($row = $result->fetch(PDO::FETCH_NUM))
  {
    $previsiones = $previsiones . "<option value=\"$row[0]\">$row[1]</option>";
  }
  return $previsiones;
}

function getitems($id_item) {
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
  WHERE 
    i.id_item = $id_item
  order by c.categoria, pro.proveedor";

  $result = $pdo->query($query);

  $items = "";
  while ($row = $result->fetch(PDO::FETCH_NUM))
  {
    $items = $items . "<option value=\"$row[0],$row[2],$row[3],$row[4],$row[5],$row[6]\"". ($id_item == $row[0] ? "selected" : "") .">$row[1]</option>";
  }
  return $items;
}

function getitem($id_item) {
  $query = "SELECT i.id_item, 
    stock_disponible,
    stock_transito,
    concat(c.categoria, ' - ', p.proveedor) as producto,
    u.unidad
  FROM item i
  JOIN categoria c on c.id_categoria = i.id_categoria
  JOIN unidad u on u.id_unidad = c.id_unidad_visual 
  JOIN proveedor p on p.id_proveedor = i.id_proveedor
  WHERE i.id_item = $id_item";

  $result = $pdo->query($query);

  $item = $result->fetch(PDO::FETCH_NUM);
  return $item;
}

?>
