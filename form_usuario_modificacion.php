<?php

include 'main.php';
include 'dbutils.php';

session_start();


$id_usuario = $_POST['id_usuario'];
$id_tipo = $_POST['id_tipo'];
$nombre = addslashes(trim(strtoupper($_POST['nombre'])));
$username = $_POST['username'];
$clave = $_POST['clave'];


$mensaje = "";
$focus = "forms[0].id_tipo";
$formname = $_POST['formname'];

db_connect();

function get_usuario_data(&$data, $id_usuario)
{
 $query = "SELECT
	nombre,
	username,
	id_tipousr
  FROM
	Usuario
  WHERE (
	(id_usuario = $id_usuario)
  )";

 $result = mysql_query($query);
 $data = mysql_fetch_array($result);
}

function update_usuario(&$mensaje, $id_usuario, $nombre, $username, $clave, $id_tipo)
{
 if ( ($nombre == "") or ($username == "") or ($clave == "") or ($id_tipo == 0) )
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
  $query = "UPDATE Usuario SET
	nombre = \"$nombre\",
	username = \"$username\",
	clave = \"$clave\",
	id_tipousr = $id_tipo
  WHERE 
	Usuario.id_usuario = $id_usuario";

  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "Error: El usuario " . htmlspecialchars(stripslashes($username)) . " no pudo ser actualizado. Motivo posible: El nombre de usuario ya ex
istia." . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El usuario " . htmlspecialchars(stripslashes($username)) . " ha sido actualizado.";
   return TRUE;
  }
 }
}



if ($formname == "usuario_modificacion")
{
 get_usuario_data($datos, $id_usuario);
 $tipo = get_tipousr_opt($datos[2]);

//if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

 $var = array("mensaje" => $mensaje,
  "id_usuario" => $id_usuario,
  "nombre" => $datos[0],
  "username" => $datos[1],
  "tipo" => $tipo,
  "focus" => $focus,
  );

 eval_html('usuario_datosmodificar.html', $var);
}
elseif ($formname == "usuario_datosmodificar")
{
 if (update_usuario($mensaje, $id_usuario, $nombre, $username, $clave, $id_tipo))
 {
  if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";
 }
 $focus = "forms[0].id_usuario";
 $usuario = get_usuario_opt(0);

 $var = array("mensaje" => $mensaje,
   "usuario" => $usuario,
   "focus" => $focus
 );

 eval_html('usuario_modificacion.html', $var);
}

?>

