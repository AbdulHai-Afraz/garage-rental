<?php
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "garage_rental"; // Database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>