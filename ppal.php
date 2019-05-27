<?php

include 'main.php';
include 'dbutils.php';

$username = $_POST['username'];
$clave = $_POST['password'];

db_connect();

$query = "SELECT 
			Categoria.categoria, 
			Categoria.stock_minimo, 
			SUM(Item.stock_disponible), 
			Item.id_categoria, 
			(SUM(Item.stock_disponible)-Categoria.stock_minimo), 
			Unidad.unidad,
			SUM(Item.stock_transito),
			(SUM(Item.stock_disponible)+SUM(Item.stock_transito)-Categoria.stock_minimo) 
		  FROM 
			Item, 
			Categoria, 
			Unidad 
		  WHERE (
			(Item.id_categoria = Categoria.id_categoria) AND 
			(Unidad.id_unidad = Categoria.id_unidad_visual) AND
			Item.stock_transito < 0
		  ) 
		  GROUP BY 
			Item.id_categoria 
		  ORDER BY 
			Categoria.categoria";

$result = mysql_query($query);
$num_results = mysql_num_rows($result);


if ($num_results != 0)
{
  
	$aux = "";
	while ($row = mysql_fetch_array($result))
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

