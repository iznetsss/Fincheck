<?php
include_once("includes/sql_connect.php");
$email = "test@tt.com";
//Check if email exists.
$query = ("SELECT EXISTS (SELECT 1 FROM users WHERE email = '$email') as email_exists;");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$email_exists = $row["email_exists"];

if ($email_exists) {
    echo "YES MOTHERFUCKER";
}
$query = ("SELECT pass FROM users WHERE email = '$email'");
$result = mysqli_query($link, $query);
$row = mysqli_fetch_assoc($result);
$pass = $row["pass"];
echo $pass;