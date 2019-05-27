<?php

include 'main.php';

check_session();
$valid_user = $_SESSION['valid_user'];
$var = array("username" => $valid_user);

if ($_SESSION['user_level'] < 11) eval_html('menu_proveedores_10.html', $var);
else eval_html('menu_proveedores.html', $var);

