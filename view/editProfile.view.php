<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

require_once "../inc/database.inc.php";

$userID = $_SESSION['UserID'];

$errors = [];

// Hvis skjemaet er sendt inn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email       = $_POST['Email']      ?? '';
    $firstName   = $_POST['FirstName']  ?? '';
    $lastName    = $_POST['LastName']   ?? '';
    $birthday = $_POST['DateOfBirth'] ?? '';

    // Sjekker 
    if ($birthday === '') {
        $errors[] = "Du må oppgi fødselsdato.";
    } else {
        $birthdayDate = DateTime::createFromFormat('Y-m-d', $birthday);
        $today        = new DateTime('today');

        if (!$birthdayDate || $birthdayDate->format('Y-m-d') !== $birthday) {
            $errors[] = "Ugyldig fødselsdato.";
        } elseif ($birthdayDate > $today) {
            $errors[] = "Fødselsdato kan ikke være i fremtiden.";
        } else {
            $age = $birthdayDate->diff($today)->y;
            if ($age < 18) {
                $errors[] = "Du må være minst 18 år for å være registrert. (Du er $age år)";
            }
        }
    }

    //Sjekker eposten
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ugyldig epostadresse.";
    } else {
        // Sjekk om e-posten allerede brukes av en annen bruker
        $sql = "
            SELECT UserID
            FROM users
            WHERE Email = :email
              AND UserID <> :userID
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()) {
            $errors[] = "Denne epostadressen er allerede i bruk av en annen bruker.";
        }
    }

    //Sjekker lengden på navnet
    if (strlen($firstName) <= 2) {
        $errors[] = "Fornavnet må være mer enn en bokstav.";
    }

    if (strlen($lastName) <= 2) {
        $errors[] = "Etternavnet må være mer enn en bokstav.";
    }

    // Hvis ingen feil: oppdater i databasen
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
        $stmtUpdate->bindParam(':dateOfBirth', $birthday, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            // Oppdater session så informasjonen er riktig
            $_SESSION['FirstName'] = $firstName;
            $_SESSION['LastName']  = $lastName;
            $_SESSION['Email']     = $email;

            header("Location: profile.view.php");
            exit;
        } else {
            echo "Noe gikk galt.";
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

// Hvis POST med feil, behold det brukeren skrev i stedet for DB-verdier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errors)) {
    $user['Email']      = $email       ?? $user['Email'];
    $user['FirstName']  = $firstName   ?? $user['FirstName'];
    $user['LastName']   = $lastName    ?? $user['LastName'];
    $user['DateOfBirth']= $dateOfBirth ?? $user['DateOfBirth'];
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Endre informasjon</title>
    <?php include "../inc/navbarController.inc.php"; 
    include "../inc/header/head.inc.php"?>
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
               value="<?= htmlspecialchars($user['DateOfBirth']); ?>" required>

        <br><br>
        <button type="submit">Lagre endringer</button>
        <a href="profile.view.php">Avbryt</a>
    </form>
</div>
</body>
</html>
