
<?php
declare(strict_types=1);

session_start();
session_regenerate_id(true);

class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION["user"]) && isset($_SESSION["userid"]);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    }

    public static function preventAuthenticatedAccess(): void {
        if (self::isLoggedIn()) {
            header("Location: control.php");
            exit();
        }
    }
}

if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    Auth::requireLogin();
}
