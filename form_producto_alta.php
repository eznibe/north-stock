<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$unidades = get_units_opt(0);
$unidad_descarga = "";

$pcategoria = $_POST['pcategoria'];
$categoria = $_POST['categoria'];
$proveedor = $_POST['proveedor'];
$proveedores = $_POST['proveedores'];
$codigo_proveedor = $_POST['codigo_proveedor'];
$codigo_barras = $_POST['codigo_barras'];
$unidad = $_POST['unidad'];
$factor_unidades = $_POST['factor_unidades'];

if (isset($_POST['autoassign'])) $autoassign = true;
else $autoassign = false;

$formname = $_POST['formname'];

$precio = $_POST['precio'];
$precio_nac = $_POST['precio_nac'];
$precio_ref = $_POST['precio_ref'];


$mensaje = "";
$hits_prov_mensaje = "";
$hits_prod_mensaje = "";
$focus = "forms[0].pcategoria";


function insert_producto(&$mensaje, $categoria, $proveedor, $scan, $codigo_proveedor, $codigo_barras, $precio, $precio_nac, $precio_ref, $autoassign, $unidad, $factor_unidades)
{
 if ( ( ($categoria == "") or ($proveedor == 0) or ($unidad == 0) or ($factor_unidades == "") ) or ( ($scan) and ($codigo_barras == "") and (!($autoassign)) ) or ($precio == "") )
 {
  // Si falta alguno de los campos requeridos. Esto es:
  // Si (producto == "" O proveedor == 0 O stock_minimo == "") O (scan == "si" Y codigo_barras == "") O precio_fob=""
  //
  $mensaje = "<em class=\"error\">Error: Debe ingresar los items marcados con *.</em>";
  return FALSE;
 }
 else
 {
  // Si estan todos los campos requeridos
  //

   	if ($codigo_barras == "") $codigo_barras="NULL";
   	else
   	{
	    $codigo_barras = addslashes(trim($codigo_barras));
    	$codigo_barras = "\"$codigo_barras\"";
   	}
  	$producto = addslashes(trim($producto));
  	$codigo_proveedor = addslashes(trim($codigo_proveedor));
  	
  	$agrup_contable = get_agrupacion_contable($categoria);


	if(obtener_tipo_proveedor($proveedor) == "EXTRANJERO")
  	{
  		//Calculo los precios nac y ref a partir del precio_fob ingresado
		$precio_nac = $precio + ($precio * porcentaje_impuesto_categoria($categoria)/100);
		$precio_ref = $precio_nac * precio_dolar();

		if ($precio == "") $precio = 'NULL';
  		if ($precio_nac == "") $precio_nac = 'NULL';
  		if ($precio_ref == "") $precio_ref = 'NULL';

  		$query = "INSERT INTO item
            	(id_categoria, id_proveedor, codigo_proveedor, codigo_barras, precio_fob, precio_nac, precio_ref, oculto_fob, oculto_nac, id_unidad_compra, factor_unidades, agrupacion_contable)
            	VALUES
            	($categoria, $proveedor,\"$codigo_proveedor\", $codigo_barras, $precio, $precio_nac, $precio_ref, $precio, $precio_nac, $unidad, $factor_unidades, $agrup_contable)";
  	}
  	else
  	{
  		if ($precio == "") $precio = 'NULL';
  		$precio_nac = 'NULL';
  		$precio_fob = 'NULL';

  		$query = "INSERT INTO item
            	(id_categoria, id_proveedor, codigo_proveedor, codigo_barras, precio_fob, precio_nac, precio_ref, oculto_fob, oculto_nac, id_unidad_compra, factor_unidades, agrupacion_contable)
            	VALUES
            	($categoria, $proveedor,\"$codigo_proveedor\", $codigo_barras, $precio_fob, $precio_nac, $precio, $precio_fob, $precio_nac, $unidad, $factor_unidades, $agrup_contable)";
  	}

  	if (!($result = $pdo->query($query)))
  	{
	   // Si hay un error al insertar los datos en la base.
	   //
   		$mensaje = "<em class=\"error\">Error: El item " . htmlspecialchars(stripslashes($producto)) . " no pudo ser dado de alta. Motivo posible: El item ya existia.</em>";
   		return FALSE;
  	}
  	else
  	{
   		if ($autoassign)
   		{
    		$result = $pdo->query("SELECT LAST_INSERT_ID()");
    		$row = $result->fetch(PDO::FETCH_NUM);
    		$query = "UPDATE item
				SET codigo_barras = \"NSSA$row[0]\"
				WHERE id_item = $row[0]";
    		$result = $pdo->query($query);
   		}

	   // Si se puede insertar los campos en la base.
	   //
   		$mensaje = "El item " . $_SESSION["catname"] . " - " . $_SESSION["provname"] . " ha sido dado de alta.";
   		return TRUE;
  	}

 }
}


function busca_proveedores(&$proveedores, &$mensaje, $proveedor)
{
 if ($proveedor == "")
 {
  $mensaje = "<em class=\"error\">Error: Debe ingresar parte del nombre del proveedor.</em>";
  return FALSE;
 }
 else
 {
  $query = "SELECT id_proveedor, Proveedor
           FROM Proveedor
           WHERE proveedor LIKE \"%$proveedor%\"
	ORDER BY proveedor";
  $result = $pdo->query($query);
  $num_results = $result->rowCount();
  if ($num_results == 0)
  {
   $mensaje = "(<em class=\"error\">Error: No hay proveedores con esos datos.)</em>";
   return FALSE;
  }
  elseif ($num_results > 0)
  {
   if ($num_results == 1)
    $mensaje = "(Hay $num_results posible proveedor.)";
   else
    $mensaje = "(Hay $num_results posibles proveedores.)";
   while ($row = $result->fetch(PDO::FETCH_NUM))
   {
    $proveedores = $proveedores . "<option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
   }
   return TRUE;
  }
 }
}

function busca_categoria(&$categoria, &$mensaje, $pcategoria)
{
 if ($pcategoria == "")
 {
  $mensaje = "<em class=\"error\">Error: Debe ingresar parte del nombre del producto.</em>";
  return FALSE;
 }
 else
 {
  $query = "SELECT id_categoria, categoria
           FROM categoria
           WHERE categoria LIKE \"%$pcategoria%\"
	ORDER BY categoria";
  $result = $pdo->query($query);
  $num_results = $result->rowCount();
  if ($num_results == 0)
  {
   $mensaje = "(<em class=\"error\">Error: No hay productos con esos datos.)</em>";
   return FALSE;
  }
  elseif ($num_results > 0)
  {
   if ($num_results == 1)
    $mensaje = "(Hay $num_results posible producto.)";
   else
    $mensaje = "(Hay $num_results posibles productos.)";
   while ($row = $result->fetch(PDO::FETCH_NUM))
   {
    $categoria = $categoria . "<option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
   }
   return TRUE;
  }
 }
}

function get_name($tabla, $columna, $valor)
{
 $query = "SELECT $columna
           FROM $tabla
           WHERE id_$columna = $valor";
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_scan($valor)
{
 $query = "SELECT scan
           FROM categoria
           WHERE id_categoria = $valor";
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 if ($row[0] == "si") return TRUE;
 else return FALSE;
}

function porcentaje_impuesto_categoria($id_categoria)
{
	$query = "SELECT porc_impuesto FROM categoria WHERE id_categoria=$id_categoria";
	$result = $pdo->query($query);
 	$row = $result->fetch(PDO::FETCH_NUM);
 	return $row[0];
}

function precio_dolar()
{
	$query = "SELECT precio_dolar FROM dolarhoy WHERE id_dolar=(SELECT MAX(id_dolar) FROM dolarhoy)";
	$result = $pdo->query($query);
 	$row = $result->fetch(PDO::FETCH_NUM);
 	return $row[0];
}

function get_moneda_proveedor($id_proveedor)
{
	$query = "SELECT pais FROM pais, Proveedor
			  WHERE pais.id_pais = Proveedor.id_pais AND
					proveedor.id_proveedor = $id_proveedor";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	if($row[0] == "ARGENTINA") return "AR$";

	return "US$";
}

/**
 * Devuelve si el proveedor es extranjero o argentino para saber como mostrar el listado,
 * a partir del id_proveedor pasado como parametro
 */
function obtener_tipo_proveedor($id_proveedor){
	$query = "SELECT pais FROM pais, Proveedor
		  WHERE pais.id_pais = Proveedor.id_pais and
				proveedor.id_proveedor = $id_proveedor";
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);
	if($row[0] == "ARGENTINA") return "NACIONAL";

	return "EXTRANJERO";
}

// Me fijo que hay que hacer y lo hago (llamando a las funcione corresp.)
if ( ($_SESSION["categoria"]) and ($_SESSION["proveedor"]) and ($formname == "producto_alta") )
{
 if ( insert_producto($mensaje, $categoria, $proveedor, get_scan($categoria), $codigo_proveedor, $codigo_barras, $precio, $precio_nac, $precio_ref, $autoassign, $unidad, $factor_unidades) )
 {
  $producto = "";
  $proveedor = "";
  $codigo_proveedor = "";
  $codigo_barras = "";

  $hits_prov_mensaje = "";
  $focus = "forms[0].pcategoria";
  $proveedores = "";
  $_SESSION["catval"] = "";
  $_SESSION["catname"] = "";
  $_SESSION["provval"] = "";
  $_SESSION["provname"] = "";
  $_SESSION["categoria"] = FALSE;
  $_SESSION["proveedor"] = FALSE;
 }
}
elseif ( ($formname == "scategoria") )
{
 if ( ($categoria != 0) )
 {
  $_SESSION["catval"] = $categoria;
  $_SESSION["catname"] = get_name("categoria", "categoria", $categoria);
  $_SESSION["categoria"] = TRUE;
  if (!$_SESSION["proveedor"]) $focus = "forms[1].proveedor";
  else $focus = "forms[2].unidad";
 }
 elseif ( busca_categoria($categoria, $hits_prod_mensaje, $pcategoria) )
 {
  $focus = "forms[0].categoria";
 }
 else
 {
  $focus = "forms[0].pcategoria";
  $_SESSION["categoria"] = FALSE;
 }
}
elseif ( ($formname == "sproveedor") )
{
 if ( ($proveedores != 0) )
 {
  $_SESSION["provval"] = $proveedores;
  $_SESSION["provname"] = get_name("proveedor", "proveedor", $proveedores);
  $_SESSION["proveedor"] = TRUE;
  $focus = "forms[2].unidad";

  $moneda = "(". get_moneda_proveedor($proveedores). ")";
 }
 elseif ( busca_proveedores($proveedores, $hits_prov_mensaje, $proveedor) )
 {
  $focus = "forms[1].proveedores";
 }
 else
 {
  $focus = "forms[1].proveedor";
  $_SESSION["proveedor"] = FALSE;
 }

}


$catval = $_SESSION["catval"];
$catname = $_SESSION["catname"];
$provval = $_SESSION["provval"];
$provname = $_SESSION["provname"];

  $producto = htmlspecialchars(trim($producto));
  $proveedor = addslashes(trim($proveedor));
  $codigo_proveedor = addslashes(trim($codigo_proveedor));
  $codigo_barras = addslashes(trim($codigo_barras));

if ($_SESSION["categoria"])
{
 if (get_scan($_SESSION["catval"]))
 {
  $barras_class = "obligatorio";
  $barras_sign = "*";
 }
 else
 {
  $barras_class = "opcional";
  $barras_sign = "";
 }
}

if ($_SESSION["catval"] == "")
{
 $unidad_descarga = "";
}
else
{
 $unidad_descarga = get_unidad_descarga($catval);
}

$var = array("mensaje" => $mensaje, "hits_prov_mensaje" => $hits_prov_mensaje,
  "hits_prod_mensaje" => $hits_prod_mensaje, "pcategoria" => $pcategoria,
  "categoria" => $categoria,
  "codigo_proveedor" => $codigo_proveedor, "codigo_barras" => $codigo_barras,
  "focus" => $focus, "proveedores" => $proveedores,
  "barras_class" => $barras_class,
  "barras_sign" => $barras_sign,
  "catval" => $catval,
  "catname" => $catname,
  "provval" => $provval,
  "provname" => $provname,
  "unidades" => $unidades,
  "unidad_descarga" => $unidad_descarga,
  "moneda" => $moneda);


eval_html('producto_alta.html', $var);

?>

