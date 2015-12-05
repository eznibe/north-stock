<?php

include_once 'main.php';
include_once 'dbutils.php';

db_connect();
check_session();

/**
 * Arma el vector de variables para pasarle al html que muestra la pagina de fechas por periodo.
 * Parametros:  $tipo_periodo: semana, mes, a�o
 * 				$tipo: todos, proveedor, producto, etc.
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
		$selecciono = "CONCAT(Unidad.unidad,'(',Item.factor_unidades,')')";
		$condicion = "Unidad.id_unidad = Item.id_unidad_compra";
	}
	else if($id_accion == 2){
		$transac = "Consumos";
		$titulo = $transac;
		$selecciono = "Unidad.unidad";
		$condicion = "Unidad.id_unidad = Categoria.id_unidad_visual";
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
			$query_fin = " AND (Categoria.id_grupo = $opcion)";
			$titulo = $titulo . " del grupo " . get_group($opcion);
			break;

		case 'proveedor':
			$query_fin = " AND (Item.id_proveedor = $opcion)";
			$titulo = $titulo . " del proveedor " . get_proveedor($opcion);
			break;

		case 'categoria':
			$query_fin = " AND (Item.id_categoria = $opcion)";
			$titulo = $titulo . " del producto " . get_categoria($opcion);
			break;

		case 'item':
			$query_fin = " AND (Item.id_item = $opcion)";
			$titulo = $titulo . " del item " . get_item($opcion);
			break;

		case 'usuario':
			$query_fin = " AND (Usuario.id_usuario = '".$opcion."')";
			$titulo = $titulo . " ralizadas por usuario " . get_usuario($opcion,2);
			break;

	}

	if($id_accion==1 || $id_accion==2) {
		
		$query = "SELECT Log.id_item, CONCAT(categoria,' - ',proveedor), Log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono
				  FROM Categoria, Log, Item, Usuario, Proveedor, Unidad
				  WHERE 	Log.id_item = Item.id_item AND
							Item.id_categoria = Categoria.id_categoria AND
							Usuario.username = Log.username AND
							Proveedor.id_proveedor = Item.id_proveedor AND
							$condicion AND
							id_accion = $id_accion AND
							fecha >= $fecha_ini AND fecha <= $fecha_fin";
	
		$query = $query . $query_fin;
	
		$query = $query . " GROUP BY Log.id_item, $groupByPeriod
						    ORDER BY Categoria, Log.id_item, fecha";
	}
	else {
		$query = crearQueryTodosByPeriodo($query_fin, $fecha_ini, $fecha_fin, $groupByPeriod);
	}

//	dump($query);
	
	$result = mysql_query($query);

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

	$row = mysql_fetch_array($result);

	$categoria = $row[1];

  	$listado = "<table border=0 width=100%>
  				<tr bgcolor='#777777'>
    			<th width=50% colspan=3><font color=#000000> Item </font></th>
    			<th width=50% colspan=3>$categoria</th>
  				</tr>
  				<tr class='provlisthead'>
    			<th width=25%>Mes</th>
    			<th width=25% colspan=2>A�o</th>
    			<th width=25% colspan=2>Cantidad</th>
    			<th width=25%>Unidad</th>
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


	while ($row = mysql_fetch_array($result))
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
    					<th width=50% colspan=3><font color=#000000> Item </font></th>
    					<th width=50% colspan=3>$categoria</th>
  						</tr>
  						<tr class='provlisthead'>
    					<th width=25%>Mes</th>
    					<th width=25% colspan=2>A�o</th>
    					<th width=25% colspan=2>Cantidad</th>
    					<th width=25%>Unidad</th>
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

	$selecciono_1 = "CONCAT(Unidad.unidad,'(',Item.factor_unidades,')')";
	$condicion_1 = "Unidad.id_unidad = Item.id_unidad_compra";
	$selecciono_2 = "Unidad.unidad";
	$condicion_2 = "Unidad.id_unidad = Categoria.id_unidad_visual";
	
	
 	$query = "(SELECT Log.id_item as iditem, CONCAT(categoria,' - ',proveedor) as cate, Log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono_1, fecha, id_accion as accion, Log.fecha as fechaOrdenar
			  FROM Categoria, Log, Item, Usuario, Proveedor, Unidad
			  WHERE 	Log.id_item = Item.id_item AND
						Item.id_categoria = Categoria.id_categoria AND
						Usuario.username = Log.username AND
						Proveedor.id_proveedor = Item.id_proveedor AND
						$condicion_1 AND
						id_accion = 1 AND
						fecha >= $fecha_ini AND fecha <= $fecha_fin";
 	
 	$query .= $query_fin;

 	$query .= " GROUP BY Log.id_item, $groupByPeriod )";
 	
 	$query .= " UNION ";
 	
 	$query .= "(SELECT Log.id_item as iditem, CONCAT(categoria,' - ',proveedor) as cate, Log.username, sum(cantidad), YEAR(fecha), MONTH(fecha), $selecciono_2, fecha, id_accion as accion, Log.fecha as fechaOrdenar
			   FROM Categoria, Log, Item, Usuario, Proveedor, Unidad
			   WHERE 	Log.id_item = Item.id_item AND
						Item.id_categoria = Categoria.id_categoria AND
						Usuario.username = Log.username AND
						Proveedor.id_proveedor = Item.id_proveedor AND
						$condicion_2 AND
						id_accion = 2 AND
						fecha >= $fecha_ini AND fecha <= $fecha_fin";
 	
 	$query .= $query_fin;

 	$query .= " GROUP BY Log.id_item, $groupByPeriod )";
 	
 	$query .= " ORDER BY  cate,iditem,fecha";
 	
 	return $query;
 }
 
function get_ano($fecha)
{
	$query = "SELECT YEAR($fecha)";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function get_mes($fecha)
{
	$query = "SELECT MONTH($fecha)";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

function get_dia($fecha)
{
	$query = "SELECT DAY($fecha)";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
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
