<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();
$pdo = get_db_connection();
$pdo = get_db_connection();
$id_grupo = $_GET['id_grupo'];
$periodo;

if(isset($_GET['periodo']) && !empty($_GET['periodo'])) $periodo = $_GET['periodo'];
else $periodo = 6;

function obtener_grupo($id_grupo)
{
 global $pdo;
	$query = "SELECT grupo FROM grupo WHERE id_grupo = $id_grupo";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	return $row[0];
}

// todos los items del grupo seleccionado
$query = "SELECT
	categoria.categoria,
	item.id_categoria,
	item.id_proveedor,
	proveedor.proveedor,
	item.stock_disponible,
	categoria.stock_minimo,
	item.stock_disponible - categoria.stock_minimo,
	unidad.unidad,
	item.stock_transito,
	item.stock_disponible + item.stock_transito - categoria.stock_minimo,
	item.codigo_proveedor,
	item.id_item,
	item.precio_fob,
	item.precio_nac,
	item.precio_ref

  FROM
	item, categoria, unidad, proveedor
  WHERE (
	(item.id_categoria = categoria.id_categoria) AND
	(unidad.id_unidad = categoria.id_unidad_visual) AND
	(categoria.id_grupo = $id_grupo)

	AND proveedor.id_proveedor = item.id_proveedor
        )

  ORDER BY
	categoria.categoria";
$result = $pdo->query($query);

$aux = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
{
  $aux = $aux . process_item($row, $periodo);
}

$grupo = obtener_grupo($id_grupo);
$titulo = "Listado de stock minimos calcuado con periodo de $periodo meses";

$var = array("rows" => $aux,
	     "id_grupo" => $id_grupo,
	     "periodos" => get_periodos($periodo),
	     "titulo" => $titulo,
	     "imprimir" => "");
eval_html('producto_listar_stock_minimos.html', $var);



// Function para armar cada table row del html
function process_item($row, $periodo) {

 global $pdo;
// calcular mesy anio iniciales a partir del periodo dado
$anio_actual = date("Y");
$mes_actual = date("n");

$periodo_ini = $periodo;

$periodo++;

while($periodo > 1)
{
	$mes_actual = $mes_actual - 1;
	if($mes_actual == 0) {
		$mes_actual = 12;
		$anio_actual = $anio_actual - 1;
	}
	$periodo--;
}

$anio_inicial = $anio_actual;
$mes_inicial = $mes_actual;

 $query = "SELECT year(fecha) anio, month(fecha) mes, 
	sum(CASE WHEN (id_accion=1) THEN cantidad ELSE 0 END) ingreso, 
	sum(CASE WHEN (id_accion=2) THEN cantidad ELSE 0 END) egreso,
	(sum(CASE WHEN (id_accion=1) THEN cantidad ELSE 0 END) - sum(CASE WHEN (id_accion=2) THEN cantidad ELSE 0 END)) diferencia
	FROM log 
	WHERE id_item = $row[11] and id_accion in (1,2) AND 
	      (year(fecha) > $anio_inicial OR (year(fecha) = $anio_inicial AND month(fecha) >= $mes_inicial)) 
	GROUP BY year(fecha), month(fecha)
	ORDER BY year(fecha) desc, month(fecha) desc";

 $result = $pdo->query($query);

 $result_mem = result_a_memoria($result);

 $movimientos = get_movimientos($result_mem, (int)$row[4], $periodo_ini, $row[11]==464);

if($row[11]==462){
//echo "Inicio periodo: $mes_actual/$anio_actual<br>";
//echo "Suma de egresos: ".get_cantidad_egresos($result_mem)."<br><br>";
}

//if($row[11]==464){var_dump($movimientos);}

 $min_en_periodo   = get_minimo_en_periodo($result_mem);
 $max_en_periodo   = get_maximo_en_periodo($result_mem);
 $promedio_mensual = get_promedio_mensual($movimientos);

 $aux = "<tr class=\"provlistrow\">
		<td><a class=\"list\" onclick=\"add_comprar($row[11]);\">$row[0]</a></td>
	        <td bgcolor=#D4D4D4>$row[4]</td>
		<td bgcolor=#D4D4D4>$row[5]</td>

		<td><b>".round(get_cantidad_egresos($result_mem) / $periodo_ini * 3)."</b></td>". // formula de calculo => total egresos / periodo en meses * 3

		"<td><b>".round(get_cantidad_egresos($result_mem) / $periodo_ini )."</b></td>". // formula de st promedio consumo mensual
		"<td nowrap><b>$min_en_periodo[0] </b> ($min_en_periodo[1])</td>
		<td nowrap><b>$max_en_periodo[0] </b> ($max_en_periodo[1])</td>".

		"<td bgcolor=#D4D4D4>$row[14]</td> 
		<td>$row[7]</td>
		<td>$row[3]</td>
		<td>$row[10]</td></tr>\n";

 return $aux;
}

