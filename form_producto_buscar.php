<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$focus = "forms[0].producto";
$mensaje = "";
$producto = $_POST['producto'];

$query = "SELECT categoria.categoria, categoria.stock_minimo, SUM(item.stock_disponible), item.id_categoria, (SUM(item.stock_disponible)-categoria.stock_minimo), unidad.unidad FROM item, categoria, unidad unidad WHERE ((item.id_categoria = categoria.id_categoria) AND (unidad.id_unidad = categoria.id_unidad_visual) AND (categoria.categoria LIKE \"%$producto%\")) GROUP BY item.id_categoria ORDER BY categoria.categoria";
$result = $pdo->query($query);

$aux = "";

if ($result->rowCount() > 0)
{
  while ($row = $result->fetch(PDO::FETCH_NUM))
  {
   $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$row[0]</a></td><td>$row[1]</td><td>$row[2]</td><td>$row[4]</td><td>$row[5]</td></tr>\n";
  }

  $var = array("rows" => $aux,
        "mensaje" => $mensaje,
        "focus" => $focus);
  eval_html('producto_listar.html', $var);
}
else
{
  $mensaje = "No se han encontrado productos con esos datos.";
  $var = array("rows" => $aux,
        "mensaje" => $mensaje,
        "focus" => $focus);
  eval_html('producto_buscar.html', $var);

}
