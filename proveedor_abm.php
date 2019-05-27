<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$var = "";

if ($_SESSION['user_level'] < 11) eval_html('proveedor_abm_10.html', $var);
else eval_html('proveedor_abm.html', $var);
