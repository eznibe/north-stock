<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

$username = $_SESSION['valid_user'];

$id_prevision = $_POST['id_prevision'];
$id_item = $_POST['id_item'];
$cantidad = $_POST['cantidad'];
$producto = $_POST['producto'];
$unidad = $_POST['unidad'];
$numero_orden = $_POST['numero_orden'];

asignar_a_prevision($id_item, $id_prevision, $cantidad, $numero_orden);

$prevision = getPrevision($id_prevision);

$mensaje = "Acaba de asignar $cantidad $unidad $producto a la prevision $prevision.<p>\n";
$focus = "forms[0].pproducto";
$producto = "";

$var = array("mensaje" => $mensaje,
  "focus" => $focus,
  "username" => $username); 

eval_html('producto_salida.html', $var);


function asignar_a_prevision($id_item, &$id_prevision, $cantidad, $numero_orden) {

  if ($id_prevision == "-1") {
    $numero_orden = isset($numero_orden) && $numero_orden <> "" ? "'$numero_orden'" : 'null';

    $query = "INSERT INTO prevision (numero_orden) VALUES ($numero_orden)";
    $result = $pdo->query($query);

    $query = "SELECT p.id_prevision
      FROM prevision p
      ORDER BY p.id_prevision desc
      LIMIT 1";

    $result = $pdo->query($query);
    $row = $result->fetch(PDO::FETCH_NUM);

    $id_prevision = $row[0];
  }

  $insert = "INSERT INTO previsionitem (id_prevision, id_item, cantidad)
    VALUES ($id_prevision, $id_item, $cantidad)";
  $result = $pdo->query($insert);
  
  log_trans($_SESSION['valid_user'], 21, $id_item, $cantidad, date("Y-m-d"), 'NULL', $id_prevision);
}

function getPrevision($id_prevision) {

  $query = "SELECT coalesce(p.numero_orden, concat('(', p.id_prevision, ')')) as prevision FROM prevision p WHERE id_prevision = $id_prevision";
  $result = $pdo->query($query);
  $row = $result->fetch(PDO::FETCH_NUM);
  return $row["prevision"];
}