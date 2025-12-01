<?php
session_start();

    if (!empty($_SESSION['logged_in'])) {
    header("Location: profile.view.php");
    exit;}
    
    include "../inc/navbarController.inc.php";

$pageTitle = "Registrering";
include "../inc/registrationForm.inc.php";
?>



<pre>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Fornavn: <input type="text" name="FirstName" placeholder="Fornavn" required><br>
        Etternavn: <input type="text" name="LastName" placeholder="Etternavn" required><br>
        E-post: <input type="email" name="Email" placeholder="E-post" required><br>
        Fødselsdato: <input type="date" name="DateOfBirth" required><br>
        Passord: <input type="password" name="Password" placeholder="Passord" required><br>
        Bekreft passord: <input type="password" name="Confirm_Password" placeholder="Gjenta passord" required><br>
        <input type="submit" name="registrer" value="Registrer"><br>
    </form>
</pre>


</body>
</html>

