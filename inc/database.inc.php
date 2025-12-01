<?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // Brukeren som skal brukes
    define('DB_PASS', ''); // MySql passordet
    define('DB_NAME', 'prosjekt_db'); // Database navnet

    // Lager en DNS streng for databasen
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME; 

    try {
        //Kobler til databasen med PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        //Gir feilmelding om den feiler
        echo "Feil ved tilkobling: " . $e->getMessage();}
?>