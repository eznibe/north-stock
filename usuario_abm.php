<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();



$var = "";

eval_html('usuario_abm.html', $var);
