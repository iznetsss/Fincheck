<?php
//Login into db
$server = "127.0.0.1"; // anysql.itcollege.ee
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
    echo "Connected to DB successfully";
    // Reg variables
    $email = 'test@test.ee';
    $username = 'test';
    $password = 'testtt';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    
    // Check if the user with the given email or username already exists
    $query = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User with the provided email or username already exists
        echo "User with this email or username already exists.";
        die;
    } else {
        // User does not exist, add the user to the database
        $query = "INSERT INTO users (email, username, pass) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sss", $email, $username, $hashed_password);
        if ($stmt->execute()) {
            echo "Your account has been created.";
        } else {
            echo "Error creating your account. Please try again later.";
            die;
        }
    }
    
    $stmt->close();
}
?>
