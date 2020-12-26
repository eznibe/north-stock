<?php

require_once('include/TinyAjax.php');
require_once('include/php-dump.php');
require_once('main.php');
require_once('dbutils.php');

session_start();

db_connect();


?>
<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />
  <script language="JavaScript" src="javascript/jsGral.js?"<?php echo $var['date']; ?>></script>
  <script language="JavaScript" src="include/jquery-1.12.4.min.js"></script>

  <script src="https://cdn.lr-ingest.io/LogRocket.min.js" crossorigin="anonymous"></script>
  <script>window.LogRocket && window.LogRocket.init('nxe6lb/north-stock-hw');</script>

<script type="text/javascript">

const username = "<?php echo $var['username'];?>";
if (username) {
	LogRocket.identify(username, {
		name: username
	});
}

function update_prevision(id_prevision_item)
{
 var url="prevision_item_update.php?id_prevision_item=" + id_prevision_item;
 window.open(url,"producto_detalle","toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=yes, resizable=yes, width=600, height=300");
}

function agregar_prevision_item(id_prevision)
{
  const url="prevision_item_nuevo.php?id_prevision=" + id_prevision;
  window.open(url,"prevsion_item_nuevo","toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=yes, resizable=yes, width=750, height=300");
}

function confirmaEliminar()
{
  return confirm("Desea eliminar la prevision?")
}

function confirmaDescargar()
{
  // validar que haya stock suficiente de todos los items en la orden
  if ($('#stock_suficiente').val() !== 'true') {
    alert('No se puede realizar la descarga, hay items con stock disponible insuficiente.')
    return false;
  }
  return confirm("Desea descargar la prevision?")
}

function confirmaRevertir()
{
  return confirm("Desea revertir la descarga de la prevision?")
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
	// document.getElementById('modifiedDescripcion').innerHTML = " (sin guardar)";
}

function guardarPrevision() {
  // var data = {despacho: $('#despacho').val()};
  let fechaEntrega = null;
  const dia = $('#dia').val();
  const mes = $('#mes').val();
  const anio = $('#ano').val();
  if (dia !== '0' && mes !== '0' && anio !== '0') {
    fechaEntrega = anio+'-'+mes+'-'+dia;
  }

  $.ajax({
    url:'api/prevision.php?guardarPrevision=true&id_prevision='+$('#id_prevision').val()
      + ($('#numero_orden').val() ? '&numero_orden='+$('#numero_orden').val() : '')
      + ($('#cliente') ? '&cliente='+$('#cliente').val() : '')
      + ($('#descripcion').val() ? '&descripcion='+$('#descripcion').val() : '')
      + (fechaEntrega ? '&fecha_entrega='+fechaEntrega : ''),
    success: function(data, status){
      //console.log("Data: " + data + "\nStatus: " + status);
      // $('#label').text('Guardado');
    }
  })
}

function despachoKeyPress() {
	$('#label').text('Guardar');
}

</script>
<style>
  .form-field {
    display: inline-block;
    min-width: 120px;
  }
</style>
</head>
<body class="ppal" >

