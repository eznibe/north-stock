<?php

include_once 'main.php';
include_once 'dbutils.php';

check_session();

db_connect();

if ( isset($_GET['id_prevision']) ) {
  $id_prevision = $_GET['id_prevision'];
} 
else if (isset($_POST['id_prevision'])) {
	$id_prevision = $_POST['id_prevision'];
} else {
  global $id_prevision;
}

$formname = isset($_POST['formname']) ? $_POST['formname'] : "";

$mensaje = "";


if ($formname == "prevision_update") {
  $id_prevision_item = $_POST['id_prevision_item'];
	$cantidad = $_POST['cantidad'];
  
	update_prevision($id_prevision_item, $cantidad);
}
else if ($formname == "prevision_item_nuevo") {
  $id_item = $_POST['id_item'];
  $cantidad = $_POST['cantidad'];
  $precio = $_POST['precio'];
  $moneda = $_POST['moneda'];
  agregar_prevision_item($id_prevision, $id_item, $cantidad, $precio, $moneda);
}

showPrevisionDetailsScreen($id_prevision);

function showPrevisionDetailsScreen($id_prevision) {
  $username = $_SESSION['valid_user'];
  
  $focus = "numero_orden";

  // Datos prevision
  $query = "SELECT
    DATE_FORMAT(p.fecha_entrega, '%d-%m-%Y') AS fecha_entrega,
    p.numero_orden,
    p.cliente,
    p.descripcion,
    p.fecha_descarga
    FROM
      prevision p
    WHERE 
      p.id_prevision = $id_prevision";
      
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  
  
  $fecha_entrega = $row[0];
  $numero_orden = $row[1];
  $cliente = $row[2];
  $descripcion = $row[3];
  $descargada = isset($row[4]) ? 'true' : 'false';

  // Prevision order items
  //
  $query = "SELECT
    DATE_FORMAT(p.fecha_entrega, '%d-%m-%Y') AS fecha_entrega,
    pro.proveedor,
    pi.id_prevision_item,
    pi.cantidad,
    c.categoria,
    CONCAT(u.unidad,'(',i.factor_unidades,')'),
    round(coalesce(i.precio_fob, i.precio_ref), 2) as precio,
    round((pi.cantidad * coalesce(i.precio_fob, i.precio_ref)), 2) as total,
    case when pro.id_pais = 1 then 'AR$' when pro.id_pais > 1 then 'US$' end as moneda,
    i.codigo_proveedor,
    (i.stock_disponible - pi.cantidad) as stock_despues_descarga
  FROM prevision p 
    JOIN previsionitem pi on p.id_prevision = pi.id_prevision
    JOIN item i on i.id_item = pi.id_item
    JOIN categoria c on c.id_categoria = i.id_categoria
    JOIN unidad u on u.id_unidad = i.id_unidad_compra
    JOIN proveedor pro on pro.id_proveedor = i.id_proveedor
  WHERE 
    p.id_prevision = $id_prevision
  ORDER BY
    c.categoria";
  
  $count=0; $previsionitems="";
  $result = mysql_query($query);
  $header = "";
  $stock_suficiente = 'true';

  while ($row = mysql_fetch_array($result))
  {
    $previsionitems = $previsionitems . "<tr class=\"provlistrow\">"
      .($descargada === 'false' 
        ? "<td class=\"centrado\"><a class=\"list\" onclick=\"update_prevision($row[2]);\">$row[4]</a></td>" 
        : "<td class=\"centrado\">$row[4]</td>").
      "
      <td class=\"centrado\">$row[9]</td>
      <td class=\"centrado\">$row[3] <input type='hidden' name='cantidad$count' id='cantidad$count' value='$row[3]'/> <input type='hidden' name='id_prevision_item$count' id='id_prevision_item$count' value='$row[2]'/></td>
      <td class=\"centrado\">$row[5]</td>
      <td class=\"centrado\">$row[6]</td>
      <td class=\"centrado\">$row[7]</td>
      <td class=\"centrado\">$row[8]</td>
      </tr>\n";

    if (+$row[10] < 0) {
      $stock_suficiente = 'false';
    }

    $count++;
  }
  
  
  if (isset($fecha_entrega)) {
    $split_date = explode("-",$fecha_entrega);
    // dump($split_date);
    $diaopc = opciones_dia($split_date[0], true);
    $mesopc = opciones_mes($split_date[1], true);
    $anoopc = opciones_ano($split_date[2], true);
  } else {
    $diaopc = opciones_dia(null, true, true);
    $mesopc = opciones_mes(null, true, true);
    $anoopc = opciones_ano(null, true, true);
  }
  
  // totales_dos_decimales($total_dolar,$total_pesos);
  
  // $cotizacion_dolar = obtener_precio_dolar_orden($id_prevision);
  // $cotizacion_fecha = obtener_fecha_orden($id_prevision);
  
  $var = array(
    "header" => $header,
    "previsionitems" => $previsionitems,
    "cant_filas" => $count,
    "id_prevision" => $id_prevision,
    // "cotiz_dolar" => $cotizacion_dolar,
    // "cotiz_fecha" => $cotizacion_fecha,
    "fecha_entrega" => $fecha_entrega,
    "cliente" => $cliente,
    "numero_orden" =>  $numero_orden,
    "descripcion" => $descripcion,
    "stock_suficiente" => $stock_suficiente,
    "dia" => $diaopc,
    "mes" => $mesopc,
    "ano" => $anoopc,
    "descargada" => $descargada,
    "username" => $username,
    "focus" => $focus);
  
  eval_html('prevision_ver_ajax.php', $var);
}


