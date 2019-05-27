<?php

$file = "pedido.txt";
$f = fopen($file, "rb"); 
$content_len = (int) filesize($file); 
$content_file = fread($f, $content_len); 
fclose($f); 

$output_file = 'Pedido.txt'; 

@ob_end_clean(); 
@ini_set('zlib.output_compression', 'Off'); 
//ob_start();
header('Pragma: public'); 
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1 
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1 
header('Content-Transfer-Encoding: binary'); 
header("Content-length: $content_len");
header('Content-Type: application/octet-stream; name="' . $output_file . '"'); //This should work for the rest 
header('Content-Disposition: attachment; filename="' . $output_file . '"');
header("Content-Type: application/force-download");
header("Content-Type: application/download");
header("Pragma:no-cache");
header("Expires:0");
//header('Content-Disposition: attachment; filename="downloaded.pdf"');

echo $content_file; 
//ob_end_flush();
exit();

?>
