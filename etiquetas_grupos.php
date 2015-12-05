<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$query = "SELECT id_grupo, grupo FROM Grupo ORDER BY grupo";
$result = mysql_query($query);

if (mysql_num_rows($result) > 0)
{
 while ($row = mysql_fetch_array($result))
 {
  $grouprow = $grouprow . "<tr><td><a class=\"link\" href=\"etiquetas_grupo_listar.php?id_grupo=$row[0]\">$row[1]</a></td>";

 }
}

$var = array("grouprow" => $grouprow);

eval_html('etiquetas_grupos.html', $var);

