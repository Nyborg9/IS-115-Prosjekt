<?php
session_start();
require_once "../inc/database.inc.php";

include "../inc/header/head.inc.php";

// Må være innlogget
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

// Må være arbeidsgiver
if (!isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
    header("Location: noAccess.view.php");
    exit;
}

$userID = (int)$_SESSION['UserID'];

// Sjekk listingID fra GET
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    die("Ugyldig stillings-ID.");
}

$listingID = $_GET['listingID'];

// Sjekk at denne utlysningen faktisk tilhører innlogget arbeidsgiver
$sqlListing = "
    SELECT ListingID, Title
    FROM listings
    WHERE ListingID = :listingID
      AND UserID    = :userID
";
$stmtListing = $pdo->prepare($sqlListing);
$stmtListing->bindParam(':listingID', $listingID, PDO::PARAM_INT);
$stmtListing->bindParam(':userID',    $userID,    PDO::PARAM_INT);
$stmtListing->execute();
$listing = $stmtListing->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die("Du har ikke tilgang til denne stillingen, eller den finnes ikke.");
}

// Hent alle søknader til denne stillingen
$sqlApplications = "
    SELECT
        a.ApplicationID,
        a.UserID,
        a.ApplicationText,
        a.created_at,
        a.ApplicationStatus,
        a.CvPath,         
        u.FirstName,
        u.LastName,
        u.Email
    FROM applications a
    JOIN users u ON a.UserID = u.UserID
    WHERE a.ListingID = :listingID
    ORDER BY a.created_at ASC
";
$stmtApp = $pdo->prepare($sqlApplications);
$stmtApp->bindParam(':listingID', $listingID, PDO::PARAM_INT);
$stmtApp->execute();
$applications = $stmtApp->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Søknader til: <?= htmlspecialchars($listing['Title']); ?></title>
    <?php include "../inc/navbarController.inc.php"; ?>
</head>
<body>
<div class="centered-content">
    <h1>Søknader til: <?= htmlspecialchars($listing['Title']); ?></h1>

    <p><a href="myListings.view.php">Tilbake til mine stillinger</a></p>

    <?php if (count($applications) === 0): ?>
        <p>Ingen søknader enda.</p>
    <?php else: ?>
        <?php foreach ($applications as $app): ?>
            <?php
            $statusText = match ((int)$app['ApplicationStatus']) {
                1       => 'Venter',
                2       => 'Godkjent',
                3       => 'Avvist',
                default => 'Ukjent',
            };
            ?>
            <div class="application-card">
                <div class="meta">
                    Søker: <?= htmlspecialchars($app['FirstName'] . ' ' . $app['LastName']); ?>
                    (<?= htmlspecialchars($app['Email']); ?>)<br>
                    Sendt: <?= htmlspecialchars($app['created_at']); ?><br>
                    Status: <span class="status"><?= htmlspecialchars($statusText); ?></span>
                </div>

                <p><?= nl2br(htmlspecialchars($app['ApplicationText'])); ?></p>

                <!-- Godta -->
                <form method="post" class="inline" action="../inc/handleApplication.inc.php">
                    <input type="hidden" name="ApplicationID" value="<?= $app['ApplicationID']; ?>">
                    <input type="hidden" name="ListingID"     value="<?= $listingID; ?>">
                    <button type="submit" name="applicationAction" value="Godta" class="btn btn-accept">
                        Godta
                    </button>
                </form>

                <!-- Avvis -->
                <form method="post" class="inline" action="../inc/handleApplication.inc.php">
                    <input type="hidden" name="ApplicationID" value="<?= $app['ApplicationID']; ?>">
                    <input type="hidden" name="ListingID"     value="<?= $listingID; ?>">
                    <button type="submit" name="applicationAction" value="Avvis" class="btn btn-danger">
                        Avvis
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