// not used
function formEliminaritem($id_prevision, $id_prevision_item) {
  return "<td class=\"centrado\">
  <form action=\"prevision_update.php\" method=\"post\" target=\"_self\" name=\"prevision_update\">
    <input type=\"hidden\" value=\"prevision_update\" name=\"formname\" id=\"formname\">
    <input type=\"hidden\" value=\"$id_prevision\" name=\"id_prevision\" id=\"id_prevision\">
    <input type=\"hidden\" value=\"$id_prevision_item\" name=\"id_prevision_item\" id=\"id_prevision_item\">
    <input type=\"hidden\" value=\"prevision_update\" name=\"cantidad\" id=\"formname\">
    <button type=\"submit\" name=\"enviar\" value=\"enviar\">Eliminar</button>
  </form>
  </td>";
}

/**
 * Actualiza la cantidad (y cant pendiente) y le precio del item de la compra pasado
 * como parametro
 */
function update_prevision($id_prevision_item, $cantidad) {
	$query = "SELECT id_prevision, id_item FROM previsionitem WHERE id_prevision_item = $id_prevision_item";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

 	if ( ($cantidad == 0) or ($cantidad == "") )
 	{
    $query = "DELETE FROM previsionitem WHERE id_prevision_item = $id_prevision_item";

    // logueo item borrado de la prevision (28)
    log_trans($_SESSION['valid_user'], 28, $row[1], 0, date("Y-m-d"), $row[0]);
 	}
 	else
 	{
    $query = "UPDATE
        previsionitem
      SET
        cantidad = $cantidad
    WHERE
        id_prevision_item = $id_prevision_item";

    log_trans($_SESSION['valid_user'], 23, $row[1], $cantidad, date("Y-m-d"), $row[0]);
 	}
  $result = mysql_query($query);
}

/**
 * Agrega un nuevo item en la prevision dada
 */
function agregar_prevision_item($id_prevision, $id_item, $cantidad, $precio, $moneda)
{
	$insert = "INSERT INTO previsionitem (id_prevision, id_item, cantidad, moneda)
    VALUES ($id_prevision, $id_item, $cantidad, '$moneda')";
  $result = mysql_query($insert);

  log_trans($_SESSION['valid_user'], 21, $id_item, $cantidad, date("Y-m-d"), $id_prevision);
}


function obtener_precio_dolar_orden($id_prevision)
{
	$query = "SELECT cotizacion_dolar FROM orden WHERE id_orden = $id_prevision";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function obtener_fecha_orden($id_prevision)
{
	$query = "SELECT fecha FROM orden WHERE id_orden = $id_prevision";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function totales_dos_decimales(&$total_dolar,&$total_pesos)
{
	$total_dolar_aux = $total_dolar;
	$total_pesos_aux = $total_pesos;

	$total_dolar=""; $total_pesos="";

  $tok1 = strtok($total_dolar_aux,".");
  $total_dolar = $tok1;
  $tok1 = strtok(".\n\t");
  if($tok1<>""){
    $tok1 = substr($tok1,0,2);
    $total_dolar .= "," . $tok1;
  }
  $tok2 = strtok($total_pesos_aux,".");
  $total_pesos = $tok2;
  $tok2 = strtok(".\n\t");
  if($tok2<>""){
    $tok2 = substr($tok2,0,2);
    $total_pesos .= "," . $tok2;
  }

}

?>
