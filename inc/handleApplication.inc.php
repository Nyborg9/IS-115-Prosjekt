<?php
session_start();
require_once "../inc/database.inc.php";

// PHPMailer
require_once "../inc/PHPMailer/src/PHPMailer.php";
require_once "../inc/PHPMailer/src/SMTP.php";
require_once "../inc/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sjekk tilgang
if (empty($_SESSION['logged_in']) || $_SESSION['RoleID'] != 1) {
    die("Ingen tilgang.");
}

$userID = $_SESSION['UserID'];

// Sjekk POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Ugyldig forespørsel.");

$applicationID = (int)($_POST['ApplicationID'] ?? 0);
$listingID     = (int)($_POST['ListingID'] ?? 0);
$action        = $_POST['applicationAction'] ?? '';

if ($applicationID < 1 || $listingID < 1 || !in_array($action, ['Godta','Avvis'])) {
    die("Mangler data.");
}

// Hent søknad
$sql = "
    SELECT 
        a.ApplicationID,
        u.Email,
        u.FirstName,
        u.LastName,
        l.Title
    FROM applications a
    JOIN users u    ON a.UserID    = u.UserID
    JOIN listings l ON a.ListingID = l.ListingID
    WHERE a.ApplicationID = :appID
      AND a.ListingID     = :listingID
      AND l.UserID        = :employerID
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'appID'      => $applicationID,
    'listingID'  => $listingID,
    'employerID' => $userID
]);

$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) die("Ingen tilgang.");

// Bestem ny status + tekst
if ($action === 'Godta') {
    $newStatus = 2;
    $messageText = "Du har gått videre i vår prosess, og vi vil ta kontakt med deg fortløpende for å avtale intervju.";
} else {
    $newStatus = 3;
    $messageText = "Du ble dessverre ikke tatt med videre i vår prosess.";
}

// Oppdater status
$update = $pdo->prepare("UPDATE applications SET ApplicationStatus = :s WHERE ApplicationID = :id");
$update->execute(['s' => $newStatus, 'id' => $applicationID]);

// Send epost
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;

    $mail->Username   = "katarinaegebakken@gmail.com";
    $mail->Password   = "xxztpmpwgdsbdhbn";

    $mail->setFrom("katarinaegebakken@gmail.com", "Bit by Bit");
    $mail->addAddress($app['Email'], $app['FirstName']." ".$app['LastName']);

    $mail->CharSet = "UTF-8";
    $mail->isHTML(true);

    $mail->Subject = "Svar på søknad: ".$app['Title'];
    $mail->Body = "
        Hei {$app['FirstName']} {$app['LastName']}<br><br>
        {$messageText}<br><br>
        Stillingen: <b>{$app['Title']}</b><br><br>
        Vennlig hilsen,<br>
        Fakultetet
    ";

    $mail->send();

} catch (Exception $e) {
    // FEIL Ikke stopp systemet
    error_log("EPOST-FEIL: ".$mail->ErrorInfo);
}

// Redirect
header("Location: ../view/listingApplications.view.php?listingID=".$listingID);
exit;