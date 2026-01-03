<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_pais = $_POST['id_pais'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_pais";

db_connect();

function delete_pais(&$mensaje, $id_pais)
{
 if ($id_pais == 0)
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
  $query = "DELETE FROM pais 
            WHERE id_pais = $id_pais";

  $result = $pdo->query($query);
  $mensaje = "El pais seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_pais($mensaje, $id_pais);
$pais = get_pais_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "pais" => $pais,
  "focus" => $focus,
  );

eval_html('pais_baja.html', $var);

?>

