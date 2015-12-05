<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].grupo";
$categoria = "";
$stock_minimo = "";
$unidades = get_units_opt(0);


$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "categoria" => $categoria, 
  "stock_minimo" => $stock_minimo,
  "unidades" => $unidades);

eval_html('grupo_alta.html', $var);
