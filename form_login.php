<?php

include 'main.php';
include 'dbutils.php';

$username = $_POST['username'];
$clave = $_POST['password'];

db_connect();

$query = "SELECT
	 nivel, nombre
  FROM
	usuario,
	tipousr
  WHERE (
	(username LIKE \"$username\") AND
	(clave LIKE \"$clave\") AND
	(tipousr.id_tipousr = usuario.id_tipousr) )";

$result = mysql_query($query);
$num_results = mysql_num_rows($result);

session_start();
if ($num_results != 0)
{
  $row = mysql_fetch_array($result);
  $valid_user = $username;
//  $valid_user = $row[1];

  $user_level = $row[0];
  $_SESSION['valid_user'] = $valid_user;
  $_SESSION['user_level'] = $user_level;
  $var = array("username" => $valid_user);
  eval_html('main_menu2.html', $var);
}
else {
  echo "usuario o clave invalidos.";
}

?>

