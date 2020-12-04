<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<p class=\"imprimir\">
        <a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
        </p>";

$id_grupo="";
$condition = "";
if (isset($_GET['id_grupo']) && !empty($_GET['id_grupo'])) {
	$id_grupo = $_GET['id_grupo'];
	$condition = " AND (Grupo.id_grupo = $id_grupo)";
}

$orderbygrupo = "";
if (isset($_GET['orderbygrupo'])) {
	$orderbygrupo = " Grupo.id_grupo, ";
}

$query = "SELECT
    Categoria.categoria,
    Categoria.stock_minimo,
    SUM(Item.stock_disponible),
    Item.id_categoria,
    (SUM(Item.stock_disponible)-Categoria.stock_minimo) AS saldo,
    Unidad.unidad,
    SUM(Item.stock_transito),
    (SUM(Item.stock_disponible)+SUM(Item.stock_transito)-Categoria.stock_minimo - (coalesce(sum(pi.cantidad), 0))) ,
    Categoria.reservado,
    coalesce(sum(pi.cantidad), 0) as prevision
  FROM
    Item  
    JOIN Categoria on Item.id_categoria = Categoria.id_categoria
    JOIN Unidad on Unidad.id_unidad = Categoria.id_unidad_visual
    JOIN Grupo on Categoria.id_grupo = Grupo.id_grupo
    LEFT JOIN previsionitem pi on pi.id_item = Item.id_item
    LEFT JOIN prevision p on p.id_prevision = pi.id_prevision
  WHERE 1=1
    $condition
  GROUP BY
	  Item.id_categoria
  HAVING
	  saldo < 0
  ORDER BY
	  $orderbygrupo Categoria.categoria";
$result = mysql_query($query);

$aux = "";
while ($row = mysql_fetch_array($result))
{
 $unidad = "<em>" . strtoupper($row[5]) . "</em>";
 if ($row[4] < 0) $row[4] = "<em>$row[4]</em>";
 if ($row[7] < 0) $row[7] = "<em>$row[7]</em>";
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$row[0]</a>
	  <td>$row[2]</td><td>$row[1]</td><td>$row[4]</td><td>$row[6]</td>
	  <td>$row[9]</td>
	  <td title='Reservado: $row[8]'>$row[7]</td><td>$unidad</td></tr>\n";
}
$titulo = "Existencias bajo minimo";

$grupos = armar_select_grupos($id_grupo);

$action = "producto_bajo_minimo.php";

$var = array("rows" => $aux,
	"imprimir" => $imprimir,
	"titulo" => $titulo,
	"grupos" => $grupos,
	"action" => $action);
eval_html('producto_listar.html', $var);


function armar_select_grupos($id_grupo)
{

  	$codigo = "<option value=''>Elige un grupo</option>";
	$result = get_groups();

	while ($row = mysql_fetch_array($result))
	{
	      $codigo = $codigo . "<option value='".$row[0]. (isset($id_grupo) && $row[0]==$id_grupo ? "' selected>" : "'>") . $row[1] ."</option>";
	}

	return $codigo;
}
