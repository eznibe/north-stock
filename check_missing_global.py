import re
import sys

def check_file(filename):
    try:
        with open(filename, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except:
        return []
    
    # Find all functions
    function_pattern = r'function\s+(\w+)\s*\([^)]*\)\s*\{([^}]*(?:\{[^}]*\}[^}]*)*)\}'
    functions = re.finditer(function_pattern, content, re.MULTILINE | re.DOTALL)
    
    missing = []
    for match in functions:
        func_name = match.group(1)
        func_body = match.group(2)
        
        # Check if function uses $pdo
        if '$pdo' in func_body:
            # Check if it has global $pdo declaration
            if not re.search(r'global\s+\$pdo\s*;', func_body):
                missing.append(func_name)
    
    return missing

if __name__ == '__main__':
    files = sys.argv[1:]
    for filename in files:
        missing = check_file(filename)
        if missing:
            print(f"{filename}: {', '.join(missing)}")
