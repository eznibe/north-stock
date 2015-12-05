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


$mensaje = "";
$focus = "forms[0].transac";

//dump($_POST);

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

$rango_periodo = $_POST['rango_periodo'];  //El valor a leer para atras en el periodo
$tipo_periodo  = $_POST['tipo_periodo'];   //Si es en dias, meses o anos


if (isset($_POST['opcion']) && !empty($_POST['opcion'])) {
	$opcion = $_POST['opcion'];
}
else {
	$opcion = 0;
}


if ( ($transac == 'comprados') or ($transac == 'Compras') )
{
 $transac = 'Compras';
}
else if (($transac == 'consumidos') or ($transac == 'Consumos'))
{
 $transac = 'Consumos';
}
else {
 $transac = 'Todos';
}


$fecha_ini = $ano_ini . $mes_ini . $dia_ini;
$fecha_fin = $ano_fin . $mes_fin . $dia_fin;


$opciones = "<select name=\"opcion\" id=\"opcion\" class=\"obligatorio\">\n";

//arma el codigo correspondiente si elegio busqueda entre fechas o por periodo
if($tipo_rango=="fechas")      		   $codigo = armar_listar_entre_fechas($transac, $dia_ini, $mes_ini, $ano_ini, $dia_fin, $mes_fin, $ano_fin);
else if($tipo_rango=="periodo") 	   $codigo = armar_listar_por_periodo($transac, $rango_periodo, $tipo_periodo);
else if($tipo_rango=="fechas_periodo") $codigo = armar_listar_entre_fechas_por_periodo($transac, $tipo_periodo, $dia_ini, $mes_ini, $ano_ini, $dia_fin, $mes_fin, $ano_fin);

$_SESSION['tipo_rango'] = $tipo_rango;


switch ($tipo)
{
case 'todos':
	$condicion = "";
	$opciones = "";
	break;

case 'grupo':
	$condicion = "tal que grupo = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_group_opt($opcion) . "</select>";
	break;

case 'proveedor':
	$condicion = "tal que proveedor = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_proveedor_opt($opcion) . "</select>";
	break;

case 'categoria':
	$condicion = "tal que producto = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_categoria_opt($opcion) . "</select>";
	break;

case 'item':
	$condicion = "tal que item = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_subproducto_opt($opcion) . "</select>";
	break;

case 'usuario':
	$condicion = "tal que usuario = ";
	$opciones = $opciones . "<option value=\"0\">seleccionar</option>" . get_usuario_opt($opcion) . "</select>";
	break;

}


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


function calcular_fecha_inicio($rango_periodo,$tipo_periodo)
{

if($tipo_periodo=="anos") return date("Ymd",mktime(0, 0, 0, date("m") , date("d"), date("Y")-$rango_periodo));
if($tipo_periodo=="meses") return date("Ymd",mktime(0, 0, 0, date("m")-$rango_periodo , date("d"), date("Y")));
if($tipo_periodo=="dias") return date("Ymd",mktime(0, 0, 0, date("m") , date("d")-$rango_periodo, date("Y")));

}
