<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_tipo = $_POST['id_tipo'];
$nombre = $_POST['nombre'];
$username = $_POST['username'];
$clave = $_POST['clave'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_tipo";

db_connect();

function insert_usuario(&$mensaje, $id_tipo, $nombre, $username, $clave)
{
 if ( ($id_tipo == 0) or ($nombre == "") or ($username == "") or ($clave == "") )
 {
  // Si falta alguno de los campos requeridos. 
  //
  $mensaje = "ERROR: Debe ingresar los items marcados con *.";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //
  $nombre = addslashes(trim(strtoupper($nombre)));
  $query = "INSERT INTO Usuario 
        (id_tipousr,
	nombre,
	username,
	clave)
  VALUES 
	($id_tipo,
	\"$nombre\",
	\"$username\",
	\"$clave\")";
  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "ERROR: El usuario " . htmlspecialchars(stripslashes($username)) . " no pudo ser dado de alta. Motivo posible: El nombre de usuario ya existia." . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El usuario " . htmlspecialchars(stripslashes($username)) . " ha sido dado de alta.";
   return TRUE;
  }
 }
}

insert_usuario($mensaje, $id_tipo, $nombre, $username, $clave);

if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$tipo = get_tipousr_opt(0);

$var = array("mensaje" => $mensaje,
  "tipo" => $tipo,
  "focus" => $focus,
  );

eval_html('usuario_alta.html', $var);

?>

