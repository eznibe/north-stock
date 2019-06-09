<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<div class=\"imprimir\">
		        	<a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
		          </div>";



$dia_ini = isset($_POST['dia_ini']) ? $_POST['dia_ini'] : sprintf("%02d", date("d"));
$mes_ini = isset($_POST['mes_ini']) ? $_POST['mes_ini'] : sprintf("%02d", date("m"));
$ano_ini = isset($_POST['ano_ini']) ? $_POST['ano_ini'] : date("Y");

$fecha = "'" . $ano_ini . "-" . $mes_ini . "-" . $dia_ini . "'";
$fecha_hasta = $ano_ini . "-" . $mes_ini . "-" . $dia_ini;

//dump($fecha);

//$orderbygrupo = "";
//if (isset($_POST['orderbygrupo'])) {
//	$orderbygrupo = " Grupo.grupo, ";
//}
$orderbygrupo = " Grupo.grupo, ";

$id_grupos = isset($_POST['id_grupos']) ? $_POST['id_grupos'] : array();
//dump($id_grupos);

$grupos_condicion = "";
if(count($id_grupos) > 0) {
	
	$grupos_condicion = " AND Grupo.id_grupo IN (";
	foreach ($id_grupos as $id_grupo) {
		$grupos_condicion .= $id_grupo . ',';
	}
	$grupos_condicion = substr($grupos_condicion, 0, -1);
	$grupos_condicion .= " ) ";


	//dump($grupos_condicion);

	// Note: Al stock disponible del item al 'current date' se le agregan todos los consumidos y se le restan todos los comprados despues de la fecha seleccionada

	$query = "SELECT
		CONCAT(Categoria.categoria,'<br>',Proveedor.proveedor),
		Item.codigo_proveedor,
		(Item.stock_disponible
			- COALESCE((SELECT sum(cantidad) from Log where Log.fecha > $fecha and Log.id_item = Item.id_item and Log.id_accion = 1 ),0)
				+ COALESCE((SELECT sum(cantidad) from Log where Log.fecha > $fecha and Log.id_item = Item.id_item and Log.id_accion = 2 ),0)) AS disponible,
		Item.precio_fob,
		Item.precio_nac,
		Item.id_item,
		Item.stock_transito,
		Item.precio_ref,
		Item.oculto_fob,
		Item.oculto_nac,
			CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
			Item.agrupacion_contable,
			Pais.pais,
			Grupo.grupo,
			Categoria.pos_arancelaria,
			orden.cotizacion_dolar,
			ordenitem.precio_ref,
			ordenitem.precio_fob,
			orden.fecha,
			orden.nr_factura,
			orden.despacho,
			ordenitem.cantidad
	FROM
		Item,
		Categoria,
		Proveedor,
			Unidad,
			Grupo,
			Pais,
			ordenitem,
			orden
	WHERE
		(Item.id_categoria = Categoria.id_categoria) AND
		(Item.id_proveedor = Proveedor.id_proveedor) AND
			(Unidad.id_unidad = Item.id_unidad_compra) AND
			(Grupo.id_grupo = Categoria.id_grupo) AND
			(Proveedor.id_pais = Pais.id_pais) AND
			ordenitem.id_item = Item.id_item AND
			orden.id_orden = ordenitem.id_orden
			$grupos_condicion
	ORDER BY
			$orderbygrupo
			Categoria.categoria,
			Orden.fecha desc";
	$result = mysql_query($query);

	//dump($query);



	$rows = array();

	$lastItem;

	while ($row = mysql_fetch_array($result))
	{
		//dump($row);

		$cantidad = $row[21];

		if (!isset($lastItem) || $lastItem != $row[5]) {
			// nuevo item
			$disponible = $row[2];
			$lastItem = $row[5];

			if ($disponible > 0) {
				
				if ($cantidad < $disponible) {
					$row[2] = $cantidad;
				}

				$disponible = $disponible - $cantidad; // disponible - cantidad pedida en orden
				array_push($rows, $row);
			}
		} else {
			// mismo item que row anterior
			if ($disponible > 0) {
				
				if ($cantidad >= $disponible) {
					$row[2] = $disponible;
				} else {
					$row[2] = $cantidad;
				}

				array_push($rows, $row);

				$disponible = $disponible - $cantidad; // disponible - cantidad pedida en orden
			}
		}
	}

	$queryInflacion = "select anio, mes, valor from inflacion order by anio, mes";
	$resultInflacion = mysql_query($queryInflacion);

	$aux = "";
	$totalFOB=0;
	$totalRef=0;
	$totalRefNac=0;
	//while ($row = mysql_fetch_array($result))
	foreach ($rows as $row) 
	{

		$precioRef = 0;
		$pctInflacion = obtener_porcentaje_inflacion($row[18], $fecha_hasta, $resultInflacion);
		
		$precioInflacion = number_format($row[2] * $row[17] * $row[15] * (1 + ($pctInflacion/100)), 2); // default FOB

		$totalFOB += $row[2] * $row[17];
		if(tipoProveedor($row[12])=='NAC') {
			$precioRef = number_format($row[2] * $row[16], 2);
			$totalRefNac += $row[2] * $row[16];

			$precioInflacion = number_format($row[2] * $row[16] * (1 + ($pctInflacion/100)), 2); // default FOB
		}

		$dolarArribo = number_format($row[15], 2);
		$fechaArribo = date('d-m-Y', strtotime($row[18]));


		$aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
				<td>$row[13]</td><td>$row[14]</td><td>$row[19]</td><td>$row[20]</td><td nowrap>$fechaArribo</td><td>$row[2]</td>
				<td>".number_format($row[2] * $row[17], 2)."</td><td>".$precioRef."</td>
				<td>$dolarArribo</td><td>$precioInflacion</td>
				<td>".tipoProveedor($row[12])."</td></tr>\n";

	}
}

