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

// Get the message data
$user_id = $_SESSION['user_id'];
$chat_id = isset($_POST['chat_id']) ? $_POST['chat_id'] : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($chat_id) || empty($message)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

// Handle global chat
if ($chat_id === 'global') {
    $chat_id = 1;
}

// Check if user is a participant in the chat
$check_query = "SELECT 1 FROM chat_participants WHERE chat_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $chat_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'You are not a participant in this chat']);
    exit();
}

$check_stmt->close();

// Insert the message
$query = "INSERT INTO messages (chat_id, sender_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $chat_id, $user_id, $message);

if ($stmt->execute()) {
    $message_id = $stmt->insert_id;
    
    // Mark as read for the sender
    $read_query = "INSERT INTO message_status (message_id, user_id, is_read, read_at) VALUES (?, ?, 1, NOW())";
    $read_stmt = $conn->prepare($read_query);
    $read_stmt->bind_param("ii", $message_id, $user_id);
    $read_stmt->execute();
    $read_stmt->close();
    
    // Get all participants except the sender
    $participants_query = "SELECT user_id FROM chat_participants WHERE chat_id = ? AND user_id != ?";
    $participants_stmt = $conn->prepare($participants_query);
    $participants_stmt->bind_param("ii", $chat_id, $user_id);
    $participants_stmt->execute();
    $participants_result = $participants_stmt->get_result();
    
    // Insert unread status for all other participants
    while ($participant = $participants_result->fetch_assoc()) {
        $status_query = "INSERT INTO message_status (message_id, user_id, is_read) VALUES (?, ?, 0)";
        $status_stmt = $conn->prepare($status_query);
        $status_stmt->bind_param("ii", $message_id, $participant['user_id']);
        $status_stmt->execute();
        $status_stmt->close();
    }
    
    $participants_stmt->close();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Message sent successfully', 'message_id' => $message_id]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}

$stmt->close();
exit();
?> 