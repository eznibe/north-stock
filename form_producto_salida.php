<?php

include 'main.php';
include 'dbutils.php';

check_session();
$valid_user = $_SESSION['valid_user'];

db_connect();

$focus = "forms[0].pproducto";
$mensaje = "";
$hits_mensaje = "";
$pproducto = $_POST['pproducto'];
$sproducto = $_POST['sproducto'];
$item = $_POST['item'];
$cantidad = $_POST['cantidad'];
$stock_disponible = $_POST['stock_disponible'];
$producto = $_POST['producto'];
$unidad = $_POST['unidad'];
$formname = $_POST['formname'];

// setear la fecha por default (current date si no hay nada seleccionado por el usuario)
$dia_ini = isset($_POST['dia_ini']) ? $_POST['dia_ini'] : sprintf("%02d", date("d"));
$mes_ini = isset($_POST['mes_ini']) ? $_POST['mes_ini'] : sprintf("%02d", date("m"));
$ano_ini = isset($_POST['ano_ini']) ? $_POST['ano_ini'] : date("Y");

$fecha = $ano_ini . "-" . $mes_ini . "-" . $dia_ini;
$fecha_select = armar_select_fechas($dia_ini, $mes_ini, $ano_ini);


function busca_item(&$mensaje, &$items, $pitem)
{
 $query = "SELECT 
	Item.id_item, 
	Categoria.categoria, 
	Proveedor.proveedor 
  FROM 
	Categoria, 
	Proveedor, 
	Item 
  WHERE (
	(Categoria.categoria LIKE \"%$pitem%\") AND 
	(Categoria.id_categoria = Item.id_categoria) AND 
	(Proveedor.id_proveedor = Item.id_proveedor)
  ) 
  ORDER BY 
	Categoria.categoria";

 $result = mysql_query($query);

 $items = "";
 $num_results = mysql_num_rows($result);
 if ($num_results == 0)
 {
  $mensaje = "No se encontraron coincidencias.";
  return FALSE;
 }
 elseif ($num_results == 1)
 {
  $mensaje = "Se encontro 1 coincidencia.";
 }
 else $mensaje = "Se encontraron $num_results coincidencias.";

 while ($row = mysql_fetch_array($result))
 {
  $items = $items . "<option value=\"" . $row[0] . "\">" . htmlspecialchars(stripslashes($row[1])) . "-" . htmlspecialchars(stripslashes($row[2])) ."</option>\n";
 }
 return TRUE;
}
 
function busca_barras(&$mensaje, &$datos, $pitem)
{
 $query = "SELECT 
        Item.id_item,
        Categoria.categoria,
        Proveedor.proveedor,
        Item.stock_disponible,
        Unidad.unidad
  FROM
        Categoria,
        Proveedor,
        Item,
        Unidad
  WHERE (
        (Item.codigo_barras LIKE \"$pitem\") AND
        (Categoria.id_categoria = Item.id_categoria) AND
        (Proveedor.id_proveedor = Item.id_proveedor) AND
        (Unidad.id_unidad = Categoria.id_unidad_visual)
  )";
 $result = mysql_query($query);
 $num_results = mysql_num_rows($result);
 $datos = mysql_fetch_array($result);

 if ($num_results == 1) return true;
 else return false;
}

if ($formname == "busca_producto")
{
 if (busca_barras($hits_mensaje, $row, $pproducto))
 {
  $item = $row[0];
  $producto = $row[1] . " - " . $row[2];
  $stock_disponible = "$row[3]";
  $unidad = "(<em>" . strtoupper($row[4]) . "</em>)";
  $focus = "forms[2].cantidad";
 }
 elseif (busca_item($hits_mensaje, $items, $pproducto))
 {
  $item = "";
  $producto = "";
  $stock_disponible = "";
  $unidad = "";
  $focus = "forms[1].sproducto";
 }
}
elseif ($formname == "select_producto")
{
 $query = "SELECT 
	Item.id_item, 
	Categoria.categoria, 
	Proveedor.proveedor, 
	Item.stock_disponible, 
	Unidad.unidad 
  FROM 
	Categoria, 
	Proveedor, 
	Item, 
	Unidad 
  WHERE (
	(Item.id_item = $sproducto) AND 
 	(Categoria.id_categoria = Item.id_categoria) AND 
	(Proveedor.id_proveedor = Item.id_proveedor) AND 
	(Unidad.id_unidad = Categoria.id_unidad_visual)
  ) 
  ORDER BY 
	Categoria.categoria";

 $result = mysql_query($query);
 $row = mysql_fetch_array($result);

 $item = $row[0];
 $producto = $row[1] . " - " . $row[2];
 $stock_disponible = "$row[3]";
 $unidad = "(<em>" . strtoupper($row[4]) . "</em>)";
 $focus = "forms[2].cantidad";
}
elseif ($formname == "producto_salida")
{
 if ($cantidad == "")
 {
  $mensaje = "<em class=\"error\">Error: Debe ingresar los items marcados con *.</em>";
  $focus = "forms[2].cantidad";
 }
 else
 {
  if (($stock_disponible - $cantidad) < 0)
  {
   $mensaje = "<em class=\"error\">Error: No se puede retirar esa cantidad de $producto.</em>";
   $item = "";
   $producto = "";
   $stock_disponible = "";
   $unidad = "";
   $focus = "forms[0].pproducto";
  }
  else
  {
   $query = "UPDATE 
	Item 
  SET 
	Item.stock_disponible = (Item.stock_disponible - $cantidad) 
  WHERE 
	Item.id_item = $item";
   $result = mysql_query($query);
   
   //log_trans($valid_user, 2, $item, $cantidad, strftime('%G-%m-%d')); 
   $fechaHoy = date(Y)."-".date(n)."-".date(d);
   
   // nota: el log del egreso se hace con la fecha que ingresa el usuario, por default es el current date
   log_trans($valid_user, 2, $item, $cantidad, $fecha); 
  
   $item = "";
//   $stock_disponible = "";
   $focus = "forms[0].pproducto";
   $mensaje = "Acaba de retirar $cantidad $unidad $producto."."<br>Quedan disponibles ".($stock_disponible - $cantidad)." $unidad<p>\n";
   $stock_disponible = "";
   $producto = "";
   $unidad = "";
  }
 }
}

//dump($fecha_select);

$var = array("items" => $items,
        "item" => $item,
        "mensaje" => $mensaje,
        "hits_mensaje" => $hits_mensaje,
        "producto" => $producto,
        "stock_disponible" => $stock_disponible,
        "unidad" => $unidad,
		"fecha" => $fecha_select,
        "focus" => $focus);
eval_html('producto_salida.html', $var);

?>
