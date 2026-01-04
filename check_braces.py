with open('main.php', 'r') as f:
    lines = f.readlines()

in_function = False
function_name = ""
brace_count = 0
line_num = 0

for i, line in enumerate(lines, 1):
    if 'function ' in line and not in_function:
        # Extract function name
        import re
        match = re.search(r'function\s+(\w+)', line)
        if match:
            function_name = match.group(1)
            in_function = True
            brace_count = 0
            line_num = i
    
    if in_function:
        brace_count += line.count('{') - line.count('}')
        
        # If we hit another function or end of file with unbalanced braces
        if brace_count == 0 and '{' in line:
            # Function ended properly
            in_function = False
        elif 'function ' in line and i > line_num and brace_count > 0:
            print(f"Line {line_num}: function {function_name} - missing {brace_count} closing braces")
            # Start new function
            match = re.search(r'function\s+(\w+)', line)
            if match:
                function_name = match.group(1)
                brace_count = 0
                line_num = i

# Check last function
if in_function and brace_count > 0:
    print(f"Line {line_num}: function {function_name} - missing {brace_count} closing braces")
