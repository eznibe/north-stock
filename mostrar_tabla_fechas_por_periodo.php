<?php

include_once 'main.php';
include_once 'dbutils.php';

db_connect();
check_session();

/**
 * Arma el vector de variables para pasarle al html que muestra la pagina de fechas por periodo.
 * Parametros:  $tipo_periodo: semana, mes, a�o
 * 				$tipo: todos, Proveedor, producto, etc.
 * 				$opcion: nro que representa el id del tipo indicado
 * 				$id_accion: compras o consumos (1 o 2)
 */
function mostrar_tabla_fechas_por_periodo($tipo_periodo, $tipo, $opcion, $id_accion, $fecha_ini, $fecha_fin,
										  $transac, $tipo_rango, $opcion, $rango_periodo)
{

	//echo "Datos entrada, fechaini: $fecha_ini, fechafin: $fecha_fin<br>";

	if ($_SESSION['user_level'] < 11) $imprimir = "";
	else $imprimir = "<div class=\"imprimir\">
						<a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
					  </div>";
	

	$tipo_periodo_orig = $tipo_periodo;
	
	$ano_ini = get_ano($fecha_ini);
	$mes_ini = get_mes($fecha_ini);
	$dia_ini = get_dia($fecha_ini);
	$ano_fin = get_ano($fecha_fin);
	$mes_fin = get_mes($fecha_fin);
	$dia_fin = get_dia($fecha_fin);

	if($tipo_periodo == "mes")      { $periodo = "MONTH"; $tipo_periodo = "MES"; $groupByPeriod = "MONTH(fecha), YEAR(fecha)"; }
	else if($tipo_periodo == "ano") { $periodo = "YEAR";  $tipo_periodo = "ANO"; $groupByPeriod = "YEAR(fecha)"; }

	if($id_accion == 1){
		$transac = "Compras";
		$titulo = $transac;
		$selecciono = "CONCAT(unidad.unidad,'(',item.factor_unidades,')')";
		$condicion = "unidad.id_unidad = item.id_unidad_compra";
	}
	else if($id_accion == 2){
		$transac = "Consumos";
		$titulo = $transac;
		$selecciono = "unidad.unidad";
		$condicion = "unidad.id_unidad = categoria.id_unidad_visual";
	}
	else {
		$transac = "Todos";
		$titulo = "Compras y Consumos";
	}

	$titulo = "$titulo entre $dia_ini-$mes_ini-$ano_ini y $dia_fin-$mes_fin-$ano_fin con periodicidad por $tipo_periodo";

	switch ($tipo)
	{
		case 'todos':
			$query_fin = "";
			break;

		case 'grupo':
			$query_fin = " AND (categoria.id_grupo = $opcion)";
			$titulo = $titulo . " del grupo " . get_group($opcion);
			break;

		case 'proveedor':
			$query_fin = " AND (item.id_proveedor = $opcion)";
			$titulo = $titulo . " del proveedor " . get_proveedor($opcion);
			break;

		case 'categoria':
			$query_fin = " AND (item.id_categoria = $opcion)";
			$titulo = $titulo . " del producto " . get_categoria($opcion);
			break;

		case 'item':
			$query_fin = " AND (item.id_item = $opcion)";
			$titulo = $titulo . " del item " . get_item($opcion);
			break;

		case 'usuario':
			$query_fin = " AND (usuario.id_usuario = '".$opcion."')";
			$titulo = $titulo . " ralizadas por usuario " . get_usuario($opcion,2);
			break;

	}

	if($id_accion==1 || $id_accion==2) {
		
		$query = "SELECT log.id_item, CONCAT(categoria,' - ',proveedor), log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono
				  FROM categoria, log, item, usuario, Proveedor, unidad
				  WHERE 	log.id_item = item.id_item AND
							item.id_categoria = categoria.id_categoria AND
							usuario.username = log.username AND
							proveedor.id_proveedor = item.id_proveedor AND
							$condicion AND
							id_accion = $id_accion AND
							fecha >= $fecha_ini AND fecha <= $fecha_fin";
	
		$query = $query . $query_fin;
	
		$query = $query . " GROUP BY log.id_item, $groupByPeriod
						    ORDER BY categoria, log.id_item, fecha";
	}
	else {
		$query = crearQueryTodosByPeriodo($query_fin, $fecha_ini, $fecha_fin, $groupByPeriod);
	}

//	dump($query);
	
	$result = $pdo->query($query);

	$listado = armar_listado($result,$tipo_periodo);

	$var = array("listado" => $listado,
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
				 "tipo_periodo" => $tipo_periodo_orig);

 	eval_html('listar_fechas_por_periodo.html', $var);
}


