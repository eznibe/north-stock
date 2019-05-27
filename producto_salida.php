<?php

include 'main.php';
include 'dbutils.php';

check_session();

$mensaje = "";
$focus = "forms[0].pproducto";
$producto = "";

$var = array("mensaje" => $mensaje,
  "focus" => $focus); 

eval_html('producto_salida.html', $var);
