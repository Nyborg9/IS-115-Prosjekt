<?php
session_start();

    if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;}

    if (!isset($_SESSION['RoleID'])) {
    header("Location: redirect.view.php");
    exit;
}

//Sjekker at bruker er Employer
elseif ($_SESSION['RoleID'] != 1) {
    header("Location: noAccess.view.php");
    exit;
}
    
    include "../inc/navbarController.inc.php";
    
include "../inc/listingForm.inc.php";
?>




<pre>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <input type="text" name="Title" placeholder="Tittel" required><br>
        <textarea type="text" name="ListingDescription" placeholder="Beskrivelse" required></textarea><br>
        <input type="text" name="Requirements" placeholder="Krav" required><br>
        <input type="text" name="TimePeriod" placeholder="Ansettelsesperiode"><br>
        <input type="number" name="HourScope" placeholder="Tidsomfang i timer" required><br>+
        <input type="submit" name="createListing" value="Fullfør"><br>
    </form>
</pre>


</body>
</html>

