<?php
session_start();
require_once "../inc/database.inc.php";

// Sjekk at bruker er logget inn
if (!isset($_SESSION['UserID'])) {
    header("Location: login.view.php");
    exit;
}

$userID = $_SESSION['UserID'];

// Hent ListingID fra GET ved første visning
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    echo("Ugyldig stilling.");
}

$listingID = $_GET['listingID'];

// Hvis skjemaet er sendt inn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationText = $_POST['ApplicationText'] ?? '';

    if (trim($applicationText) === '') {
        $error = "Søknadstekst kan ikke være tom.";
    } else {
        // Lagre søknaden i databasen
        $sql = "
            INSERT INTO applications (UserID, ListingID, ApplicationText, created_at)
            VALUES (:UserID, :ListingID, :ApplicationText, NOW())
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':UserID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':ListingID', $listingID, PDO::PARAM_INT);
        $stmt->bindParam(':ApplicationText', $applicationText, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Ferdig – send bruker tilbake til stillinger eller en "takk"-side
            header("Location: listings.view.php");
            exit;
        } else {
            $error = "Kunne ikke lagre søknaden. Prøv igjen.";
        }
    }
}

// Hent litt info om stillingen til overskrift (valgfritt, men fint)
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Søk på: <?= htmlspecialchars($listing['Title']) ?></title>
    <?php include "../inc/navbarController.inc.php"; ?>
</head>
<body>
<div class="centered-content">
    <h1>Søk på: <?= htmlspecialchars($listing['Title']) ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="ListingID" value="<?= $listingID ?>">

        <label for="ApplicationText">Søknadstekst</label><br>
        <textarea name="ApplicationText" id="ApplicationText" rows="10" cols="60" required></textarea><br><br>

        <button type="submit">Send søknad</button>
    </form>

    <p><a href="listings.view.php">← Tilbake til stillinger</a></p>
</div>
</body>
</html>
