<?php

include 'main.php';

session_start();
$valid_user = $_SESSION['valid_user'];
$var = array("username" => $valid_user);
eval_html('col_menu.html', $var);

?>
