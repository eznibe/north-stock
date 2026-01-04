<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();
$pdo = get_db_connection();

$focus = "forms[0].producto";
$mensaje = "";
$categoria = $_POST['categoria'];

$query = "SELECT categoria.categoria, categoria.scan, categoria.stock_minimo, unidad.unidad FROM categoria, unidad unidad WHERE ((unidad.id_unidad = categoria.id_unidad_visual) AND (categoria.categoria LIKE \"%$categoria%\")) ORDER BY categoria.categoria";
$result = $pdo->query($query);

$aux = "";

if ($result->rowCount() > 0)
{
  while ($row = $result->fetch(PDO::FETCH_NUM))
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
