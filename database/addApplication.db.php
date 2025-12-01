<?php
require_once('../inc/database.inc.php');

function addApplication(PDO $pdo, int $userID, int $listingID, string $applicationText, ?string $cvPath): bool
{
    try {
        $sql = "
            INSERT INTO applications (UserID, ListingID, ApplicationText, CvPath, created_at, ApplicationStatus)
            VALUES (:UserID, :ListingID, :ApplicationText, :CvPath, NOW(), 1)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':UserID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':ListingID', $listingID, PDO::PARAM_INT);
        $stmt->bindParam(':ApplicationText', $applicationText, PDO::PARAM_STR);
        $stmt->bindParam(':CvPath', $cvPath, PDO::PARAM_STR); // kan være null

        $stmt->execute();

        return $pdo->lastInsertId() > 0;
    } catch (PDOException $e) {
        echo "Feil ved lagring av søknad: " . $e->getMessage() . "<br>";
        return false;
    }
}
