<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$focus = "forms[0].producto";
$mensaje = "";
$producto = $_POST['producto'];

$query = "SELECT Categoria.categoria, Categoria.stock_minimo, SUM(Item.stock_disponible), Item.id_categoria, (SUM(Item.stock_disponible)-Categoria.stock_minimo), Unidad.unidad FROM Item, Categoria, Unidad WHERE ((Item.id_categoria = Categoria.id_categoria) AND (Unidad.id_unidad = Categoria.id_unidad_visual) AND (Categoria.categoria LIKE \"%$producto%\")) GROUP BY Item.id_categoria ORDER BY Categoria.categoria";
$result = mysql_query($query);

$aux = "";

if (mysql_num_rows($result) > 0)
{
  while ($row = mysql_fetch_array($result))
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
