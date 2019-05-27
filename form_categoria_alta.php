<?php

include 'main.php';
include 'dbutils.php';

session_start();

$categoria = $_POST['categoria'];
$id_grupo = $_POST['id_grupo'];
$scan = $_POST['scan'];
$stock_minimo = $_POST['stock_minimo'];
$unidad = $_POST['unidad'];
$porcentaje = $_POST['porcentaje'];
$pos_arancelaria = $_POST['pos_arancelaria'];
$formname = $_POST['formname'];

$mensaje = "";
$focus = "forms[0].categoria";

db_connect();

function insert_categoria(&$mensaje, $categoria, $id_grupo, $scan, $stock_minimo, $unidad, $porcentaje, $pos_arancelaria)
{
 if ( ($categoria == "") or ($id_grupo == 0) or ($unidad == 0) or ($porcentaje == ""))
 {
  // Si falta alguno de los campos requeridos.
  //
  $mensaje = "<em class=\"error\">Error: Debe ingresar los items marcados con *.</em>";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //
  $categoria = addslashes(trim(strtoupper($categoria)));
  $stock_minimo = addslashes(trim($stock_minimo));
  $query = "INSERT INTO Categoria
            (categoria, id_grupo, scan, stock_minimo, id_unidad_visual, porc_impuesto, pos_arancelaria)
            VALUES
            (\"$categoria\", $id_grupo, \"$scan\", $stock_minimo, $unidad, $porcentaje, \"$pos_arancelaria\")";

  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "<em class=\"error\">Error: El producto " . htmlspecialchars(stripslashes($categoria)) . " no pudo ser dado de alta. Motivo posible: El producto ya existia.</em>" . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El producto " . htmlspecialchars(stripslashes($categoria)) . " ha sido dado de alta.";
   $result = mysql_query("SELECT LAST_INSERT_ID()");
   $row = mysql_fetch_array($result);
   $mensaje = $mensaje . "<br />
	<form action=\"form_producto_alta.php\" method=\"post\" target=\"_self\" name=\"scategoria\">
<input type=\"hidden\" value=\"scategoria\" size=\"10\" name=\"formname\" id=\"formname\">
<input type=\"hidden\" value=\"$row[0]\" size=\"10\" name=\"categoria\" id=\"categoria\">
<button type=\"submit\" name=\"enviar\" value=\"enviar\">Alta de subproducto asociado</button>
</form><hr /> ";
   return TRUE;
  }
 }
}

if (insert_categoria($mensaje, $categoria, $id_grupo, $scan, $stock_minimo, $unidad, $porcentaje, $pos_arancelaria))
{
 $categoria = "";
 $stock_minimo = "";
 $focus = "forms[1].categoria";
}

$unidades = get_units_opt(0);
$grupo = get_group_opt(0);

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "grupo" => $grupo,
  "categoria" => $categoria,
  "stock_minimo" => $stock_minimo,
  "unidades" => $unidades,
  "porcentaje" => $porcentaje,
  "pos_arancelaria" => $pos_arancelaria);

eval_html('categoria_alta.html', $var);

?>

