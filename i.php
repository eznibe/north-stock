<?php

$codigo = $_GET['codigo'];

require("barcode.php");
$barcode =& new barcode();
$barcode->create("4006353", "UPC-E","");

?>
