<?php

include 'main.php';
include 'dbutils.php';

session_start();


$id_proveedor = $_POST['id_proveedor'];
$proveedor = addslashes(trim(strtoupper($_POST['proveedor'])));
$direccion = addslashes(trim($_POST['direccion']));
$id_pais = $_POST['pais'];
$telefono = addslashes(trim($_POST['telefono']));
$fax = addslashes(trim($_POST['fax']));
$contacto = addslashes(trim($_POST['contacto']));
$mail = addslashes(trim($_POST['mail']));


$mensaje = "";
$focus = "forms[0].proveedor";
$formname = $_POST['formname'];

db_connect();

function get_prov_data(&$data, $id_proveedor)
{
 $query = "SELECT
	proveedor,
	direccion,
	id_pais,
	telefono,
	fax,
	contacto,
	mail
  FROM
	proveedor
  WHERE (
	(id_proveedor = $id_proveedor)
  )";

 $result = mysql_query($query);
 $data = mysql_fetch_array($result);
}

function update_proveedor(&$mensaje, $id_proveedor, $proveedor, $direccion, $id_pais, $telefono, $fax, $contacto, $mail)
{
 if ( ($proveedor == "") or ($id_pais == 0) )
 {
  // Si falta alguno de los campos requeridos.
  //
  $mensaje = "Error: Debe ingresar los items marcados con *.";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //
  $query = "UPDATE proveedor SET
	proveedor = \"$proveedor\",
	direccion = \"$direccion\",
	id_pais = $id_pais,
	telefono = \"$telefono\",
	fax = \"$fax\",
	contacto = \"$contacto\",
	mail = \"$mail\"
  WHERE 
	proveedor.id_proveedor = $id_proveedor";

  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "Error: El proveedor " . htmlspecialchars(stripslashes($proveedor)) . " no pudo ser actualizado. Motivo posible: El nombre de proveedor ya ex
istia." . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El proveedor " . htmlspecialchars(stripslashes($proveedor)) . " ha sido actualizado.";
   return TRUE;
  }
 }
}



if ($formname == "proveedor_modificacion")
{
 get_prov_data($datos, $id_proveedor);
 $pais = get_pais_opt($datos[2]);

//if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

 $var = array("mensaje" => $mensaje,
  "id_proveedor" => $id_proveedor,
  "proveedor" => $datos[0],
  "direccion" => $datos[1],
  "select-pais" => $pais,
  "telefono" => $datos[3],
  "fax" => $datos[4],
  "contacto" => $datos[5],
  "mail" => $datos[6],
  "focus" => $focus,
  );

 eval_html('proveedor_datosmodificar.html', $var);
}
elseif ($formname == "proveedor_datosmodificar")
{
 if (update_proveedor($mensaje, $id_proveedor, $proveedor, $direccion, $id_pais, $telefono, $fax, $contacto, $mail))
 {
  if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";
 }
 $focus = "forms[0].id_proveedor";
 $proveedor = get_proveedor_opt(0);

 $var = array("mensaje" => $mensaje,
   "proveedor" => $proveedor,
   "focus" => $focus
 );

 eval_html('proveedor_modificacion.html', $var);
}

?>

