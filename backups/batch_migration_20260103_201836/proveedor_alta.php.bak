<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$query = "SELECT id_pais, pais FROM pais";
$result = mysql_query($query);

/*echo $query . "<br />";
if ($result)
{
 echo "RESULT = true" . "<br />";
}
else
{
 echo"RESULT = false" . mysql_error() . "<br />";
}
*/

$aux = "";
while ($row = mysql_fetch_array($result))
{
 $aux = $aux . "<option value=\"$row[0]\">$row[1]</option>\n";
}

$var = array("mensaje" => "", "select-pais" => $aux);
eval_html('proveedor_alta.html', $var);
