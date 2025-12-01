<?php
// Tidssone
$dts = new DateTimeZone("Europe/Oslo");

// Tid ved lasting av skjema
$dtstart = new DateTime("now", $dts);

// Feilmelding til bruk i view
$error = "";

// Sjekk at bruker er logget inn
if (!isset($_SESSION['UserID'])) {
    header("Location: login.view.php");
    exit;
}

$userID = (int)$_SESSION['UserID'];

// Hent ListingID fra GET
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    die("Ugyldig stilling.");
}

$listingID = (int)$_GET['listingID'];

// Koble til DB og hent stillingstittel (til overskrift)
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
    die("Fant ikke stillingen.");
}

// Kjøres når brukeren trykker "Send søknad"
if (isset($_POST['createApplication'])) {

    // Bot-sjekk tid
    if (!empty($_POST['dtstart'])) {
        $dtstart = new DateTime($_POST['dtstart'], $dts);
    }
    $dtslutt = new DateTime("now", $dts);

    // Hent ut data
    $applicationText = $_POST['ApplicationText'] ?? '';

    if (trim($applicationText) === '') {
        $error = "Søknadstekst kan ikke være tom.";
    } else {
        require_once "../database/addApplication.db.php";

        if (addApplication($pdo, $userID, $listingID, $applicationText)) {
            // Ferdig send bruker tilbake til stillinger eller en takk-side
            header("Location: listings.view.php");
            exit;
        } else {
            $error = "Kunne ikke lagre søknaden. Prøv igjen.";
        }
    }
}
