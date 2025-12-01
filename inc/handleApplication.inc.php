<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../inc/database.inc.php";


session_start();
require_once "../inc/database.inc.php";

// PHPMailer
require_once "../inc/PHPMailer/src/PHPMailer.php";
require_once "../inc/PHPMailer/src/SMTP.php";
require_once "../inc/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ---- 1. SIKKERHET / INPUT ----------------------------------

// Må være innlogget og arbeidsgiver
if (empty($_SESSION['logged_in']) || !isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
    die("Ingen tilgang.");
}

$userID = $_SESSION['UserID'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Ugyldig forespørsel.");
}

$applicationID = isset($_POST['ApplicationID']) ? $_POST['ApplicationID'] : 0;
$listingID     = isset($_POST['ListingID']) ? $_POST['ListingID'] : 0;
$action        = $_POST['applicationAction'] ?? '';

if ($applicationID <= 0 || $listingID <= 0 || ($action !== 'Godta' && $action !== 'Avvis')) {
    die("Mangler data.");
}

// Midlertidig test:
if (!isset($_POST['applicationAction'])) {
    die("handleApplication.inc.php ble kalt, men applicationAction mangler.");
}
// die("handleApplication.inc.php ble kalt OK.");  // kan brukes hvis du vil teste

// ---- 2. HENT DATA FRA DB ----------------------------------

// Hent søknad + bruker + tittel, og sjekk at stillingen tilhører innlogget arbeidsgiver
$sql = "
    SELECT 
        a.ApplicationID,
        a.ApplicationStatus,
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

if (!$app) {
    die("Fant ikke søknaden, eller du har ikke tilgang.");
}

// Sett ny status og standardtekst
if ($action === 'Godta') {
    $newStatus = 2;
    $messageText = "Du har gått videre i vår prosess, og vi vil ta kontakt med deg fortløpende for å avtale intervju.";
} else { // Avvis
    $newStatus = 3;
    $messageText = "Du ble dessverre ikke tatt med videre i vår prosess.";
}

// Oppdater status i databasen
$update = $pdo->prepare("
    UPDATE applications
    SET ApplicationStatus = :status
    WHERE ApplicationID = :id
");
$update->execute([
    'status' => $newStatus,
    'id'     => $applicationID
]);

// ---- 3. SEND E-POST MED PHPMAILER (pensum-stil) ----------

$mail = new PHPMailer(true);

// VIKTIG: debugging på
$mail->SMTPDebug = 0;              // viser hva som skjer
// $mail->Debugoutput = 'html';    // valgfritt, gjør output penere

try {
    // Serveroppsett (slik du allerede har det)
    $mail->isSMTP();
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;

    $mail->Username   = 'katarinaegebakken@gmail.com';
    $mail->Password   = 'xxztpmpwgdsbdhbn';

    $mail->setFrom('katarinaegebakken@gmail.com', 'Bit by Bit');
    $mail->addAddress($app['Email'], $app['FirstName'] . ' ' . $app['LastName']);

    $mail->isHTML(true);
    $mail->Subject = "Svar på søknad: " . $app['Title'];

    $mail->Body = "
        Hei {$app['FirstName']} {$app['LastName']}<br><br>
        {$messageText}<br><br>
        Stillingen: <b>{$app['Title']}</b><br><br>
        Vennlig hilsen,<br>
        Bit by Bit
    ";
    $mail->AltBody =
        "Hei {$app['FirstName']} {$app['LastName']}\n\n" .
        $messageText . "\n\n" .
        "Stillingen: {$app['Title']}\n\n" .
        "Vennlig hilsen,\n" .
        "Bit by Bit";

    if ($mail->send()) {
        echo "<p>E-posten ble sendt uten feil fra PHPMailer.</p>";
    } else {
        echo "<p>PHPMailer->send() returnerte false.</p>";
    }

} catch (Exception $e) {
    echo "<p><strong>E-posten kunne ikke sendes.</strong><br>";
    echo "PHPMailer-feil: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
}

// LEGG *IKKE* redirect her nå – vi vil se outputen!
// header("Location: ../view/listingApplications.view.php?listingID=" . $listingID);
// exit;
