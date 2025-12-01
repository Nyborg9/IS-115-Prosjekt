<?php session_start();?>
<?php include "../inc/header/head.inc.php";

if(isset($_SESSION['RoleID'])){
if ($_SESSION['RoleID'] == 3 || $_SESSION['RoleID'] == 1) {
    header("Location: noAccess.view.php");
    exit;
}}

?>



<!DOCTYPE html>
<html>
    <head>
        
        <?php 
            require_once "../inc/database.inc.php";

  //Henter alle listings, med navn på eier fra users-tabellen, sortert etter nyeste først
        $sql = "
            SELECT
                l.ListingID,
                l.Title,
                l.ListingDescription,
                l.Requirements,
                l.TimePeriod,
                l.HourScope,
                l.created_at,
                u.FirstName,
                u.LastName
            FROM listings l
            JOIN users u ON l.UserID = u.UserID
            ORDER BY l.created_at DESC
        ";


        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $listings = $stmt->fetchAll();
            include "../inc/navbarController.inc.php";
        ?>
    </head>
    <body>
        <div class="centered-content">
            <h1>Ledige stillinger</h1>


            <?php
                if (count($listings) === 0) {
                    echo '<p>Ingen annonser enda.</p>';
                } else {
                    foreach ($listings as $listing) {
                        echo '<div class="utlysning bordered-content utlysningsdiv">';
                        echo '<h2>' . htmlspecialchars($listing['Title']) . '</h2>';
                        echo '<div class="meta"> Lagt ut av: ' . htmlspecialchars($listing['FirstName'] . ' ' . $listing['LastName']) .
                            ' • ' . htmlspecialchars($listing['created_at']) .
                            '</div>';
                        echo '<p>' . nl2br(htmlspecialchars($listing['ListingDescription'])) . '</p>';

                        if (!empty($listing['Requirements'])) {
                            echo '<p><span class="label">Krav:</span><br>' .
                                nl2br(htmlspecialchars($listing['Requirements'])) .
                                '</p>';
                        }  


                        if (!empty($listing['TimePeriod'])) {
                            echo '<p><span class="label">Tidsperiode:</span> ' .
                                htmlspecialchars($listing['TimePeriod']) .
                                '</p>';
                        }


                        if (!empty($listing['HourScope'])) {
                            echo '<p><span class="label">Omfang (timer):</span> ' .
                                htmlspecialchars($listing['HourScope']) .
                                '</p>';
                        }

                        echo '<a class="btn right-place bordered-content" href="createApplication.view.php?listingID=' . $listing['ListingID'] . '">
                        Søk på stillingen
                        </a>';



                        echo '</div>';
                    }
                }
            ?>

    </body>
</html>

