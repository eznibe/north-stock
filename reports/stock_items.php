<?php

include '../main.php';
include '../dbutils.php';

check_session();

db_connect();

// create file from sql query

$campos = array(
    "id_item",
    "codigo_barras",
    "categoria",
    "grupo",
    "tipo_proveedor",
    "proveedor",
    "codigo_proveedor",
    "unidad",
    "factor_unidades",
    "precio_fob",
    "precio_nac",
    "precio_ref",
    "fecha_ultimo_precio",
    "stock_disponible",
    "stock_minimo",
    "porcentaje_impuesto_categoria",
    "prevision",
    "compras",
    "en_transito_nacional",
    "en_transito_courier",
    "en_transito_courier2",
    "en_transito_aereo1",
    "en_transito_aereo2",
    "en_transito_maritimo",
    "en_transito_desconocido"
);

$content = "";
foreach ($campos as $campo) {
    $content .= $campo . ";";
}
$content .= "\n";

$query = "SELECT *,
    ROUND(precio_fob, 2) as precio_fob, ROUND(precio_nac, 2) as precio_nac, ROUND(precio_ref, 2) as precio_ref
    FROM v_export_items
    ";
    //where id_item = 1086 or id_item = 3341 or id_item = 1632

$result = $pdo->query($query);

while ($row = $result->fetch(PDO::FETCH_NUM))
{
    foreach ($campos as $campo) {
        //$content .= '"  ' . $row[$campo] . '";';
        $content .= $row[$campo] . ';';
    }
    $content .= "\n";
}

// download file 

$content_len = strlen($content); 

$output_file = 'export.stock.items.csv'; 

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

echo $content;

//ob_end_flush();
exit();

?>
