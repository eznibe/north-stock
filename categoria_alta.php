<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].categoria";
$categoria = "";
$stock_minimo = "";
$unidades = get_units_opt(0);
$porcentaje = "";
$grupo = get_group_opt(0);

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "grupo" => $grupo,
  "categoria" => $categoria,
  "stock_minimo" => $stock_minimo,
  "unidades" => $unidades,
  "porcentaje" => $porcentaje);

eval_html('categoria_alta.html', $var);
