<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_proveedor = $_GET['id_proveedor'];

$query = "SELECT 
        Proveedor.proveedor,
        Proveedor.direccion,
        Pais.pais,
        Proveedor.telefono,
        Proveedor.fax,
        Proveedor.contacto,
        Proveedor.mail
  FROM
        Proveedor
  LEFT JOIN
        Pais
  USING
        (id_pais)
  WHERE
	Proveedor.id_proveedor = $id_proveedor";

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
$row = mysql_fetch_array($result);

$proveedor = $row[0];
$direccion = $row[1];
$pais = $row[2];
$telefono = $row[3];
$fax = $row[4];
$contacto = $row[5];
$mail = $row[6];


$var = array(
	"proveedor" => $proveedor,
	"direccion" => $direccion,
	"pais" => $pais,
	"telefono" => $telefono,
	"fax" => $fax,
	"contacto" => $contacto,
	"mail" => $mail,
	);
eval_html('proveedor_detalle.html', $var);
