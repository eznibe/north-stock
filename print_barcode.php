<?php

include 'main.php';

//check_session();


//$tipo = "Code 39";
$tipo = "EAN-13";

$var = array("codigo_barras" => "*NSA123456*",
	"tipo" => $tipo);

//$var = array("codigo_barras" => "*NSA123456*");
eval_html('print_barcode.html', $var);
