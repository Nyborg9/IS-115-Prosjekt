<?php
if (!empty($_SESSION['logged_in'])) {

    if ($_SESSION['RoleID'] == 3) {
        if (file_exists("inc/header/adminHeader.inc.php")) {
            include "inc/header/adminHeader.inc.php";
        } else {
            include "../inc/header/adminHeader.inc.php";
        }

    }elseif ($_SESSION['RoleID'] == 1) {
        if (file_exists("inc/header/empoyerHeader.inc.php")) {
            include "inc/header/employerHeader.inc.php";
        } else {
            include "../inc/header/employerHeader.inc.php";
        }

    } else {
        // Vanlig bruker
        if (file_exists("inc/header/userHeader.inc.php")) {
            include "inc/header/userHeader.inc.php";
        } else {
            include "../inc/header/userHeader.inc.php";
        }
    }

} else {
    // Gjest
    if (file_exists("inc/header/guestHeader.inc.php")) {
        include "inc/header/guestHeader.inc.php";
    } else {
        include "../inc/header/guestHeader.inc.php";
    }
}
?>
