<?php
session_start();
require_once "../inc/database.inc.php";

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

$userID = $_SESSION['UserID'];

// Sjekk listingID fra GET
if (!isset($_GET['listingID']) || !is_numeric($_GET['listingID'])) {
    die("Ugyldig stillings-ID.");
}

$listingID = (int)$_GET['listingID'];

// Sjekk at denne utlysningen faktisk tilhører innlogget arbeidsgiver
$sqlListing = "
    SELECT ListingID, Title
    FROM listings
    WHERE ListingID = :listingID
      AND UserID = :userID
";
$stmtListing = $pdo->prepare($sqlListing);
$stmtListing->bindParam(':listingID', $listingID, PDO::PARAM_INT);
$stmtListing->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmtListing->execute();
$listing = $stmtListing->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die("Du har ikke tilgang til denne stillingen, eller den finnes ikke.");
}

// Håndter POST godta / avvis søknad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicationAction'])) {
    $applicationID = isset($_POST['ApplicationID']) ? (int)$_POST['ApplicationID'] : 0;
    $action = $_POST['applicationAction'];

    if ($applicationID > 0) {

        // Epost
        $to      = $_POST["Email"];
        $subject = "Vedrørende din søknad på stillingen " . $_POST["Title"];
        $from    = "jegernyborg@gmail.com";
        $mheader = "From: " . $from . "\r\n" .
                   "Reply-To: " . $to . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        if ($action === "Godta") {
            $newStatus = 2;
            $message   = "Du har gått videre i vår prosses, og vi vil ta kontakt med deg fortløpene for å avtale intevju";
        } elseif ($action === "Avvis") {
            $newStatus = 3;
            $message   = "Du ble dessverre ikke tatt med videre i vår prosess.";
        } else {
            $newStatus = 1; // fallback, burde egentlig ikke skje
        }

        // Oppdater bare hvis søknaden tilhører denne stillingen
        $sqlUpdate = "
            UPDATE applications a
            JOIN listings l ON a.ListingID = l.ListingID
            SET a.ApplicationStatus = :status
            WHERE a.ApplicationID = :applicationID
              AND a.ListingID = :listingID
              AND l.UserID = :userID
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':status', $newStatus, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':listingID', $listingID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmtUpdate->execute();

        mail($to, $subject, $message, $mheader);
    }

    // Redirect for å unngå resubmission ved refresh
    header("Location: listingApplications.view.php?listingID=" . $listingID);
    exit;
}

// Hent alle søknader til denne stillingen
$sqlApplications = "
    SELECT
        a.ApplicationID,
        a.UserID,
        a.ApplicationText,
        a.created_at,
        a.ApplicationStatus,
        a.CvPath,          -- 🔹 NY: hent sti til PDF
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
    <style>
        .centered-content {
            max-width: 900px;
            margin: 20px auto;
        }
        .application-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 12px;
        }
        .meta {
            font-size: 0.9em;
            color: #555;
        }
        .status {
            font-weight: bold;
        }
        form.inline {
            display: inline;
        }
        button.btn {
            padding: 3px 8px;
            cursor: pointer;
        }
        a.cv-link {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            border: 1px solid #333;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="centered-content">
    <h1>Søknader til: <?= htmlspecialchars($listing['Title']); ?></h1>

    <p><a href="myListings.view.php"> Tilbake til mine stillinger</a></p>

    <?php if (count($applications) == 0): ?>
        <p>Ingen søknader enda.</p>
    <?php else: ?>
        <?php foreach ($applications as $app): ?>
            <?php
            $statusText = match ((int)$app['ApplicationStatus']) {
                1 => 'Venter',
                2 => 'Godkjent',
                3 => 'Avvist',
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

                <?php if (!empty($app['CvPath'])): ?>
                    <p>
                        CV:
                        <a class="cv-link" href="../<?= htmlspecialchars($app['CvPath']); ?>" target="_blank">
                            Åpne CV (PDF)
                        </a>
                    </p>
                <?php else: ?>
                    <p>CV: Ikke lastet opp</p>
                <?php endif; ?>

                <form method="post" class="inline">
                    <input type="hidden" name="ApplicationID" value="<?= (int)$app['ApplicationID']; ?>">
                    <input type="hidden" name="Email" value="<?= htmlspecialchars($app['Email']); ?>">
                    <input type="hidden" name="Title" value="<?= htmlspecialchars($listing['Title']); ?>">
                <!-- Godta -->
                <form method="post" class="inline" action="../inc/handleApplication.inc.php">
                    <input type="hidden" name="ApplicationID" value="<?= (int)$app['ApplicationID']; ?>">
                    <input type="hidden" name="ListingID"     value="<?= (int)$listingID; ?>">
                    <button type="submit" name="applicationAction" value="Godta" class="btn"
                            style="background:#2ecc71;color:white;">
                        Godta
                    </button>
                </form>

                <form method="post" class="inline">
                    <input type="hidden" name="ApplicationID" value="<?= (int)$app['ApplicationID']; ?>">
                    <input type="hidden" name="Email" value="<?= htmlspecialchars($app['Email']); ?>">
                    <input type="hidden" name="Title" value="<?= htmlspecialchars($listing['Title']); ?>">
                <!-- Avvis -->
                <form method="post" class="inline" action="../inc/handleApplication.inc.php">
                    <input type="hidden" name="ApplicationID" value="<?= (int)$app['ApplicationID']; ?>">
                    <input type="hidden" name="ListingID"     value="<?= (int)$listingID; ?>">
                    <button type="submit" name="applicationAction" value="Avvis" class="btn"
                            style="background:#e74c3c;color:white;">
                        Avvis
                    </button>
                </form>


            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
