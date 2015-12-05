<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$query = "SELECT 
	Proveedor.id_proveedor, 
	Proveedor.proveedor, 
	Proveedor.direccion, 
	Pais.pais, 
	Proveedor.telefono, 
	Proveedor.contacto 
  FROM 
	Proveedor 
  LEFT JOIN 
	Pais 
  USING 
	(id_pais)
  ORDER BY
	Proveedor.proveedor";
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
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[0]);\">$row[1]</a></td></tr>\n";
// $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$row[1]</a></td><td>$row[2] - $row[3]</td><td>$row[4]</td><td>$row[5]</td></tr>\n";
}

$var = array("rows" => $aux);
eval_html('proveedor_listar.html', $var);
