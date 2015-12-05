<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$focus = "forms[0].producto";
$mensaje = "";
$categoria = $_POST['categoria'];

$query = "SELECT Categoria.categoria, Categoria.scan, Categoria.stock_minimo, Unidad.unidad FROM Categoria, Unidad WHERE ((Unidad.id_unidad = Categoria.id_unidad_visual) AND (Categoria.categoria LIKE \"%$categoria%\")) ORDER BY Categoria.categoria";
$result = mysql_query($query);

$aux = "";

if (mysql_num_rows($result) > 0)
{
  while ($row = mysql_fetch_array($result))
  {
   $aux = $aux . "<tr class=\"provlistrow\"><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>\n";
  }

  $var = array("rows" => $aux,
        "mensaje" => $mensaje,
        "focus" => $focus);
  eval_html('categoria_listar.html', $var);
}
else
{
  $mensaje = "No se han encontrado productos con esos datos.";
  $var = array("rows" => $aux,
        "mensaje" => $mensaje,
        "focus" => $focus);
  eval_html('categoria_buscar.html', $var);

}
