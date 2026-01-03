<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$query = "SELECT id_pais, pais FROM pais";
$result = $pdo->query($query);

/*echo $query . "<br />";
if ($result)
{
 echo "RESULT = true" . "<br />";
}
else
{
 echo"RESULT = false" . "<br />";
}
*/

$aux = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $aux = $aux . "<option value=\"$row[0]\">$row[1]</option>\n";
}

$var = array("mensaje" => "", "select-pais" => $aux);
eval_html('proveedor_alta.html', $var);