// return ej. [[15,'01-14'],[10,'02-14'],[20,'03-14']]
function get_movimientos($result_mem, $st_disp_actual, $periodo, $debug) {

	$movimientos_dif = array();
	$movimientos_real = array();

	$anio_actual = date("Y");	$anio_actual_ini = $anio_actual;
	$mes_actual = date("n");	$mes_actual_ini = $mes_actual;

	// cargar en memoria el result set para poder trabajarlo mejor despues
	$rows = $result_mem;

//	if($debug){var_dump($rows);echo "<br>Per: $periodo<br>";}

	$suma_diferencias = 0;
	$count=0;
	// primer paso armar movimientos con las diferencias
	while($count < ($periodo+1)) {

		if($mes_actual<>date("n") || $anio_actual<>date("Y")){ // not include current month

			$row = find_movimientos_en_mes($rows, $mes_actual, $anio_actual);

			if($row) {

				array_push($movimientos_dif, array($row[4], "$mes_actual/$anio_actual"));
				$suma_diferencias = $suma_diferencias + $row[4];
			}
			else {
				// no hay resultados en la query pero el periodo sigue => poner en 0
				array_push($movimientos_dif, array(0, "$mes_actual/$anio_actual"));
			}
		}

		$mes_actual = $mes_actual - 1;
		if($mes_actual == 0) {
			$mes_actual = 12;
			$anio_actual = $anio_actual - 1;
		}

		$count = $count + 1;

	}

	// segundo paso, calcular el stock al inicio periodo
	$st_disp_inicio = $st_disp_actual - $suma_diferencias;
	
//	if($debug) {echo "<br>";var_dump($st_disp_actual); echo "<br>";var_dump($suma_diferencias);}

	// para que quede iniciando desde el mes mas antiguo
	$movimientos_dif = array_reverse($movimientos_dif);

//	if($debug){echo "<br>MOV DIF: "; var_dump($movimientos_dif); echo "<br><br>";}

	// tercer paso, armar movimientos con calculo de stock usando el disp al comienzo del periodo + diferencia
	foreach ($movimientos_dif as $value) {
		
		$st_disp_inicio = $st_disp_inicio + $value[0];
	
		array_push($movimientos_real, array($st_disp_inicio, $value[1]));
	}	

	return $movimientos_real;
}

function find_movimientos_en_mes($rows, $mes_actual, $anio_actual) {

	foreach ($rows as $row) {
		if($row[1]==$mes_actual && $row[0]==$anio_actual) {
			return $row;
		}
	}

	return null;
}

function get_promedio_mensual($movimientos) {

	$sumatoria = 0;
	$count = 0;
	$result;
	foreach ($movimientos as $value) {
		$sumatoria = $sumatoria + $value[0];
		$count = $count + 1;
	}
	return round($sumatoria / $count);
}

// minimo consumo (egreso)
function get_minimo_en_periodo($result_mem){

	$minimo = 9999;
	$result;
	foreach ($result_mem as $value) {
		if($value[3] < $minimo && ($value[1]<>date("n") || $value[0]<>date("Y"))) {
			$result = $value;
			$minimo = $value[3];
		}
	}
	return $minimo<>9999 ? array($minimo, "$result[1]/$result[0]") : array("-", "-");
}

// maximo consumo (egreso)
function get_maximo_en_periodo($result_mem){
	
	$maximo = -1;
	$result;
	foreach ($result_mem as $value) {
		if($value[3] > $maximo && ($value[1]<>date("n") || $value[0]<>date("Y"))) {
			$result = $value;
			$maximo = $value[3];
		}
	}
	return $maximo<>-1 ? array($maximo, "$result[1]/$result[0]") : array("-", "-");
}

function get_periodos($periodo) {

	$codigo = "";
	$count=1;
	while($count<=12) {

		$codigo = $codigo . "<option value='$count'". (($periodo==$count) ? "selected" : "" ) .">$count</option>";

		$count++;
	}

	return $codigo;
}

// Solo suma los egresos en el periodo en el resulset que ya esta filtrado con valores solo del periodo
function get_cantidad_egresos($result) {

	$suma_egresos = 0;

	foreach($result as $row) {
		if($row[1]<>date("n") || $row[2]<>date("Y")) //not include current month
		{
			$suma_egresos += $row[3];
		}
	}

	return $suma_egresos;
}
