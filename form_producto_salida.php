<?php

include 'main.php';
include 'dbutils.php';

check_session();
$valid_user = $_SESSION['valid_user'];

db_connect();

$focus = "forms[0].pproducto";
$mensaje = "";
$hits_mensaje = "";
$items = "";
$pproducto = isset($_POST['pproducto']) ? $_POST['pproducto'] : "";
$sproducto = isset($_POST['sproducto']) ? $_POST['sproducto'] : "";
$item = isset($_POST['item']) ? $_POST['item'] : "";
$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : "";
$stock_disponible = isset($_POST['stock_disponible']) ? $_POST['stock_disponible'] : "";
$producto = isset($_POST['producto']) ? $_POST['producto'] : "";
$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : "";
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
	item.id_item, 
	categoria.categoria, 
	proveedor.proveedor 
  FROM 
	categoria, 
	proveedor, 
	item 
  WHERE (
	(categoria.categoria LIKE \"%$pitem%\") AND 
	(categoria.id_categoria = item.id_categoria) AND 
	(proveedor.id_proveedor = item.id_proveedor)
  ) 
  ORDER BY 
	categoria.categoria";

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
        item.id_item,
        categoria.categoria,
        proveedor.proveedor,
        item.stock_disponible,
        unidad.unidad
  FROM
        categoria,
        proveedor,
        item,
        unidad
  WHERE (
        (item.codigo_barras LIKE \"$pitem\") AND
        (categoria.id_categoria = item.id_categoria) AND
        (proveedor.id_proveedor = item.id_proveedor) AND
        (unidad.id_unidad = categoria.id_unidad_visual)
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
	item.id_item, 
	categoria.categoria, 
	proveedor.proveedor, 
	item.stock_disponible, 
	unidad.unidad 
  FROM 
	categoria, 
	proveedor, 
	item, 
	unidad 
  WHERE (
	(item.id_item = $sproducto) AND 
 	(categoria.id_categoria = item.id_categoria) AND 
	(proveedor.id_proveedor = item.id_proveedor) AND 
	(unidad.id_unidad = categoria.id_unidad_visual)
  ) 
  ORDER BY 
	categoria.categoria";

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
	item 
  SET 
	item.stock_disponible = (item.stock_disponible - $cantidad) 
  WHERE 
	item.id_item = $item";
   $result = mysql_query($query);
   
   //log_trans($valid_user, 2, $item, $cantidad, strftime('%G-%m-%d')); 
   $fechaHoy = date('Y')."-".date('n')."-".date('d');
   
  // nota: el log del egreso se hace con la fecha que ingresa el usuario, por default es el current date
  if ($cantidad < 0) {
    $id_orden = ingresarordenManual($item, $cantidad * -1, $fecha);
    log_trans($valid_user, 1, $item, $cantidad * -1, $fecha, $id_orden); 
  } else {
    log_trans($valid_user, 2, $item, $cantidad, $fecha); 
  }
  
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


function ingresarordenManual($id_item, $cantidad, $fecha) {

  $cotiz_dolar = obtener_precio_dolar();

  $query = "INSERT INTO orden (fecha,	cotizacion_dolar,	id_status, descripcion) 
    VALUES ('$fecha', $cotiz_dolar, 2, 'orden desde descarga manual')";

  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  
  // id ultima orden ingresada
  $query = "SELECT id_orden FROM orden ORDER BY id_orden desc LIMIT 1";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  
  $id_orden = $row[0];

  // datos item
  $query = "SELECT round(precio_fob, 2), round(precio_ref, 2),
    case when pro.id_pais = 1 then 'AR$' when pro.id_pais > 1 then 'US$' end as moneda 
    FROM item i 
    JOIN proveedor pro on pro.id_proveedor = i.id_proveedor
    WHERE i.id_item = $id_item";

  $result = mysql_query($query);
  $row = mysql_fetch_array($result);

  $precio_fob = $row[0];
  $precio_ref = $row[1];
  $moneda = $row[2];

  $query = "INSERT INTO ordenitem (id_orden, id_item, cantidad, cantidad_pendiente, precio_fob, precio_ref, moneda) 
    VALUES ($id_orden, $id_item, $cantidad, 0, $precio_fob, $precio_ref, '$moneda')";

  $result = mysql_query($query);

  return $id_orden;
}


function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar from dolarhoy where id_dolar=(SELECT max(id_dolar) FROM dolarhoy)";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	//Devuelvo el precio del dolar actual
	return $row[0];
}
?>
