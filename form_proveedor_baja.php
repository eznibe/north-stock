<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_proveedor = $_POST['id_proveedor'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_proveedor";

db_connect();
$pdo = get_db_connection();

function delete_proveedor(&$mensaje, $id_proveedor)
{
 if ($id_proveedor == 0)
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
  $query = "DELETE FROM proveedor 
            WHERE id_proveedor = $id_proveedor";

  $result = $pdo->query($query);
  $mensaje = "El proveedor seleccionado ha sido eliminado.";
  return TRUE;
 }
}

delete_proveedor($mensaje, $id_proveedor);
$proveedor = get_proveedor_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$var = array("mensaje" => $mensaje,
  "proveedor" => $proveedor,
  "focus" => $focus,
  );

eval_html('proveedor_baja.html', $var);

?>

