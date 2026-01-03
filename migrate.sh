#!/bin/bash
#
# Quick Migration Script
# Automates common migration patterns for remaining files
#

echo "North-Stock PHP 7.4 Migration Helper"
echo "====================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to check PHP version
check_php_version() {
    echo "Checking PHP version..."
    php_version=$(php -r "echo phpversion();")
    echo "Current PHP version: $php_version"
    
    if php -r "exit(version_compare(phpversion(), '7.4.0', '>=') ? 0 : 1);"; then
        echo -e "${GREEN}✓ PHP 7.4+ detected${NC}"
    else
        echo -e "${RED}✗ PHP version is below 7.4${NC}"
        exit 1
    fi
}

# Function to check PDO
check_pdo() {
    echo ""
    echo "Checking PDO MySQL extension..."
    if php -m | grep -q pdo_mysql; then
        echo -e "${GREEN}✓ PDO MySQL extension is available${NC}"
    else
        echo -e "${RED}✗ PDO MySQL extension not found${NC}"
        echo "Install with: sudo apt-get install php-mysql"
        exit 1
    fi
}

# Function to backup files
backup_files() {
    echo ""
    echo "Creating backup..."
    backup_dir="backups/backup_$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$backup_dir"
    
    # Backup PHP files
    find . -name "*.php" -not -path "./backups/*" -not -path "./.git/*" | while read file; do
        cp --parents "$file" "$backup_dir/"
    done
    
    echo -e "${GREEN}✓ Backup created in $backup_dir${NC}"
}

# Function to find files using mysql_ functions
find_mysql_files() {
    echo ""
    echo "Finding files with mysql_* functions..."
    grep -r "mysql_" . --include="*.php" --files-with-matches | grep -v "./db-connect/dbutils.php" | grep -v "./backups/" | sort | uniq
}

# Function to count remaining files
count_remaining() {
    echo ""
    echo "Migration Progress:"
    total=$(find . -name "*.php" -not -path "./backups/*" -not -path "./.git/*" | wc -l)
    migrated=4  # dbutils, form_login, main, form_impresion_etiquetas
    mysql_files=$(grep -r "mysql_" . --include="*.php" --files-with-matches | grep -v "./backups/" | wc -l)
    
    echo "Total PHP files: $total"
    echo -e "${GREEN}Core files migrated: $migrated${NC}"
    echo -e "${YELLOW}Files still using mysql_*: $mysql_files${NC}"
    
    percentage=$(( (migrated * 100) / total ))
    echo "Progress: $percentage%"
}

# Function to run tests
run_tests() {
    echo ""
    echo "Running migration tests..."
    php test_migration.php > test_results.html
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Tests completed${NC}"
        echo "Results saved to test_results.html"
        
        # Try to open in browser
        if command -v xdg-open &> /dev/null; then
            xdg-open test_results.html
        fi
    else
        echo -e "${RED}✗ Tests failed${NC}"
        exit 1
    fi
}

# Function to generate migration script for a file
generate_migration() {
    local file=$1
    echo ""
    echo "Analyzing $file..."
    
    # Check what needs to be migrated
    if grep -q "mysql_query\|mysql_fetch" "$file"; then
        echo -e "${YELLOW}Found mysql_* functions${NC}"
    fi
    
    if grep -q "ereg\|split\|session_register" "$file"; then
        echo -e "${YELLOW}Found deprecated functions${NC}"
    fi
    
    if grep -q '\$_(GET|POST|REQUEST)\[' "$file"; then
        echo -e "${YELLOW}Found potential SQL injection points${NC}"
    fi
}

# Main menu
main_menu() {
    echo ""
    echo "What would you like to do?"
    echo "1) Check prerequisites"
    echo "2) Create backup"
    echo "3) Run migration tests"
    echo "4) View migration progress"
    echo "5) Find files to migrate"
    echo "6) Analyze specific file"
    echo "7) Exit"
    echo ""
    read -p "Enter choice [1-7]: " choice
    
    case $choice in
        1)
            check_php_version
            check_pdo
            main_menu
            ;;
        2)
            backup_files
            main_menu
            ;;
        3)
            run_tests
            main_menu
            ;;
        4)
            count_remaining
            main_menu
            ;;
        5)
            find_mysql_files
            main_menu
            ;;
        6)
            read -p "Enter file path: " filepath
            generate_migration "$filepath"
            main_menu
            ;;
        7)
            echo "Goodbye!"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option${NC}"
            main_menu
            ;;
    esac
}

# Run main menu
main_menu
