import re

files = [
    'form_producto_addcomprar.php', 'form_producto_alta.php', 
    'form_producto_modificacion_99.php', 'form_producto_modificacion.php',
    'form_producto_salida.php', 'form_proveedor_modificacion.php',
    'form_usuario_modificacion.php', 'grupo_listar_stock_minimos.php',
    'grupo_listar_todos_items.php', 'item_ver_previsiones.php',
    'modificar_precio_dolar.php', 'mostrar_tabla_fechas_por_periodo.php',
    'orden_update.php', 'precio_dolar.php', 'prevision_item_nuevo.php',
    'prevision_ver.php', 'producto_asignar_prevision.php',
    'producto_datosmodificar.php', 'producto_ver_previsiones.php'
]

for filename in files:
    try:
        with open(filename, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace db_connect(); with db_connect();\n$pdo = get_db_connection();
        pattern = r'(db_connect\(\);)\s*\n'
        replacement = r'\1\n$pdo = get_db_connection();\n'
        
        new_content = re.sub(pattern, replacement, content, count=1)
        
        if new_content != content:
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Fixed: {filename}")
        else:
            print(f"No change: {filename}")
    except Exception as e:
        print(f"Error with {filename}: {e}")
