
<?php
session_start();
session_regenerate_id(true);

function isLoggedIn() {
    return isset($_SESSION["user"]) && isset($_SESSION["userid"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

function preventAuthenticatedAccess() {
    if (isLoggedIn()) {
        header("Location: control.php");
        exit();
    }
}

if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    requireLogin();
}
?>
