<?php
// Database connection file

$host = "localhost";   // XAMPP default
$user = "root";        // default MySQL user
$pass = "";            // leave empty in XAMPP
$dbname = "alumni_portal"; // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("? Connection failed: " . $conn->connect_error);
}
?>

