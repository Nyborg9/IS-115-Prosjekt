<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

require_once "../inc/database.inc.php";

$userID = $_SESSION['UserID'];

// Hvis skjemaet er sendt inn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = $_POST['Email']      ?? '';
    $firstName  = $_POST['FirstName']  ?? '';
    $lastName   = $_POST['LastName']   ?? '';
    $dateOfBirth = $_POST['DateOfBirth'] ?? '';

    // Enkle valideringer
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ugyldig epostadresse.";
    }

    if (empty($errors)) {
        $sqlUpdate = "
            UPDATE users
            SET Email = :email,
                FirstName = :firstName,
                LastName = :lastName,
                DateOfBirth = :dateOfBirth
            WHERE UserID = :userID
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':dateOfBirth', $dateOfBirth, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            
            // Oppdater session så informasjonen er riktig
            $_SESSION['FirstName'] = $firstName;
            $_SESSION['LastName']  = $lastName;
            $_SESSION['Email']     = $email;

            // Tilbake til profilside
            header("Location: profile.view.php");
            exit;
        } else {
            $errors[] = "Noe gikk galt.";
        }
    }
}

// Hent eksisterende data til å fylle inn skjemaet
$sql = "
    SELECT Email, FirstName, LastName, DateOfBirth
    FROM users
    WHERE UserID = :userID
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Fant ikke brukeren.");
}

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Endre informasjon</title>
    <?php include "../inc/navbarController.inc.php"; ?>
    <style>
        .centered-content {
            max-width: 600px;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"] {
            width: 100%;
            padding: 6px;
            box-sizing: border-box;
        }
        .errors {
            color: red;
        }
    </style>
</head>
<body>
<div class="centered-content">
    <h1>Endre informasjon</h1>

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
        <label for="Email">E-post</label>
        <input type="email" name="Email" id="Email"
               value="<?= htmlspecialchars($user['Email']); ?>" required>

        <label for="FirstName">Fornavn</label>
        <input type="text" name="FirstName" id="FirstName"
               value="<?= htmlspecialchars($user['FirstName']); ?>" required>

        <label for="LastName">Etternavn</label>
        <input type="text" name="LastName" id="LastName"
               value="<?= htmlspecialchars($user['LastName']); ?>" required>

        <label for="DateOfBirth">Fødselsdato</label>
        <input type="date" name="DateOfBirth" id="DateOfBirth"
               value="<?= htmlspecialchars($user['DateOfBirth']); ?>">

        <br><br>
        <button type="submit">Lagre endringer</button>
        <a href="profile.view.php">Avbryt</a>
    </form>
</div>
</body>
</html>
