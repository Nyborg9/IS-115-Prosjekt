<?php
session_start();
require_once "../inc/database.inc.php";

// Må være innlogget
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

// Må være arbeidsgiver (RoleID = 1)
if (!isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
    header("Location: noAccess.view.php");
    exit;
}

$userID = $_SESSION['UserID'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteListing'])) {
    $listingIDToDelete = isset($_POST['ListingID']) ? $_POST['ListingID'] : 0;

    if ($listingIDToDelete > 0) {

        // (Valgfritt, men lurt): Slett søknader knyttet til denne stillingen først,
        // hvis du ikke har ON DELETE CASCADE på foreign keys.
        $sqlDeleteApplications = "
            DELETE FROM applications
            WHERE ListingID = :listingID
        ";
        $stmtDelApps = $pdo->prepare($sqlDeleteApplications);
        $stmtDelApps->bindParam(':listingID', $listingIDToDelete, PDO::PARAM_INT);
        $stmtDelApps->execute();

        // Slett selve stillingen, men bare hvis den tilhører innlogget arbeidsgiver
        $sqlDeleteListing = "
            DELETE FROM listings
            WHERE ListingID = :listingID
              AND UserID = :userID
        ";
        $stmtDelListing = $pdo->prepare($sqlDeleteListing);
        $stmtDelListing->bindParam(':listingID', $listingIDToDelete, PDO::PARAM_INT);
        $stmtDelListing->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmtDelListing->execute();
    }

    // Redirect for å unngå resubmission ved refresh
    header("Location: myListings.view.php");
    exit;
}

// Hent alle listings som tilhører denne arbeidsgiveren
$sql = "
    SELECT ListingID, Title, created_at
    FROM listings
    WHERE UserID = :userID
    ORDER BY created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Mine stillinger</title>
    <?php include "../inc/navbarController.inc.php"; ?>
    <style>
        .centered-content {
            max-width: 800px;
            margin: 20px auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        a.btn {
            padding: 4px 8px;
            border: 1px solid #333;
            text-decoration: none;
        }
        form.inline {
            display: inline;
        }
        button.btn {
            padding: 4px 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="centered-content">
    <h1>Mine stillingsutlysninger</h1>

    <?php if (count($listings) == 0): ?>
        <p>Du har ikke lagt ut noen stillinger enda.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Tittel</th>
                <th>Opprettet</th>
                <th>Søknader</th>
                <th>Rediger</th>
                <th>Slett</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($listings as $listing): ?>
                <tr>
                    <td><?= htmlspecialchars($listing['Title']) ?></td>
                    <td><?= htmlspecialchars($listing['created_at']) ?></td>
                    <td>
                        <a class="btn"
                           href="listingApplications.view.php?listingID=<?= $listing['ListingID'] ?>">
                            Se søknader
                        </a>
                    </td>
                    <td>
                        <!-- Rediger: lenker til en egen side med skjema -->
                        <a class="btn"
                           href="editListing.view.php?listingID=<?= $listing['ListingID'] ?>">
                            Rediger
                        </a>
                    </td>
                    <td>
                        <!-- Slett stilling: POST-skjema med bekreftelse -->
                        <form method="post" class="inline"
                              onsubmit="return confirm('Er du sikker på at du vil slette denne stillingen og alle tilhørende søknader?');">
                            <input type="hidden" name="ListingID" value="<?= (int)$listing['ListingID'] ?>">
                            <button type="submit" name="deleteListing" class="btn"
                                    style="background:#e74c3c;color:white;">
                                Slett
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
