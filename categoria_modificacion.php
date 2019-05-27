<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_categoria";

$categoria = get_categoria_opt(0);

$var = array("mensaje" => $mensaje,
  "categoria" => $categoria,
  "focus" => $focus,
  );

eval_html('categoria_modificacion.html', $var);
