<?php
#For å slutte hele sessionen
session_start();
session_unset();
session_destroy();
include "../inc/navbarController.inc.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include "../inc/header/head.inc.php"?>
    </head>
    <body> 
        <div class="centered-content">      
            <h1>Du er nå logget ut</h1>
            <a href="login.view.php" class="btn bordered-content">Logg inn</a>
            <a href="register.view.php" class="btn bordered-content">Registrer deg</a>
        </div>
    </body>
</html>