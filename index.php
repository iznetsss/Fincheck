<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit; 
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['loginButton']) && $_POST['loginButton'] == "Login" &&
!empty($_POST['email']) && !empty($_POST['password'])) {

    include_once("includes/sql_connect.php");
    //Check email.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }
    $email = $_POST['email'];
    //Check if email exists.
    $query = ("SELECT EXISTS (SELECT 1 FROM users WHERE email = '$email') as email_exists;");
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    $email_exists = $row["email_exists"];
    if (!$email_exists) {
        $error = "User with this email is not registered.";
    }
    else {
        //Check if the password is correct.
        $query = ("SELECT pass FROM users WHERE email = '$email'");
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($result);
        $pass = $row["pass"];
        if (password_verify($_POST['password'], $pass)) 
        {
            $query = ("SELECT username FROM users WHERE email = '$email'");
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_assoc($result);
            $username = $row['username'];
            session_start();
            $_SESSION['user_id'] = $username;
            header("Location: dashboard.php");
            exit();
        }
        else {
            $error = "Incorrect password.";
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