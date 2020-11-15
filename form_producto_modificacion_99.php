<?php

include 'main.php';
include 'dbutils.php';

check_session();


$id_item = $_POST['id_subproducto'];
$categoria = $_POST['categoria'];
$proveedores = $_POST['proveedores'];
$id_proveedor = $_POST['proveedor'];
$codigo_proveedor = $_POST['codigo_proveedor'];
$codigo_barras = $_POST['codigo_barras'];
$stock_disponible = $_POST['stock_disponible'];
$stock_transito = $_POST['stock_transito'];
$agrupacion = $_POST['agrupacion_dd'];

$precio_fob = $_POST['precio_fob'];
$precio_nac = $_POST['precio_nac'];
$precio_ref = $_POST['precio_ref'];


$mensaje = "";
$focus = "forms[0].proveedor";
$formname = $_POST['formname'];

db_connect();

function get_item_data(&$data, $id_item)
{
 $query = "SELECT
	Categoria.categoria,
	Proveedor.proveedor,
	codigo_proveedor,
	codigo_barras,
	stock_disponible,
	stock_transito,
	precio_fob,
	precio_nac,
	precio_ref,
	oculto_fob,
	oculto_nac
  FROM
	Item,
	Categoria,
	Proveedor
  WHERE (
	(id_item = $id_item) AND
	(Categoria.id_categoria = Item.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor)
  )";
 $result = mysql_query($query);
 $data = mysql_fetch_array($result);
}

function item_scan_oblig($id_item)
{
 $query = "SELECT
	Categoria.scan
  FROM
	Categoria,
	Item
  WHERE (
	(Item.id_categoria = Categoria.id_categoria) AND
	(Item.id_item = $id_item)
  )";
 $result = mysql_query($query);
 $row = mysql_fetch_array($result);
 if ($row[0] == 'si') return TRUE;
 else return FALSE;
}

function log_modificacion_item($id_item, $precio_fob, $precio_nac, $precio_ref, $stock_anterior, $stock_nuevo)
{
 $query = "INSERT INTO logprecios
	(id_item, precio_fob, precio_nac, precio_ref, stock_anterior, stock_disponible)
  VALUES
	($id_item, $precio_fob, $precio_nac, $precio_ref, $stock_anterior, $stock_nuevo)";
 $result = mysql_query($query);
 echo mysql_error();
}

function update_item(&$mensaje, $id_item, $codigo_proveedor, $codigo_barras, $stock_disponible, $stock_transito, $precio_fob, $precio_nac, $precio_ref, $id_proveedor, $agrupacion)
{
 if ( (item_scan_oblig($id_item)) and ($codigo_barras == "") )
 {
  // Si falta alguno de los campos requeridos.
  //
  $mensaje = "Error: Debe ingresar los items marcados con *.";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //

  if(obtener_tipo_proveedor($id_item) == "EXTRANJERO")
  {
	//Calculo los precios nac y ref a partir del precio_fob ingresado si es un item de proveedor extranjero
	//
  	$id_categoria  = obtener_categoria($id_item);
  	$precio_nac = $precio_fob + ($precio_fob * porcentaje_impuesto_categoria($id_categoria)/100);
  	$precio_ref = $precio_nac * precio_dolar();
  }

  $codigo_proveedor = addslashes(trim(strtoupper($codigo_proveedor)));
  if ($codigo_barras == "") $codigo_barras = 'NULL';
  else $codigo_barras = "\"" . addslashes(trim($codigo_barras)) . "\"";
  if ($precio_fob == "") $precio_fob = 'NULL';
  if ($precio_nac == "") $precio_nac = 'NULL';
  if ($precio_ref == "") $precio_ref = 'NULL';

  get_item_data($datos, $id_item);
  
  // solo si los precios y los ocultos eran iguales updatear los ocultos con el nuevo precio ingresado
  $oculto_fob = ($datos[6]==$datos[9]) ? $precio_fob : $datos[9];
  
  $oculto_nac = ($datos[7]==$datos[10]) ? $precio_nac : $datos[10];
  
  log_modificacion_item($id_item, $precio_fob, $precio_nac, $precio_ref, $datos[4], $stock_disponible);
  
  $query = "UPDATE Item SET
	codigo_proveedor = \"$codigo_proveedor\",
	codigo_barras = $codigo_barras,
	stock_disponible = $stock_disponible,
	stock_transito = $stock_transito,
	precio_fob = $precio_fob,
	precio_nac = $precio_nac,
	precio_ref = $precio_ref,
	oculto_fob = $oculto_fob,
	oculto_nac = $oculto_nac,
	id_proveedor = $id_proveedor,
	agrupacion_contable = $agrupacion
  WHERE
	Item.id_item = $id_item";

  if (!($result = mysql_query($query)))
  {
   // Si hay un error al insertar los datos en la base.
   //
   $mensaje = "Error: El item no pudo ser actualizado. Motivo posible: El codigo de barras del item ya existia.";// . mysql_error();
   return FALSE;
  }
  else
  {
   // Si se puede insertar los campos en la base.
   //
   $mensaje = "El item ha sido actualizado.";
   return TRUE;
  }
 }
}

