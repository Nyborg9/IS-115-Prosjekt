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

// Inkluderer logikken for søknadsskjemaet
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

    <form method="post"
      action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?listingID=' . urlencode($listingID)); ?>"
      enctype="multipart/form-data">


        <label for="ApplicationText">Søknadstekst</label><br>
        <textarea name="ApplicationText" id="ApplicationText" rows="10" cols="60" required></textarea><br><br>

        <label for="CvFile">Last opp CV (PDF)</label><br>
        <input type="file" name="CvFile" id="CvFile" accept="application/pdf" required><br><br>
        

        <input type="submit" name="createApplication" value="Send søknad"><br>
    </form>

    <p><a href="listings.view.php"> Tilbake til stillinger</a></p>
</div>
</body>
</html>
