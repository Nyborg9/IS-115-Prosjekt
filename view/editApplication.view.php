<?php
session_start();
require_once "../inc/database.inc.php";

// Må være innlogget
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

$userID = $_SESSION['UserID'];

// Sjekk applicationID fra GET
if (!isset($_GET['applicationID']) || !is_numeric($_GET['applicationID'])) {
    echo("Ugyldig søknads-ID.");
    exit;
}

$applicationID = (int)$_GET['applicationID'];

$errors = [];

// Hvis skjemaet er sendt inn
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicationText = $_POST['ApplicationText'] ?? '';
    $applicationText = trim($applicationText);

    if ($applicationText == '') {
        $errors[] = "Søknadsteksten kan ikke være tom.";
    }

    if (empty($errors)) {
        $sqlUpdate = "
            UPDATE applications
            SET ApplicationText = :applicationText
            WHERE ApplicationID = :applicationID
              AND UserID = :userID
        ";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':applicationText', $applicationText, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            header("Location: myApplications.view.php");
            exit;
        } else {
            $errors[] = "Noe gikk galt under lagring. Prøv igjen.";
        }
    }
}

// Hent søknaden for å fylle inn skjemaet
$sql = "
    SELECT
        a.ApplicationID,
        a.ApplicationText,
        a.ApplicationStatus,
        a.created_at,
        l.Title AS ListingTitle
    FROM applications a
    JOIN listings l ON a.ListingID = l.ListingID
    WHERE a.ApplicationID = :applicationID
      AND a.UserID = :userID
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$application = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$application) {
    echo("Fant ikke søknaden, eller du har ikke tilgang til den.");
    exit;
}

// Hvis vi nettopp har POSTet med feil, behold endret tekst
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['ApplicationText'])) {
    $application['ApplicationText'] = $_POST['ApplicationText'];
}

$statusInt = (int)$application['ApplicationStatus'];
$statusText = match ($statusInt) {
    1 => 'Venter på svar',
    2 => 'Godkjent',
    3 => 'Avvist',
};
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Rediger søknad</title>
    <?php include "../inc/navbarController.inc.php"; ?>
    <style>
        .centered-content {
            max-width: 700px;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 6px;
            box-sizing: border-box;
        }
        .errors {
            color: red;
            margin-bottom: 10px;
        }
        .meta {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 10px;
        }
        .actions {
            margin-top: 15px;
        }
        .actions button, .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="centered-content">
    <h1>Rediger søknad</h1>

    <div class="meta">
        Stilling: <?= htmlspecialchars($application['ListingTitle']); ?><br>
        Sendt: <?= htmlspecialchars($application['created_at']); ?><br>
        Status: <?= htmlspecialchars($statusText); ?>
    </div>

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
        <label for="ApplicationText">Søknadstekst</label>
        <textarea id="ApplicationText" name="ApplicationText" required><?= htmlspecialchars($application['ApplicationText']); ?></textarea>

        <div class="actions">
            <button type="submit">Lagre endringer</button>
            <a href="myApplications.view.php">Avbryt</a>
        </div>
    </form>
</div>
</body>
</html>
