<?php
/**
 * Migration Test Suite
 * Run this file to verify the migration is working correctly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP 7.4 Migration Test Suite</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 1: Check PHP Version
echo "<h2>Test 1: PHP Version Check</h2>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "✅ PHP version is 7.4 or higher<br>";
} else {
    echo "❌ PHP version is below 7.4<br>";
}

// Test 2: Check PDO MySQL
echo "<h2>Test 2: PDO MySQL Driver</h2>";
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded<br>";
} else {
    echo "❌ PDO MySQL extension is NOT loaded<br>";
}

// Test 3: Database Connection
echo "<h2>Test 3: Database Connection</h2>";
try {
    include_once 'dbutils.php';
    include_once 'db-connect/dbutils.php';
    
    db_connect();
    $pdo = get_db_connection();
    
    if ($pdo instanceof PDO) {
        echo "✅ PDO connection established successfully<br>";
        echo "Connection Type: " . get_class($pdo) . "<br>";
    } else {
        echo "❌ Connection is not a PDO instance<br>";
    }
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
}

// Test 4: Query Execution
echo "<h2>Test 4: Query Execution Test</h2>";
try {
    // Test simple query
    $result = $pdo->query("SELECT VERSION() as version");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "✅ MySQL Version: " . $row['version'] . "<br>";
    
    // Test database selection
    $result = $pdo->query("SELECT DATABASE() as db");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "✅ Current Database: " . $row['db'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Query failed: " . $e->getMessage() . "<br>";
}

// Test 5: Check Tables
echo "<h2>Test 5: Database Tables Check</h2>";
try {
    $tables = ['usuario', 'tipousr', 'item', 'proveedor', 'categoria', 'grupo', 'pais', 'unidad'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
            $existing_tables[] = $table;
            echo "✅ Table '$table' exists<br>";
        } catch (PDOException $e) {
            echo "⚠️  Table '$table' not found<br>";
        }
    }
    
    echo "<p><strong>Found " . count($existing_tables) . " out of " . count($tables) . " expected tables</strong></p>";
    
} catch (Exception $e) {
    echo "❌ Table check failed: " . $e->getMessage() . "<br>";
}

// Test 6: Utility Functions
echo "<h2>Test 6: Utility Functions Test</h2>";
try {
    include_once 'main.php';
    
    // Test get_units_opt
    $units = get_units_opt(0);
    if (!empty($units)) {
        echo "✅ get_units_opt() working<br>";
    } else {
        echo "⚠️  get_units_opt() returned empty (might be no data)<br>";
    }
    
    // Test get_group_opt
    $groups = get_group_opt(0);
    if (!empty($groups)) {
        echo "✅ get_group_opt() working<br>";
    } else {
        echo "⚠️  get_group_opt() returned empty (might be no data)<br>";
    }
    
    // Test get_pais_opt
    $paises = get_pais_opt(0);
    if (!empty($paises)) {
        echo "✅ get_pais_opt() working<br>";
    } else {
        echo "⚠️  get_pais_opt() returned empty (might be no data)<br>";
    }
    
    // Test get_categoria_opt
    $categorias = get_categoria_opt(0);
    if (!empty($categorias)) {
        echo "✅ get_categoria_opt() working<br>";
    } else {
        echo "⚠️  get_categoria_opt() returned empty (might be no data)<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Utility functions test failed: " . $e->getMessage() . "<br>";
}

// Test 7: Prepared Statements
echo "<h2>Test 7: Prepared Statements Test</h2>";
try {
    // Test prepared statement with parameter
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM usuario WHERE username = :username");
    $stmt->execute(['username' => 'nonexistent_user_test']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Prepared statements working correctly<br>";
    echo "Test query returned: " . $result['count'] . " rows (expected 0)<br>";
    
} catch (PDOException $e) {
    echo "❌ Prepared statement failed: " . $e->getMessage() . "<br>";
}

// Test 8: Regular Expression Functions
echo "<h2>Test 8: Regular Expression Functions</h2>";
$test_string = "*CODE39*";
if (preg_match("/^\*.*\*$/", $test_string)) {
    echo "✅ preg_match() working (ereg replacement)<br>";
} else {
    echo "❌ preg_match() test failed<br>";
}

// Test 9: Password Hashing
echo "<h2>Test 9: Password Hashing Test</h2>";
$test_password = "test123";
$hash = password_hash($test_password, PASSWORD_BCRYPT);
if (password_verify($test_password, $hash)) {
    echo "✅ Password hashing functions available<br>";
    echo "Sample hash: " . substr($hash, 0, 30) . "...<br>";
} else {
    echo "❌ Password hashing test failed<br>";
}

// Test 10: Error Handling
echo "<h2>Test 10: Error Handling Test</h2>";
try {
    // Intentionally cause an error
    $pdo->query("SELECT * FROM nonexistent_table_xyz");
    echo "❌ Error handling not working - should have thrown exception<br>";
} catch (PDOException $e) {
    echo "✅ Error handling working correctly<br>";
    echo "Caught expected exception: " . substr($e->getMessage(), 0, 50) . "...<br>";
}

// Summary
echo "<h2>Test Summary</h2>";
echo "<p><strong>Testing complete!</strong></p>";
echo "<p>Check the results above. All tests with ✅ are passing.</p>";
echo "<p>Tests with ⚠️  may be warnings (usually due to empty tables).</p>";
echo "<p>Tests with ❌ indicate failures that need attention.</p>";

?>
