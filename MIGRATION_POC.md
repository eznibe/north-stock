# PHP 7.4 Migration - Proof of Concept

**Branch:** migration  
**Date:** January 3, 2026  
**Status:** Ready for Testing

## Files Modified

### ✅ Core Database Layer
**File:** `db-connect/dbutils.php`
- ✓ Replaced `mysql_pconnect()` with PDO connection
- ✓ Added UTF-8 charset support
- ✓ Implemented proper exception handling
- ✓ Added helper functions for backward compatibility
- ✓ Global PDO connection management

### ✅ Authentication System
**File:** `form_login.php`
- ✓ **CRITICAL SECURITY FIX:** Replaced SQL injection vulnerability with prepared statements
- ✓ Added input validation and sanitization
- ✓ Implemented hybrid password verification (backward compatible with plain text, ready for bcrypt)
- ✓ Changed old-style table joins to INNER JOIN
- ✓ Added proper error handling

### ✅ Utility Functions
**File:** `main.php`
- ✓ Updated `get_units_opt()` to use PDO
- ✓ Updated `get_group_opt()` to use PDO
- ✓ Updated `get_pais_opt()` to use PDO
- ✓ Updated `get_categoria_opt()` to use PDO
- ✓ Removed deprecated `mysql_num_rows()` checks

### ✅ Deprecated Functions
**File:** `form_impresion_etiquetas.php`
- ✓ Replaced `ereg()` with `preg_match()`

---

## Key Improvements

### Security Enhancements
1. **SQL Injection Prevention:** Login now uses prepared statements
2. **Input Validation:** Added checks for empty inputs
3. **Password Future-proofing:** Supports both plain text (for migration) and bcrypt hashes

### PHP 7.4 Compatibility
1. **PDO Instead of mysql_\*:** All core functions now use PDO
2. **No Deprecated Functions:** Removed ereg(), mysql_query(), mysql_fetch_array()
3. **Proper Error Handling:** Using try-catch with PDOException

### Backward Compatibility
1. **Helper Functions:** `get_db_connection()`, `db_query()`, `db_escape_string()`
2. **Gradual Migration Path:** Other files can be migrated incrementally
3. **Password Compatibility:** Works with existing plain text passwords

---

## Testing Checklist

### Prerequisites
```bash
# Ensure PHP 7.4.33 is installed
php -v

# Check PDO MySQL driver
php -m | grep pdo_mysql
```

### Test Cases

#### 1. Database Connection Test
```bash
php -r "
include 'dbutils.php';
include 'db-connect/dbutils.php';
db_connect();
echo 'Connection successful!';
"
```

#### 2. Login Test
- [ ] Test with valid credentials
- [ ] Test with invalid username
- [ ] Test with invalid password
- [ ] Test with empty fields
- [ ] Verify session is created

#### 3. Utility Functions Test
- [ ] Test `get_units_opt()` - should return option HTML
- [ ] Test `get_group_opt()` - should return option HTML
- [ ] Test `get_pais_opt()` - should return option HTML
- [ ] Test `get_categoria_opt()` - should return option HTML

#### 4. Barcode Function Test
- [ ] Access page with barcode generation
- [ ] Verify no ereg() errors appear

---

## Database Preparation (Optional but Recommended)

### Upgrade User Passwords to Bcrypt
```php
<?php
// Script to hash existing passwords (run once)
include 'dbutils.php';
include 'db-connect/dbutils.php';
db_connect();
$pdo = get_db_connection();

$users = $pdo->query("SELECT id_usuario, username, clave FROM usuario");
foreach ($users as $user) {
    // Only hash if not already hashed
    if (strpos($user['clave'], '$2y$') !== 0) {
        $hashed = password_hash($user['clave'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE usuario SET clave = :clave WHERE id_usuario = :id");
        $stmt->execute(['clave' => $hashed, 'id' => $user['id_usuario']]);
        echo "Updated user: {$user['username']}\n";
    }
}
?>
```

---

## Next Steps

### Remaining Files to Migrate (164 total PHP files)

**Priority 1 - High Traffic/Critical:**
- [ ] `producto_*.php` (product management)
- [ ] `proveedor_*.php` (supplier management)
- [ ] `orden_*.php` (order management)
- [ ] `prevision_*.php` (forecasting)

**Priority 2 - Moderate:**
- [ ] `categoria_*.php`
- [ ] `grupo_*.php`
- [ ] `pais_*.php`
- [ ] `usuario_*.php`

**Priority 3 - Low Traffic:**
- [ ] Reporting files
- [ ] Utility scripts
- [ ] Administrative functions

### Migration Pattern for Remaining Files

```php
// OLD PATTERN
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    // process $row[0], $row[1]
}

// NEW PATTERN
$pdo = get_db_connection();
$result = $pdo->query($query);
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    // process $row[0], $row[1]
}

// OR for queries with user input:
$stmt = $pdo->prepare("SELECT * FROM table WHERE id = :id");
$stmt->execute(['id' => $user_input]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // process $row['column_name']
}
```

---

## Known Issues & Limitations

1. **eval_html() function:** Still uses `eval()` which is not recommended. Consider refactoring to use proper templating.
2. **Session Management:** Should add `session_regenerate_id()` after login for security.
3. **Error Messages:** Currently showing generic messages. Should implement proper logging.
4. **CSRF Protection:** Not yet implemented. Should add token verification.

---

## Rollback Plan

If issues arise:
```bash
git checkout main -- db-connect/dbutils.php form_login.php main.php form_impresion_etiquetas.php
```

---

## Performance Notes

- PDO with prepared statements may be slightly slower for simple queries
- Connection pooling via persistent connections can be added if needed
- Consider adding query caching for frequently accessed data

---

## Support

For issues or questions during migration, check:
1. PHP error logs: `/var/log/php/error.log`
2. Apache/Nginx error logs
3. Browser console for JavaScript errors
4. Database logs for query issues
