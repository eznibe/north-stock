<?php

include 'main.php';
include 'dbutils.php';

session_start();

$grupo = $_POST['grupo'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].grupo";

db_connect();

function insert_grupo(&$mensaje, $grupo)
{
 if ($grupo == "")
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
  $grupo = addslashes(trim(strtoupper($grupo)));
  $query = "INSERT INTO Grupo 
            (grupo)
            VALUES 
            (\"$grupo\")";
  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "ERROR: El grupo " . htmlspecialchars(stripslashes($grupo)) . " no pudo ser dado de alta. Motivo posible: El grupo ya existia." . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El grupo " . htmlspecialchars(stripslashes($grupo)) . " ha sido dado de alta.";
   return TRUE;
  }
 }
}

insert_grupo($mensaje, $grupo);

if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  );

eval_html('grupo_alta.html', $var);

?>

