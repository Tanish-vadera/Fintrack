<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = ""; // default for XAMPP
$dbname = "fintrack_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Database Connection Failed: " . $conn->connect_error);
}
?>
