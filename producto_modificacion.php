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

if ($_SESSION['user_level'] > 99) eval_html('producto_modificacion.html', $var);
else eval_html('producto_modificacion_99.html', $var);
