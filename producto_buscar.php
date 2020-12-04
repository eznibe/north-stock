<?php
// @deprecated
include 'main.php';
include 'dbutils.php';

session_start();

$mensaje = "";
$focus = "forms[0].producto";
$producto = "";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "producto" => $producto); 

eval_html('producto_buscar.html', $var);
