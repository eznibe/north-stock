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

if ($_SESSION['user_level'] < 11) $excel = "";
else $excel = "<div class=\"imprimir\">
					<a class=\"imprimir\" target=”_blank” href=\"reports/stock_items.php\">Excel</a>
				</div>";



$dia_ini = isset($_POST['dia_ini']) ? $_POST['dia_ini'] : sprintf("%02d", date("d"));
$mes_ini = isset($_POST['mes_ini']) ? $_POST['mes_ini'] : sprintf("%02d", date("m"));
$ano_ini = isset($_POST['ano_ini']) ? $_POST['ano_ini'] : date("Y");

$fecha = "'" . $ano_ini . "-" . $mes_ini . "-" . $dia_ini . "'";

//dump($fecha);

$orderbygrupo = "";
if (isset($_POST['orderbygrupo'])) {
	$orderbygrupo = " grupo.id_grupo, ";
}

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
        categoria.categoria,
        item.codigo_proveedor,
        sum(item.stock_disponible),
        item.precio_fob,
        item.precio_nac,
        item.id_item,
        item.stock_transito,
        item.precio_ref,
        item.oculto_fob,
        item.oculto_nac,
		CONCAT(unidad.unidad,'(',item.factor_unidades,')'),
		item.agrupacion_contable,
		pais.pais
  FROM
        item,
        categoria,
        proveedor,
		unidad,
		grupo,
		pais
  WHERE (
        (item.id_categoria = categoria.id_categoria) AND
        (item.id_proveedor = proveedor.id_proveedor) AND
		(unidad.id_unidad = item.id_unidad_compra) AND
		(grupo.id_grupo = categoria.id_grupo) AND
		(proveedor.id_pais = pais.id_pais)
		$grupos_condicion
  )
  GROUP BY
        item.id_categoria
  ORDER BY
	$orderbygrupo
	categoria.categoria";
$result = $pdo->query($query);

//dump($query);

$aux = "";
$aux2 = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $aux = $aux . "<tr class=\"provlistrow\"><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>\n";
}

$titulo = "Stock completo";

$fecha_select = armar_select_fechas($dia_ini, $mes_ini, $ano_ini);

$header = "";

 $var = array("header" => $header,
			  "imprimir" => $imprimir,
			  "excel" => $excel,
			  "titulo" => $titulo,
		 	  "fecha" => $fecha_select,
				"grupos" => armar_select_grupos(),
		      "rows" => $aux);
 eval_html('listar_stock_completo.html', $var);



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
