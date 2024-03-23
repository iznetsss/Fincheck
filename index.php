<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['loginButton']) && $_POST['loginButton'] == "Login" &&
!empty($_POST['email']) && !empty($_POST['password'])) {
    //Check email.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }
    if (!file_exists("data/users.csv")) {
        $error = "User with this email is not registered.";
    }
    if (!isset($error) && file_exists("data/users.csv")) {
        $file = fopen("data/users.csv", "r");
        $found = FALSE;
        while (($user = fgetcsv($file, 1000, ";")) !== FALSE) {
            if ($user[0] == $_POST['email']) {
                if (password_verify($_POST['password'], $user[1])) {
                    fclose($file);
                    header("Location: dashboard.php");
                    exit();
                }
                else {
                    fclose($file);
                    $found = TRUE;
                    $error = "Incorrect password.";
                    break;
                }
            }
        }
        if ($found == FALSE) {
            fclose($file);
            $error = "User with this email is not registered.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="styles/log_reg.css">
    <link rel="icon" href="img/icon.PNG">
    <meta charset="utf-8">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Login•FinCheck</title>
</head>

<body class="content-center">
    <header>
        <img src="img/logo.png" width="200px">
    </header>
    <div class="flex-container">
        <form method="post" action="index.php" id="loginForm" name="loginForm">
            <h1 class="welcome-txt">Welcome to FinCheck</h1>
            <input class="form_field" type="email" id="email" name="email" placeholder="email" <?php if (isset($error)) {echo 'value="'.$_POST['email'].'"';} ?> required>
            <br>
            <input class="form_field" type="password" id="password" name="password" placeholder="password" required>
            <br>
            <input type="submit" class="btn" id="loginButton" name="loginButton" value="Login">
        </form>
        <div class="flex-item">
            <span>Don't have an account?</span>
            <a href="registration.php">Sign Up</a>
        </div>
        <?php
        if (isset($error)) {
            echo $error;
        }
        ?>
    </div>


</body>

</html>