<?php

include 'main.php';
include 'dbutils.php';

session_start();

$mensaje = "";
$focus = "forms[0].categoria";
$categoria = "";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "categoria" => $categoria); 

eval_html('categoria_buscar.html', $var);
