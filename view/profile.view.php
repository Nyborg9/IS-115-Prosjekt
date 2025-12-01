<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

require_once "../inc/database.inc.php";

$userID = $_SESSION['UserID'];
$roleID = $_SESSION['RoleID'];

// Hvis bruker prøver å slette kontoen sin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAccount'])) {

    // Admin skal ikke kunne slette seg selv
    if ($roleID === 3) {
        die("Admin-kontoer kan ikke slettes.");
    }

    // Slett brukeren
    $sqlDelete = "
        DELETE FROM users
        WHERE UserID = :userID
    ";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmtDelete->execute();

    // Logg ut
    session_unset();
    session_destroy();
    header("Location: redirect.view.php");
    exit;
}

// Hent info om brukeren fra databasen
$sql = "
    SELECT UserID, Email, FirstName, LastName, DateOfBirth, RoleID
    FROM users
    WHERE UserID = :userID
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo("Fant ikke brukeren.");
}

$roleText = match ($user['RoleID']) {
    1 => "Arbeidsgiver",
    2 => "Student/jobbsøker",
    3 => "Administrator",
};

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Min profil</title>
    <?php include "../inc/navbarController.inc.php"; ?>
    <style>
        .centered-content {
            max-width: 600px;
            margin: 20px auto;
        }
        .field-label {
            font-weight: bold;
        }
        .actions {
            margin-top: 20px;
        }
        .actions a, .actions button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="centered-content">

    <h2>Din informasjon</h2>
    <p><span class="field-label">E-post:</span> <?= htmlspecialchars($user['Email']); ?></p>
    <p><span class="field-label">Fornavn:</span> <?= htmlspecialchars($user['FirstName']); ?></p>
    <p><span class="field-label">Etternavn:</span> <?= htmlspecialchars($user['LastName']); ?></p>
    <p><span class="field-label">Fødselsdato:</span> <?= htmlspecialchars($user['DateOfBirth']); ?></p>
    <p><span class="field-label">Rolle:</span> <?= htmlspecialchars($roleText); ?></p>

    <div class="actions">
        <!-- Knapp for å redigere info -->
        <a href="editProfile.view.php" class="btn" style="border:1px solid black;">Endre informasjon</a>

        <!-- Slett-konto knapp (ikke for admin) -->
        <?php if ($user['RoleID'] != 3): ?>
            <form method="post" style="display:inline;"
                  onsubmit="return confirm('Er du sikker på at du vil slette kontoen din? Dette kan ikke angres.');">
                <button type="submit" name="deleteAccount" class="btn"
                        style="background:#e74c3c;color:white;">
                    Slett konto
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
