<?php

// Se encarga de armar y mostrar el listado con los opciones seleccionadas en las paginas previas

include 'main.php';
include 'dbutils.php';

include 'mostrar_tabla_fechas_por_periodo.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else {
	$imprimir = "<div class=\"imprimir\">
					<a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
				  </div>";	
}


$mensaje = "";
$focus = "forms[0].transac";

$transac = $_POST['transac'];
$tipo = $_POST['tipo'];

$tipo_rango = $_POST['tipo_rango'];  //Si se busca por fechas o por fechas por periodo o por periodo

$dia_ini = $_POST['dia_ini'];
$mes_ini = $_POST['mes_ini'];
$ano_ini = $_POST['ano_ini'];
$dia_fin = $_POST['dia_fin'];
$mes_fin = $_POST['mes_fin'];
$ano_fin = $_POST['ano_fin'];

if (isset($_POST['opcion'])) $opcion = $_POST['opcion'];


if ( ($transac == 'comprados') or ($transac == 'Compras') )
{
	$transac = 'Compras';
	$titulo_tipo = $transac;
	$id_accion = 1;
}
else if (($transac == 'consumidos') or ($transac == 'Consumos'))
{
	$transac = 'Consumos';
	$titulo_tipo = $transac;
	$id_accion = 2;
}
else {
	$transac = 'Todos';	
	$titulo_tipo = "Compras y Consumos";
}

//dump($_POST);

$fecha_ini = $ano_ini . $mes_ini . $dia_ini;
$fecha_fin = $ano_fin . $mes_fin . $dia_fin;

$titulo = "$titulo_tipo entre $dia_ini-$mes_ini-$ano_ini y $dia_fin-$mes_fin-$ano_fin";

$rango_periodo = $_POST['rango_periodo'];  //El valor a leer para atras en el periodo
$tipo_periodo  = $_POST['tipo_periodo'];   //Si es en dias, meses o anios

if($rango_periodo<>"")  //Si es distinto a vacio es porque se utiliza la busqueda por periodo
{
  $fecha_ini = calcular_fecha_inicio($rango_periodo,$tipo_periodo);
  $fecha_fin = date(Y).date(m).date(d);

  $titulo = "$titulo_tipo desde hace  $rango_periodo $tipo_periodo  hasta el ".date(d)."-".date(m)."-".date(Y);
}


$opciones = "<select name=\"opcion\" id=\"opcion\" class=\"obligatorio\">\n";

