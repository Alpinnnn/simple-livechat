<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        header("Location: ../index.php?error=Username and password are required");
        exit();
    }
    
    // Remove @ symbol if present
    if (substr($username, 0, 1) === '@') {
        $username = substr($username, 1);
    }
    
    // Check if user exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            
            // Update user status to online
            $updateQuery = "UPDATE users SET is_online = 1, last_seen = NOW() WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Redirect to chat
            header("Location: ../chat.php");
            exit();
        } else {
            // Password is incorrect
            header("Location: ../index.php?error=Invalid username or password");
            exit();
        }
    } else {
        // User does not exist
        header("Location: ../index.php?error=Invalid username or password");
        exit();
    }
    
    $stmt->close();
} else {
    // Redirect to login page if accessed directly
    header("Location: ../index.php");
    exit();
}
?> 