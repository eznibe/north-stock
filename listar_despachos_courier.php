<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<p class=\"imprimir\">
        <a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
        </p>";


$query = "SELECT o.despacho , count(*) items, max(o.factura_AR) facturaAR, sum(oi.cantidad) units, DATE_FORMAT(max(fecha), '%d-%m-%Y') as arriveDate, 
    truncate(sum(coalesce(oi.precio_fob, oi.precio_ref) * oi.cantidad), 2) total, max(proveedor_AR) proveedorAR, GROUP_CONCAT(o.id_orden) 
  FROM orden o join ordenitem oi on o.id_orden = oi.id_orden
  WHERE o.despacho like '%PART%'
  GROUP BY o.despacho
  ORDER BY o.fecha desc";
$result = $pdo->query($query);

$aux = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $aux = $aux . "<tr class=\"provlistrow\"><td>$row[0]<td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>\n";
}

$titulo = "Despachos courier";

$var = array("rows" => $aux,
	"imprimir" => $imprimir,
	"titulo" => $titulo);
eval_html('listar_despachos_courier.html', $var);
