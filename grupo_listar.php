<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<p class=\"imprimir\">
        <a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
        </p>";

$id_grupo = $_GET['id_grupo'];

// $tipo_prodcuto: 1-Todos, 2-Importados, 3-Nacionales
$tipo_producto = isset($_GET['tipo_producto']) ? $_GET['tipo_producto'] : 1;

if($tipo_producto==1){
	// todos los productos del grupo
	$query = "SELECT
		categoria.categoria,
		categoria.stock_minimo,
		SUM(item.stock_disponible),
		item.id_categoria,
		(SUM(item.stock_disponible)-categoria.stock_minimo),
		unidad.unidad,
		SUM(item.stock_transito),
		(SUM(item.stock_disponible)+SUM(item.stock_transito)-categoria.stock_minimo-(coalesce(sum(en_prevision.cantidad), 0))),
		grupo.grupo,
		categoria.reservado,
		coalesce(sum(en_prevision.cantidad), 0) as prevision
	  FROM
      item  
      JOIN categoria on item.id_categoria = categoria.id_categoria
      JOIN unidad on unidad.id_unidad = categoria.id_unidad_visual
      JOIN grupo on categoria.id_grupo = grupo.id_grupo
      LEFT JOIN (
    	SELECT pi.id_item, sum(pi.cantidad) as cantidad
    	FROM prevision p
    	JOIN previsionitem pi on pi.id_prevision = p.id_prevision
    	where p.fecha_descarga is null and pi.descargado = false
    	group by pi.id_item
      ) en_prevision on en_prevision.id_item = item.id_item
	  WHERE 
		grupo.id_grupo = $id_grupo
	  GROUP BY
		  item.id_categoria
	  ORDER BY
		  categoria.categoria";
}
else{
	// productos segun el tipo de producto elegido (import o naci)
	$id_argentina = obtener_id_pais_argentina();
	if($tipo_producto==2) $condicion = "<> $id_argentina"; else $condicion = "= $id_argentina";

	$query = "SELECT
    categoria.categoria,
    categoria.stock_minimo,
    SUM(item.stock_disponible),
    item.id_categoria,
    (SUM(item.stock_disponible)-categoria.stock_minimo),
    unidad.unidad,
    SUM(item.stock_transito),
    (SUM(item.stock_disponible)+SUM(item.stock_transito)-categoria.stock_minimo-(coalesce(sum(en_prevision.cantidad), 0))),
    grupo.grupo,
    categoria.reservado,
    coalesce(sum(en_prevision.cantidad), 0) as prevision
  FROM
    item  
    JOIN categoria on item.id_categoria = categoria.id_categoria
    JOIN unidad on unidad.id_unidad = categoria.id_unidad_visual
    JOIN grupo on categoria.id_grupo = grupo.id_grupo
    JOIN proveedor on proveedor.id_proveedor = item.id_proveedor
    JOIN pais on pais.id_pais = proveedor.id_pais
    LEFT JOIN (
    	SELECT pi.id_item, sum(pi.cantidad) as cantidad
    	FROM prevision p
    	JOIN previsionitem pi on pi.id_prevision = p.id_prevision
    	where p.fecha_descarga is null and pi.descargado = false
    	group by pi.id_item
      ) en_prevision on en_prevision.id_item = item.id_item
  WHERE 
    grupo.id_grupo = $id_grupo AND
    pais.id_pais $condicion
  GROUP BY
	  item.id_categoria
  ORDER BY
	  categoria.categoria";
}

$result = mysql_query($query);


$titulo = obtener_grupo($id_grupo);
$aux = "";
while ($row = mysql_fetch_array($result))
{
 if ($row[4] < 0) $row[4] = "<em>$row[4]</em>";
 if ($row[7] < 0) $row[7] = "<em>$row[7]</em>";
 $producto = htmlspecialchars(stripslashes($row[0]));
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$producto</a></td>
	  <td>$row[2]</td><td>$row[1]</td><td>$row[4]</td>"
	  . desglose_transito_por_tipo_envio($row[3]) 
	  . ($row[10] > 0 ? "<td><a class=\"list\" onclick=\"show_detail_previsiones($row[3]);\">$row[10]</a></td>" : "<td>$row[10]</td>").
	  "<td>$row[7]</td><td>$row[5]</td></tr>\n";
}

$action = "grupo_listar.php";

$var = array("rows" => $aux,
			 "titulo" => $titulo,
			 "imprimir" => $imprimir,
			 "id_grupo" => $id_grupo,
			 "tipo_producto" => $tipo_producto,
			 "action" => $action);
eval_html('producto_grupo_listar.html', $var);


/**
 * Obtiene el id en la base de datos del pais con nombre Argentina
 */
function obtener_id_pais_argentina(){
	$query = "SELECT id_pais FROM pais
		 	  WHERE pais.pais = 'ARGENTINA'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	return $row[0];
}

/**
 * Obtiene el nombre del grupo con el id pasado como parametro
 */
function obtener_grupo($id_grupo)
{
	$query = "SELECT grupo FROM grupo WHERE id_grupo = $id_grupo";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	return $row[0];
}

/**
* Arma unos td's con los distintos tipos de envio y le pone el valor correspodiente para la categoria dada.
*/
function desglose_transito_por_tipo_envio($id_categoria) {

	$query = "SELECT count(*) FROM tipoenvio";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);

	$cant_tipo_envios = $row[0];

	$query = "SELECT oi.id_tipo_envio, sum(oi.cantidad_pendiente) as pendiente
		FROM ordenitem oi join item i on oi.id_item = i.id_item join orden o on o.id_orden = oi.id_orden
		WHERE i.id_categoria = $id_categoria and o.id_status = 1 and oi.cantidad_pendiente > 0
		GROUP BY oi.id_tipo_envio
		ORDER BY oi.id_tipo_envio";
	$result = mysql_query($query);

	$count=0;
	$en_transito = array();
	while ($row = mysql_fetch_array($result))
	{
		if($count==$row[0]) {
			array_push($en_transito, array($row[0], $row[1]));
		}
		else {
			while($count < $row[0])
			{
				array_push($en_transito, array($count, 0));
				$count++;
			}
			array_push($en_transito, array($row[0], $row[1]));
		}

		$count++;
	}

	while($count < $cant_tipo_envios) {
		array_push($en_transito, array($count, 0));
		$count++;
	}

	$codigo=""; $trans_desc=0;
	foreach ($en_transito as $item)
	{
		if($item[0] > 0) {
			$codigo .= "<td>$item[1]</td>";
		}
		else {
			$trans_desc = $item[1];
		}
	}

	$codigo .= "<td>$trans_desc</td>";

	return $codigo;
}
