<?php

include 'main.php';
include 'dbutils.php';

session_start();

$producto = $_POST['producto'];
$producto_nombre = $_POST['producto_nombre'];
$cantidad = $_POST['cantidad'];
$precio_compra = $_POST['precio_compra'];
$estado = $_POST['estado'];
$mensaje = "";

db_connect();
$pdo = get_db_connection();
if ($estado == "confirma") 
{
 $query = "update productos set stock_disponible = stock_disponible+$cantidad where id_producto=$producto";
 if ($result = $pdo->query($query))
 {
  $query = "select productos.stock_disponible from productos where productos.id_producto=$producto";
  $result = $pdo->query($query);
  $row = $result->fetch(PDO::FETCH_NUM);
  $mensaje = "La compra se ingreso exitosamente. El stock actual del producto \"$producto_nombre\" es $row[0].";
  $var = array("mensaje" => $mensaje,
    "producto" => $producto,
    "producto_nombre" => $producto_nombre,
    "cantidad" => $cantidad,
    "precio_compra" => $precio_compra,
    "estado" => $estado);

  eval_html('orden_confirmada.html', $var);
  exit;
 }
}
elseif ($estado == "check") 
{
 if ( ($producto != 0) and ($cantidad != "") )
 {
  $query = "select productos.producto, proveedores.proveedor from productos left join proveedores using (id_proveedor) where productos.id_producto=$producto";
  $result = $pdo->query($query);
  $row = $result->fetch(PDO::FETCH_NUM);
  $producto_nombre = "$row[0] - $row[1]";
  $estado = "confirma";
  $var = array("mensaje" => $mensaje,
    "producto" => $producto,
    "producto_nombre" => $producto_nombre,
    "cantidad" => $cantidad,
    "precio_compra" => $precio_compra,
    "estado" => $estado);

  eval_html('orden_confirma.html', $var);
  exit;
 }
}


?>

