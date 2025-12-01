        <?php
            // Prøvd å logge inn?
            if(isset($_POST['logginn'])) {
                // Kobler til database


                $email = $_POST['Email'] ?? '';
                $password = $_POST['Password'] ?? '';


                // Lager SQL-spørringen struktur
                $sql = "SELECT UserID, FirstName, LastName, RoleID, Password_hash as password_hash, FailedAttempts, LastFailedAttempt, LockedUntil
                        FROM users WHERE Email = :Email LIMIT 1";


                // Forbereder spørringen
                $sp = $pdo->prepare($sql);
                $sp->execute([':Email' => $email]);


                // Henter dataen
                $medlem = $sp->fetch(PDO::FETCH_OBJ);



                #Om medlem er tomt så gir den feilmelding
                if(!$medlem) {
                    echo "<p style='color:red;'>Brukernavn/epost og/eller passord er ikke riktig</p>";
                    exit;
                } 
                #Om LastFailedAttemt ikke er tom så sjekker den opp imot nåværende tidspunk for å se om det har gått en time
                if(!empty($medlem->LastFailedAttempt)) {
                    $tz = new DateTimeZone("Europe/Oslo");
                    $now = new DateTime("now", $tz);
                    $lastFail = new DateTime($medlem->LastFailedAttempt, $tz);

                    $sekunderSiden = $now->getTimestamp() - $lastFail->getTimestamp();

                    if($sekunderSiden >= 60*60) {
                        nullstillFeilforsøk($pdo, $medlem->UserID);
                        $medlem->FailedAttempts = 0;
                        $medlem->LockedUntil = null;
                        $medlem->LastFailedAttempt = null;
                    }
                }
                #Om funksjonen erBrukerLåst slår ut så får mann ikke logget inn på siden.
                if(erBrukerLåst($medlem)) {
                    echo "<p style='color:red;'>For mange feilede forsøk. Prøv igjen om 1 time</p>";
                    exit;
                }

                #Ser om passordet er det samme som brukeren
                if(password_verify($password, $medlem->password_hash)) {

                    nullstillFeilforsøk($pdo, $medlem->UserID);

                    $_SESSION['logged_in'] = true;
                    $_SESSION['UserID'] = $medlem->UserID;
                    $_SESSION['FirstName'] = $medlem->FirstName;
                    $_SESSION['LastName'] = $medlem->LastName;
                    $_SESSION['RoleID'] = $medlem->RoleID;
                        
                    header("Location: listings.view.php");
                    exit();
                } else {
                    #Om passordet ikke er likt, så blir du enten utestengt (om du har mer en 3 forsøk), ellers så får du +1 i attempts
                    $attempts = registrerFeiletInnlogging($pdo, $medlem->UserID);
                    if ($attempts >= 3) {
                        echo "<p style='color:red;'>For mange feilede forsøk. Prøv igjen om 1 time.</p>";
                        exit;
                    } else {
                        echo "<p style='color:red;'>Brukernavn/epost og/eller passord er ikke riktig. Forsøk: $attempts av 3.</p>";
                }
                }
            }
        ?>