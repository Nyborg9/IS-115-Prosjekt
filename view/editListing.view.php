<?php
session_start();
require_once "../inc/database.inc.php";

// Må være innlogget
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

// Må være arbeidsgiver
if (!isset($_SESSION['RoleID']) || (int)$_SESSION['RoleID'] != 1) {
    header("Location: noAccess.view.php");
    exit;
}

$userID = (int)$_SESSION['UserID'];

// Sjekk listingID fra GET
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    die("Ugyldig stillings-ID.");
}

$listingID = (int)$_GET['listingID'];

// Hent stillingen og sjekk at den tilhører innlogget arbeidsgiver
$sql = "
    SELECT
        ListingID,
        Title,
        ListingDescription,
        Requirements,
        TimePeriod,
        HourScope
    FROM listings
    WHERE ListingID = :listingID
      AND UserID = :userID
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':listingID', $listingID, PDO::PARAM_INT);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die("Du har ikke tilgang til denne stillingen, eller den finnes ikke.");
}

$errors = [];

// Håndter POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hent verdier fra skjema
    $title               = $_POST['Title'] ?? '';
    $listingDescription  = $_POST['ListingDescription'] ?? '';
    $requirements        = $_POST['Requirements'] ?? '';
    $timePeriod          = $_POST['TimePeriod'] ?? '';
    $hourScope           = $_POST['HourScope'] ?? '';

    // Trim
    $title              = trim($title);
    $listingDescription = trim($listingDescription);
    $requirements       = trim($requirements);
    $timePeriod         = trim($timePeriod);
    $hourScope          = trim($hourScope);

    // Enkel validering
    if ($title === '') {
        $errors[] = "Tittelen kan ikke være tom.";
    }
    if ($listingDescription === '') {
        $errors[] = "Beskrivelsen kan ikke være tom.";
    }

    // Hvis det er feil, legg verdiene tilbake i $listing så skjemaet beholder det bruker skrev
    $listing['Title']              = $title;
    $listing['ListingDescription'] = $listingDescription;
    $listing['Requirements']       = $requirements;
    $listing['TimePeriod']         = $timePeriod;
    $listing['HourScope']          = $hourScope;

    if (empty($errors)) {
        // Oppdater stillingen
        $sqlUpdate = "
            UPDATE listings
            SET
                Title = :title,
                ListingDescription = :listingDescription,
                Requirements = :requirements,
                TimePeriod = :timePeriod,
                HourScope = :hourScope
            WHERE ListingID = :listingID
              AND UserID = :userID
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':title', $title, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':listingDescription', $listingDescription, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':requirements', $requirements, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':timePeriod', $timePeriod, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':hourScope', $hourScope, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':listingID', $listingID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            // Tilbake til oversikten over egne stillinger
            header("Location: myListings.view.php");
            exit;
        } else {
            $errors[] = "Noe gikk galt ved lagring. Prøv igjen.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger stilling</title>
    <link rel="stylesheet" href="../css/css.css">
    <?php 
    include "../inc/navbarController.inc.php"; ?>
</head>
<body>
<div class="centered-content">
    <h1>Rediger stilling</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="Title">Tittel</label>
        <input type="text" id="Title" name="Title"
               value="<?= htmlspecialchars($listing['Title']); ?>" required>

        <label for="ListingDescription">Beskrivelse</label>
        <textarea id="ListingDescription" name="ListingDescription" required><?= htmlspecialchars($listing['ListingDescription']); ?></textarea>

        <label for="Requirements">Krav (valgfritt)</label>
        <textarea id="Requirements" name="Requirements"><?= htmlspecialchars($listing['Requirements']); ?></textarea>

        <label for="TimePeriod">Tidsperiode (valgfritt)</label>
        <input type="text" id="TimePeriod" name="TimePeriod"
               value="<?= htmlspecialchars($listing['TimePeriod']); ?>">

        <label for="HourScope">Omfang (timer) (valgfritt)</label>
        <input type="text" id="HourScope" name="HourScope"
               value="<?= htmlspecialchars($listing['HourScope']); ?>">

        <div class="actions">
            <button type="submit">Lagre endringer</button>
            <a href="myListings.view.php">Avbryt</a>
        </div>
    </form>
</div>
</body>
</html>
