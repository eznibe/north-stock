<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$mensaje = "";
$focus = "forms[0].transac";


$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  );

eval_html('listar_fechas.html', $var);
