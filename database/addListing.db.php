<?php
require_once('../inc/database.inc.php');


function addListing(PDO $pdo, $title, $userID, $listingDescription, $requirements, $timePeriod, $hourScope): bool {


#Når funksjonen kjøres så prøver mann å legge til en bruker i databasen.
try {
    $sql = "INSERT INTO Listings
            (Title, UserID, ListingDescription, Requirements, TimePeriod, HourScope)
            VALUES
            (:Title, :UserID, :ListingDescription, :Requirements, :TimePeriod, :HourScope)";


    $q = $pdo->prepare($sql);



    $q->bindParam(':Title', $title, PDO::PARAM_STR);
    $q->bindParam(':UserID', $userID, PDO::PARAM_STR);
    $q->bindParam(':ListingDescription', $listingDescription, PDO::PARAM_STR);
    $q->bindParam(':Requirements', $requirements, PDO::PARAM_STR);
    $q->bindParam(':TimePeriod', $timePeriod, PDO::PARAM_STR);
    $q->bindParam(':HourScope', $hourScope, PDO::PARAM_STR);


    $q->execute();

    #Etter brukeren lages så får du returnert true, fordi lastInsertId blir mer enn 0
    return $pdo->lastInsertId() > 0;
} catch (PDOException $e) {
    echo "Feil ved tilkobling: " . $e->getMessage() . "<br>";
    return false;
}
}
?>



