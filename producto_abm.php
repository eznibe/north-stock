<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$var = "";
eval_html('producto_abm.html', $var);
