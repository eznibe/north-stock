<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_usuario = $_POST['id_usuario'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_usuario";

db_connect();
$pdo = get_db_connection();

function delete_usuario(&$mensaje, $id_usuario)
{
 if ($id_usuario == 0)
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
  $query = "DELETE FROM usuario
            WHERE id_usuario = $id_usuario";

  $result = $pdo->query($query);
  $mensaje = "El usuario seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_usuario($mensaje, $id_usuario);
$usuario = get_usuario_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "usuario" => $usuario,
  "focus" => $focus,
  );

eval_html('usuario_baja.html', $var);

?>

