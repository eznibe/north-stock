<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$query = "SELECT id_grupo, grupo FROM grupo ORDER BY grupo";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0)
{
 while ($row = mysql_fetch_array($result))
 {
  $grouprow = $grouprow . "<tr><td ><a class=\"link\" href=\"grupo_listar.php?id_grupo=$row[0]\">$row[1]</a></td></tr>";

 }

}

$var = array("grouprow" => $grouprow);

eval_html('grupo_ver.html', $var);
