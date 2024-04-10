<?php
$server = "127.0.0.1";
$user = "root";
$password = "Gorgorod1460";
$database = "ICS0008_4";
$link = new mysqli($server, $user, $password, $database);
if ($link->connect_error) {
    die("Connection to DB failed: " . $link->connect_error);
}
$link -> query("DROP TABLE users;");
$link -> query("CREATE TABLE IF NOT EXISTS users (
                ID INT PRIMARY KEY AUTO_INCREMENT NOT NULL, 
                username VARCHAR(12) NOT NULL, 
                email VARCHAR(320) NOT NULL, 
                pass VARCHAR(100) NOT NULL);");

?>