<?php

session_start();
if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;
}

include "../inc/navbarController.inc.php";
?>
<h1>Velkommen, <?php echo htmlspecialchars($_SESSION['FirstName']); ?>!</h1>
<p>Hyggelig å se deg igjen, <?php echo htmlspecialchars($_SESSION['FirstName'] . " " . $_SESSION['LastName']); ?>.</p>

</body>