function armar_listado($result,$tipo_periodo)
{
	$item_anterior = -1;

	// Row: [0]:id_item, [1]:categoria, [2]:username, [3]:sum(cantidad), [4]:a�o de fecha, [5]: mes de fecha, [6]:unidad
	//

	$row = $result->fetch(PDO::FETCH_NUM);

	$categoria = $row[1];

  	$listado = "<table border=0 width=100%>
  				<tr bgcolor='#777777'>
    			<th width=50% colspan=3><font color=#000000> item </font></th>
    			<th width=50% colspan=3>$categoria</th>
  				</tr>
  				<tr class='provlisthead'>
    			<th width=25%>Mes</th>
    			<th width=25% colspan=2>A�o</th>
    			<th width=25% colspan=2>Cantidad</th>
    			<th width=25%>unidad</th>
  				</tr>";

	if($tipo_periodo == "MES") $nombre_mes = get_nombre_mes($row[5]);
	else $nombre_mes = "-";

	$cantidad = $row[3];
	$total_item = $cantidad;

	$unidad = $row[6];
	$ano = $row[4];
	$listado .= "<tr class=\"provlistrow\" align='center'>
			    <td width=25%>$nombre_mes</td>
    			<td width=25% colspan=2>$ano</td>
    			<td width=25% colspan=2 class='accion_$row[8]'>$cantidad</td>
    			<td width=25%>$unidad</td>
  				</tr>";

	$item_anterior = $row[0];


	while ($row = $result->fetch(PDO::FETCH_NUM))
	{
		$item = $row[0];
		if($item <> $item_anterior)
		{
			//Imprimo total del item
			$listado .= "<tr class='provlisthead'>
						<th width=25% ></th>
    					<th width=25% colspan=2>Total</th>
    					<th width=25% colspan=2>$total_item</th>
    					<th width=25% ></th>
  						</tr>
						</table><p>";

			$item_anterior = $item;
			$total_item = 0;

			//Imprimo cabecera nuevo item
			$categoria = $row[1];

  			$listado .= "<table border=0 width=100%>
  						<tr bgcolor='#777777'>
    					<th width=50% colspan=3><font color=#000000> item </font></th>
    					<th width=50% colspan=3>$categoria</th>
  						</tr>
  						<tr class='provlisthead'>
    					<th width=25%>Mes</th>
    					<th width=25% colspan=2>A�o</th>
    					<th width=25% colspan=2>Cantidad</th>
    					<th width=25%>unidad</th>
  						</tr>";

			if($tipo_periodo == "MES") $nombre_mes = get_nombre_mes($row[5]);
			else $nombre_mes = "-";
			$cantidad = $row[3];
			$total_item += $cantidad;

			//Agrego info del item del mes
			$unidad = $row[6];
			$ano = $row[4];
  			$listado .= "<tr class=\"provlistrow\" align='center'>
					    <td width=25%>$nombre_mes</td>
    					<td width=25% colspan=2>$ano</td>
    					<td width=25% colspan=2 class='accion_$row[8]'>$cantidad</td>
    					<td width=25%>$unidad</td>
  						</tr>";
		}
		else
		{
			if($tipo_periodo == "MES") $nombre_mes = get_nombre_mes($row[5]);
			else $nombre_mes = "-";
			$cantidad = $row[3];
			$total_item += $cantidad;

			$unidad = $row[6];
  			$ano = $row[4];
  			$listado .= "<tr class=\"provlistrow\" align='center'>
					    <td width=25%>$nombre_mes</td>
    					<td width=25% colspan=2>$ano</td>
    					<td width=25% colspan=2 class='accion_$row[8]'>$cantidad</td>
    					<td width=25%>$unidad</td>
  						</tr>";
		}

	}

	//Imprimo total del ultimo item
	$listado .= "<tr class='provlisthead'>
				<th width=25% ></th>
				<th width=25% colspan=2>Total</th>
				<th width=25% colspan=2>$total_item</th>
				<th width=25% ></th>
				</tr>
				</table><p>";


	return $listado;
}

function crearQueryTodosByPeriodo($query_fin, $fecha_ini, $fecha_fin, $groupByPeriod) {

	$selecciono_1 = "CONCAT(unidad.unidad,'(',item.factor_unidades,')')";
	$condicion_1 = "unidad.id_unidad = item.id_unidad_compra";
	$selecciono_2 = "unidad.unidad";
	$condicion_2 = "unidad.id_unidad = categoria.id_unidad_visual";
	
	
 	$query = "(SELECT log.id_item as iditem, CONCAT(categoria,' - ',proveedor) as cate, log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono_1, fecha, id_accion as accion, log.fecha as fechaordenar
			  FROM categoria, log, item, usuario, Proveedor, unidad
			  WHERE 	log.id_item = item.id_item AND
						item.id_categoria = categoria.id_categoria AND
						usuario.username = log.username AND
						proveedor.id_proveedor = item.id_proveedor AND
						$condicion_1 AND
						id_accion = 1 AND
						fecha >= $fecha_ini AND fecha <= $fecha_fin";
 	
 	$query .= $query_fin;

 	$query .= " GROUP BY log.id_item, $groupByPeriod )";
 	
 	$query .= " UNION ";
 	
 	$query .= "(SELECT log.id_item as iditem, CONCAT(categoria,' - ',proveedor) as cate, log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono_2, fecha, id_accion as accion, log.fecha as fechaordenar
			   FROM categoria, log, item, usuario, Proveedor, unidad
			   WHERE 	log.id_item = item.id_item AND
						item.id_categoria = categoria.id_categoria AND
						usuario.username = log.username AND
						proveedor.id_proveedor = item.id_proveedor AND
						$condicion_2 AND
						id_accion = 2 AND
						fecha >= $fecha_ini AND fecha <= $fecha_fin";
 	
 	$query .= $query_fin;

 	$query .= " GROUP BY log.id_item, $groupByPeriod )";
 	
 	$query .= " ORDER BY  cate,iditem,fecha";
 	
 	return $query;
 }
 
function get_ano($fecha)
{
	$query = "SELECT YEAR($fecha)";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function get_mes($fecha)
{
	$query = "SELECT MONTH($fecha)";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

function get_dia($fecha)
{
	$query = "SELECT DAY($fecha)";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}


function get_nombre_mes($mes)
{
	switch($mes)
	{
		case 1:  return "Enero";
		case 2:  return "Febrero";
		case 3:  return "Marzo";
		case 4:  return "Abril";
		case 5:  return "Mayo";
		case 6:  return "Junio";
		case 7:  return "Julio";
		case 8:  return "Agosto";
		case 9:  return "Septiembre";
		case 10: return "Octubre";
		case 11: return "Noviembre";
		case 12: return "Diciembre";
	}
}


?>
