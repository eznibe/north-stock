import re

with open('main.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Functions that need global $pdo
functions_needing_global = [
    'get_units_opt', 'get_group_opt', 'get_pais_opt', 'get_categoria_opt', 
    'get_subproducto_opt', 'get_proveedor_opt', 'get_tipousr_opt', 'get_usuario_opt', 
    'log_trans', 'get_group', 'get_groups', 'get_proveedor', 'get_categoria', 'get_usuario', 
    'get_item', 'get_unidad_descarga', 'get_unidad_compra', 'get_stock_transito', 
    'set_stock_transito', 'log_stock_transito_negativo', 'get_factor_unidades', 
    'get_cantidad_comprar', 'get_ordenitem_id_item', 'get_orden_status', 
    'get_agrupacion_contable', 'get_item_agrupacion_contable', 'get_tipos_de_envio', 
    'es_proveedor_nacional'
]

for func_name in functions_needing_global:
    # Pattern to find the function and add global $pdo after the opening brace
    pattern = r'(function\s+' + func_name + r'\s*\([^)]*\)\s*\{\s*\n)((?!\s*global\s+\$pdo))'
    
    # Replace with function declaration, opening brace, newline, global $pdo
    replacement = r'\1 global $pdo;\n'
    
    # Check if global $pdo already exists in this function
    func_pattern = r'function\s+' + func_name + r'\s*\([^)]*\)\s*\{[^}]*?global\s+\$pdo'
    if not re.search(func_pattern, content, re.DOTALL):
        content = re.sub(pattern, replacement, content, count=1)

with open('main.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed main.php with global $pdo declarations")
