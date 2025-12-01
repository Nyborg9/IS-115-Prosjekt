<?php
session_start();
require_once "../inc/database.inc.php";

// Må være innlogget
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}


$userID = $_SESSION['UserID'];

//Sletting av søknader
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteApplication'])) {
    $applicationIDToDelete = isset($_POST['ApplicationID']) ? $_POST['ApplicationID'] : 0;

    if ($applicationIDToDelete > 0) {
        $sqlDelete = "
            DELETE FROM applications
            WHERE ApplicationID = :applicationID
              AND UserID = :userID
        ";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':applicationID', $applicationIDToDelete, PDO::PARAM_INT);
        $stmtDelete->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmtDelete->execute();
    }

    // Redirect for å unngå resubmission ved refresh
    header("Location: myApplications.view.php");
    exit;
}

// Hent alle søknader tilhørende innlogget bruker
$sql = "
    SELECT
        a.ApplicationID,
        a.ListingID,
        a.ApplicationText,
        a.created_at,
        a.ApplicationStatus,
        l.Title AS ListingTitle
    FROM applications a
    JOIN listings l ON a.ListingID = l.ListingID
    WHERE a.UserID = :userID
    ORDER BY a.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Mine søknader</title>
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
        button.btn, a.btn {
            padding: 3px 8px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid #333;
        }
    </style>
</head>
<body>
<div class="centered-content">
    <h1>Mine søknader</h1>

    <?php if (count($applications) == 0): ?>
        <p>Du har ikke sendt inn noen søknader enda.</p>
    <?php else: ?>
        <?php foreach ($applications as $app): ?>
            <?php
            $statusInt = $app['ApplicationStatus'];
            $statusText = match ($statusInt) {
                1 => 'Venter på svar',
                2 => 'Godkjent',
                3 => 'Avvist',
            };
            ?>
            <div class="application-card">
                <div class="meta">
                    Stilling: <?= htmlspecialchars($app['ListingTitle']); ?><br>
                    Sendt: <?= htmlspecialchars($app['created_at']); ?><br>
                    Status: <span class="status"><?= htmlspecialchars($statusText); ?></span>
                </div>

                <p><?= nl2br(htmlspecialchars($app['ApplicationText'])); ?></p>

                <div class="actions">
                    <!-- Endre søknad -->
                    <a class="btn" href="editApplication.view.php?applicationID=<?= $app['ApplicationID']; ?>">
                        Endre søknad
                    </a>

                    <!-- Slett søknad -->
                    <form method="post" class="inline"
                          onsubmit="return confirm('Er du sikker på at du vil slette denne søknaden?');">
                        <input type="hidden" name="ApplicationID" value="<?= $app['ApplicationID']; ?>">
                        <button type="submit" name="deleteApplication" class="btn"
                                style="background:#e74c3c;color:white;">
                            Slett
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
