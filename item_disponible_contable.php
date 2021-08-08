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
//	$orderbygrupo = " grupo.grupo, ";
//}
$orderbygrupo = " grupo.grupo, ";

$id_grupos = isset($_POST['id_grupos']) ? $_POST['id_grupos'] : array();
//dump($id_grupos);

// TODO remove
// array_push($id_grupos,7);

$grupos_condicion = "";
if(count($id_grupos) > 0) {
	
	$grupos_condicion = " AND grupo.id_grupo IN (";
	foreach ($id_grupos as $id_grupo) {
		$grupos_condicion .= $id_grupo . ',';
	}
	$grupos_condicion = substr($grupos_condicion, 0, -1);
	$grupos_condicion .= " ) ";


	//dump($grupos_condicion);

	// Note: Al stock disponible del item al 'current date' se le agregan todos los consumidos y se le restan todos los comprados despues de la fecha seleccionada

	$query = "SELECT
		CONCAT(categoria.categoria,'<br>',proveedor.proveedor),
		item.codigo_proveedor,
		(item.stock_disponible
			- COALESCE((SELECT COALESCE(sum(cantidad), 0) from log where log.fecha > $fecha and log.id_item = item.id_item and log.id_accion = 1 ),0)
				+ COALESCE((SELECT COALESCE(sum(cantidad), 0) from log where log.fecha > $fecha and log.id_item = item.id_item and log.id_accion = 2 ),0)) AS disponible,
		item.precio_fob,
		item.precio_nac,
		item.id_item,
		item.stock_transito,
		item.precio_ref,
		item.oculto_fob,
		item.oculto_nac,
			CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
			item.agrupacion_contable,
			pais.pais,
			grupo.grupo,
			categoria.pos_arancelaria,
			COALESCE(orden.cotizacion_dolar, 1),
			COALESCE(ordenitem.precio_ref, item.precio_ref),
			COALESCE(ordenitem.precio_fob, item.precio_fob),
			COALESCE(orden.fecha, '2003-01-01'),
			orden.nr_factura,
			orden.despacho,
			coalesce(ordenitem.cantidad, -1),
			orden.factura_AR
	FROM
		item
		join categoria on item.id_categoria = categoria.id_categoria
		join proveedor on item.id_proveedor = proveedor.id_proveedor
		join unidad on unidad.id_unidad = item.id_unidad_compra
		join grupo on grupo.id_grupo = categoria.id_grupo
		join pais on proveedor.id_pais = pais.id_pais
		left join ordenitem on ordenitem.id_item = item.id_item
		left join orden on orden.id_orden = ordenitem.id_orden
	WHERE 1=1
		AND (ordenitem.id_item is null or ordenitem.cantidad - ordenitem.cantidad_pendiente > 0)
		-- AND categoria.id_categoria = 98
			$grupos_condicion
	ORDER BY
			$orderbygrupo
			categoria.categoria,
			item.id_item,
			orden.fecha desc";
	$result = mysql_query($query);

	// dump($query);

	$rows = array();

	$lastitem;

	while ($row = mysql_fetch_array($result))
	{
		//dump($row);

		$cantidad = $row[21];

		if (!isset($lastitem) || $lastitem != $row[5]) {
			// nuevo item
			$disponible = $row[2];
			$lastitem = $row[5];

			if ($disponible > 0) {
				
				if ($cantidad < $disponible) {
					$row[2] = $cantidad > 0 ? $cantidad : $disponible;
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
	$totalStock=0;
	//while ($row = mysql_fetch_array($result))
	foreach ($rows as $row) 
	{

		$precioRef = 0;
		$pctInflacion = obtener_porcentaje_inflacion($row[18], $fecha_hasta, $resultInflacion);
		
		$precioInflacion = number_format($row[2] * $row[17] * $row[15] * (1 + ($pctInflacion/100)), 2); // default FOB

		$totalFOB += $row[2] * $row[17];
		if(tipoproveedor($row[12])=='NAC') {
			$precioRef = number_format($row[2] * $row[16], 2);
			$totalRefNac += $row[2] * $row[16];

			$precioInflacion = number_format($row[2] * $row[16] * (1 + ($pctInflacion/100)), 2); // default FOB
		}

		$dolarArribo = number_format($row[15], 2);
		$fechaArribo = date('d-m-Y', strtotime($row[18]));

		$totalStock += $row[2];

		$aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
				<td>$row[13]</td><td>$row[14]</td><td>$row[19]</td><td>$row[22]</td><td>$row[20]</td><td nowrap>$fechaArribo</td><td>$row[2]</td>
				<td>".number_format($row[2] * $row[17], 2)."</td><td>".$precioRef."</td>
				<td>$dolarArribo</td><td>$precioInflacion</td>
				<td>".tipoproveedor($row[12])."</td></tr>\n";

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
			"totalRefNac" => $totalRefNac,
			"totalStock" => $totalStock);
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

function tipoproveedor($pais) {

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
