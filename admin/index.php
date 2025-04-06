<?php
$page = "index.php";
require_once("auth.php");
require_once("database.php");

$error = "";
$success = "";

if (isset($_POST["submit"])) {
    $username = filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING);
    $password = $_POST["pass"];

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND password = ? LIMIT 1");
        $stmt->execute([$username, md5($password)]);

        if ($row = $stmt->fetch()) {
            $_SESSION["user"] = $username;
            $_SESSION["userid"] = $row["id"];
            $success = "Login successful! Redirecting...";
            header("refresh:2;url=control.php");
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Administration Panel</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <input type="text" name="user" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" placeholder="Password" required>
            </div>
            <button type="submit" name="submit" class="btn-login">Log in</button>
        </form>
    </div>
</body>
</html>