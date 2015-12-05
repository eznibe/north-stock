<?php

include 'main.php';
include 'dbutils.php';

session_start();

$proveedor = addslashes(trim(strtoupper($_POST['proveedor'])));
$direccion = addslashes(trim($_POST['direccion']));
$id_pais = $_POST['pais'];
$telefono = addslashes(trim($_POST['telefono']));
$fax = addslashes(trim($_POST['fax']));
$contacto = addslashes(trim($_POST['contacto']));
$mail = addslashes(trim($_POST['mail']));


db_connect();
if (($proveedor != "") and ($id_pais != 0))
{
 $query = "INSERT INTO 
	Proveedor 
  (
	proveedor, 
	direccion, 
	id_pais, 
	telefono, 
	fax,
	contacto,
	mail
  ) 
  VALUES 
  (
	\"$proveedor\", 
	\"$direccion\", 
	$id_pais, 
	\"$telefono\", 
	\"$fax\", 
	\"$contacto\",
	\"$mail\" )";

 if ( $result = mysql_query($query) )
 {
  $mensaje = "El proveedor " . stripslashes($proveedor) . " ha sido dado de alta.";
 }
 else
 {
  $mensaje = "<em class=\"error\">Error: El proveedor " . stripslashes($proveedor) . " no pudo ser dado de alta. Motivo posible: El proveedor ya existia.</em>";
 }
}
else
{
 $mensaje = "<em class=\"error\">Error: Debe ingresar el nombre del proveedor y seleccionar un pais.</em>";
}

$query = "SELECT id_pais, pais FROM Pais";
$result = mysql_query($query);

$aux = "";
while ($row = mysql_fetch_array($result))
{
 $aux = $aux . "<option value=\"$row[0]\">$row[1]</option>\n";
}

$var = array("mensaje" => $mensaje, "select-pais" => $aux);
eval_html('proveedor_alta.html', $var);


?>

