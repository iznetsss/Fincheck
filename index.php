<?php

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
        <form class="login_form" action="dashboard.php">
            <h1 class="welcome-txt">Welcome to FinCheck</h1>
            <input class="form_field" type="text" placeholder="email" required>
            <br>
            <input class="form_field" type="password" placeholder="password" required>
            <br>
            <input type="submit" class="btn" value="Login">
        </form>
        <div class="flex-item">
            <span>Don't have an account?</span>
            <a href="registration.php">Sign Up</a>
        </div>
    </div>


</body>

</html>