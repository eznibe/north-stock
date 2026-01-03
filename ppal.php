<?php

include 'main.php';
include 'dbutils.php';

$username = isset($_POST['username']) ? $_POST['username'] : "";
$clave = isset($_POST['password']) ? $_POST['password'] : "";

db_connect();

$query = "SELECT 
			categoria.categoria, 
			categoria.stock_minimo, 
			SUM(item.stock_disponible), 
			item.id_categoria, 
			(SUM(item.stock_disponible)-categoria.stock_minimo), 
			unidad.unidad,
			SUM(item.stock_transito),
			(SUM(item.stock_disponible)+SUM(item.stock_transito)-categoria.stock_minimo) 
		  FROM 
			item, 
			categoria, 
			unidad 
		  WHERE (
			(item.id_categoria = categoria.id_categoria) AND 
			(unidad.id_unidad = categoria.id_unidad_visual) AND
			item.stock_transito < 0
		  ) 
		  GROUP BY 
			item.id_categoria 
		  ORDER BY 
			categoria.categoria";

$result = $pdo->query($query);
$num_results = $result->rowCount();


if ($num_results != 0)
{
  
	$aux = "";
	while ($row = $result->fetch(PDO::FETCH_NUM))
	{
	 $unidad = "<em>" . strtoupper($row[5]) . "</em>";
	 $producto = htmlspecialchars(stripslashes($row[0]));
	 
	 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$producto</a>
	   			    <td>$row[2]</td><td>$row[6]</td><td>$unidad</td></tr>\n";
	}
	$titulo = "Listado de productos con stock en transito negativo";
	
	$var = array("rows" => $aux,
			 	 "titulo" => $titulo);
	  
	eval_html('producto_stock_transito_negativo.html', $var);
}
else {	
	$var = array("username" => "");
  	eval_html('ppal.html', $var);
}

?>

