<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$var = "";
eval_html('subproducto_abm.html', $var);
