<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get the user IDs
$current_user_id = $_SESSION['user_id'];
$target_user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// Validate input
if (empty($target_user_id) || $current_user_id === $target_user_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit();
}

// Check if target user exists
$user_query = "SELECT id FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $target_user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user_stmt->close();

// Check if a direct chat already exists between the two users
$chat_query = "SELECT c.id FROM chats c
               JOIN chat_participants cp1 ON c.id = cp1.chat_id AND cp1.user_id = ?
               JOIN chat_participants cp2 ON c.id = cp2.chat_id AND cp2.user_id = ?
               WHERE c.chat_type = 'direct'
               LIMIT 1";
$chat_stmt = $conn->prepare($chat_query);
$chat_stmt->bind_param("ii", $current_user_id, $target_user_id);
$chat_stmt->execute();
$chat_result = $chat_stmt->get_result();

// If chat exists, return its ID
if ($chat_result->num_rows > 0) {
    $chat = $chat_result->fetch_assoc();
    $chat_id = $chat['id'];
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Chat already exists', 'chat_id' => $chat_id]);
    exit();
}

$chat_stmt->close();

// Create a new direct chat
$conn->begin_transaction();

try {
    // Insert new chat
    $create_chat_query = "INSERT INTO chats (chat_type) VALUES ('direct')";
    $conn->query($create_chat_query);
    $chat_id = $conn->insert_id;
    
    // Add current user to the chat
    $add_current_user = "INSERT INTO chat_participants (chat_id, user_id) VALUES (?, ?)";
    $current_stmt = $conn->prepare($add_current_user);
    $current_stmt->bind_param("ii", $chat_id, $current_user_id);
    $current_stmt->execute();
    $current_stmt->close();
    
    // Add target user to the chat
    $add_target_user = "INSERT INTO chat_participants (chat_id, user_id) VALUES (?, ?)";
    $target_stmt = $conn->prepare($add_target_user);
    $target_stmt->bind_param("ii", $chat_id, $target_user_id);
    $target_stmt->execute();
    $target_stmt->close();
    
    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Chat created successfully', 'chat_id' => $chat_id]);
} catch (Exception $e) {
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to create chat: ' . $e->getMessage()]);
}

exit();
?> 