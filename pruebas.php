<?php
echo "<a href='test'>Test</a>";
echo '<br>';
$new = htmlspecialchars("<a href='test'>Test\"</a>", ENT_QUOTES);
echo $new; // &lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;

$var = htmlspecialchars("0052505 - SOLENOID 12V SINGLE DIR'EC\"TION IM", ENT_QUOTES);

?>

<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />

</head>
<body class="ppal" onload = "setfocus()">


<div class="altas">
<fieldset>
 <legend>Modificacion de producto</legend>

<table>
<form action="form_categoria_modificacion.php" method="post" target="_self" name="categoria_alta">
  <input type="hidden" value="categoria_datosmodificar" size="10" name="formname" id="formname">
 
<tr>
<td>
 <label for="categoria">Nombre del producto:</label>
</td>
<td>
  <input type="text" value="<?php echo $var; ?>" size="50" name="categoria" id="categoria" class="obligatorio"> *
</td>
</tr>

</table>
 <center>
 <br />Los casilleros marcados con * son obligatorios.<br />
 <button type="submit" name="enviar" value="enviar">Modificar</button>
 </center>
 </form>
</fieldset>
</div>


</body>
</html>