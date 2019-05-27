function update_cantidad_item(id_itemcomprar, id_proveedor)
{
 var url="../orden_compra_update_cant.php?id_itemcomprar=" + id_itemcomprar + "&id_proveedor=" + id_proveedor;
 window.open(url,"producto_detalle","toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=yes, resizable=yes, width=600, height=300");
}