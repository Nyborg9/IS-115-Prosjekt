<?php
session_start();

    if (!empty($_SESSION['logged_in'])) {
    header("Location: profile.view.php");
    exit;}
    
    include "../inc/navbarController.inc.php";

?>

</body>
<!DOCTYPE html>
<html>
    <head>
        <title>Registrer en bruker</title>
        <?php include "../inc/header/head.inc.php";?>
        <?php include "../inc/registrationForm.inc.php";?>
    </head>
    <body>
        <div class="centered-content">
            <pre>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    Fornavn: <input class="small-input" type="text" name="FirstName" placeholder="Fornavn" required><br>
                    Etternavn: <input class="small-input" type="text" name="LastName" placeholder="Etternavn" required><br>
                    E-post: <input class="small-input" type="email" name="Email" placeholder="E-post" required><br>
                    Fødselsdato: <input class="small-input" type="date" name="DateOfBirth" required><br>
                    Passord: <input class="small-input" type="password" name="Password" placeholder="Passord" required><br>
                    Bekreft passord: <input class="small-input" type="password" name="Confirm_Password" placeholder="Gjenta passord" required><br>
                    <input type="submit" name="registrer" value="Registrer"><br>
                    <input type="hidden" name="dtstart" value="<?php echo $dtstart->format("Y-m-d H:i:s.u"); ?>">
                </form>
            </pre>
        </div>
    </body>
</html>

