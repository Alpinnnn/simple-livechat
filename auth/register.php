<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($fullname) || empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: ../register.php?error=All fields are required");
        exit();
    }
    
    // Remove @ symbol if present
    if (substr($username, 0, 1) === '@') {
        $username = substr($username, 1);
    }
    
    // Validate username (alphanumeric + underscore)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        header("Location: ../register.php?error=Username can only contain letters, numbers, and underscores");
        exit();
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match");
        exit();
    }
    
    // Check if username already exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: ../register.php?error=Username already exists");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into database
    $insert_query = "INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("sss", $fullname, $username, $hashed_password);
    
    if ($insert_stmt->execute()) {
        // Get the new user ID
        $user_id = $insert_stmt->insert_id;
        
        // Add user to global chat
        $add_to_global = "INSERT INTO chat_participants (chat_id, user_id) VALUES (1, ?)";
        $global_stmt = $conn->prepare($add_to_global);
        $global_stmt->bind_param("i", $user_id);
        $global_stmt->execute();
        $global_stmt->close();
        
        // Success message and redirect
        header("Location: ../index.php?success=Registration successful. Please login.");
        exit();
    } else {
        header("Location: ../register.php?error=Registration failed. Please try again.");
        exit();
    }
    
    $stmt->close();
    $insert_stmt->close();
} else {
    // Redirect to registration page if accessed directly
    header("Location: ../register.php");
    exit();
}
?> 