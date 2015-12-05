<?php

require_once('include/TinyAjax.php');
require_once('include/php-dump.php');
include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

$mensaje = $_GET['mensaje'];
$focus = "forms[0].id_grupo";

$grupo = get_group_opt(0);

$var = array("mensaje" => $mensaje,
  "grupo" => $grupo,
  "agrupacion_contable" => 2,
  "focus" => $focus,
  );


  	function updateGroupFields($id_grupo) {

		$query = "SELECT grupo, agrupacion_contable FROM Grupo WHERE id_grupo = $id_grupo";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		$grupo = $row[0];
		$agrupacion_contable = $row[1];
		
		$grupo_input = "<input type='text' value='$grupo' size='50' name='grupo' id='grupo' class='obligatorio'> *";
		
		$tab = new TinyAjaxBehavior();
//		$tab->add( TabInnerHtml::getBehavior("g_cont", $grupo_input));
		$tab->add( TabEval::getBehavior("cambiarComboAgrupacion($agrupacion_contable)"));
		$tab->add( TabEval::getBehavior("cambiarFieldGrupo('$grupo')"));
//		$tab->add( TabAlert::getBehavior("Hola"));		
//		$tab->add( TabSetValue::getBehaviour("grupo", $grupo));

		return $tab->getString();
	}

	$ajax = new TinyAjax();
	$ajax->showLoading();
	$ajax->exportFunction("updateGroupFields", array("id_grupo"));

	$ajax->process();
?>

<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />

<script type="text/javascript">

var old_agrup_contable;

function setfocus()
{ 
document.<?php echo $var['focus']; ?>.focus()
}

function confirma()
{

var message = (document.getElementById("agrupacion_dd").value != old_agrup_contable) 
				? "Atencion! La modificacion que intenta realizar en el grupo, modificara la agrupacion contable de todos los items del mismo." 
				: "Desea modificar el grupo seleccionado?";

var envio = confirm(message);
 
 if (envio == true)
 {
  return true;
 }
 else
 {
  return false;
 }
}

function cambiarFieldGrupo(grupo) {

	document.getElementById("grupo").value = grupo;
}

function cambiarComboAgrupacion(agrupacion) {

	var selectBox = document.getElementById("agrupacion_dd"); 
	
	selectBox[agrupacion + 1].selected = true;

	old_agrup_contable = agrupacion;
}

</script>
<?php 	$ajax->drawJavaScript(); ?>
</head>
<body class="ppal" onload = "setfocus()">

<div class="altas">
<fieldset>
 <legend>Modificacion de grupo</legend>
   <?php echo $var['mensaje']; ?>
<table>
<form action="form_grupo_modificacion.php" method="post" target="_self" name="grupo_modificacion" onsubmit="return confirma();">
  <input type="hidden" value="grupo_modificacion" size="10" name="formname" id="formname"> 

<tr>
<td>
 <label for="id_grupo">Seleccionar el grupo:</label>
</td>
<td>
  <select name="id_grupo" id="id_grupo" class="obligatorio" onchange="updateGroupFields();">
   <option value="0">seleccionar</option>
   <?php echo $var['grupo']; ?>
  </select> *
</td>
</tr>

<tr>
<td>
 <label for="grupo">Nuevo nombre del grupo:</label>
</td>
<td>
  <div id="g_cont"><input type="text" value="" size="50" name="grupo" id="grupo" class="obligatorio"> * </div>
</td>
</tr>

<tr>
<td>
 <label for="agrupacion">Agrupacion contable:</label>
</td>
<td>
 <select name="agrupacion_dd" id="agrupacion_dd" class="obligatorio">
   <option value="-1">seleccionar</option>
   <option value="0">0</option>
   <option value="1">1</option>
   <option value="2">2</option>
   <option value="3">3</option>
   <option value="4">4</option>
   <option value="5">5</option>
   <option value="6">6</option>
   <option value="7">7</option>
   <option value="8">8</option>
   <option value="9">9</option>
   </select> *
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
