<?php
	
	echo date('d.m.y');
	
	echo '<br/>';
	
	$input="Alienigenados";
	
	echo str_pad($input, 16 , "*").'Fin<br/>';
	
	echo str_pad($input, 10 , "*").'Fin<br/>';
	 
	 $SERVER_NAME = $_SERVER["SERVER_NAME"];
$IP = gethostbyname ($SERVER_NAME);
$server = gethostbyaddr($IP);
echo "<br>Server IP: $IP";
echo "<br>Server Name: $server";

?>
