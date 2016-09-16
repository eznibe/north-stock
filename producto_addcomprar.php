<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$id_item = $_GET['id_item'];

$query = "SELECT
	Categoria.categoria,
	Proveedor.proveedor,
	Unidad.unidad,
	Item.id_item,
	Item.factor_unidades,
	Categoria.id_categoria,
	Proveedor.id_proveedor
  FROM
	Categoria,
	Proveedor,
	Unidad,
	Item
  WHERE (
	(Item.id_item = $id_item) AND
	(Item.id_categoria = Categoria.id_categoria) AND
	(Proveedor.id_proveedor = Item.id_proveedor) AND
	(Unidad.id_unidad = Item.id_unidad_compra)
  )
  GROUP BY
	Categoria.categoria
  ORDER BY
	Categoria.categoria";
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
