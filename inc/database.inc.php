<?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // Brukeren som skal brukes
    define('DB_PASS', 'dev123'); // MySql passordet
    define('DB_NAME', 'prosjekt_db'); // Database navnet

    // Database-DSN med UTF-8 aktivert
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        // Koble til med PDO + UTF-8
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);

    } catch (PDOException $e) {
        echo "Feil ved tilkobling: " . $e->getMessage();
    }
?>