function porcentaje_impuesto_categoria($id_categoria)
{
	$query = "SELECT porc_impuesto FROM Categoria WHERE id_categoria=$id_categoria";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}

function precio_dolar()
{
	$query = "SELECT precio_dolar FROM DolarHoy WHERE id_dolar=(SELECT MAX(id_dolar) FROM DolarHoy)";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}

function obtener_categoria($id_item)
{
	$query = "SELECT id_categoria FROM Item WHERE id_item=$id_item";
	$result = mysql_query($query);
 	$row = mysql_fetch_array($result);
 	return $row[0];
}

function obtener_proveedores($provname)
{
	$query = "SELECT id_proveedor, proveedor FROM Proveedor ORDER BY proveedor";
	$result = mysql_query($query);

	$opcionesprov="";
	while($row = mysql_fetch_array($result)){
		if($row[1] <> $provname)
			$opcionesprov .= "<option value=\"$row[0]\">$row[1]</option>\n";
	}

	return $opcionesprov;
}

function obtener_id_proveedor($provname)
{
	$query = "SELECT id_proveedor FROM Proveedor " .
			"WHERE proveedor = '$provname'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado,
 * a partir del id_proveedor pasado como parametro
 */
function obtener_tipo_proveedor($id_item){
	$query = "SELECT pais FROM pais, proveedor, item
		  WHERE pais.id_pais = proveedor.id_pais AND
		  		item.id_proveedor = proveedor.id_proveedor AND
				item.id_item = $id_item";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

//Fin Funciones



if ($formname == "item_datosmodificar")
{
 if (update_item($mensaje, $id_item, $codigo_proveedor, $codigo_barras, $stock_disponible, $stock_transito, $precio_fob, $precio_nac, $precio_ref, $id_proveedor, $agrupacion))
 {
  $focus = "forms[0].id_subproducto";
  $subproducto = get_subproducto_opt(0);
  if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

  $var = array("mensaje" => $mensaje,
    "subproducto" => $subproducto,
    "focus" => $focus
  );

  eval_html('producto_modificacion_99.html', $var);
 }
 else
 {
  get_item_data($datos, $id_item);
  $catname = $datos[0];
  $provname = $datos[1];
  $codigo_proveedor = $datos[2];
  $codigo_barras = $datos[3];
  $stock_disponible = $datos[4];
  $stock_transito = $datos[5];
  $precio_fob = $datos[6];
  $precio_nac = $datos[7];
  $precio_ref = $datos[8];
  $oculto_fob = $datos[9];
  $oculto_nac = $datos[10];
  $old_precio_fob = $datos[6];
  $old_precio_nac = $datos[7];
  $id_subproducto = $id_item;
  if (item_scan_oblig($id_item))
  {
   $barras_class = "obligatorio";
   $barras_sign = "*";
  }
  else
  {
   $barras_class = "opcional";
   $barras_sign = "";
  }


  if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

  $var = array("mensaje" => $mensaje,
   "catname" => $catname,
   "provname" => $provname,
   "opcionesprov" => obtener_proveedores($provname),
   "id_prov" => obtener_id_proveedor($provname),
   "codigo_proveedor" => $codigo_proveedor,
   "codigo_barras" => $codigo_barras,
   "stock_disponible" => $stock_disponible,
   "stock_transito" => $stock_transito,
   "precio_fob" => $precio_fob,
   "precio_nac" => $precio_nac,
   "precio_ref" => $precio_ref,
   "oculto_fob" => $oculto_fob,
   "oculto_nac" => $oculto_nac,
   "old_precio_fob" => $old_precio_fob,
   "old_precio_nac" => $old_precio_nac,
   "id_subproducto" => $id_subproducto,
   "barras_class" => $barras_class,
   "barras_sign" => $barras_sign,
   "agrupacion" => get_item_agrupacion_contable($id_item),
   "focus" => $focus,
   );

  eval_html('producto_datosmodificar_99.html', $var);
 }
}
elseif ($formname == "item_modificacion")
{
 get_item_data($datos, $id_item);
 $catname = $datos[0];
 $provname = $datos[1];
 $codigo_proveedor = $datos[2];
 $codigo_barras = $datos[3];
 $stock_disponible = $datos[4];
 $stock_transito = $datos[5];
 $precio_fob = $datos[6];
 $precio_nac = $datos[7];
 $precio_ref = $datos[8];
 $oculto_fob = $datos[9];
 $oculto_nac = $datos[10];
 $old_precio_fob = $datos[6];
 $old_precio_nac = $datos[7];
 $id_subproducto = $id_item;
 if (item_scan_oblig($id_item))
 {
  $barras_class = "obligatorio";
  $barras_sign = "*";
 }
 else
 {
  $barras_class = "opcional";
  $barras_sign = "";
 }

  //Segun que tipo de proveedor sea muestro el input correspondiente en precio
  //
  if(obtener_tipo_proveedor($id_item) == "EXTRANJERO")
  {
	$precio_fob = "<input type=\"text\" value=\"$precio_fob\" size=\"10\" name=\"precio_fob\" id=\"precio_fob\" class=\"opcional\">";
  }
  else
  {
	$precio_ref = "<input type=\"text\" value=\"$precio_ref\" size=\"10\" name=\"precio_ref\" id=\"precio_ref\" class=\"opcional\">";
  }

//if (mensaje != "") $mensaje = "<script type=\"text/javascript\">alert(\"$mensaje\")</script>";

 $var = array("mensaje" => $mensaje,
  "catname" => $catname,
  "provname" => $provname,
  "opcionesprov" => obtener_proveedores($provname),
  "id_prov" => obtener_id_proveedor($provname),
  "codigo_proveedor" => $codigo_proveedor,
  "codigo_barras" => $codigo_barras,
  "stock_disponible" => $stock_disponible,
  "stock_transito" => $stock_transito,
  "precio_fob" => $precio_fob,
  "precio_nac" => $precio_nac,
  "precio_ref" => $precio_ref,
  "oculto_fob" => $oculto_fob,
  "oculto_nac" => $oculto_nac,
  "old_precio_fob" => $old_precio_fob,
  "old_precio_nac" => $old_precio_nac,
  "id_subproducto" => $id_subproducto,
  "barras_class" => $barras_class,
  "barras_sign" => $barras_sign,
  "agrupacion" => get_item_agrupacion_contable($id_item),
  "focus" => $focus,
  );

 eval_html('producto_datosmodificar_99.html', $var);
}

?>