switch ($tipo)
{
case 'todos':
	$query_fin = ") ORDER BY Categoria.categoria";
	break;

case 'grupo':
	$query_fin = "AND (Categoria.id_grupo = $opcion) ) ORDER BY Categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " por grupo " . get_group($opcion);
	break;

case 'proveedor':
	$query_fin = "AND (Item.id_proveedor = $opcion) ) ORDER BY Categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del proveedor " . get_proveedor($opcion);
	break;

case 'categoria':
	$query_fin = "AND (Item.id_categoria = $opcion) ) ORDER BY Categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del producto " . get_categoria($opcion);
	break;

case 'item':
	$query_fin = "AND (Item.id_item = $opcion) ) ORDER BY Categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del item " . get_item($opcion);
	break;

case 'usuario':
	if (isset($opcion)) $query_fin = "AND (Log.username = '".get_usuario($opcion,1)."') ) ORDER BY Categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " ralizadas por usuario " . get_usuario($opcion,2);
	break;

}



 $tipo_rango = $_SESSION['tipo_rango'];
 
 if($tipo_rango == "fechas_periodo"){
 	// Muestro la tabla de items entre fechas con corte de control segun tipo_periodo
 	//
	mostrar_tabla_fechas_por_periodo($tipo_periodo,$tipo,$opcion,$id_accion,$fecha_ini,$fecha_fin,
									 $transac, $tipo_rango, $opcion, $rango_periodo);
 }
 else
 {
	 //
	 if ($transac == 'Consumos') {
	  $query = "SELECT
		CONCAT(Categoria.categoria, \" - \", Proveedor.proveedor) AS articulo,
		DATE_FORMAT(Log.fecha, '%d-%m-%Y') AS fech,
		Log.cantidad,
		Unidad.unidad,
		$id_accion as accion
	  FROM
		Log,
		Item,
		Categoria,
		Proveedor,
		Unidad
	  WHERE (
		(Item.id_item = Log.id_item) AND
		(Log.id_accion = $id_accion) AND
		(Categoria.id_categoria = Item.id_categoria) AND
		(Proveedor.id_proveedor = Item.id_proveedor) AND
		(Unidad.id_unidad = Categoria.id_unidad_visual) AND
		(Log.fecha >= $fecha_ini) AND
		(Log.fecha <= $fecha_fin)
	    ";
	  
	  $query = $query . $query_fin;
	 }
	 if ($transac == 'Compras') {
	  $query = "SELECT
		CONCAT(Categoria.categoria, \" - \", Proveedor.proveedor) AS articulo,
		DATE_FORMAT(Log.fecha, '%d-%m-%Y') AS fech,
		Log.cantidad,
		CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
		$id_accion as accion
	  FROM
		Log,
		Item,
		Categoria,
		Proveedor,
		Unidad
	  WHERE (
		(Item.id_item = Log.id_item) AND
		(Log.id_accion = $id_accion) AND
		(Categoria.id_categoria = Item.id_categoria) AND
		(Proveedor.id_proveedor = Item.id_proveedor) AND
		(Unidad.id_unidad = Item.id_unidad_compra) AND
		(Log.fecha >= $fecha_ini) AND
		(Log.fecha <= $fecha_fin)
	    ";
	  
	  $query = $query . $query_fin;
	 }
	 if($transac == 'Todos') {
	 	$query = crearQueryTodos($query_fin, $fecha_ini, $fecha_fin);
	 }
	 
//	 dump($query);
	
	 $result = mysql_query($query);
	 while ($row = mysql_fetch_array($result))
	 {
	 	if($transac <> 'Todos')	
	 		$listado = $listado . "<tr class=\"provlistrow\"><td class=\"list\">$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>\n";
	 	else
	 		$listado = $listado . "<tr class=\"provlistrow\"><td class=\"list\">$row[0]</td><td>$row[1]</td><td class='accion_$row[4]'>$row[2]</td><td>$row[3]</td></tr>\n";
	 }
	 
	 $var = array("mensaje" => $mensaje,
				  "listado" => $listado,
				  "imprimir" => $imprimir,
				  "titulo" => $titulo,
	 			  "transac" => $transac, 
				  "tipo" => $tipo, 
				  "tipo_rango" => $tipo_rango,
	 			  "opcion" => $opcion,
				  "dia_ini" => $dia_ini,
				  "mes_ini" => $mes_ini,
				  "ano_ini" => $ano_ini,
				  "dia_fin" => $dia_fin,
				  "mes_fin" => $mes_fin,
				  "ano_fin" => $ano_fin,
				  "rango_periodo" => $rango_periodo,
				  "tipo_periodo" => $tipo_periodo);
	
	 eval_html('listar_fechas_tercero.html', $var);
 }

 function crearQueryTodos($query_fin, $fecha_ini, $fecha_fin) {
 	
 	$query = "(SELECT
				CONCAT(Categoria.categoria, \" - \", Proveedor.proveedor) AS articulo,
				DATE_FORMAT(Log.fecha, '%d-%m-%Y') AS fech,
				Log.cantidad,
				Unidad.unidad,
				'2' as accion, Categoria.categoria as cate, Log.fecha as fechaOrdenar
			  FROM
				Log,
				Item,
				Categoria,
				Proveedor,
				Unidad
			  WHERE (
				(Item.id_item = Log.id_item) AND
				(Log.id_accion = 2) AND
				(Categoria.id_categoria = Item.id_categoria) AND
				(Proveedor.id_proveedor = Item.id_proveedor) AND
				(Unidad.id_unidad = Categoria.id_unidad_visual) AND
				(Log.fecha >= $fecha_ini) AND
				(Log.fecha <= $fecha_fin)
			    ";
 	
 	$query .= $query_fin . ")";
 	
 	$query .= " UNION ";
 	
 	$query .= "(SELECT
				CONCAT(Categoria.categoria, \" - \", Proveedor.proveedor) AS articulo,
				DATE_FORMAT(Log.fecha, '%d-%m-%Y') AS fech,
				Log.cantidad,
				CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
				'1' as accion, Categoria.categoria as cate, Log.fecha as fechaOrdenar
			  FROM
				Log,
				Item,
				Categoria,
				Proveedor,
				Unidad
			  WHERE (
				(Item.id_item = Log.id_item) AND
				(Log.id_accion = 1) AND
				(Categoria.id_categoria = Item.id_categoria) AND
				(Proveedor.id_proveedor = Item.id_proveedor) AND
				(Unidad.id_unidad = Item.id_unidad_compra) AND
				(Log.fecha >= $fecha_ini) AND
				(Log.fecha <= $fecha_fin)
			    ";
 	
 	$query .= $query_fin . ")";
 	
 	$query .= " ORDER BY cate, fechaOrdenar";
 	
 	return $query;
 }

function calcular_fecha_inicio($rango_periodo,$tipo_periodo)
{

if($tipo_periodo=="anos") return date("Ymd",mktime(0, 0, 0, date("m") , date("d"), date("Y")-$rango_periodo));
if($tipo_periodo=="meses") return date("Ymd",mktime(0, 0, 0, date("m")-$rango_periodo , date("d"), date("Y")));
if($tipo_periodo=="dias") return date("Ymd",mktime(0, 0, 0, date("m") , date("d")-$rango_periodo, date("Y")));

}
