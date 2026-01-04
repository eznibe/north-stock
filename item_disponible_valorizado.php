<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();
$pdo = get_db_connection();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<div class=\"imprimir\">
		        	<a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
		          </div>";



$dia_ini = isset($_POST['dia_ini']) ? $_POST['dia_ini'] : sprintf("%02d", date("d"));
$mes_ini = isset($_POST['mes_ini']) ? $_POST['mes_ini'] : sprintf("%02d", date("m"));
$ano_ini = isset($_POST['ano_ini']) ? $_POST['ano_ini'] : date("Y");

$fecha = "'" . $ano_ini . "-" . $mes_ini . "-" . $dia_ini . "'";

//dump($fecha);

//$orderbygrupo = "";
//if (isset($_POST['orderbygrupo'])) {
//	$orderbygrupo = " grupo.grupo, ";
//}
$orderbygrupo = " grupo.grupo, ";

$id_grupos = isset($_POST['id_grupos']) ? $_POST['id_grupos'] : array();
//dump($id_grupos);

$grupos_condicion = "";
if(count($id_grupos) > 0) {
	$grupos_condicion = " AND grupo.id_grupo IN (";
	foreach ($id_grupos as $id_grupo) {
		$grupos_condicion .= $id_grupo . ',';
	}
	$grupos_condicion = substr($grupos_condicion, 0, -1);
	$grupos_condicion .= " ) ";
}

//dump($grupos_condicion);

// Note: Al stock disponible del item al 'current date' se le agregan todos los consumidos y se le restan todos los comprados despues de la fecha seleccionada

$query = "SELECT
    CONCAT(categoria.categoria,'<br>',proveedor.proveedor),
    item.codigo_proveedor,
    (item.stock_disponible
    	- COALESCE((SELECT sum(cantidad) from log where log.fecha > $fecha and log.id_item = item.id_item and log.id_accion = 1 ),0)
			+ COALESCE((SELECT sum(cantidad) from log where log.fecha > $fecha and log.id_item = item.id_item and log.id_accion = 2 ),0)) AS disponible,
	(SELECT round(l.precio_fob, 2) FROM logprecios l where l.id_item = item.id_item and l.insertado < $fecha order by insertado desc limit 1) as precio_fob,
    (SELECT round(l.precio_nac, 2) FROM logprecios l where l.id_item = item.id_item and l.insertado < $fecha order by insertado desc limit 1) as precio_nac,
    item.id_item,
    item.stock_transito,
    (SELECT round(l.precio_ref, 2) FROM logprecios l where l.id_item = item.id_item and l.insertado < $fecha order by insertado desc limit 1) as precio_ref,
    item.oculto_fob,
    item.oculto_nac,
		CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
		item.agrupacion_contable,
		pais.pais,
		grupo.grupo
  FROM
    item,
    categoria,
    proveedor,
		unidad,
		grupo,
		pais
  WHERE
    (item.id_categoria = categoria.id_categoria) AND
    (item.id_proveedor = proveedor.id_proveedor) AND
		(unidad.id_unidad = item.id_unidad_compra) AND
		(grupo.id_grupo = categoria.id_grupo) AND
		(proveedor.id_pais = pais.id_pais)
		-- AND item.id_item = 431 
		$grupos_condicion
  GROUP BY
    item.id_item, item.id_categoria
  HAVING
    disponible > 0
  ORDER BY
		$orderbygrupo
		categoria.categoria";
$result = $pdo->query($query);

// dump($query);

$aux = "";
$totalFOB=0;
$totalRef=0;
$totalRefNac=0;
$totalStock=0;
while ($row = $result->fetch(PDO::FETCH_NUM))
{

	$precioRef = 0;

	$totalFOB += $row[2] * $row[3];
	if(tipoproveedor($row[12])=='NAC') {
		$precioRef = $row[2] * $row[7];
		$totalRefNac += $row[2] * $row[7];
	}

	$totalStock += $row[2];

 	$aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
              <td>$row[13]</td><td>$row[2]</td><td>$row[10]</td><td>".($row[2] * $row[3])."</td><td>".$precioRef."</td><td>".tipoproveedor($row[12])."</td></tr>\n";


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
eval_html('item_disponible_valorizado.html', $var);


function tipoproveedor($pais) {

	if($pais == "ARGENTINA") return "NAC";

	return "EXT";
}

function armar_select_grupos()
{

	$codigo = "";
	$result = get_groups();

	while ($row = $result->fetch(PDO::FETCH_NUM))
	{
	      $codigo = $codigo . "<option value='".$row[0]. (isset($id_grupo) && $row[0]==$id_grupo ? "' selected>" : "'>") . $row[1] ."</option>";
	}

	return $codigo;
}
