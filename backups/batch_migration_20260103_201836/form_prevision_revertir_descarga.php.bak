<?php

include 'main.php';
include 'dbutils.php';

session_start();

$id_prevision = $_POST['id_prevision'];

$formname = $_POST['formname'];

$valid_user = $_SESSION['valid_user'];

$mensaje = "";
$focus = "forms[0].pais";

db_connect();

$fecha = date("Y-m-d");

// actualizar stock disponible de los items de la prevision
$query = "SELECT pi.id_prevision_item, pi.id_item, pi.cantidad
  FROM prevision p 
  JOIN previsionitem pi on pi.id_prevision = p.id_prevision
  WHERE p.id_prevision = $id_prevision";


$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
  $cantidad_factor = (get_factor_unidades($row[1])) * $row[2];
  $query = "UPDATE item i 
    SET	i.stock_disponible = i.stock_disponible + $cantidad_factor
    WHERE i.id_item = $row[1]";

  $res = mysql_query($query);

  if($row[2] != 0) { // log cantidad revertida distinta a cero
    log_trans($valid_user, 1, $row[1], $row[2], $fecha, 'NULL', $id_prevision);
  }
}

// actualizar prevision
$query = "UPDATE prevision SET fecha_descarga = null, usuario_descarga = null WHERE id_prevision = $id_prevision";

$result = mysql_query($query);

// actualizar prevision items
$query = "UPDATE previsionitem SET descargado = false WHERE id_prevision = $id_prevision";

$result = mysql_query($query);

log_trans($valid_user, 27, 0, 0, $fecha, 'NULL', $id_prevision);

$var = array(
  "id_prevision" => $id_prevision,
  "mensaje" => "La descarga de la previsión ha sido revertida."
);
eval_html('prevision_accion_fin.html', $var);

?>

