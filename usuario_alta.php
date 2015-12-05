<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_tipo";

$tipo = get_tipousr_opt(0);

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "tipo" => $tipo,
  );

eval_html('usuario_alta.html', $var);
