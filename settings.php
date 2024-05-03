<?php
require ("includes/session_check.php");
require ("includes/sql_connect.php");

// checking includeRecurring and carryOver values and set checkboxes 
$query = "SELECT includeRecurring, carryOver FROM users WHERE username = ?";
$stmt = $link->prepare($query); 

if ($stmt === false) {
    die('MySQL prepare error: ' . $link->error);
}
$stmt->bind_param('s', $username); 
$stmt->execute();
$stmt->bind_result($includeRecurring, $carryOver);
if ($stmt->fetch()) {
    $recurringChecked = $includeRecurring ? "checked" : "";
    $carryOverChecked = $carryOver ? "checked" : "";
} else {
    //If smth went totally wrong...
    $recurringChecked = "";
    $carryOverChecked = "";
}
$stmt->close();

// sending into DB includeRecurring and carryOver values
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['updateSettings']) && $_POST['updateSettings'] == "Update Settings")
{
    $newIncludeRecurring = isset($_POST['includeRecurring']) ? 1 : 0; 
    $newCarryOver = isset($_POST['carryOver']) ? 1 : 0;

    $updateQuery = "UPDATE users SET includeRecurring = ?, carryOver = ? WHERE username = ?";
    $updateStmt = $link->prepare($updateQuery);
    if ($updateStmt === false) {
        die('MySQL prepare error: ' . $link->error);
    }

    $updateStmt->bind_param('iis', $newIncludeRecurring, $newCarryOver, $username);
    $updateStmt->execute();
    $updateStmt->close();

    $recurringChecked = $newIncludeRecurring ? "checked" : "";
    $carryOverChecked = $newCarryOver ? "checked" : "";

    $updatedSettings = "<br><span>Settings updated successfully!</span>";
}




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
            session_destroy();
            header("Location: index.php");
            exit;
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
            <h1>Username: <?php echo $username ?></h1>
        </div>
        <div class="flex-zone">
            <form method="post" id="settingsForm" name="settingsForm" action="settings.php">
                <h1>Settings:</h1>
                <input type="checkbox" id="includeRecurring" name="includeRecurring" <?php echo $recurringChecked; ?>>
                <label for="includeRecurring">Include recurring payments in spendings graph</label>
                <br>
                <input type="checkbox" id="carryOver" name="carryOver" <?php echo $carryOverChecked; ?>>
                <label for="includeRecurring">Carry over</label>
                <br>
                <input type="submit" class="btn" id="updateSettings" name="updateSettings" value="Update Settings">
                <?php
                if(isset($updatedSettings))
                {
                    echo $updatedSettings;
                }
                ?>
            </form>     
        </div>
        <div class="flex-zone">
            <form method="post" id="deleteForm" name="deleteForm" action="settings.php">
                <h1>Delete account:</h1>
                <input class="form-field" type="email" id="email" name="email" placeholder="email" <?php if (isset($error) && $error == TRUE) {echo 'value="'.$_POST['email'].'"';} ?> required>
                <br>
                <input class="form-field" type="password" id="password" name="password" placeholder="password" required>
                <br>
                <input class="form-field" type="password" id="passwordConfirm" name="passwordConfirm" placeholder="confirm password" required>
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