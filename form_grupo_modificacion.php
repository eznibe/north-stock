<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_grupo = $_POST['id_grupo'];
$grupo = $_POST['grupo'];
$agrupacion = $_POST['agrupacion_dd'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].id_grupo";

db_connect();
$pdo = get_db_connection();

function modify_grupo(&$mensaje, $id_grupo, $grupo, $agrupacion)
{
 if ( ($id_grupo == 0) OR ($grupo == "") OR ($agrupacion == -1) )
 {
  // Si falta alguno de los campos requeridos. 
  //
  $mensaje = "ERROR: Debe ingresar todos los items marcados con *.";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //
  $grupo = addslashes(trim(strtoupper($grupo)));
  $query = "UPDATE grupo 
			SET grupo = \"$grupo\", agrupacion_contable = \"$agrupacion\" 
            WHERE id_grupo = $id_grupo";
  $result = db_query($query);
  
  // Los items del grupo tambien modifican a la nueva agrupacion contable, solo si esta cambio en el grupo
  if($agrupacion != grupo_agrupacion_contable($id_grupo)) {
	  $query = "UPDATE item i join categoria c on i.id_categoria = c.id_categoria join grupo g on g.id_grupo = c.id_grupo
				SET i.agrupacion_contable = $agrupacion 
				WHERE g.id_grupo = $id_grupo";
	  $result = db_query($query);
  }
  
  $mensaje = "El grupo seleccionado ha sido modificado.";
  return TRUE;
 }
}

function grupo_agrupacion_contable($id_grupo) 
{
	$query = "SELECT agrupacion_contable FROM grupo WHERE grupo_id_grupo = $id_grupo";
	$result = db_query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

modify_grupo($mensaje, $id_grupo, $grupo, $agrupacion);
$grupo = get_group_opt(0);



if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

$redirect = 'grupo_modificacion_ajax.php';

$var = array("mensaje" => $mensaje,
			 "redirect" => $redirect
			  );

eval_html('redirect.html', $var);

?>

