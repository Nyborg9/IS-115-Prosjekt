<?php 
session_start();
include "../inc/navbarController.inc.php";?>

<!DOCTYPE html>
<html>
    <head>
        <?php include "../inc/header/head.inc.php"?>
    </head>
    <body> 
        <div class="centered-content">      
            <h1>Du må være logget inn for å se denne siden</h1>
            <a href="login.view.php" class="btn bordered-content">Logg inn</a>
            <a href="register.view.php" class="btn bordered-content">Registrer deg</a>
        </div>
    </body>
</html>