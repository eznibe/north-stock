<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_proveedor";

$proveedor = get_proveedor_opt(0);

$var = array("mensaje" => $mensaje,
  "proveedor" => $proveedor,
  "focus" => $focus,
  );

eval_html('proveedor_baja.html', $var);
