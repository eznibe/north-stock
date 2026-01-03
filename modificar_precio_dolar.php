<?php
	include 'main.php';
	include 'dbutils.php';

	db_connect();

	$precio_dolar = $_POST['precio'];
	$fecha_mod    = $_POST['fecha_mod'];

	if($precio_dolar == "") $precio_dolar=1;

	if(modificar_precio_dolar($precio_dolar)){
		actualizar_precios_ref($precio_dolar);

		$mensaje = "El precio del dolar se ha actualizado a $precio_dolar $.";
		$var = array("precio_dolar" => $precio_dolar,
				 	 "fecha_mod" => $fecha_mod,
				 	 "mensaje" => $mensaje);
		eval_html('precio_dolar_modificado.html', $var);
	}
	else{
		$mensaje = "Error: El precio del dolar no pudo ser modificado. Motivo posible: Precio no valido";
		$var = array("precio_dolar" => $precio_dolar,
				 	 "fecha_mod" => $fecha_mod,
				 	 "mensaje" => $mensaje);
		eval_html('precio_dolar.html', $var);
	}


function modificar_precio_dolar($precio_dolar)
{
	$query = "INSERT INTO dolarhoy (precio_dolar) VALUES ($precio_dolar)";
	if (!($result = $pdo->query($query)))
	   return FALSE;

    return TRUE;
}

/**
 * Actualizar los precios ref de todos los items que sean extranjeros,
 * o sea que tengan precio nac distinto de vacio (0 o NULL)
 */
function actualizar_precios_ref($precio_dolar)
{
	$query = "UPDATE item SET precio_ref = precio_nac * $precio_dolar " .
			 "WHERE precio_nac IS NOT NULL AND" .
			 " precio_nac <> 0";
	$result = $pdo->query($query);
}

?>
