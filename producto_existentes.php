<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$query = "select productos.producto, proveedores.proveedor, productos.stock_minimo, productos.stock_disponible, productos.precio_venta  from productos left join proveedores using (id_proveedor) where productos.stock_disponible > 0";
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
 $aux = $aux . "<tr class=\"provlistrow\"><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td></tr>\n";
}

$var = array("rows" => $aux);
eval_html('producto_listar.html', $var);
