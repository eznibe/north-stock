var mostrarFiltro=false;
var cant_pend;
var cant_arribada;

function mostrar_filtro()
{
	mostrarFiltro = !mostrarFiltro;
	
	if(mostrarFiltro) document.getElementById("filtro").style.display = 'block';
	else  document.getElementById("filtro").style.display = 'none';
}


/*
	El valor ingresado en el campo de cant arribada tiene  q ser numerico y no mayor a la cant_pend
*/
function validar_valor_ingresado(rowIndex)
{
	var nr_cant_pend=0;
	var nr_cant_arribada=0;

	var inputPendiente = document.getElementById("cant_pend"+rowIndex);
	cant_pend = inputPendiente.value;
	nr_cant_pend = parseInt(cant_pend);
	
	var inputArribada = document.getElementById("cant_arribada"+rowIndex);
	cant_arribada = inputArribada.value;
	nr_cant_arribada = parseInt(cant_arribada);

	if(!is_numeric(cant_arribada) || (nr_cant_arribada > nr_cant_pend) || (nr_cant_arribada < 0)){
		inputArribada.style.backgroundColor = 'red';
	}		
	else{
		inputArribada.style.backgroundColor = 'white';
	}	
}

/*
	Verificar que no se haya marcado con error alguna fila de la tabla
*/
function validar_tabla()
{
	 
	var error = 0;
	
	var tabla = document.getElementById("orden_table");
		
	// recorro la tabla buscando valores de cant_pend y cant_arribada
	for(var i=0; i < (tabla.rows.length-1); i++)
	{		
		var td1 = document.getElementById("cant_arribada"+i);
		
		if(td1.style.backgroundColor == 'red')
			error++;
	}
	
	if(error > 0){
		mostrar_error();
		return false;
	}
	
	return true;
}

/*
	Mostrar mensaje de error
*/
function mostrar_error()
{

	document.getElementById("error").style.display = 'block';
}

function is_numeric(strString)
   //  check for valid numeric strings	
{
   var strValidChars = "0123456789";
   var strChar;
   var blnResult = true;

   if (strString.length == 0) return false;

   //  test strString consists of valid characters listed above
   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         {
         blnResult = false;
         }
      }
   return blnResult;
}

function update_cantidad_item(id_itemcomprar, id_proveedor)
{
 var url="../orden_compra_update_cant.php?id_itemcomprar=" + id_itemcomprar + "&id_proveedor=" + id_proveedor;
 window.open(url,"producto_detalle","toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=yes, resizable=yes, width=600, height=300");
}

/*
	El valor ingresado en el campo de cant arribada tiene  q ser numerico y no mayor a la cant_pend
*/
function validar_valor_ingresado_orden_confirmar(rowIndex)
{
	var nr_cantidad=0;
	var nr_cant_aereo=0;
	var nr_cant_maritimo=0;
	var nr_cant_courier=0;

	var cantidad = document.getElementById("cantidad"+rowIndex).value;
	nr_cantidad = parseInt(cantidad);

	var input_cantidad_ae =	document.getElementById("cant_aereo"+rowIndex);
	cant_ae = input_cantidad_ae.value;
	nr_cant_aereo = parseInt(cant_ae);
	
	var input_cantidad_mar = document.getElementById("cant_maritimo"+rowIndex);
	cant_mar = input_cantidad_mar.value;
	nr_cant_maritimo = parseInt(cant_mar);

	var input_cantidad_cou = document.getElementById("cant_courier"+rowIndex);
	cant_cou = input_cantidad_cou.value;
	nr_cant_courier = parseInt(cant_cou);

	if(!is_numeric(cant_ae) || !is_numeric(cant_mar) || !is_numeric(cant_cou)){
		input_cantidad_ae.style.backgroundColor = 'red';
		input_cantidad_mar.style.backgroundColor = 'red';
		input_cantidad_cou.style.backgroundColor = 'red';
		return;
	}
	
	if((nr_cant_aereo + nr_cant_maritimo + nr_cant_courier) != nr_cantidad){
		input_cantidad_ae.style.backgroundColor = 'red';
		input_cantidad_mar.style.backgroundColor = 'red';
		input_cantidad_cou.style.backgroundColor = 'red';
	}		
	else{
		input_cantidad_ae.style.backgroundColor = 'white';
		input_cantidad_mar.style.backgroundColor = 'white';
		input_cantidad_cou.style.backgroundColor = 'white';
	}	
}

function validar_tabla_orden_confirmar()
{
	 
	var error = 0;
	
	var tabla = document.getElementById("orden_table");
		
	// recorro la tabla buscando valores de cant_pend y cant_arribada
	for(var i=0; i < (tabla.rows.length-1); i++)
	{		
		var td = document.getElementById("cant_aereo"+i);
		
		if(td.style.backgroundColor == 'red')
			error++;
	}
	
	if(error > 0){
		mostrar_error();
		return false;
	}
	
	return true;
}