$titulo = "Existencias disponibles";

$fecha_select = armar_select_fechas($dia_ini, $mes_ini, $ano_ini);


$var = array("header" => $header,
		  "imprimir" => $imprimir,
		  "titulo" => $titulo,
	 	  "fecha" => $fecha_select,
			"grupos" => armar_select_grupos(),
	    "rows" => $aux,
			"totalFOB" => $totalFOB,
			"totalRefNac" => $totalRefNac);
eval_html('item_disponible_contable.html', $var);


function obtener_porcentaje_inflacion2($fecha_arribo, $fecha, $inflacion_porcentajes) {
	return 10;
}

function obtener_porcentaje_inflacion($fecha_arribo, $fecha, $inflacion_porcentajes) {

	$dif_anios = date('Y', strtotime($fecha)) - date('Y', strtotime($fecha_arribo)) + 1;

	$hasta_anio = date('Y', strtotime($fecha));
	$hasta_mes = date('m', strtotime($fecha));

	$sum_inflacion_pct = 0;

	$inflacion_valores = array();
	$meses_no_encontrados = array();

	//return $dif_anios;

	$count = 0;
	while ($count < $dif_anios) {
		$a = date('Y', strtotime($fecha_arribo)) + $count;
		$m = 1;

		if ($count == 0) {
			$m = date('m', strtotime($fecha_arribo)) + 1; // calculte since the next month
		}

		while ($m <= 12 && ($a != $hasta_anio || $m < $hasta_mes)) {
			
			$pct_inflacion = obtener_pct_anio_mes($a, $m, $inflacion_porcentajes);

			if ($pct_inflacion != -1) {
				array_push($inflacion_valores, $pct_inflacion);
			} else {
				$obj = new stdClass();
				$obj->anio = $a;
				$obj->mes = $m;
				array_push($meses_no_encontrados, $obj);
			}

			$m = $m + 1;
		}

		$count = $count + 1;
	}

	foreach ($meses_no_encontrados as &$obj) {

		//$pct_inflacion = 24; // TODO
		$pct_inflacion = obtener_pct_anio_mes($obj->anio, 13, $inflacion_porcentajes); // TODO

		if ($pct_inflacion != -1) {
			array_push($inflacion_valores, $pct_inflacion / 12);
		} else {
			array_push($inflacion_valores, 0);
		}
	}

	foreach ($inflacion_valores as &$valor) {
		//echo 'valor: ' . $valor . '<br>';
		$sum_inflacion_pct = $sum_inflacion_pct + $valor;
	}

	return $sum_inflacion_pct;
}

function obtener_pct_anio_mes($anio, $mes, $result_inflacion) {

    //echo 'obtener: ' . $anio . ' ' . $mes . '<br>';
    
    mysql_data_seek($result_inflacion, 0);

    while ($row = mysql_fetch_array($result_inflacion))
    {
        if ($row[0] == $anio && $row[1] == $mes) {
            return $row[2];
        }
    }
    return -1;
}

function tipoProveedor($pais) {

	if($pais == "ARGENTINA") return "NAC";

	return "EXT";
}

function armar_select_grupos()
{

	$codigo = "";
	$result = get_groups();

	while ($row = mysql_fetch_array($result))
	{
	      $codigo = $codigo . "<option value='".$row[0]. (isset($id_grupo) && $row[0]==$id_grupo ? "' selected>" : "'>") . $row[1] ."</option>";
	}

	return $codigo;
}
