<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].pais";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  );

eval_html('pais_alta.html', $var);
