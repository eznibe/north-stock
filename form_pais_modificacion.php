<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_pais = $_POST['id_pais'];
$pais = $_POST['pais'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_pais";

db_connect();

function modify_pais(&$mensaje, $id_pais, $pais)
{
 if ( ($id_pais == 0) OR ($pais == "") )
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
  $query = "UPDATE pais 
		SET pais = \"$pais\"
            WHERE id_pais = $id_pais";
  $result = $pdo->query($query);
  $mensaje = "El pais seleccionado ha sido modificado.";
  return TRUE;
 }
}

modify_pais($mensaje, $id_pais, $pais);
$pais = get_pais_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "pais" => $pais,
  "focus" => $focus,
  );

eval_html('pais_modificacion.html', $var);

?>

