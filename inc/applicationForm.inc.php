<?php

// Sjekk at bruker er logget inn
if (!isset($_SESSION['UserID'])) {
    header("Location: login.view.php");
    exit;
}

$userID = $_SESSION['UserID'];

// Hent ListingID fra GET
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    die("Ugyldig stilling.");
}

$listingID = $_GET['listingID'];

// Koble til DB og hent stillingstittel
require_once "../inc/database.inc.php";

$sqlListing = "
    SELECT Title
    FROM listings
    WHERE ListingID = :ListingID
";
$stmtListing = $pdo->prepare($sqlListing);
$stmtListing->bindParam(':ListingID', $listingID, PDO::PARAM_INT);
$stmtListing->execute();
$listing = $stmtListing->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    echo("Fant ikke stillingen.");
    exit;
}

// Kjøres når brukeren trykker "Send søknad"
if (isset($_POST['createApplication'])) {

    // Hent ut data
    $applicationText = $_POST['ApplicationText'] ?? '';
    $applicationText = trim($applicationText);

    if ($applicationText == '') {
        echo "Søknadstekst kan ikke være tom.";
        exit;
    } else {

        // Sjekk om brukeren allerede har søkt på denne stillingen
    $sqlCheck = "
        SELECT *
        FROM applications
        WHERE UserID = :userID
          AND ListingID = :listingID
    ";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmtCheck->bindParam(':listingID', $listingID, PDO::PARAM_INT);
    $stmtCheck->execute();
    $oldApplication = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!empty($oldApplication)) {
        echo  ("Du har allerede sendt inn en søknad til denne stillingen.");
        exit;
    }

    // Håndter CV-opplasting
    $cvPath = null;

    if (isset($_FILES['CvFile'])) {

        $allowedTypes = ['application/pdf' => 'pdf'];
        $maxSize = 1 * 1024 * 1024; // 1MB
        
            $tmpName  = $_FILES['CvFile']['tmp_name'];
            $fileType = $_FILES['CvFile']['type'];
            $fileSize = $_FILES['CvFile']['size'];

            if (!array_key_exists($fileType, $allowedTypes)) {
                echo "CV må være en PDF-fil.";
                exit;
            }

            if ($fileSize > $maxSize) {
                echo "CV-filen er for stor (maks 1MB).";
                exit;
            }


            $cvDir = __DIR__ . "    ../../CV/";

            $suffix = $allowedTypes[$fileType]; // "pdf"
            $fileName = $userID . "_" . $listingID . "." . $suffix;
            $targetPath = $cvDir . $fileName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                echo "Kunne ikke lagre CV-filen på serveren.";
                exit;
            }

            // Sti som lagres i databasen
            $cvPath = "CV/" . $fileName;

        } else {
            echo "Noe gikk galt ved opplasting av CV.";
            exit;
        }
    }

    // Lagre søknaden i databasen
    require_once "../database/addApplication.db.php";

        if (addApplication($pdo, $userID, $listingID, $applicationText, $cvPath)) {

            header("Location: listings.view.php");
            exit;
        } else {
            echo "Kunne ikke lagre søknaden. Prøv igjen.";
            exit;
        }
    }
