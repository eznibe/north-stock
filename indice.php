<html>
<head>
  <title>North Sails - Sistema de Control de Stock</title>
  <link rel="stylesheet" type="text/css" href="north.css" />

<script type="text/javascript">

function setfocus()
{ 
 window.name="auth";
 au = window.open("","_self","");
 au.focus();
 au.document.forms[0].username.focus();
}

function abrir_ventana()
{
 mw = window.open("","main_win","resizable=1");
 mw.focus();
}

</script>

</head>
<body class="ppal" onload = "setfocus()">

<form action="form_login.php" method="post" target="main_win" onsubmit="abrir_ventana()" name="auth">

<div class="auth">
<fieldset>
 <legend>Registraci&oacute;n</legend>
 <label for="username">Nombre de usuario:</label><br />
  <input type="text" name="username" id="username"><br />
 <label for="password">Contrase&ntilde;a:</label><br />
  <input type="password" name="password" id="password"><br />
 <center>
 <button type="submit" name="enviar" value="enviar">Ingresar</button>
 </center>
</fieldset>
</div>

</form>

</body>
</html>  
