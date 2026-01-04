<?php

include_once 'include/php-dump.php';

//check_session();

function desconectar ()
{
 session_unset();
 session_destroy();
 $var="";
 eval_html('index.php',$var);
// eval_html('window_close.html',$var);
// eval_html('main_menu_desconectado.html',$var);
}

function check_session() {
  session_start();
  if (!isset($_SESSION["valid_user"]))
  {
   desconectar();
   exit();
  }
}



function eval_html($filename, $var) {
  if ((file_exists($filename)) and (is_readable($filename))) {
    $handle = fopen($filename, "r");
    $openedfile = fread($handle, filesize($filename));

    eval('?>' . $openedfile);
  }
  else {
    print "<br />The file $filename does not exist or is not readable<br />";
  }
}

function get_units_opt($id_unidad)
{
 $unidades = "";
 $query = "SELECT unidad.id_unidad, unidad.unidad
           FROM unidad";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_unidad) $option = "option selected";
  else $option = "option";
  $unidades = $unidades . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $unidades;
}

function get_group_opt($id_grupo)
{
 $query = "SELECT id_grupo, grupo FROM grupo ORDER BY grupo";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $grupo = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_grupo) $option = "option selected";
  else $option = "option";
  $grupo = $grupo . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $grupo;
}

function get_pais_opt($id_pais)
{
 $query = "SELECT id_pais, pais FROM pais ORDER BY pais";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $pais = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_pais) $option = "option selected";
  else $option = "option";
  $pais = $pais . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $pais;
}

function get_categoria_opt($id_categoria)
{
 $query = "SELECT id_categoria, categoria FROM categoria ORDER BY categoria";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $categoria = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_categoria) $option = "option selected";
  else $option = "option";
  $categoria = $categoria . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $categoria;
}

function get_subproducto_opt($id_item)
{
 $query = "SELECT
	item.id_item, concat(categoria.categoria, \" - \", proveedor.proveedor)
	FROM
	item, categoria, proveedor
	WHERE (
	(item.id_categoria = categoria.id_categoria) AND
	(item.id_proveedor = proveedor.id_proveedor)
	)
	ORDER BY categoria.categoria";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $subproducto = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_item) $option = "option selected";
  else $option = "option";
  $subproducto = $subproducto . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $subproducto;
}

function get_scan_opt($scan)
{
 $scanvals = array('si', 'no');
 foreach ($scanvals as $val)
 {
  if ($val == $scan) $option = "option selected";
  else $option = "option";
  $scan_opt = $scan_opt . "<$option value=\"" . $val . "\">" . $val . "</option>\n";
 }
 return $scan_opt;
}

function get_proveedor_opt($id_proveedor)
{
 $query = "SELECT
	proveedor.id_proveedor, proveedor.proveedor
  FROM
	proveedor
  ORDER BY proveedor";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $proveedor = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_proveedor) $option = "option selected";
  else $option = "option";
  $proveedor = $proveedor . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $proveedor;
}

function get_tipousr_opt($id_tipo)
{
 $query = "SELECT id_tipousr, tipousr FROM tipousr tipousr ORDER BY tipousr";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $tipousr = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_tipo) $option = "option selected";
  else $option = "option";
  $tipousr = $tipousr . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $tipousr;
}

function get_usuario_opt($id_usuario)
{
 $query = "SELECT id_usuario, username FROM usuario ORDER BY username";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $usuario = "";
 while ($row = $result->fetch(PDO::FETCH_NUM))
 {
  if ($row[0] == $id_usuario) $option = "option selected";
  else $option = "option";
  $usuario = $usuario . "<$option value=\"" . htmlspecialchars(stripslashes($row[0])) . "\">" . htmlspecialchars(stripslashes($row[1])) . "</option>\n";
 }
 return $usuario;
}

