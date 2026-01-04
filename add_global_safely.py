import re
import sys

def add_global_pdo(filename):
    with open(filename, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # Find all function definitions
    # Match: function name(...) followed by { and newline
    pattern = r'(function\s+\w+\s*\([^)]*\)\s*\{\s*\n)'
    
    matches = list(re.finditer(pattern, content))
    
    # Process matches in reverse order to maintain positions
    offset = 0
    changes = []
    
    for match in matches:
        func_start = match.start()
        func_def = match.group(0)
        
        # Find the function body to check if it uses $pdo
        brace_count = 1
        pos = match.end()
        func_body_start = pos
        
        while pos < len(content) and brace_count > 0:
            if content[pos] == '{':
                brace_count += 1
            elif content[pos] == '}':
                brace_count -= 1
            pos += 1
        
        func_body = content[func_start:pos]
        
        # Check if function uses $pdo and doesn't already have global $pdo
        if '$pdo' in func_body and not re.search(r'global\s+\$pdo\s*;', func_body):
            # Determine indentation from next line
            next_line_match = re.search(r'\n([ \t]*)', content[match.end():match.end()+50])
            if next_line_match:
                indent = next_line_match.group(1)
            else:
                indent = ' '
            
            changes.append((match.end(), f'{indent}global $pdo;\n'))
    
    # Apply changes in reverse order
    for pos, text in reversed(changes):
        content = content[:pos] + text + content[pos:]
    
    with open(filename, 'w', encoding='utf-8') as f:
        f.write(content)
    
    return len(changes)

if __name__ == '__main__':
    for filename in sys.argv[1:]:
        count = add_global_pdo(filename)
        if count > 0:
            print(f"{filename}: added {count} global declarations")
