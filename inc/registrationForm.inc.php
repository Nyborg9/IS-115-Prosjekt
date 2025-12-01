<?php


        // Kjøres når brukeren trykker "registrer"
        if(isset($_POST['registrer'])) {

            // Henter ut dataen fra feltene
            $firstName = $_POST['FirstName'] ?? '';
            $lastName = $_POST['LastName'] ?? '';
            $email = $_POST['Email'] ?? '';
            $birthdate = $_POST['DateOfBirth'] ?? '';
            $password = $_POST['Password'] ?? '';
            $confirmPassword = $_POST['Confirm_Password'] ?? '';
            $messages = [];

            if ($birthdate == '') {
            $messages[] = "Du må oppgi fødselsdato.";
            } else {
                // Lager ett DateTime objekt med birthdate strengen.
                $birthdayDate = DateTime::createFromFormat('Y-m-d', $birthdate);
                $today = new DateTime('today');

                if (!$birthdayDate || $birthdayDate->format('Y-m-d') !== $birthdate) {
                    $messages[] = "Ugyldig fødselsdato.";
                } elseif ($birthdayDate > $today) {
                    $messages[] = "Fødselsdato kan ikke være i fremtiden.";
                } else {
                    $age = $birthdayDate->diff($today)->y;
                if ($age < 18) {
                    $messages[] = "Du må være minst 18 år for å registrere deg. (Du er $age år)";
                    }
                }
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $messages[] = "Ugyldig epost brukt";
            }

            require_once "../inc/database.inc.php";

                $sql = "
                    SELECT UserID
                    FROM users
                    WHERE Email = :email
                    LIMIT 1
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->fetch()) {
                    $messages[] = "Denne epostadressen er allerede i bruk.";
                }
            
            
            if(strlen($firstName) <= 2){
                 $messages[] = "Fornavnet må være mer en bokstav";
            }

            if(strlen($lastName) <= 2){
                 $messages[] = "Etternavnet må være mer en bokstav";
            }

                if (strlen($password) < 9){
             $messages[] = "Passordet må være minst 9 tegn";
            }

            #Skjekker om det ikke er en stor bokstav ved bruk av preg_match, og legger inn en feilmelding i listen dersom det mangles 
            if(!preg_match("/[A-ZÆØÅ]/", $password)){
            $messages[] = "Passordet må ha minst en stor bokstav";
            }
            #Ser igjennom listen med spesialtegn, om det ikke finnes ett spesialtegn i passordet så legges feilmeldingen inn i lista
            if(!preg_match("/[!@#$%^&*()\-_=+{};:,<.>]/", $password)){
            $messages[] = "Passordet må ha minst ett spesialtegn";
            }
            #Bruker preg_match_all for å se hvor mange ganger ett tall blir matchet ifra passordet, om det er under 2 så blir feilmeldingen lagt inn i lista
            if(!preg_match_all("/[0-9]/", $password) >= 2){
            $messages[] = "Passordet må ha minst to tall";
            }
            #Ser om passordene er like eller ikke
            if($password != $confirmPassword){
                $messages[] =  "Passordene må være like";
            }

            if(empty($messages)){
                echo "<p style='color:green;'>Passordene stemmer!</p>";
                // Lager hash av passordet, for sikker lagring (her med salt via PASSWORD_DEFAULT)
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);


                // Kobler til database for å legge til bruker
                require_once "../inc/database.inc.php";
                require_once "../database/addUser.db.php";


                // Legger til brukeren
                addUser($pdo, $firstName, $lastName, $email, $birthdate, $passwordHash);


                // Sender brukeren videre til login etter registrering
                header("Location: login.view.php");
                exit;
            } else {
            foreach($messages as $message){
                echo "$message <br>";
            }
            exit;
        }
        }
        ?>