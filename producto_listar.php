<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<p class=\"imprimir\">
        <a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
        </p>";

$query = "SELECT
	Categoria.categoria,
	Categoria.stock_minimo,
	SUM(Item.stock_disponible),
	Item.id_categoria,
	(SUM(Item.stock_disponible)-Categoria.stock_minimo),
	Unidad.unidad,
	SUM(Item.stock_transito),
	(SUM(Item.stock_disponible)+SUM(Item.stock_transito)-Categoria.stock_minimo-Categoria.reservado),
  Categoria.reservado
  FROM
	Item,
	Categoria,
	Unidad
  WHERE (
	(Item.id_categoria = Categoria.id_categoria) AND
	(Unidad.id_unidad = Categoria.id_unidad_visual)
  )
  GROUP BY
	Item.id_categoria
  ORDER BY
	Categoria.categoria";
$result = mysql_query($query);

/*echo $query . "<br />";
if ($result)
{
 echo "RESULT = true" . "<br />";
}
else
{
 echo"RESULT = false" . mysql_error() . "<br />";
}
*/

$aux = "";
while ($row = mysql_fetch_array($result))
{
 $unidad = "<em>" . strtoupper($row[5]) . "</em>";
 if ($row[4] < 0) $row[4] = "<em>$row[4]</em>";
 if ($row[7] < 0) $row[7] = "<em>$row[7]</em>";
 $producto = htmlspecialchars(stripslashes($row[0]));
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$producto</a>
      <td>$row[2]</td><td>$row[1]</td><td>$row[4]</td><td>$row[6]</td><td title='Reservado: $row[8]'>$row[7]</td><td>$unidad</td></tr>\n";
}
$titulo = "Listado total de existancias";

$var = array("rows" => $aux,
	"imprimir" => $imprimir,
	"titulo" => $titulo);
eval_html('producto_listar.html', $var);
