<?php

include 'main.php';
include 'dbutils.php';

session_start();

$pais = $_POST['pais'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].pais";

db_connect();

function insert_pais(&$mensaje, $pais)
{
 if ($pais == "")
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
  $pais = addslashes(trim(strtoupper($pais)));
  $query = "INSERT INTO Pais 
            (pais)
            VALUES 
            (\"$pais\")";
  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "ERROR: El pais " . htmlspecialchars(stripslashes($pais)) . " no pudo ser dado de alta. Motivo posible: El pais ya existia." . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El pais " . htmlspecialchars(stripslashes($pais)) . " ha sido dado de alta.";
   return TRUE;
  }
 }
}

insert_pais($mensaje, $pais);

if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  );

eval_html('pais_alta.html', $var);

?>

