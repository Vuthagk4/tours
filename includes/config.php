<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tour";
$port = 3308;  // Specify the port number

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>