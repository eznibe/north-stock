<?php

// require_once('include/TinyAjax.php');
// require_once('include/php-dump.php');
// require_once('main.php');
// require_once('dbutils.php');

//session_start();

//db_connect();


	// $ajax = new TinyAjax();
	// $ajax->showLoading();

	// $ajax->process();

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
 var url="orden_update_arribo.php?id_orden_item=" + ID_ORDEN_ITEM;
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

  $.ajax({
    url:'api/ordenes.php?guardarNrFactura=true&id_orden='+$('#id_orden').val()+'&nr_factura='+$('#nr_factura').val(),
    success: function(data, status){
      //console.log("Data: " + data + "\nStatus: " + status);
      $('#label_despacho').text('Guardado');
    }
  })

  $.ajax({
    url:'api/ordenes.php?guardarFacturaAR=true&id_orden='+$('#id_orden').val()+'&factura_AR='+$('#factura_AR').val(),
    success: function(data, status){
      //console.log("Data: " + data + "\nStatus: " + status);
      $('#label_despacho').text('Guardado');
    }
  })

  $.ajax({
    url:'api/ordenes.php?guardarproveedorAR=true&id_orden='+$('#id_orden').val()+'&proveedor_AR='+$('#proveedor_AR').val(),
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

<form action="form_orden_compra_proveedor.php" method="post" target="_self" name="orden_compra" onsubmit="return validar_tabla();">

<table width="100%" id="orden_table" name="orden_table">
<tr class="provlisthead">
<th>Item</th>  <th>Cod. producto</th> <th>Cantidad original</th> <th>Cantidad pendiente</th> <th>unidad</th> <th>Precio<br /> unitario</th> <th>Precio</th> <th>Moneda</th>
</tr>
  <?php echo $var['orden']; ?>
</table>

<center>
  <br><span id="error" style="display:none; color:red;"><b>Hay error en una de las cantidades pedidas. No se puede genarar la orden</b></span>
  <br />Total de la compra: US$ <?php echo $var['total_dolar']; ?> &nbsp;=&nbsp; $ <?php echo $var['total_pesos']; ?><br /><br />
</center>


</form>


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
