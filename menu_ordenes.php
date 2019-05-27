<?php

include 'main.php';

check_session();
$valid_user = $_SESSION['valid_user'];
$var = array("username" => $valid_user);

if ($_SESSION['user_level'] < 11) eval_html('col_menu.html', $var);
else eval_html('menu_ordenes.html', $var);
