<?php
session_start();
require_once "../inc/database.inc.php";

if (!isset($_SESSION['RoleID'])) {
    header("Location: redirect.view.php");
    exit;
}

//Sjekker at bruker er admin
elseif ($_SESSION['RoleID'] != 3) {
    header("Location: noAccess.view.php");
    exit;
}

//Funksjon som sletter brukere
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    $userID = isset($_POST['UserID']) ? $_POST['UserID'] : 0;

    // Ikke la admin slette seg selv, og slett kun brukere med rolle 1 eller 2
    if ($userID > 0 && $userID != $_SESSION['UserID']) {
        $sqlDelete = "
            DELETE FROM users
            WHERE UserID = :userID
              AND RoleID IN (1, 2)
        ";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmtDelete->execute();
    }

    // Redirect for å unngå resubmission ved refresh
    header("Location: roleAdmin.view.php");
    exit;
}

// Håndterer endring av rolle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeRole'])) {
    $userID = isset($_POST['UserID']) ? $_POST['UserID'] : 0;
    $currentRoleID = isset($_POST['CurrentRoleID']) ? $_POST['CurrentRoleID'] : 0;

    // Bare lov å endre mellom 1 og 2
    if ($userID > 0 && ($currentRoleID == 1 || $currentRoleID == 2)) {

        // Bestem ny rolle
        if($currentRoleID == 1){
            $newRoleID = 2;
        }elseif($currentRoleID == 2){
            $newRoleID = 1;
        }

        $sqlUpdate = "
            UPDATE users
            SET RoleID = :newRoleID
            WHERE UserID = :userID
              AND RoleID IN (1, 2)
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':newRoleID', $newRoleID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmtUpdate->execute();
    }

    // Redirect for å unngå resubmission ved refresh
    header("Location: roleAdmin.view.php");
    exit;
}

// Hent alle brukere med rolle 1 eller 2
$sql = "
    SELECT
        UserID,
        Email,
        FirstName,
        LastName,
        DateOfBirth,
        RoleID
    FROM users
    WHERE RoleID IN (1, 2)
    ORDER BY UserID ASC, LastName ASC, FirstName ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Brukeradministrasjon</title>
    <?php include "../inc/navbarController.inc.php"; 
    include "../inc/header/head.inc.php";
    ?>
</head>
<body>
<div class="centered-content">
    <h1>Brukeradministrasjon</h1>

    <?php if (count($users) == 0): ?>
        <p>Ingen brukere funnet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
            <tr>
                <th>UserID</th>
                <th>Navn</th>
                <th>E-post</th>
                <th>Fødselsdato</th>
                <th>Rolle</th>
                <th>Endre rolle</th>
                <th>Slett</th>
            </tr>
            </thead>
            <tbody>
    <!-- Loop som går igjennom alle brukerene og viser dem i en tabell-->
            <?php foreach ($users as $user): ?>
                <?php
                if ($user['RoleID'] == 1) {
                    $roleText = "Arbeidsgiver";
                    $btnText = "Endre til student/jobbsøker";
                } elseif($user['RoleID'] == 2) {
                    $roleText = "Student/jobbsøker";
                    $btnText = "Gjør om til en arbeidsgiver";
                }

                ?>
                <tr>
                    <td><?= $user['UserID'] ?></td>
                    <td><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></td>
                    <td><?= htmlspecialchars($user['Email']) ?></td>
                    <td><?= htmlspecialchars($user['DateOfBirth']) ?></td>
                    <td><?= htmlspecialchars($roleText) ?></td>
                    <td>
                        <form method="post" class="inline">
                            <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                            <input type="hidden" name="CurrentRoleID" value="<?= $user['RoleID'] ?>">
                            <button type="submit" name="changeRole" class="role-btn">
                                <?= htmlspecialchars($btnText) ?>
                            </button>
                        </form>
                    </td>
                    <td>
                        <?php if ($user['UserID'] != $_SESSION['UserID']): ?>
                            <form method="post" class="inline"
                                  onsubmit="return confirm('Er du sikker på at du vil slette denne brukeren?');">
                                <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                                <button type="submit" name="deleteUser" class="role-btn role-btn-danger">
                                    Slett
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