function log_trans($username, $id_accion, $id_item, $cantidad, $fecha, $id_orden='NULL', $id_prevision='NULL')
{
 // id_accion: 1 ingreso 2 egreso (manual y desde descarga prevision) 3 update 4 confirma orden 5 elimina orden 6 orden arribada completa 7 orden creada 8 elimina item de orden 9 compra confirmada
 // id_accion: 21 agrego item - 23 update prevision item - 24 descarga item - 25 elimina prevision - 26 prevision descargada - 27 prevision revertida - 28 elimina item de prevision - 29 item revertido
 $query = "INSERT INTO log
	(username, id_accion, id_item, cantidad, fecha, id_orden, id_prevision)
  VALUES
	(\"$username\", $id_accion, $id_item, $cantidad, \"$fecha\", $id_orden, $id_prevision)";
 $pdo = get_db_connection();
 try {
  $pdo->exec($query);
 } catch (PDOException $e) {
  echo $e->getMessage();
 }
}

function get_group($id_grupo)
{
 $query = "SELECT grupo FROM grupo WHERE id_grupo = $id_grupo";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_groups()
{
 $query = "SELECT * FROM grupo ORDER BY grupo";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 return $result;
}

function get_proveedor($id_proveedor)
{
 $query = "SELECT proveedor FROM proveedor WHERE id_proveedor = $id_proveedor";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_categoria($id_categoria)
{
 $query = "SELECT categoria FROM categoria WHERE id_categoria = $id_categoria";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_usuario($id_usuario, $tipo)
{
 //$tipo: 1: retorna username
 //	      2: retorna nombre de usuario
 if($tipo==1) $query = "SELECT username FROM usuario WHERE id_usuario = $id_usuario";
 else         $query = "SELECT nombre   FROM usuario WHERE id_usuario = $id_usuario";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_item($id_item)
{
 $query = "SELECT
	CONCAT(categoria.categoria, \" - \", proveedor.proveedor)
  FROM
	item,
	categoria,
	proveedor
  WHERE (
	(item.id_item = $id_item) AND
	(categoria.id_categoria = item.id_categoria) AND
	(proveedor.id_proveedor = item.id_proveedor)
	)";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_unidad_descarga($id_categoria)
{
 $query = "SELECT unidad.unidad
        FROM unidad, categoria
        WHERE
                unidad.id_unidad = categoria.id_unidad_visual AND
                categoria.id_categoria = $id_categoria";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return (strtoupper($row[0]));
}

/**
 * Obtiene la unidad de compra del item dado
 */
function get_unidad_compra($id_item)
{
 $query = "SELECT unidad.unidad
  		   FROM unidad, item
    	   WHERE
            	unidad.id_unidad = item.id_unidad_compra AND
            	item.id_item = $id_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return (strtoupper($row[0]));
}

function get_stock_transito($id_item)
{
 $query = "SELECT item.stock_transito
        FROM item
        WHERE item.id_item = $id_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function set_stock_transito($id_item, $stock)
{
 $query = "UPDATE item
        SET
		stock_transito = $stock
        WHERE
                item.id_item = $id_item";
 $pdo = get_db_connection();
 return $pdo->exec($query);
}

function log_stock_transito_negativo($username, $id_item,  $id_orden, $stock_transito_actual, $stock_transito_nuevo, $cantidad_pendiente, $cantidad_user, $tipo_accion)
{
 $query = "INSERT INTO DG_transito_negativo
		   (username, id_item, id_orden, stock_transito_actual, stock_transito_nuevo, cantidad_pendiente, cantidad_user, tipo_accion)
  		   VALUES
		   (\"$username\", $id_item, $id_orden, $stock_transito_actual, $stock_transito_nuevo, $cantidad_pendiente, $cantidad_user, \"$tipo_accion\")";
 //var_dump($query);
 $pdo = get_db_connection();
 try {
  $pdo->exec($query);
 } catch (PDOException $e) {
  echo $e->getMessage();
 }
}

/**
 * Obtine el factor de unidades del item pasado como parametro
 */
function get_factor_unidades($id_item)
{
 $query = "SELECT item.factor_unidades
        FROM item
        WHERE item.id_item = $id_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_cantidad_comprar($id_orden_item)
{
 $query = "SELECT ordenitem.cantidad
        FROM ordenitem
        WHERE ordenitem.id_orden_item = $id_orden_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_ordenitem_id_item($id_orden_item)
{
 $query = "SELECT ordenitem.id_item
        FROM ordenitem
        WHERE
                ordenitem.id_orden_item = $id_orden_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_orden_status($id_orden)
{
 $query = "SELECT orden.id_status
        FROM orden
        WHERE orden.id_orden = $id_orden";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_ordenes_a_confirmar($id_proveedor)
{
 $condition = "";
 if(isset($id_proveedor) && !empty($id_proveedor)) {
	$condition =  " AND proveedor.id_proveedor = $id_proveedor ";
 }

 $query = "SELECT orden.id_orden, DATE_FORMAT(orden.fecha, '%d-%m-%Y') AS fecha, proveedor.proveedor
           FROM
		orden,
		ordenitem,
		item,
		proveedor
	  WHERE (
		(orden.id_status = 0) AND
		(ordenitem.id_orden = orden.id_orden) AND
		(item.id_item = ordenitem.id_item) AND
		(proveedor.id_proveedor = item.id_proveedor)
		$condition
	  )
	  GROUP BY orden.id_orden, orden.fecha, proveedor.proveedor
	  ORDER BY fecha, proveedor";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 return $result;
}

function get_agrupacion_contable($id_categoria)
{
 $query = "SELECT g.agrupacion_contable FROM grupo g join categoria c on c.id_grupo = g.id_grupo
		   WHERE c.id_categoria = $id_categoria";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_item_agrupacion_contable($id_item)
{
 $query = "SELECT i.agrupacion_contable FROM item i
		   WHERE i.id_item = $id_item";
 $pdo = get_db_connection();
 $result = $pdo->query($query);
 $row = $result->fetch(PDO::FETCH_NUM);
 return $row[0];
}

function get_tipos_de_envio() {

 $query = "SELECT * FROM tipoenvio ORDER BY tipo_envio, id_tipo_envio";
 $pdo = get_db_connection();
 $result = $pdo->query($query);

 return $result;
}

function es_proveedor_nacional($id_proveedor, $pais) {
	$query = "SELECT pais FROM pais, proveedor
		  WHERE pais.id_pais = proveedor.id_pais AND proveedor.id_proveedor = $id_proveedor";
	$pdo = get_db_connection();
	$result = $pdo->query($query);
	$row = $result->fetch(PDO::FETCH_NUM);

	return $row[0] == $pais;
}

function result_a_memoria($result)
{
	$rows = array();
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		array_push($rows, $row);
	}

	return $rows;
}

// Los parametros indican la fecha a seleccionar por default
function armar_select_fechas($dia_ini, $mes_ini, $ano_ini)
{

	$meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

  	$codigo = "<select name='dia_ini' id='dia_ini' class='obligatorio'>";
				for ($i = 1; $i <= 31; $i++) {
					$dia = sprintf("%02d", $i);
				    $codigo = $codigo . ((!empty($dia_ini) && $dia_ini==$dia) ? "<option value='".$dia."' selected>" : "<option value='".$dia."'>"). $i ."</option>";
				}
	$codigo = 	$codigo .
				"</select>
				/
				<select name='mes_ini' id='mes_ini' class='obligatorio'>";
				$count=1;
				foreach ($meses as $mesDesc) {
					$mes = sprintf("%02d", $count);
				    $codigo = $codigo . ((!empty($mes_ini) && $mes_ini==$mes) ? "<option value='".$mes."' selected>" : "<option value='".$mes."'>"). $mesDesc ."</option>";
				    $count++;
				}
	$codigo = 	$codigo .
				"</select>
				/
				<select name='ano_ini' id='ano_ini' class='obligatorio'>";
				for ($i = 2010; $i <= 2025; $i++) {
				    $codigo = $codigo . ((!empty($ano_ini) && $ano_ini==$i) ? "<option value='".$i."' selected>" : "<option value='".$i."'>"). $i ."</option>";
				}
	$codigo = 	$codigo . "</select>";

	return $codigo;
}

function opciones_dia($dia = null, $conOpcionVacia = false, $iniciaVacio = false)
{
  $diahoy = isset($dia) ? $dia : strftime("%d");
  if ($iniciaVacio) {
    $diahoy = -1;
  }

  $diaopc = "";
  if ($conOpcionVacia) {
    $diaopc = $diaopc . "<option value='0'>-</option>\n";
  }
	for ($i = 1; $i <= 31 ; $i++) {
		if($i <> $diahoy)
			$diaopc = $diaopc . "<option value='$i'>$i</option>\n";
		else
			$diaopc = $diaopc . "<option value='$i' selected='true'>$i</option>\n";
	}
	return $diaopc;
}

function opciones_mes($mes = null, $conOpcionVacia = false, $iniciaVacio = false)
{
  $meshoy = isset($mes) ? $mes : strftime("%m");
  if ($iniciaVacio) {
    $meshoy = -1;
  }

	$meses = array(1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre');
  $mesopc = "";
  if ($conOpcionVacia) {
    $mesopc = $mesopc . "<option value='0'>-</option>\n";
  }
	for ($i = 1; $i <= 12 ; $i++) {
		if($i <> $meshoy)
			$mesopc = $mesopc . "<option value='$i'>$meses[$i]</option>\n";
		else
			$mesopc = $mesopc . "<option value='$i' selected='true'>$meses[$i]</option>\n";
	}
	return $mesopc;
}

function opciones_ano($anio = null, $conOpcionVacia = false, $iniciaVacio = false)
{
  $anohoy = isset($anio) ? $anio : strftime("%Y");
  if ($iniciaVacio) {
    $anohoy = -1;
  }
  $anoopc = "";
  if ($conOpcionVacia) {
    $anoopc = $anoopc . "<option value='0'>-</option>\n";
  }
	for ($i = 2010; $i <= 2025 ; $i++) {
		if($i <> $anohoy)
			$anoopc = $anoopc . "<option value='$i'>$i</option>\n";
		else
			$anoopc = $anoopc . "<option value='$i' selected='true'>$i</option>\n";
	}
	return $anoopc;
}
?>
