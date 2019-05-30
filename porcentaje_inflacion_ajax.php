
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title></title>

 <link rel="stylesheet" type="text/css" href="north.css" />

 <script language="JavaScript" src="javascript/jsGral.js?"<?php echo $var['date']; ?>></script>
  <script language="JavaScript" src="include/jquery-1.12.4.min.js"></script>

<script type="text/javascript">

let isNew;

function actualizarPorcentajeInflacion() {
  // var data = {despacho: $('#despacho').val()};
  $.ajax({
    url:'api/inflacion.php?actualizarInflacion=true&anio='+$('#anio').val()+'&mes='+$('#mes').val()+'&valor='+$('#valor').val()+(isNew ? '&isNew=true' : ''),
    success: function(data, status){
      //console.log("Data: " + data + "\nStatus: " + status);
      isNew = false;
    }
  })
}

function obtenerPorcentajeInflacion(anio, mes) {

  if (!anio) {
    anio = $('#anio').val();
  }
  if (!mes) {
    mes = $('#mes').val();
  }

  $.ajax({
    url:'api/inflacion.php?obtenerInflacion=true&anio='+anio+'&mes='+mes,
    success: function(data, status){
      console.log("Data: " + data + "\nStatus: " + status);
      data = JSON.parse(data);
      $('#valor').val(data.valor);
      if (!data.valor) {
        isNew = true
      } else {
        isNew = false;
      }
    }
  })
}

function init() {

  var optionsAnios = "";
  let anio_actual = 2019;// TODO

  for(var i = anio_actual - 5; i <= anio_actual + 1; i++) {
    optionsAnios += "<option value='" + i + "' " + (i == anio_actual ? "selected" : "") + ">" + i + "</option>";
  }
  $('#anio').append(optionsAnios);

  var optionsMeses = "";
  let mes_actual = 5; // TODO
  let meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
  for(var i = 0; i < 12; i++) {
    optionsMeses += "<option value='" + (i+1) + "' " + (i+1 == mes_actual ? "selected" : "") + ">" + meses[i] + "</option>";
  }
  $('#mes').append(optionsMeses);

  obtenerPorcentajeInflacion(anio_actual, mes_actual);
}

</script>

</head>
<body class="ppal" onload="init()">
<fieldset>
 <legend>Inflacion</legend>
	 <em class="error"><?php echo $var['mensaje']; ?><br></em>
<table>
<tr>
<td>
 <label for="precio">Porcentaje inflacion:</label>
</td>
<td>
  <select type="text" id="anio" onchange="obtenerPorcentajeInflacion()">
    <option>-</option>
  </select>
</td>
<td>
  <select type="text" id="mes" onchange="obtenerPorcentajeInflacion()">
    <option>-</option>
  </select>
</td>
<td>
  <input type="text" id="valor" size="5">
</td>
</tr>
 </table>
 <center><button type="button" name="enviar" value="enviar" onclick="actualizarPorcentajeInflacion();">Guardar</button></center>
 
</fieldset>
</body>
</html>