<?php

include 'main.php';
include 'dbutils.php';

check_session();

$id_subproducto = $_POST['id_subproducto'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_subproducto";

db_connect();

function delete_subproducto(&$mensaje, $id_subproducto)
{
 if ($id_subproducto == 0)
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
  $query = "DELETE FROM Item 
            WHERE id_item = $id_subproducto";

  $result = mysql_query($query);
  $mensaje = "El subproducto seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_subproducto($mensaje, $id_subproducto);
$subproducto = get_subproducto_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "subproducto" => $subproducto,
  "focus" => $focus,
  );

eval_html('producto_baja.html', $var);

?>

