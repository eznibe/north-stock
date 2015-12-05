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
}

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
		Grupo.grupo
  FROM
    Item,
    Categoria,
    Proveedor,
		Unidad,
		Grupo,
		Pais
  WHERE
    (Item.id_categoria = Categoria.id_categoria) AND
    (Item.id_proveedor = Proveedor.id_proveedor) AND
		(Unidad.id_unidad = Item.id_unidad_compra) AND
		(Grupo.id_grupo = Categoria.id_grupo) AND
		(Proveedor.id_pais = Pais.id_pais)
		$grupos_condicion
  GROUP BY
    Item.id_item, Item.id_categoria
  HAVING
    disponible > 0
  ORDER BY
		$orderbygrupo
		Categoria.categoria";
$result = mysql_query($query);

//dump($query);

$aux = "";
$totalFOB=0;
$totalRef=0;
$totalRefNac=0;
while ($row = mysql_fetch_array($result))
{

	$precioRef = 0;

	$totalFOB += $row[2] * $row[3];
	if(tipoProveedor($row[12])=='NAC') {
		$precioRef = $row[2] * $row[7];
		$totalRefNac += $row[2] * $row[7];
	}

 	$aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"add_comprar($row[5]);\">$row[0]</a></td>
              <td>$row[13]</td><td>$row[2]</td><td>$row[10]</td><td>".($row[2] * $row[3])."</td><td>".$precioRef."</td><td>".tipoProveedor($row[12])."</td></tr>\n";


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
eval_html('item_disponible_valorizado.html', $var);


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
