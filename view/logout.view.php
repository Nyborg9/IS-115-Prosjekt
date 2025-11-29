<?php
#For å slutte hele sessionen
session_start();
session_unset();
session_destroy();
include "../inc/navbarController.inc.php";
?>

<h1>Du er nå logget ut</h1>
<a href="login.view.php" class="btn">Logg inn</a>
<a href="register.view.php" class="btn">Registrer deg</a>