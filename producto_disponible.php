<?php

include 'main.php';
include 'dbutils.php';

check_session();

db_connect();

if ($_SESSION['user_level'] < 11) $imprimir = "";
else $imprimir = "<p class=\"imprimir\">
        <a class=\"imprimir\" onclick=\"self.print();\">Imprimir</a>
        </p>";


$query = "SELECT
    categoria.categoria,
    categoria.stock_minimo,
    SUM(item.stock_disponible) AS disponible,
    item.id_categoria,
    (SUM(item.stock_disponible)-categoria.stock_minimo),
    unidad.unidad,
    SUM(item.stock_transito),
    (SUM(item.stock_disponible)+SUM(item.stock_transito)-categoria.stock_minimo-(coalesce(sum(en_prevision.cantidad), 0))),
    categoria.reservado,
    coalesce(sum(en_prevision.cantidad), 0) as prevision,
    (SUM(item.stock_disponible) - categoria.stock_minimo - coalesce(sum(en_prevision.cantidad), 0)) AS disponible_real
  FROM
    item  
    JOIN categoria on item.id_categoria = categoria.id_categoria
    JOIN unidad on unidad.id_unidad = categoria.id_unidad_visual
    LEFT JOIN (
    	SELECT pi.id_item, sum(pi.cantidad) as cantidad
    	FROM prevision p
    	JOIN previsionitem pi on pi.id_prevision = p.id_prevision
    	where p.fecha_descarga is null and pi.descargado = false
    	group by pi.id_item
    ) en_prevision on en_prevision.id_item = item.id_item
  WHERE 1=1
  GROUP BY
	  item.id_categoria
  HAVING
	  disponible_real > 0
  ORDER BY
	  categoria.categoria";
$result = $pdo->query($query);

$aux = "";
while ($row = $result->fetch(PDO::FETCH_NUM))
{
 $unidad = "<em>" . strtoupper($row[5]) . "</em>";
 if ($row[4] < 0) $row[4] = "<em>$row[4]</em>";
 if ($row[7] < 0) $row[7] = "<em>$row[7]</em>";
 $aux = $aux . "<tr class=\"provlistrow\"><td><a class=\"list\" onclick=\"show_detail($row[3]);\">$row[0]</a>
       <td>$row[2]</td><td>$row[1]</td><td>$row[4]</td><td>$row[6]</td>"
       . ($row[9] > 0 ? "<td><a class=\"list\" onclick=\"show_detail_previsiones($row[3]);\">$row[9]</a></td>" : "<td>$row[9]</td>").
       "<td>$row[7]</td><td>$unidad</td></tr>\n";
}

$titulo = "Existencias disponibles";

$var = array("rows" => $aux,
	"imprimir" => $imprimir,
	"titulo" => $titulo);
eval_html('producto_listar.html', $var);
