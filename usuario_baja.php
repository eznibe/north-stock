<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = "";
$focus = "forms[0].id_usuario";

$usuario = get_usuario_opt(0);

$var = array("mensaje" => $mensaje,
  "usuario" => $usuario,
  "focus" => $focus,
  );

eval_html('usuario_baja.html', $var);
