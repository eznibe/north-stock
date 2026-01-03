<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$query = "SELECT id_grupo, grupo FROM grupo ORDER BY grupo";
$result = $pdo->query($query);

if ($result->rowCount() > 0)
{
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  $grouprow = $grouprow . "<tr><td ><a class=\"link\" href=\"grupo_listar.php?id_grupo=$row[0]\">$row[1]</a></td></tr>";

 }

}

$var = array("grouprow" => $grouprow);

eval_html('grupo_ver.html', $var);
