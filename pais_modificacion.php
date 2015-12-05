<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_pais";

$pais = get_pais_opt(0);

$var = array("mensaje" => $mensaje,
  "pais" => $pais,
  "focus" => $focus,
  );

eval_html('pais_modificacion.html', $var);
