<?php

include 'main.php';
include 'dbutils.php';

session_start();

db_connect();

//$producto = "";
$pcategoria = "";
$categoria = "";
$proveedor = "";
//$scan = "si";
//$stock_minimo = "";
$codigo_proveedor = "";
$codigo_barras = "";

$mensaje = "";
$hits_prov_mensaje = "";
$hits_prod_mensaje = "";
$focus = "forms[0].pcategoria";
$proveedores = "";
$proveedor = "";

$catval = "";
$catname = "";
$provval = "";
$provname = "";
$barras_class = "obligatorio";
$barras_sign = "*";
$unidades = get_units_opt(0);
$unidad_descarga = "";


$_SESSION["estado"] = "busca_proveedores";
$_SESSION["categoria"] = FALSE;
$_SESSION["proveedor"] = FALSE;

$_SESSION["catval"] = "";
$_SESSION["catname"] = "";
$_SESSION["provval"] = "";
$_SESSION["provname"] = "";


$var = array("mensaje" => $mensaje, "hits_prov_mensaje" => $hits_prov_mensaje,
  "hits_prod_mensaje" => $hits_prod_mensaje = "", "pcategoria" => $pcategoria,
  "categoria" => $categoria, 
  "codigo_proveedor" => $codigo_proveedor, "codigo_barras" => $codigo_barras,
  "focus" => $focus, "proveedores" => $proveedores,
  "barras_class" => $barras_class,
  "barras_sign" => $barras_sign,
  "proveedor" => $proveedor,
  "catval" => $catval,
  "catname" => $catname,
  "provval" => $provval,
  "provname" => $provname,
  "unidades" => $unidades,
  "unidad_descarga" => $unidad_descarga);


eval_html('producto_alta.html', $var);
