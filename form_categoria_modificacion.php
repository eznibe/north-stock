<?php

include 'main.php';
include 'dbutils.php';

check_session();


$categoria = $_POST['categoria'];
$id_categoria = $_POST['id_categoria'];
$id_grupo = $_POST['id_grupo'];
$scan = $_POST['scan'];
$stock_minimo = $_POST['stock_minimo'];
$unidad = $_POST['unidad'];
$porcentaje = $_POST['porcentaje'];
$pos_arancelaria = $_POST['pos_arancelaria'];


$mensaje = "";
$focus = "forms[0].categoria";
$formname = $_POST['formname'];

db_connect();

function get_cat_data(&$data, $id_categoria)
{
 $query = "SELECT
	categoria,
	id_grupo,
	scan,
	stock_minimo,
	id_unidad_visual,
	porc_impuesto,
  pos_arancelaria
  FROM
	Categoria
  WHERE (
	(id_categoria = $id_categoria)
  )";
 $result = mysql_query($query);
 $data = mysql_fetch_array($result);
}

function update_categoria(&$mensaje, $categoria, $id_grupo, $scan, $stock_minimo, $unidad, $id_categoria, $porcentaje, $pos_arancelaria)
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

  //Actalizo la categoria
  $query = "UPDATE Categoria SET
	categoria = \"$categoria\",
	id_grupo = $id_grupo,
	scan = \"$scan\",
	stock_minimo = $stock_minimo,
	id_unidad_visual = $unidad,
  	porc_impuesto = $porcentaje,
    pos_arancelaria = \"$pos_arancelaria\"
  WHERE
	Categoria.id_categoria = $id_categoria";

  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "<em class=\"error\">Error: El producto " . htmlspecialchars(stripslashes($categoria)) . " no pudo ser actualizado. Motivo posible: El producto ya existia.</em>" . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El producto " . htmlspecialchars(stripslashes($categoria)) . " ha sido actualizado.";

   //Actualizo los precios_nac de los items de la categoria modificada que son extranjeros
   //
   $precio_dolar = obtener_precio_dolar();

   $query = "SELECT id_item, precio_fob FROM Item WHERE id_categoria = $id_categoria and precio_fob IS NOT NULL";
   $result = mysql_query($query);
   while($row = mysql_fetch_array($result))
   {
   		$precio_nac = $row[1] + ($row[1] * $porcentaje / 100);
   		$precio_ref = $precio_nac * $precio_dolar;

   		$query = "UPDATE Item SET precio_nac = $precio_nac, precio_ref = $precio_ref WHERE id_item = $row[0]";
   		$result2 = mysql_query($query);
   }

   return TRUE;
  }
 }
}

function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar from DolarHoy where id_dolar=(SELECT max(id_dolar) FROM DolarHoy)";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	//Devuelvo el precio del dolar actual
	return $row[0];
}

//Fin Funciones



if ($formname == "categoria_modificacion")
{
 get_cat_data($datos, $id_categoria);
 $grupo = get_group_opt($datos[1]);
 $unidades = get_units_opt($datos[4]);
 $scan = get_scan_opt($datos[2]);

//if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

 $var = array("mensaje" => $mensaje,
  "categoria" => $datos[0],
  "id_categoria" => $id_categoria,
  "grupo" => $grupo,
  "scan" => $scan,
  "stock_minimo" => $datos[3],
  "unidades" => $unidades,
  "porcentaje" => $datos[5],
  "pos_arancelaria" => $datos[6],
  "focus" => $focus,
  );

 eval_html('categoria_datosmodificar.html', $var);
}
elseif ($formname == "categoria_datosmodificar")
{
 if (update_categoria($mensaje, $categoria, $id_grupo, $scan, $stock_minimo, $unidad, $id_categoria, $porcentaje, $pos_arancelaria))
 {
  if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";
 }
 $focus = "forms[0].id_categoria";
 $categoria = get_categoria_opt(0);

 $var = array("mensaje" => $mensaje,
   "categoria" => $categoria,
   "focus" => $focus
 );

 eval_html('categoria_modificacion.html', $var);
}

?>

