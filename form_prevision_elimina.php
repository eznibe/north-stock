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


// Elimino prevision y sus items
$query = "DELETE FROM previsionitem WHERE id_prevision = $id_prevision";

$result = $pdo->query($query);

$query = "DELETE FROM prevision WHERE id_prevision = $id_prevision";

$result = $pdo->query($query);

log_trans($valid_user, 25, 0, 0, date("Y-m-d"), 'NULL', $id_prevision);


$var = array(
  "id_prevision" => $id_prevision,
  "mensaje" => "La previsión ha sido eliminada."
 );
eval_html('prevision_accion_fin.html', $var);

?>

