<?php
/**
 * Professional Database Connection
 * Supports Local Development (XAMPP) and Production (Railway/Render/VPS)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Database Credentials (Uses Environment Variables if available)
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'style_hub';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: ''; // XAMPP default is empty
$charset = 'utf8mb4';

// 2. DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. PDO Options for Security & Error Handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws errors as exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns data as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Uses real prepared statements for SQL injection protection
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     /**
      * SECURITY TIP: On a live server, we log the error privately 
      * and show a generic message to the user so we don't leak DB details.
      */
     error_log("Database Connection Error: " . $e->getMessage());
     
     if (getenv('APP_ENV') === 'production') {
         die("Our luxury vault is temporarily undergoing maintenance. Please try again shortly.");
     } else {
         // Show full error only while developing locally
         die("Database connection failed: " . $e->getMessage());
     }
}
?>