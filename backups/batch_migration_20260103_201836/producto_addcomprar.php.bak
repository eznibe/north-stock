<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_item = $_GET['id_item'];

$query = "SELECT
	categoria.categoria,
	proveedor.proveedor,
	unidad.unidad,
	item.id_item,
	item.factor_unidades,
	categoria.id_categoria,
	proveedor.id_proveedor
  FROM
	categoria,
	proveedor,
	unidad,
	item
  WHERE (
	(item.id_item = $id_item) AND
	(item.id_categoria = categoria.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor) AND
	(unidad.id_unidad = item.id_unidad_compra)
  )
  GROUP BY
	categoria.categoria
  ORDER BY
	categoria.categoria";
$result = mysql_query($query);


$row = mysql_fetch_array($result);
$categoria = $row[0];
$proveedor = $row[1];
$id_proveedor = $row[6];
if ($row[4] != 1)
{
 $unidad = "<em>" . strtoupper($row[2]) . "</em> [" . $row[4] . " " . get_unidad_descarga($row[5]) . "]";
}
else
{
 $unidad = "<em>" . strtoupper($row[2]) . "</em>"; // unidad de compra del item seleccionado
}
$id_item = $row[3];
$focus = "forms[0].cantidad";

$ordenes = ordenes_a_confirmar($id_proveedor);

$tipoenvio = tipos_de_envio($id_proveedor);

$var = array("focus" => $focus,
        "categoria" => $categoria,
        "proveedor" => $proveedor,
	"id_proveedor" => $id_proveedor,
        "unidad" => $unidad,
        "id_item" => $id_item,
	"ordenes" => $ordenes,
	"tipoenvio" => $tipoenvio);

eval_html('producto_addcomprar.html', $var);


function ordenes_a_confirmar($id_proveedor) {
	
	$codigo = "<option value=''>Elige una orden</option>";
	$result = get_ordenes_a_confirmar($id_proveedor);

	while ($row = mysql_fetch_array($result))
	{
	      $codigo = $codigo . "<option value='".$row[0]."'> $row[0] - $row[2] ($row[1]) </option>";
	}

	return $codigo;
}

function tipos_de_envio($id_proveedor) {

	$default = (es_proveedor_nacional($id_proveedor, 'ARGENTINA')) ? '1' : '';

//	$codigo = "<option value=''>Elige un tipo de envio</option>";
	$codigo = "";
	$result = get_tipos_de_envio();

	while ($row = mysql_fetch_array($result))
	{
	      $codigo = $codigo . "<option value='".$row[0]."'". ($row[0]==$default ? 'selected' : '') ."> $row[1] </option>";
	}

	return $codigo;
}

?>
