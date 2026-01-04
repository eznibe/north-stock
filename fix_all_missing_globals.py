import re
import sys

def fix_file(filename):
    try:
        with open(filename, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except Exception as e:
        print(f"Error reading {filename}: {e}")
        return False
    
    # Find all functions and add global $pdo if missing
    def fix_function(match):
        func_def = match.group(1)  # function name(...) {
        func_body = match.group(2)  # body
        
        # Check if function uses $pdo
        if '$pdo' in func_body:
            # Check if it already has global $pdo
            if not re.search(r'global\s+\$pdo\s*;', func_body):
                # Add global $pdo at the start of function body
                # Find the indentation
                indent_match = re.search(r'\n([ \t]*)', func_body)
                if indent_match:
                    indent = indent_match.group(1)
                else:
                    indent = ' '
                
                # Insert global $pdo after opening brace
                func_body = f"\n{indent}global $pdo;" + func_body
        
        return func_def + func_body + '}'
    
    # Match functions with their bodies (handling nested braces)
    original_content = content
    
    # Simple approach: find function declarations and add global $pdo after opening brace
    lines = content.split('\n')
    new_lines = []
    i = 0
    
    while i < len(lines):
        line = lines[i]
        
        # Check if this is a function declaration
        if re.match(r'^\s*function\s+\w+\s*\([^)]*\)\s*\{?\s*$', line) or re.match(r'^\s*function\s+\w+\s*\([^)]*\)\s*$', line):
            new_lines.append(line)
            
            # Find opening brace if not on same line
            brace_line = i
            if '{' not in line:
                brace_line = i + 1
                while brace_line < len(lines) and '{' not in lines[brace_line]:
                    new_lines.append(lines[brace_line])
                    brace_line += 1
                if brace_line < len(lines):
                    new_lines.append(lines[brace_line])
                    i = brace_line
            
            # Look ahead to see if function uses $pdo
            brace_count = 1
            j = i + 1
            func_lines = []
            
            while j < len(lines) and brace_count > 0:
                func_lines.append(lines[j])
                brace_count += lines[j].count('{') - lines[j].count('}')
                j += 1
            
            # Check if function uses $pdo and doesn't have global $pdo
            func_body = '\n'.join(func_lines)
            if '$pdo' in func_body and not re.search(r'global\s+\$pdo\s*;', func_body):
                # Get indentation from first non-empty line
                indent = ''
                for fl in func_lines:
                    if fl.strip():
                        indent_match = re.match(r'^(\s*)', fl)
                        if indent_match:
                            indent = indent_match.group(1)
                        break
                
                if not indent:
                    indent = ' '
                
                # Add global $pdo line
                new_lines.append(f"{indent}global $pdo;")
            
            # Add the rest of the function
            new_lines.extend(func_lines[:-1])  # Don't add the last one yet, we'll get it in the loop
            i = j - 1
        else:
            new_lines.append(line)
        
        i += 1
    
    new_content = '\n'.join(new_lines)
    
    if new_content != original_content:
        try:
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(new_content)
            return True
        except Exception as e:
            print(f"Error writing {filename}: {e}")
            return False
    
    return False

if __name__ == '__main__':
    files = sys.argv[1:]
    for filename in files:
        if fix_file(filename):
            print(f"Fixed: {filename}")
        else:
            print(f"No changes: {filename}")
