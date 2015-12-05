<?php
/**
 * Este script actualiza el valor de porc_impuesto de las categorias que tienen items de proveedores extranjeros;
 * Modifica el campo codigo_barras de la tabla item para que todos queden sin * y se vean bien cuando se imprime la imagen;
 * Pone en 1 la cotizacion del dolar en las ordenes de compra que no tienen valor de esta cotizacion (por ser anteriores a las moficaciones)
 */

include_once 'main.php';
include_once 'dbutils.php';

db_connect();

//nuevo porcentaje a actualizar
$valor = $_POST['valor'];

//Actualiza porc_impuesto de categorias con items extranjeros
//
$query = "SELECT DISTINCT(categoria.id_categoria) FROM categoria, item, proveedor, pais
		  WHERE categoria.id_categoria = item.id_categoria and
				pais <> 'ARGENTINA' and
				pais.id_pais = Proveedor.id_pais and
				item.id_proveedor = proveedor.id_proveedor";
$result = mysql_query($query);

while ($row = mysql_fetch_array($result))
{
	$id_categoria = $row[0];
	$update = "UPDATE categoria SET porc_impuesto = $valor WHERE id_categoria = $id_categoria";
	if(mysql_query($update))
		$mensaje = "La actualizacion se realizo con exito";
	else
		$mensaje = "Problemas!!!";
}

//Cambio valores de precio_nacionalizado segun nuevo porc_impuestos en items extranjeros
$precio_dolar = obtener_precio_dolar();
																	  //Categorias con items extranjeros
$query = "SELECT id_item, precio_fob FROM Item WHERE id_categoria IN (SELECT DISTINCT(categoria.id_categoria) FROM categoria, item, proveedor, pais
		  															  WHERE categoria.id_categoria = item.id_categoria and
																	  pais <> 'ARGENTINA' and
																	  pais.id_pais = Proveedor.id_pais and
																	  item.id_proveedor = proveedor.id_proveedor) 
													 AND precio_fob IS NOT NULL";
$result = mysql_query($query);
while($row = mysql_fetch_array($result))
{
	$precio_nac = $row[1] + ($row[1] * $valor / 100);
	$precio_ref = $precio_nac * $precio_dolar;

   	$query = "UPDATE Item SET precio_nac = $precio_nac, precio_ref = $precio_ref WHERE id_item = $row[0]";
   	$result2 = mysql_query($query);
}


//Modifica codigo_barras
//
$update = "UPDATE item SET codigo_barras = SUBSTR(codigo_barras,2,LENGTH(codigo_barras)-2) WHERE codigo_barras LIKE '*%*'";
if(!mysql_query($update))
	echo "Error modificando codigo de barras en Item<p>";

//Cambia cotizacion del dolar de ordenes
//
$update = "UPDATE Orden SET cotizacion_dolar = 1 WHERE cotizacion_dolar IS NULL OR cotizacion_dolar = 0";
if(!mysql_query($update))
	echo "Error modificando cotizacion del dolar en Orden<p>";

	
function obtener_precio_dolar()
{
	$query = "SELECT precio_dolar from DolarHoy where id_dolar=(SELECT max(id_dolar) FROM DolarHoy)";

	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	//Devuelvo el precio del dolar actual
	return $row[0];
}
?>

<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />
  <link rel="shortcut icon" href="imagenes/go.png"">
</head>

<body class="ppal">

<fieldset>
<legend>Actualizacion</legend>
<table align='center'>
<tr>
<td>
<?php echo $mensaje; ?>
</td>
</tr>
</table>
</fieldset>

</body>
</html>
