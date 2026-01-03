<?php

// Global PDO connection
$GLOBALS['db_pdo'] = null;

function db_do_connection()
{
    try {
        // Docker environment - connect to mysql service
        $GLOBALS['db_pdo'] = new PDO(
            'mysql:host=mysql;dbname=north_asus;charset=utf8mb4',
            'suda',
            'suda',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $GLOBALS['db_pdo'];
    } catch (PDOException $e) {
        echo "Error: No es posible conectar al motor de base de datos. " . $e->getMessage();
        exit;
    }
}

// Helper function to get PDO connection
function get_db_connection()
{
    if ($GLOBALS['db_pdo'] === null) {
        db_do_connection();
    }
    return $GLOBALS['db_pdo'];
}

// Helper function for backward compatibility - executes query with PDO
function db_query($query)
{
    $pdo = get_db_connection();
    try {
        return $pdo->query($query);
    } catch (PDOException $e) {
        echo "Query Error: " . $e->getMessage();
        return false;
    }
}

// Helper to escape strings (for gradual migration)
function db_escape_string($string)
{
    $pdo = get_db_connection();
    return trim($pdo->quote($string), "'");
}

?>
