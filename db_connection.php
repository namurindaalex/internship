<?php
$host = "localhost";
$user = "root";
$pass = "Alex@mysql123";
$dbname = "power_monitor";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