<div class="altas">
<fieldset>
 <legend><?php echo $var['header']; ?></legend>
   <?php if (isset($var['mensaje'])) echo $var['mensaje']; ?>

   
  <table width="100%" id="orden_table">

     <tr class="provlisthead">
       <th>item</th> <th>Cod. producto</th> <th>Cantidad</th> <th>unidad</th> <th>Precio<br /> unitario</th> <th>Precio</th> <th>Moneda</th>
      </tr>
      <?php echo $var['previsionitems']; ?>
  </table>
    
    <!-- <center>
      <br><span id="error" style="display:none; color:red;"><b>Hay error en las cantidades de uno de los items. No se puede descargar la prevision.</b></span>
      <br />Total de la compra: US$ <?php echo $var['total_dolar']; ?> &nbsp;=&nbsp; $ <?php echo $var['total_pesos']; ?><br /><br />
    </center> -->
    
  <div style="margin-top: 15px;">

    <div style="float:left; <?php echo $var['descargada'] == 'true' ? 'display:none;' : '' ?>">
      <a href="#" onclick="agregar_prevision_item(<?php echo $var['id_prevision']; ?>)"><button type="submit">Agregar item</button></a>
    </div>
  
    <div style="float:right; <?php echo $var['descargada'] == 'true' ? 'display:none;' : '' ?>">
      <form action="form_prevision_descarga.php" method="post" target="_self" name="prevision_confirma" style="display:inline-block; margin-right:50px;" onsubmit="return confirmaDescargar();">
        <input type="hidden" value="<?php echo $var['cant_filas']; ?>" size="10" name="rows" id="rows">
        <input type="hidden" value="prevision_confirma" size="10" name="formname" id="formname">
        <input type="hidden" value="<?php echo $var['id_prevision']; ?>" size="10" name="id_prevision" id="id_prevision">
        <input type="hidden" value="<?php echo $var['stock_suficiente']; ?>" size="10" name="stock_suficiente" id="stock_suficiente">
        <button type="submit" name="enviar" value="enviar">Descargar prevision</button>
      </form>
      <form action="form_prevision_elimina.php" method="post" target="_self" name="prevision_elimina" style="display:inline-block;" onsubmit="return confirmaEliminar();">
        <input type="hidden" value="prevision_elimina" size="10" name="formname" id="formname">
        <input type="hidden" value="<?php echo $var['id_prevision']; ?>" size="10" name="id_prevision" id="id_prevision">
        <button type="submit" name="enviar" value="enviar">Eliminar prevision</button>
      </form>
    </div>

    <div style="float:right; <?php echo $var['descargada'] == 'false' ? 'display:none;' : '' ?>">
      <form action="form_prevision_revertir_descarga.php" method="post" target="_self" name="prevision_confirma" style="display:inline-block;" onsubmit="return confirmaRevertir();">
        <input type="hidden" value="prevision_revertir" size="10" name="formname" id="formname">
        <input type="hidden" value="<?php echo $var['id_prevision']; ?>" size="10" name="id_prevision" id="id_prevision">
        <input type="hidden" value="<?php echo $var['descargada']; ?>" size="10" name="desc" id="desc">
        <button type="submit" name="enviar" value="enviar">Revertir descarga</button>
      </form>
    </div>
  </div>  


 <table width="100%" style="padding-top:15px;">
  <tr>
    <td>
      <div style="margin-bottom: 10px;">
        <label class="form-field">Nr. de orden:</label>
        <input type="text" value="<?php echo $var['numero_orden']; ?>" size="30" name="numero_orden" id="numero_orden">
      </div>
    </td>
  </tr>

  <tr>
    <td>
      <div style="margin-bottom: 10px;">
        <label class="form-field">Cliente:</label>
        <input type="text" value="<?php echo $var['cliente']; ?>" size="30" name="cliente" id="cliente">
      </div>
    </td>
  </tr>

  <tr>
  <td>
    <div style="margin-bottom: 10px;">
      <label class="form-field">Fecha de entrega:</label>
      <select name="dia" id="dia" class="obligatorio">
          <?php echo $var['dia']; ?>
      </select>
      /
      <select name="mes" id="mes" class="obligatorio">
          <?php echo $var['mes']; ?>
      </select>
      /
      <select name="ano" id="ano" class="obligatorio">
          <?php echo $var['ano']; ?>
      </select>

      <button name="guardar" value="guardar" style="margin-left: 100px;" onclick="guardarPrevision();"><label id="label">Guardar</label></button>
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
 		<span class="guardarDescripcion"><a onclick="guardarPrevision();" class="linkGuardarDescripcion">Guardar</a></span>
 	</td>
 </tr>
 <tr>
	 <td>
	 	<textarea rows="6" name="descripcion" id="descripcion" style="width:100%;" onkeypress="descripcionKeyPress();"><?php echo $var['descripcion']; ?></textarea>
  		<input type="hidden" value="<?php echo $var['id_prevision']; ?>" size="10" name="id_prevision" id="id_prevision">
	 </td>
 </tr>
</table>

 <table>
<tr>
<!-- <td align="left">La cotizacion del dolar considerada es <?php echo $var['cotiz_dolar']; ?> $ correspondiente al dia <?php echo $var['cotiz_fecha']; ?> cuando se genero la compra -->
</td>
</tr>
</table>

</fieldset>
</div>


</body>
</html>
