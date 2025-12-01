<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

if (!isset($_SESSION['RoleID'])) {
    header("Location: redirect.view.php");
    exit;
}

//Sjekker at bruker er standard bruker
elseif ($_SESSION['RoleID'] != 2) {
    header("Location: noAccess.view.php");
    exit;
}

include "../inc/navbarController.inc.php";

// Inkluderer logikken for søknadsskjemaet (bot-sjekk, insert, $listing, $error, $dtstart)
include "../inc/applicationForm.inc.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Søk på: <?= htmlspecialchars($listing['Title']); ?></title>
</head>
<body>
<div class="centered-content">
    <h1>Søk på: <?= htmlspecialchars($listing['Title']); ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?listingID=' . urlencode($listingID)); ?>">

        <label for="ApplicationText">Søknadstekst</label><br>
        <textarea name="ApplicationText" id="ApplicationText" rows="10" cols="60" required></textarea><br><br>

        <input type="submit" name="createApplication" value="Send søknad"><br>

        <!-- Bot-sjekk -->
        <input type="hidden" name="dtstart" value="<?php echo $dtstart->format("Y-m-d H:i:s.u"); ?>">
    </form>

    <p><a href="listings.view.php">← Tilbake til stillinger</a></p>
</div>
</body>
</html>
