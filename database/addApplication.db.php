<?php
require_once('../inc/database.inc.php');

function addApplication(PDO $pdo, int $userID, int $listingID, string $applicationText): bool
{
    try {
        $sql = "
            INSERT INTO applications (UserID, ListingID, ApplicationText, created_at)
            VALUES (:UserID, :ListingID, :ApplicationText, NOW())
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':UserID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':ListingID', $listingID, PDO::PARAM_INT);
        $stmt->bindParam(':ApplicationText', $applicationText, PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId() > 0;
    } catch (PDOException $e) {
        echo "Feil ved lagring av søknad: " . $e->getMessage() . "<br>";
        return false;
    }
}
