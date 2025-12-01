<?php
session_start();

    if (empty($_SESSION['logged_in'])) {
    header("Location: redirect.view.php");
    exit;}
    
    include "../inc/navbarController.inc.php";

$pageTitle = "Lag utlysning";
include "../inc/listingForm.inc.php";
?>




<pre>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <input type="text" name="Title" placeholder="Tittel" required><br>
        <textarea type="text" name="ListingDescription" placeholder="Beskrivelse" required></textarea><br>
        <input type="text" name="Requirements" placeholder="Kravene" required><br>
        <input type="text" name="TimePeriod" placeholder="Ansettelsesperiode"><br>
        <input type="number" name="HourScope" placeholder="Tidsomfang i timer" required><br>+
        <input type="submit" name="createListing" value="Fullfør"><br>
        
        <!-- En bot sjekk -->
        <input type="hidden" name="dtstart" value="<?php echo $dtstart->format("Y-m-d H:i:s.u"); ?>">
    </form>
</pre>


</body>
</html>

