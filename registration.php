<?php
$arrErrors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST' && 
isset($_POST['registerButton']) && $_POST['registerButton'] == "Register" &&
!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm']) && !empty($_POST['username'])) {
    $error = FALSE; //Defines whether there were any errors found.
    //Check email.
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = TRUE;
        $emailError = "Invalid email address.";
        array_push($arrErrors, $emailError);
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

    //Check username
    if(!preg_match('/^[a-zA-Z0-9_-]{1,12}$/', $_POST['username']))
    {
        $error = TRUE;
        $usernameError = "You can use letters, numbers, underscore, dash and max length is 12.";
        array_push($arrErrors, $usernameError);
    }

    //SQL STUFF
    if ($error == FALSE) {
        //Login into db
        $server = "127.0.1.1"; // anysql.itcollege.ee
        $user = "root"; // ICS0008_WT_4
        $password = "1234"; // 14b9a69f0e86
        $database = "ICS0008_4";
        $mysqli = new mysqli($server, $user, $password, $database);
        if ($mysqli->connect_error) {
            echo"<script>alert('Connection to DB failed');</script>";
            die;
        }
        else 
        {
            // Reg variables
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            
            // Check if the user with the given email or username already exists
            $query = "SELECT * FROM users WHERE email = ? OR username = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // User with the provided email or username already exists
                $error = TRUE;
                $userExistsError = "<p>User with this email or username already exists.</p>";
                array_push($arrErrors, $userExistsError);
            } else {
                // User does not exist, add the user to the database
                $query = "INSERT INTO users (email, username, pass) VALUES (?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("sss", $email, $username, $hashed_password);
                if ($stmt->execute()) {
                    $confirmation = "<p>Your account has been created.</p>";
                } else {
                    $error = TRUE;
                    $databaseAddingError = "<p>Error creating your account. Please try again later.</p>";
                    array_push($arrErrors, $databaseAddingError);
                }
            }
            
            $stmt->close();
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
            <input class="form_field" type="text" id="username" name="username" placeholder="username" pattern="[a-zA-Z0-9_-]{1,12}" required>
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