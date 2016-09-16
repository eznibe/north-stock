<?php

require_once('include/TinyAjax.php');
require_once('include/php-dump.php');
require_once('main.php');
require_once('dbutils.php');

session_start();

db_connect();


  function saveDescripcion($descripcion, $id_orden) {

	$query = "UPDATE Orden SET descripcion = '$descripcion' WHERE id_orden = $id_orden";
	$result = mysql_query($query);

	$tab = new TinyAjaxBehavior();
	$tab->add( TabEval::getBehavior("showFeedback()"));

	return $tab->getString();
  }

	$ajax = new TinyAjax();
	$ajax->showLoading();
	$ajax->setRequestUri('orden_ver_ajax.php');
	$ajax->exportFunction("saveDescripcion", array("descripcion", "id_orden"));

	$ajax->process();

?>
<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />
  <script language="JavaScript" src="javascript/jsGral.js?"<?php echo $var['date']; ?>></script>
  <script language="JavaScript" src="include/jquery-1.12.4.min.js"></script>

<script type="text/javascript">

function update_orden(ID_ORDEN_ITEM)
{
 var url="orden_update.php?id_orden_item=" + ID_ORDEN_ITEM;
 window.open(url,"producto_detalle","toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=yes, resizable=yes, width=600, height=300");
}

function confirma()
{
var envio = confirm("Desea eliminar el subproducto seleccionado?")
if (envio == true)
 {
  document.forms[0].submit();
 }
}

function showFeedback()
{
	document.getElementById('modifiedDescripcion').innerHTML = " (guardada)";
}

function toggleDescripcion() {
	var container = document.getElementById('descripcionContainer');

	container.style.display = (container.style.display=='none') ? 'inline-table' : 'none';
}

function descripcionKeyPress() {
	document.getElementById('modifiedDescripcion').innerHTML = " (sin guardar)";
}

function guardarDespacho() {
  // var data = {despacho: $('#despacho').val()};
  $.ajax({
    url:'api/ordenes.php?guardarDespacho=true&id_orden='+$('#id_orden').val()+'&despacho='+$('#despacho').val(),
    success: function(data, status){
      //console.log("Data: " + data + "\nStatus: " + status);
      $('#label_despacho').text('Guardado');
    }
  })
}

function despachoKeyPress() {
	$('#label_despacho').text('Guardar');
}

</script>
<?php 	$ajax->drawJavaScript(); ?>
</head>
<body class="ppal" >

<div class="altas">
<fieldset>
 <legend><?php echo $var['header']; ?></legend>
   <?php echo $var['mensaje']; ?>

<form action="form_orden_confirma.php" method="post" target="_self" name="orden_confirma" onsubmit="return validar_tabla_orden_confirmar();">

<table width="100%" id="orden_table">

<tr class="provlisthead">
  <th>Item</th> <th>Cod. producto</th> <th>Cantidad</th> <th>Tipo<br>envio</th> <th>Unidad</th> <th>Precio<br /> unitario</th> <th>Precio</th> <th>Moneda</th>
</tr>
  <?php echo $var['orden']; ?>
</table>
 <center>
 <br><span id="error" style="display:none; color:red;"><b>Hay error en las cantidades de uno de los items. No se puede confirmar la orden.</b></span>
 <br />Total de la compra: US$ <?php echo $var['total_dolar']; ?> &nbsp;=&nbsp; $ <?php echo $var['total_pesos']; ?><br /><br />


</center>


<div style="float:left; margin-top: 15px;">
  <input type="hidden" value="<?php echo $var['cant_filas']; ?>" size="10" name="rows" id="rows">
  <input type="hidden" value="orden_confirma" size="10" name="formname" id="formname">
  <input type="hidden" value="<?php echo $var['id_orden']; ?>" size="10" name="id_orden" id="id_orden">
  <button type="submit" name="enviar" value="enviar">Confirmar pedido</button>
</div>

</form>


<div style="float:right;">
  <form action="form_orden_elimina.php" method="post" target="_self" name="orden_elimina">
    <input type="hidden" value="orden_elimina" size="10" name="formname" id="formname">
    <input type="hidden" value="<?php echo $var['id_orden']; ?>" size="10" name="id_orden" id="id_orden">
    <button type="submit" name="enviar" value="enviar">Eliminar pedido</button>
  </form>
</div>


 <table width="100%">
 <tr>
 <td>
   <div style="margin-bottom: 10px;">
     <label>Despacho:</label>
     <input type="text" value="<?php echo $var['despacho']; ?>" size="20" name="despacho" id="despacho" onkeypress="despachoKeyPress();">
     <button name="guardar" value="guardar" style="margin-left: 20px;" onclick="guardarDespacho();"><label id="label_despacho">Guardar</label></button>
   </div>
 </td>
 </tr>

 <tr>
   <td>
     <div style="margin-bottom: 15px;"><a onclick="toggleDescripcion();" class="toggleDescripcion">Ver descripcion</a></div>
   </td>
 </tr>
 </table>

<table style="width: 100%; display:none;" id="descripcionContainer">
 <tr class="headerDescripcion">
 	<td>
 		<span class="tituloDescripcion">Descripcion</span><span id="modifiedDescripcion"></span>
 		<span class="guardarDescripcion"><a onclick="saveDescripcion();" class="linkGuardarDescripcion">Guardar</a></span>
 	</td>
 </tr>
 <tr>
	 <td>
	 	<textarea rows="6" name="descripcion" id="descripcion" style="width:100%;" onkeypress="descripcionKeyPress();"><?php echo $var['descripcion']; ?></textarea>
  		<input type="hidden" value="<?php echo $var['id_orden']; ?>" size="10" name="id_orden" id="id_orden">
	 </td>
 </tr>
</table>

 <table>
<tr>
<td align="left">La cotizacion del dolar considerada es <?php echo $var['cotiz_dolar']; ?> $ correspondiente al dia <?php echo $var['cotiz_fecha']; ?> cuando se genero la compra
</td>
</tr>
</table>

</fieldset>
</div>


</body>
</html>
