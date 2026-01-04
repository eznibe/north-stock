<?php

function armar_listar_entre_fechas($transac, $dia_ini, $mes_ini, $ano_ini, $dia_fin, $mes_fin, $ano_fin)
{
  //$tipoRango  1: rango por fechas

	$meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

  	$codigo = "Listar $transac entre &nbsp;
				<select name='dia_ini' id='dia_ini' class='obligatorio'>";
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
				for ($i = 2010; $i <= 2035; $i++) {
				    $codigo = $codigo . ((!empty($ano_ini) && $ano_ini==$i) ? "<option value='".$i."' selected>" : "<option value='".$i."'>"). $i ."</option>";
				}
	$codigo = 	$codigo .
				"</select>

				&nbsp;&nbsp; y &nbsp;&nbsp;

				<select name='dia_fin' id='dia_fin' class='obligatorio'>";
				for ($i = 1; $i <= 31; $i++) {
					$dia = sprintf("%02d", $i);
				    $codigo = $codigo . ((!empty($dia_fin) && $dia_fin==$dia) ? "<option value='".$dia."' selected>" : "<option value='".$dia."'>"). $i ."</option>";
				}
	$codigo = 	$codigo .
				"</select>
				/
				<select name='mes_fin' id='mes_fin' class='obligatorio'>";
				$count=1;
				foreach ($meses as $mesDesc) {
					$mes = sprintf("%02d", $count);
				    $codigo = $codigo . ((!empty($mes_fin) && $mes_fin==$mes) ? "<option value='".$mes."' selected>" : "<option value='".$mes."'>"). $mesDesc ."</option>";
				    $count++;
				}
	$codigo = 	$codigo .
				"</select>
				/
				<select name='ano_fin' id='ano_fin' class='obligatorio'>";
				for ($i = 2010; $i <= 2035; $i++) {
				    $codigo = $codigo . ((!empty($ano_fin) && $ano_fin==$i) ? "<option value='".$i."' selected>" : "<option value='".$i."'>"). $i ."</option>";
				}
	$codigo = 	$codigo .
				"</select> ";

	return $codigo;
}

function armar_listar_por_periodo($transac, $rango_periodo, $tipo_periodo)
{
  //$tipoRango
  //		    2: rango por periodo

		$periodos = array('dias', 'meses', 'anos');

		$codigo = "Listar $transac desde &nbsp;
			<select name='rango_periodo' id='rango_periodo' class='obligatorio'>";
			for ($i = 0; $i <= 31; $i++) {
					$rango = sprintf("%02d", $i);
				    $codigo = $codigo . ((!empty($rango_periodo) && $rango_periodo==$rango) ? "<option value='".$rango."' selected>" : "<option value='".$rango."'>"). $i ."</option>";
			}
$codigo = 	$codigo .
			"</select> &nbsp;
			<select name='tipo_periodo' id='tipo_periodo' class='obligatorio'>";
			foreach ($periodos as $periodo) {
				    $codigo = $codigo . ((!empty($tipo_periodo) && $tipo_periodo==$periodo) ? "<option value='".$periodo."' selected>" : "<option value='".$periodo."'>"). $periodo ."</option>";
			}
$codigo = 	$codigo .
			"</select> &nbsp;
			atras";

	return $codigo;
}

function armar_listar_entre_fechas_por_periodo($transac, $tipo_periodo, $dia_ini, $mes_ini, $ano_ini, $dia_fin, $mes_fin, $ano_fin)
{
  //$tipoRango
  //			3: rango por fechas por periodo

	$meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
	$periodos = array('mes', 'ano');

	$codigo = "Listar $transac entre &nbsp;
			<select name='dia_ini' id='dia_ini' class='obligatorio'>";
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
			for ($i = 2010; $i <= 2035; $i++) {
				    $codigo = $codigo . ((!empty($ano_ini) && $ano_ini==$i) ? "<option value='".$i."' selected>" : "<option value='".$i."'>"). $i ."</option>";
			}
	$codigo = 	$codigo .
			"</select>

			&nbsp;&nbsp; y &nbsp;&nbsp;

			<select name='dia_fin' id='dia_fin' class='obligatorio'>";
			for ($i = 1; $i <= 31; $i++) {
					$dia = sprintf("%02d", $i);
				    $codigo = $codigo . ((!empty($dia_fin) && $dia_fin==$dia) ? "<option value='".$dia."' selected>" : "<option value='".$dia."'>"). $i ."</option>";
			}
	$codigo = 	$codigo .
			"</select>
			/
			<select name='mes_fin' id='mes_fin' class='obligatorio'>";
			$count=1;
			foreach ($meses as $mesDesc) {
					$mes = sprintf("%02d", $count);
				    $codigo = $codigo . ((!empty($mes_fin) && $mes_fin==$mes) ? "<option value='".$mes."' selected>" : "<option value='".$mes."'>"). $mesDesc ."</option>";
				    $count++;
			}
	$codigo = 	$codigo .
			"</select>
			/
			<select name='ano_fin' id='ano_fin' class='obligatorio'>";
			for ($i = 2010; $i <= 2035; $i++) {
				    $codigo = $codigo . ((!empty($ano_fin) && $ano_fin==$i) ? "<option value='".$i."' selected>" : "<option value='".$i."'>"). $i ."</option>";
			}
	$codigo = 	$codigo .
			"</select> <p>" .

			"con periodo por &nbsp&nbsp&nbsp" .
			"<select name='tipo_periodo' id='tipo_periodo' class='obligatorio'>";
			foreach ($periodos as $periodo) {
				    $codigo = $codigo . ((!empty($tipo_periodo) && $tipo_periodo==$periodo) ? "<option value='".$periodo."' selected>" : "<option value='".$periodo."'>"). $periodo ."</option>";
			}
$codigo = 	$codigo .
			"</select>";

	return $codigo;
}



?>
