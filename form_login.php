<?php

include 'main.php';
include 'dbutils.php';

// Sanitize inputs
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$clave = isset($_POST['password']) ? $_POST['password'] : '';

// Validate inputs
if (empty($username) || empty($clave)) {
    echo "usuario o clave invalidos.";
    exit;
}

db_connect();
$pdo = get_db_connection();

// Use prepared statement to prevent SQL injection
$query = "SELECT nivel, nombre, clave
  FROM usuario
  INNER JOIN tipousr ON tipousr.id_tipousr = usuario.id_tipousr
  WHERE username = :username";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $row = $stmt->fetch();
    
    session_start();
    
    // Check if user exists and verify password
    if ($row) {
        // Check if password is hashed (starts with $2y$ for bcrypt)
        if (strpos($row['clave'], '$2y$') === 0) {
            // New hashed password - use password_verify
            $password_valid = password_verify($clave, $row['clave']);
        } else {
            // Old plain text password - direct comparison (for backward compatibility)
            $password_valid = ($row['clave'] === $clave);
        }
        
        if ($password_valid) {
            $valid_user = $username;
            $user_level = $row['nivel'];
            $_SESSION['valid_user'] = $valid_user;
            $_SESSION['user_level'] = $user_level;
            $var = array("username" => $valid_user);
            eval_html('main_menu2.html', $var);
        } else {
            echo "usuario o clave invalidos.";
        }
    } else {
        echo "usuario o clave invalidos.";
    }
} catch (PDOException $e) {
    echo "Error en la autenticación.";
    error_log("Login error: " . $e->getMessage());
}

?>

