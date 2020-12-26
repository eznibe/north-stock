<?php

include 'main.php';
include 'dbutils.php';

check_session();

$username = $_SESSION['valid_user'];

$mensaje = isset($var["mensaje"]) ? $var["mensaje"] : "";
$focus = "forms[0].pproducto";
$producto = "";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "focusId" => "pproducto",
  "username" => $username); 

eval_html('producto_salida.html', $var);
