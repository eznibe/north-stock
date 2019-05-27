<?php

include 'main.php';
include 'dbutils.php';

include 'armar_listar_fechas_segundo.php';

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

$despacho = $_POST['despacho'];
$id_orden = $_POST['id_orden'];

$dia_ini = $_POST['dia_ini'];
$mes_ini = $_POST['mes_ini'];
$ano_ini = isset($_POST['ano_ini']) ? $_POST['ano_ini'] : date("Y");
$dia_fin = isset($_POST['dia_fin']) ? $_POST['dia_fin'] : sprintf("%02d", date("d"));
$mes_fin = isset($_POST['mes_fin']) ? $_POST['mes_fin'] : sprintf("%02d", date("m"));
$ano_fin = isset($_POST['ano_fin']) ? $_POST['ano_fin'] : date("Y");



$fecha_ini = $ano_ini . $mes_ini . $dia_ini;
$fecha_fin = $ano_fin . $mes_fin . $dia_fin;


//arma el codigo correspondiente si elegio busqueda entre fechas o por periodo
$fechas = armar_listar_entre_fechas('despachos', $dia_ini, $mes_ini, $ano_ini, $dia_fin, $mes_fin, $ano_fin);



 $focus = "forms[0].dia_ini";
 $var = array("mensaje" => $mensaje,
			  "fechas" => $fechas,
        "despacho" => $despacho,
        "id_orden" => $id_orden,
			  "opciones" => $opciones,
		      "focus" => $focus,
		     );
// var_dump($var);
 eval_html('listar_despachos_filtros.html', $var);
