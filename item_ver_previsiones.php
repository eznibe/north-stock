<?php

include 'main.php';
include 'dbutils.php';

// session_start();

db_connect();

$id_item = isset($_GET['id_item']) ? $_GET['id_item'] : "";

// Todos los previsiones del item
//
$previsiones = getPrevisiones($id_item);

$focus = "forms[0].cantidad";

$mensaje = "Previsiones del item:";

$var = array("focus" => $focus,
        "mensaje" => $mensaje,
        "previsiones" => $previsiones);

eval_html('item_ver_previsiones.html', $var);


// Functions

function getPrevisiones($id_item) {
  $query = "SELECT 
    p.id_prevision, 
    coalesce(numero_orden, concat('(', p.id_prevision, ')')),
    coalesce(DATE_FORMAT(p.fecha_entrega, '%d-%m-%Y'), '-') AS fecha_entrega, 
    pi.cantidad
  FROM prevision p 
  JOIN previsionitem pi on p.id_prevision = pi.id_prevision
  WHERE p.fecha_descarga is null and pi.descargado = false 
    AND pi.id_item = $id_item
  GROUP BY p.id_prevision
	ORDER BY p.fecha_entrega, p.numero_orden, p.id_prevision";

  $result = mysql_query($query);

  $previsiones = "";
  while ($row = mysql_fetch_array($result))
  {
    $previsiones = $previsiones . "<tr class=\"provlistrow\">
      <td><a class=\"list\" href=\"prevision_ver.php?id_prevision=$row[0]\">$row[1]</a></td>
      <td>$row[2]</td>
      <td>$row[3]</td>
    </tr>";
  }
  return $previsiones;
}

?>
