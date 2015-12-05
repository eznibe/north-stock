<?php
	require_once('include/TinyAjax.php');
	include_once 'dbutils.php';

	db_connect();

	function mostrar_precio_correcto($proveedor,$precio_fob,$precio_ref) {

		$query = "insert into auxiliar (precio) values (3.5)";
		$result = mysql_query($query);

		$query = "SELECT proveedor, pais FROM proveedor, pais WHERE id_proveedor = $proveedor AND proveedor.id_pais = pais.id_pais";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		$nom_proveedor = $row[0];
		$pais = $row[1];
		if($pais == "ARGENTINA"){
			$p_fob = "<input type='hidden' value='$precio_fob' size='10' name='precio_fob' id='precio_fob' class='opcional' disabled>$precio_fob";
			$p_ref = "<input type='text' value='$precio_ref' size='10' name='precio_ref' id='precio_ref' class='opcional'>";
		}
		else{
			$p_fob = "<input type='text' value='$precio_fob' size='10' name='precio_fob' id='precio_fob' class='opcional'>";
			$p_ref = "<input type='hidden' value='$precio_ref' size='10' name='precio_ref' id='precio_ref' class='opcional' disabled>$precio_ref";
		}


		$tab = new TinyAjaxBehavior();
		$tab->add( TabInnerHtml::getBehavior("p_fob", $p_fob));
		$tab->add( TabInnerHtml::getBehavior("p_ref", $p_ref));
		return $tab->getString();
	}

	$ajax = new TinyAjax();
	$ajax->showLoading();
	$ajax->exportFunction("mostrar_precio_correcto", array("proveedor","precio_fob","precio_ref"));

	$ajax->process();
?>

<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />

<script type="text/javascript">

function mensaje()
{
	alert("On change seleccionado");
}

function setfocus()
{
document.<?php echo $var['focus']; ?>.focus()
}
</script>

<?php 	$ajax->drawJavaScript(); ?>
</head>
<body class="ppal" onload = "setfocus()">


<div class="altas">
<fieldset>
 <legend>Modificacion de item</legend>
   <?php echo $var['mensaje']; ?>
<table>
<form action="form_producto_modificacion.php" method="post" target="_self" name="scategoria">
  <input type="hidden" value="item_datosmodificar" size="10" name="formname" id="formname">
  <input type="hidden" value="<?php echo $var['id_subproducto']; ?>" size="10" name="id_subproducto" id="id_subproducto">
<tr>
<td>
Producto:
</td>
<td>
<?php echo $var['catname']; ?>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>
Proveedor:
</td>
<td>


<select name="proveedor" id="proveedor" class="obligatorio" onchange="mostrar_precio_correcto()">
    <option value="1" onclicked="mensaje()"> Proveedor 1</option>
    <option value="2"> Proveedor 2</option>
    <option value="25">Proveedor 25</option>
  </select>

</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>
Unidad de descarga:
</td>
<td>
<?php echo $var['unidad_descarga']; ?>
</td>
<td>&nbsp;</td>
</tr>

<td>
 <label for="unidad">Unidad de compra:</label>
</td>
<td>
  <select name="unidad" id="unidad" class="obligatorio">
   <option value="0">seleccionar</option>
   <?php echo $var['unidades']; ?>
  </select> *
</td>
<td>
  <label for="factor_unidades">U desc X U compra:</label>
  <input type="text" value="<?php echo $var['factor_unidades']; ?>" size="4" name="factor_unidades" id="factor_unidades" class="obligatorio"> *
</td>
<tr></tr>

<tr>
<td>
 <label for="codigo_proveedor">Codigo del proveedor:</label>
</td>
<td>
  <input type="text" value="<?php echo $var['codigo_proveedor']; ?>" size="20" name="codigo_proveedor" id="codigo_proveedor" class="opcional">
</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>
 <label for="codigo_barras">Codigo de barras:</label>
</td>
<td>
  <input type="text" value="<?php echo $var['codigo_barras']; ?>" size="20" name="codigo_barras" id="codigo_barras" class="<?php echo $var['barras_class']; ?>">
<?php echo $var['barras_sign']; ?>
</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>
 <label for="stock_disponible">Stock disponible:</label>
</td>
<td>
  <input type="text" value="<?php echo $var['stock_disponible']; ?>" size="10" name="stock_disponible" id="stock_disponible" class="opcional">
</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>
 <label for="stock_transito">Stock transito:</label>
</td>
<td>
  <input type="text" value="<?php echo $var['stock_transito']; ?>" size="10" name="stock_transito" id="stock_transito" class="opcional">
</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>
 <label for="precio_fob">Precio FOB (US$):</label>
</td>
<td>
  <div id="p_fob"> <input type="text" value="5" size="10" name="precio_fob" id="precio_fob" class="opcional"> </div>
</td>
<td><label for="oculto_fob">Oculto:</label>
  <input type="text" value="<?php echo $var['oculto_fob']; ?>" size="10" name="oculto_fob" id="oculto_fob" class="opcional">
</td>
</tr>

<tr>
<td>
 <label for="precio_nac">Precio nacionalizado (US$):</label>
</td>
<td>

  <?php echo $var['precio_nac']; ?>
</td>
<td><label for="oculto_nac">Oculto:</label>
  <input type="text" value="<?php echo $var['oculto_nac']; ?>" size="10" name="oculto_nac" id="oculto_nac" class="opcional">
</td>
</tr>

<tr>
<td>
 <label for="precio_ref">Precio de referencia en AR$:</label>
</td>
<td>
  <div id="p_ref"> <input type="text" value="15" size="10" name="precio_ref" id="precio_ref" class="opcional"> </div>
</td>
</tr>

</table>
 <center>
 <br />Los casilleros marcados con * son obligatorios.<br />
 <button type="submit" name="enviar" value="enviar">Modificar</button>
</form>
 </center>
</fieldset>
</div>


</body>
</html>
