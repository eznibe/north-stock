<?php

// Se encarga de armar y mostrar el listado con los opciones seleccionadas en las paginas previas

include 'main.php';
include 'dbutils.php';

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

$despacho = $_POST['despacho'];
$id_orden = $_POST['id_orden'];

$dia_ini = $_POST['dia_ini'];
$mes_ini = $_POST['mes_ini'];
$ano_ini = $_POST['ano_ini'];
$dia_fin = $_POST['dia_fin'];
$mes_fin = $_POST['mes_fin'];
$ano_fin = $_POST['ano_fin'];

//dump($_POST);

$fecha_ini = "'$ano_ini-$mes_ini-$dia_ini 00:00'";
$fecha_fin = "'$ano_fin-$mes_fin-$dia_fin 23:59'";

$titulo = "Despacho(s)";
if(isset($despacho) && $despacho!="") {
 $titulo .= " ".$despacho;
}
if(isset($id_orden) && $id_orden!="") {
	$titulo .=  " de orden ".$id_orden;
}
$titulo .= " entre $dia_ini-$mes_ini-$ano_ini y $dia_fin-$mes_fin-$ano_fin";


$query_condiciones = " AND fecha >= $fecha_ini AND fecha <= $fecha_fin";

if(isset($despacho) && $despacho!="") {
	$query_condiciones .= " AND orden.despacho = '$despacho'";
}

if(isset($id_orden) && $id_orden!="") {
	$query_condiciones .= " AND orden.id_orden = $id_orden";
} else {
	$query_condiciones .= " AND orden.despacho is not null AND orden.despacho != ''";
}


	  $query = "SELECT
		orden.id_orden,
		orden.despacho,
		ordenitem.cantidad,
		ordenitem.precio_fob,
		ordenitem.precio_ref,
		ordenitem.moneda,
		categoria.categoria,
		proveedor.id_proveedor,
		proveedor.proveedor,
		CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
		DATE_FORMAT(orden.fecha, '%d-%m-%Y')
	  FROM
		ordenitem,
		orden,
		item,
		categoria,
		proveedor,
		unidad
	  WHERE ordenitem.id_orden = orden.id_orden AND
		item.id_item = ordenitem.id_item AND
		categoria.id_categoria = item.id_categoria AND
		proveedor.id_proveedor = item.id_proveedor AND
		unidad.id_unidad = item.id_unidad_compra
	    ";

	  $query = $query . $query_condiciones;

		$query .= " ORDER BY orden.despacho, orden.fecha desc, orden.id_orden";

	//  dump($query);

	 $result = $pdo->query($query);
	 while ($row = $result->fetch(PDO::FETCH_NUM))
	 {
	 		$listado = $listado . "<tr class=\"provlistrow\"> <td class=\"list\">$row[1]</td><td>$row[0]</td><td>$row[10]</td>
																												<td>$row[6]</td><td>$row[8]</td><td>$row[2]</td>
																												<td>".getPrecio($row)."&nbsp;$row[5]</td><td>$row[9]</td></tr>\n";
	 }

	 $var = array("mensaje" => $mensaje,
				  "listado" => $listado,
				  "imprimir" => $imprimir,
				  "titulo" => $titulo,
	 			  "despacho" => $despacho,
					"id_orden" => $id_orden,
				  "dia_ini" => $dia_ini,
				  "mes_ini" => $mes_ini,
				  "ano_ini" => $ano_ini,
				  "dia_fin" => $dia_fin,
				  "mes_fin" => $mes_fin,
				  "ano_fin" => $ano_fin);

	 eval_html('listar_despachos.html', $var);


function getPrecio($row) {
	$nac = es_proveedor_nacional($row[7], "ARGENTINA");
	return $nac ? $row[4] : $row[3];
}
