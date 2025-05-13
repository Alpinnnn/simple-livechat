<?php
session_start();

// Update user status to offline
if (isset($_SESSION['user_id'])) {
    require_once '../config/database.php';
    
    $user_id = $_SESSION['user_id'];
    $query = "UPDATE users SET is_online = 0, last_seen = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Clear session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../index.php");
exit();
?> 