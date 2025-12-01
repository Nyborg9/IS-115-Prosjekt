<?php

        // Kjøres når brukeren trykker "registrer"
        if(isset($_POST['createListing'])) {
            // Sjekk tid når skjemaet er sendt
            $dtstart = new DateTime($_POST['dtstart'], $dts);
            $dtslutt = new DateTime("now", $dts);


            // Henter ut dataen fra feltene
            $title = $_POST['Title'] ?? '';
            $userID = $_SESSION['UserID'] ?? '';
            $listingDescription = $_POST['ListingDescription'] ?? '';
            $requirements = $_POST['Requirements'] ?? '';
            $timePeriod = $_POST['TimePeriod'] ?? '';
            $hourScope = $_POST['HourScope'] ?? '';

                // Kobler til database for å legge til bruker
                require_once "../inc/database.inc.php";
                require_once "../database/addListing.db.php";

                // Legger til brukeren
                addListing($pdo, $title, $userID, $listingDescription, $requirements, $timePeriod, $hourScope);


                // Sender brukeren videre til login etter registrering
                header("Location: myListings.view.php");
                exit;
            }
        ?>