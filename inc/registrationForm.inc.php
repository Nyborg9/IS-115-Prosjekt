<?php
        // Tidssone
        $dts = new DateTimeZone("Europe/Oslo");
       
        // Tid ved lasting av skjema
        $dtstart = new DateTime("now", $dts);


        // Kjøres når brukeren trykker "registrer"
        if(isset($_POST['registrer'])) {
            // Sjekk tid når skjemaet er sendt
            $dtstart = new DateTime($_POST['dtstart'], $dts);
            $dtslutt = new DateTime("now", $dts);


            // Henter ut dataen fra feltene
            $fornavn = $_POST['FirstName'] ?? '';
            $etternavn = $_POST['LastName'] ?? '';
            $epost = $_POST['Email'] ?? '';
            $fødselsdato = $_POST['DateOfBirth'] ?? '';
            $passord = $_POST['Password'] ?? '';
            $bekreftPassord = $_POST['Confirm_Password'] ?? '';


            // Sjekker at passordene er like
            if($passord === $bekreftPassord) {
                echo "<p style='color:green;'>Passordene stemmer!</p>";
                // Lager hash av passordet, for sikker lagring (her med salt via PASSWORD_DEFAULT)
                $passordHash = password_hash($passord, PASSWORD_DEFAULT);


                // Kobler til database for å legge til bruker
                require_once "../inc/database.inc.php";
                require_once "../database/addUser.db.php";


                // Legger til brukeren
                addUser($pdo, $fornavn, $etternavn, $epost, $fødselsdato, $passordHash);


                // Sender brukeren videre til login etter registrering
                header("Location: login.view.php");
                exit;
            } else {
            echo "<p style='color:red';'>Passordene er ikke like</p>";


        }
        }
        ?>