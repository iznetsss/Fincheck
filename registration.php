<?php
$arrErrors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['registerButton']) && $_POST['registerButton'] == "Register" &&
!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm'])) {
    $error = FALSE; //Defines whether there were any errors found.
    //Check email.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = TRUE;
        $emailError = "Invalid email address.";
        array_push($arrErrors, $emailError);
    }
    //Check if user with this email already exists.
    if ($error == FALSE && file_exists("data/users.csv")) {
        $file = fopen("data/users.csv", "r");
        while (($user = fgetcsv($file, 1000, ";")) !== FALSE) {
            if ($user[0] == $_POST['email']) {
                $error = TRUE;
                $userExistsError = "User with this email is already registered.";
                array_push($arrErrors, $userExistsError);
                break;
            }
        }
    }
    //Check password length.
    if (strlen($_POST['password']) < 8 || strlen($_POST['passwordConfirm'] < 8)) {
        $error = TRUE;
        $passwordLengthError = "The password should contain at least 8 characters.";
        array_push($arrErrors, $passwordLengthError);
    }
    //Check password pattern.
    $passwordPattern = "/^[a-zA-Z0-9~`!@#$%^&*()_+=:;<,>.?'-]+$/";
    if (!preg_match($passwordPattern, $_POST['password']) || !preg_match($passwordPattern, $_POST['passwordConfirm'])) {
        $error = TRUE;
        $passwordRegxError = "The password can contain letters, numbers, and the following characters (~`!@#$%^&*()_+=:;<,>.?'-).";
        array_push($arrErrors, $passwordRegxError);
    }
    //Check if passwords are the same.
    if ($_POST['password'] != $_POST['passwordConfirm']) {
        $error = TRUE;
        $passwordMatch = "The passwords do not match.";
        array_push($arrErrors, $passwordMatch);
    }

    if ($error == FALSE) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $hashed_passwrod = password_hash($password, PASSWORD_DEFAULT);
        $arrUser = ["email" => $email, "password" => $hashed_passwrod];
       //Write to the file.
        if (!file_exists("data/users.csv")) {
            touch("data/users.csv");
            chmod("data/users.csv", 0777);
        }
        $file = fopen("data/users.csv", "a");
        fputcsv($file, $arrUser, ";");
        fclose($file);
        $confirmation = "Your account has been created.";
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
    <title>Register•FinCheck</title>
</head>

<body class="content-center">
    <header>
        <img src="img/logo.png" width="200px">
    </header>
    <div class="flex-container">
        <form method="post" id="registrationForm" name="registrationForm" action="registration.php">
            <h1 class="welcome-txt">Create an account</h1>
            <input class="form_field" type="email" id="email" name="email" placeholder="email" <?php if (isset($error) && $error == TRUE) {echo 'value="'.$_POST['email'].'"';} ?> required>
            <br>
            <input class="form_field" type="password" id="password" name="password" placeholder="password" required>
            <br>
            <input class="form_field" type="password" id="passwordConfirm" name="passwordConfirm" placeholder="confirm password" required>
            <br>
            <input type="submit" class="btn" id="registerButton" name="registerButton" value="Register">
        </form>
        <div class="flex-item">
            <span>Have an account?</span>
            <a href="index.php">Login</a>
        </div>
        <?php
            if (isset($error) && $error == TRUE) {
                foreach ($arrErrors as $err) {
                    printf("%s<br>", $err); 
                }
            }
            else if (isset($error) && $error == FALSE) {
                echo $confirmation;
            }
            ?>
    </div>


</body>

</html>