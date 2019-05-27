<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_grupo";

$grupo = get_group_opt(0);

$var = array("mensaje" => $mensaje,
  "grupo" => $grupo,
  "focus" => $focus,
  );

eval_html('grupo_baja.html', $var);
