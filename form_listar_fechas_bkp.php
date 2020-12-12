<?php

include 'main.php';
include 'dbutils.php';

include 'armar_listar_fechas_segundo.php';
include 'mostrar_tabla_fechas_por_periodo.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else {
	$imprimir = "<div class=\"imprimir\">
					<a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
				  </div>";	
}

$volver = "<div class=\"volver\">
			 <a class=\"volver\" href=\"form_listar_fechas.php\">Volver</a>
		   </div>";


$mensaje = "";
$focus = "forms[0].transac";

dump($_POST);

$formname = $_POST['formname'];
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
 $id_accion = 1;
}
else
{
 $transac = 'Consumos';
 $id_accion = 2;
}


//$fecha_ini = $ano_ini . $mes_ini . $dia_ini . "000000";
//$fecha_fin = $ano_fin . $mes_fin . $dia_fin . "999999";
$fecha_ini = $ano_ini . $mes_ini . $dia_ini;
$fecha_fin = $ano_fin . $mes_fin . $dia_fin;

$titulo = "$transac entre $dia_ini-$mes_ini-$ano_ini y $dia_fin-$mes_fin-$ano_fin";

$rango_periodo = $_POST['rango_periodo'];  //El valor a leer para atras en el periodo
$tipo_periodo  = $_POST['tipo_periodo'];   //Si es en dias, meses o a�os

if($rango_periodo<>"")  //Si es distinto a vacio es porque se utiliza la busqueda por periodo
{
  $fecha_ini = calcular_fecha_inicio($rango_periodo,$tipo_periodo);
  $fecha_fin = date(Y).date(m).date(d);

  $titulo = "$transac desde hace  $rango_periodo $tipo_periodo  hasta el ".date(d)."-".date(m)."-".date(Y);
}


$opciones = "<select name=\"opcion\" id=\"opcion\" class=\"obligatorio\">\n";

//arma el codigo correspondiente si elegio busqueda entre fechas o por periodo
if($tipo_rango=="fechas")      		   $codigo = armar_listar_fechas_segundo($transac,1);
else if($tipo_rango=="periodo") 	   $codigo = armar_listar_fechas_segundo($transac,2);
else if($tipo_rango=="fechas_periodo") $codigo = armar_listar_fechas_segundo($transac,3);

switch ($tipo)
{
case 'todos':
	$condicion = "";
	$opciones = "";
	$query_fin = ") ORDER BY categoria.categoria";
	break;

case 'grupo':
	$condicion = "tal que grupo = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_group_opt(0) . "</select>";
	$query_fin = "AND (categoria.id_grupo = $opcion) ) ORDER BY categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " por grupo " . get_group($opcion);
	break;

case 'proveedor':
	$condicion = "tal que proveedor = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_proveedor_opt(0) . "</select>";
	$query_fin = "AND (item.id_proveedor = $opcion) ) ORDER BY categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del proveedor " . get_proveedor($opcion);
	break;

case 'categoria':
	$condicion = "tal que producto = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_categoria_opt(0) . "</select>";
	$query_fin = "AND (item.id_categoria = $opcion) ) ORDER BY categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del producto " . get_categoria($opcion);
	break;

case 'item':
	$condicion = "tal que item = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_subproducto_opt(0) . "</select>";
	$query_fin = "AND (item.id_item = $opcion) ) ORDER BY categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " del item " . get_item($opcion);
	break;

case 'usuario':
	$condicion = "tal que usuario = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_usuario_opt(0) . "</select>";
	if (isset($opcion)) $query_fin = "AND (log.username = '".get_usuario($opcion,1)."') ) ORDER BY categoria.categoria";
	if (isset($opcion)) $titulo = $titulo . " ralizadas por usuario " . get_usuario($opcion,2);
	break;

}

if ($formname == 'listar_fechas_segundo')
{
//echo "<br><br>$transac<br><br> y tipo rango: $rango<p>";

 $tipo_rango = $_SESSION['tipo_rango'];
 if($tipo_rango == "fechas_periodo"){
 	// Muestro la tabla de items entre fechas con corte de control segun tipo_periodo
 	//
 	mostrar_tabla_fechas_por_periodo($tipo_periodo,$tipo,$opcion,$id_accion,$fecha_ini,$fecha_fin);
 }

 else
 {
 //
 if ($transac == 'Consumos') {
  $query = "SELECT
	CONCAT(categoria.categoria, \" - \", proveedor.proveedor) AS articulo,
	DATE_FORMAT(log.fecha, '%d-%m-%Y') AS fech,
	log.cantidad,
	unidad.unidad
  FROM
	log,
	item,
	categoria,
	proveedor,
	unidad
  WHERE (
	(item.id_item = log.id_item) AND
	(log.id_accion = $id_accion) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = categoria.id_unidad_visual) AND
	(log.fecha >= $fecha_ini) AND
	(log.fecha <= $fecha_fin)
    ";
 }
 if ($transac == 'Compras') {
  $query = "SELECT
	CONCAT(categoria.categoria, \" - \", proveedor.proveedor) AS articulo,
	DATE_FORMAT(log.fecha, '%d-%m-%Y') AS fech,
	log.cantidad,
	CONCAT(unidad.unidad,'(',item.factor_unidades,')')
  FROM
	log,
	item,
	categoria,
	proveedor,
	unidad
  WHERE (
	(item.id_item = log.id_item) AND
	(log.id_accion = $id_accion) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra) AND
	(log.fecha >= $fecha_ini) AND
	(log.fecha <= $fecha_fin)
    ";
 }
 $query = $query . $query_fin;

 $result = mysql_query($query);
 while ($row = mysql_fetch_array($result))
 {
  $listado = $listado . "<tr class=\"provlistrow\"><td class=\"list\">$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>\n";
 }
 $var = array("mensaje" => $mensaje,
	"listado" => $listado,
	"imprimir" => $imprimir,
 	"volver" => $volver,
	"titulo" => $titulo);

 eval_html('listar_fechas_tercero.html', $var);
 }
}
else
{
// echo "<br><br>Distinito<br><br>";
$_SESSION['tipo_rango'] = $tipo_rango;

 $focus = "forms[0].dia_ini";
 $var = array("mensaje" => $mensaje,
	"codigo" => $codigo,
	"transac" => $transac,
	"tipo" => $tipo,
	"condicion" => $condicion,
	"opciones" => $opciones,
   "focus" => $focus,
   );

 eval_html('listar_fechas_segundo.html', $var);
}


function calcular_fecha_inicio($rango_periodo,$tipo_periodo)
{

if($tipo_periodo=="anos") return date("Ymd",mktime(0, 0, 0, date("m") , date("d"), date("Y")-$rango_periodo));
if($tipo_periodo=="meses") return date("Ymd",mktime(0, 0, 0, date("m")-$rango_periodo , date("d"), date("Y")));
if($tipo_periodo=="dias") return date("Ymd",mktime(0, 0, 0, date("m") , date("d")-$rango_periodo, date("Y")));

}
