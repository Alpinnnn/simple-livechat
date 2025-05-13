<?php
// Database configuration
$host = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$database = "whatsapp_clone";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?> 