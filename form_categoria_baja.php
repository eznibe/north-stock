<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_categoria = $_POST['id_categoria'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_categoria";

db_connect();

function delete_categoria(&$mensaje, $id_categoria)
{
 if ($id_categoria == 0)
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
  $query = "DELETE FROM Categoria 
            WHERE id_categoria = $id_categoria";

  $result = mysql_query($query);
  $mensaje = "El producto seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_categoria($mensaje, $id_categoria);
$categoria = get_categoria_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "categoria" => $categoria,
  "focus" => $focus,
  );

eval_html('categoria_baja.html', $var);

?>

