<?php
$server = "127.0.0.1";
$user = "root";
$password = "1234";
$database = "ICS0008_4";
$link = new mysqli($server, $user, $password, $database);
if ($link->connect_error) {
    die("Connection to DB failed: " . $link->connect_error);
}
?>