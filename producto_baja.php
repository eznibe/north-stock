<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$mensaje = "";
$focus = "forms[0].id_subproducto";

$subproducto = get_subproducto_opt(0);

$var = array("mensaje" => $mensaje,
  "subproducto" => $subproducto,
  "focus" => $focus,
  );

eval_html('producto_baja.html', $var);
