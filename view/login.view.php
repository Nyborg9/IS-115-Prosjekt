<?php
session_start();

if (!empty($_SESSION['logged_in'])) {
    header("Location: profile.view.php");
    exit;
}
require_once("../inc/database.inc.php");
require_once("../database/loginAttempt.db.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include "../inc/navbarController.inc.php";?>
    </head>
    <body>
    <?php
    include "../inc/loginForm.inc.php";
    ?>


        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div>
                <input type="text" name="Email" placeholder="Brukernavn/Epost" />
                <input type="password" name="Password" placeholder="Passord" />
                <input type="submit" name="logginn" value="Logg inn">
            </div>
        </form>
    </body>
</html>


