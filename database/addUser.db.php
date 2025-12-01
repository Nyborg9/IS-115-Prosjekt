<?php
require_once('../inc/database.inc.php');


function addUser(PDO $pdo, string $firstName, string $lastName, string $email, string $birthday, string $passwordHash): bool {


#Når funksjonen kjøres så prøver mann å legge til en bruker i databasen.
try {
    $sql = "INSERT IGNORE INTO Users
            (FirstName, LastName, Email, DateOfBirth, Password_hash)
            VALUES
            (:FirstName, :LastName, :Email, :DateOfBirth,:Password_hash)";


    $q = $pdo->prepare($sql);




    $q->bindParam(':FirstName', $firstName, PDO::PARAM_STR);
    $q->bindParam(':LastName', $lastName, PDO::PARAM_STR);
    $q->bindParam(':Email', $email, PDO::PARAM_STR);
    $q->bindParam(':DateOfBirth', $birthday, PDO::PARAM_STR);
    $q->bindParam(':Password_hash', $passwordHash, PDO::PARAM_STR);


    $q->execute();

    #Etter brukeren lages så får du returnert true, fordi lastInsertId blir mer enn 0
    return $pdo->lastInsertId() > 0;
} catch (PDOException $e) {
    echo "Feil ved tilkobling: " . $e->getMessage() . "<br>";
    return false;
}
}
?>



