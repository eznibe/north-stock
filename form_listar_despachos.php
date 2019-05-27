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
	$query_condiciones .= " AND Orden.despacho = '$despacho'";
}

if(isset($id_orden) && $id_orden!="") {
	$query_condiciones .= " AND Orden.id_orden = $id_orden";
} else {
	$query_condiciones .= " AND Orden.despacho is not null AND Orden.despacho != ''";
}


	  $query = "SELECT
		Orden.id_orden,
		Orden.despacho,
		OrdenItem.cantidad,
		OrdenItem.precio_fob,
		OrdenItem.precio_ref,
		OrdenItem.moneda,
		Categoria.categoria,
		Proveedor.id_proveedor,
		Proveedor.proveedor,
		CONCAT(Unidad.unidad,'(',Item.factor_unidades,')'),
		DATE_FORMAT(Orden.fecha, '%d-%m-%Y')
	  FROM
		OrdenItem,
		Orden,
		Item,
		Categoria,
		Proveedor,
		Unidad
	  WHERE OrdenItem.id_orden = Orden.id_orden AND
		Item.id_item = OrdenItem.id_item AND
		Categoria.id_categoria = Item.id_categoria AND
		Proveedor.id_proveedor = Item.id_proveedor AND
		Unidad.id_unidad = Item.id_unidad_compra
	    ";

	  $query = $query . $query_condiciones;

		$query .= " ORDER BY Orden.despacho, Orden.fecha desc, Orden.id_orden";

	//  dump($query);

	 $result = mysql_query($query);
	 while ($row = mysql_fetch_array($result))
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
