#!/bin/bash
# 
# Batch Migration Script - Automatically migrates common mysql_* patterns to PDO
# This script handles the most common patterns found in the codebase
#

echo "Starting batch migration of remaining PHP files..."
echo "=================================================="

# Backup first
BACKUP_DIR="backups/batch_migration_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
echo "Creating backup in $BACKUP_DIR..."

# Find all PHP files with mysql_ functions
FILES=$(find . -name "*.php" -type f -not -path "./backups/*" -not -path "./.git/*" -exec grep -l "mysql_" {} \;)

COUNT=0
MIGRATED=0

for FILE in $FILES; do
    COUNT=$((COUNT + 1))
    echo ""
    echo "[$COUNT] Processing: $FILE"
    
    # Backup the file
    cp "$FILE" "$BACKUP_DIR/$(basename $FILE).bak"
    
    # Create a temporary file for the migration
    TEMP_FILE="${FILE}.tmp"
    
    # Apply common transformations
    sed -e '
        # Add $pdo = get_db_connection(); after db_connect();
        /^db_connect();$/a\
$pdo = get_db_connection();
        
        # mysql_query($query) -> $pdo->query($query)
        s/mysql_query(\$query)/$pdo->query($query)/g
        
        # mysql_query($query2) -> $pdo->query($query2) etc
        s/mysql_query(\$\([a-zA-Z0-9_]*\))/$pdo->query($\1)/g
        
        # mysql_fetch_array -> fetch(PDO::FETCH_NUM)
        s/mysql_fetch_array/fetch(PDO::FETCH_NUM)/g
        
        # mysql_fetch_assoc -> fetch(PDO::FETCH_ASSOC)
        s/mysql_fetch_assoc/fetch(PDO::FETCH_ASSOC)/g
        
        # mysql_num_rows -> rowCount (but this needs special attention)
        s/mysql_num_rows(\$\([a-zA-Z0-9_]*\))/$\1->rowCount()/g
        
        # Remove mysql_error() calls as PDO uses exceptions
        s/ \. mysql_error()//g
        s/mysql_error() \. //g
        
        # $result = mysql_query -> $result = $pdo->query
        s/\$result = mysql_query/\$result = $pdo->query/g
        
    ' "$FILE" > "$TEMP_FILE"
    
    # Check if changes were made
    if ! diff -q "$FILE" "$TEMP_FILE" > /dev/null 2>&1; then
        mv "$TEMP_FILE" "$FILE"
        echo "  ✓ Migrated"
        MIGRATED=$((MIGRATED + 1))
    else
        rm "$TEMP_FILE"
        echo "  - No changes needed"
    fi
done

echo ""
echo "=================================================="
echo "Migration complete!"
echo "Files processed: $COUNT"
echo "Files migrated: $MIGRATED"
echo "Backup location: $BACKUP_DIR"
echo ""
echo "IMPORTANT: Test your application thoroughly!"
echo "Some patterns may need manual adjustment."

