<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_grupo = $_POST['id_grupo'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_grupo";

db_connect();
$pdo = get_db_connection();

function delete_grupo(&$mensaje, $id_grupo)
{ global $pdo; if ($id_grupo == 0)
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
  $query = "DELETE FROM grupo 
            WHERE id_grupo = $id_grupo";

  $result = db_query($query);
  $mensaje = "El grupo seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_grupo($mensaje, $id_grupo);
$grupo = get_group_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "grupo" => $grupo,
  "focus" => $focus,
  );

eval_html('grupo_baja.html', $var);

?>

