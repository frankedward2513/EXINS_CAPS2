<?php
// connect to the database
$conn = new mysqli("localhost", "root", "", "jkexinewdb");

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>