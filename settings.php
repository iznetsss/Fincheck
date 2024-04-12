<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");

$arrErrors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['deleteButton']) && $_POST['deleteButton'] == "Delete account" &&
!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm']) && 
!empty($_POST['deleteConfirm']) && $_POST['deleteConfirm'] == "confirm") {
    $error = FALSE; //Defines whether there were any errors found.
    //Check email.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = TRUE;
        $emailError = "Invalid email address.";
        array_push($arrErrors, $emailError);
    }
    //Check if passwords are the same.
    if ($_POST['password'] != $_POST['passwordConfirm']) {
        $error = TRUE;
        $passwordMatchError = "The passwords do not match.";
        array_push($arrErrors, $passwordMatchError);
    }
    $email = $_POST['email'];
    if (!$error) {
        //Check if email exists.
        $query = ("SELECT EXISTS (SELECT 1 FROM users WHERE email = '$email') as email_exists;");
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_assoc($result);
        $email_exists = $row["email_exists"];
        if (!$email_exists) {
            $error = TRUE;
            $noEmailError = "User with this email is not registered.";
            array_push($arrErrors, $noEmailError);
        }
    }
    if (!$error) {
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
            $link -> query("DELETE FROM users WHERE username = '$username'");
            $link -> query("DELETE FROM incomes WHERE username = '$username'");
            $link -> query("DELETE FROM spendings WHERE username = '$username'");
            $link -> query("DELETE FROM categories WHERE username = '$username'");
            $link -> query("DELETE FROM recurring WHERE username = '$username'");
        }
        else {
            $error = TRUE;
            $incorrectPassError = "Incorrect password.";
            array_push($arrErrors, $incorrectPassError);
        }
    }
        
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="icon" href="img/icon.PNG">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="authors" content="kisevt, ikuzne">
    <title>Settings•FinCheck</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="content">
        <div class="flex-zone">
            <form method="post" id="settingsForm" name="settingsForm" action="settings.php">
                <select id="selectCurrency" name="selectCurrency">
                    <option>&#8364; EUR</option>
                    <option>&#36; USD</option>
                    <option>&#163; GBR</option>
                    <option>&#165; CNY</option>
                    <option>&#8377; INR</option>
                    <option>&#8361; KRW</option>
                    <option>&#165; JPY</option>
                </select>
                <label for="selectCurrency">Change currency</label>
                <br>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" value="checked" checked>
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" value="checked">
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" value="checked">
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" value="checked">
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" value="checked">
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>

            </form>     
        </div>
        <div class="flex-zone">
            <form method="post" id="deleteForm" name="deleteForm" action="settings.php">
                <h1 class="welcome-txt">Delete account:</h1>
                <input class="form_field" type="email" id="email" name="email" placeholder="email" <?php if (isset($error) && $error == TRUE) {echo 'value="'.$_POST['email'].'"';} ?> required>
                <br>
                <input class="form_field" type="password" id="password" name="password" placeholder="password" required>
                <br>
                <input class="form_field" type="password" id="passwordConfirm" name="passwordConfirm" placeholder="confirm password" required>
                <br>
                <input type="checkbox" id="deleteConfirm" name="deleteConfirm" value="confirm" required>
                <label for="deleteConfirm">I am sure I want to delete my account</label>
                <br>
                <input type="submit" class="btn" id="deleteButton" name="deleteButton" value="Delete account">
                <br>
                <?php
                if (isset($error) && $error) {
                    foreach ($arrErrors as $err) {
                        printf("%s<br>", $err); 
                    }
                }
                else if (isset($error) && !$error) {
                    echo "Done";
                }
                ?>
            </form>
        </div>
    </div>
    <footer>
        <span>Copyright © 2024 FinCheck OÜ. All rights reserved.</span>
    </footer>
</body>

</html>