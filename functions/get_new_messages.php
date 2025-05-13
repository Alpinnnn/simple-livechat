<?php
session_start();
require_once '../config/database.php';
require_once 'chat_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit();
}

// Get parameters
$user_id = $_SESSION['user_id'];
$chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : null;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Validate input
if (empty($chat_id)) {
    exit();
}

// Special case for global chat
if ($chat_id === 'global') {
    $chat_id = 1;
}

// Get new messages after the last seen ID
$query = "SELECT m.*, u.fullname as sender_name, u.username as sender_username,
        EXISTS(SELECT 1 FROM message_status ms WHERE ms.message_id = m.id AND ms.is_read = 1) as `read`
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.chat_id = ? AND m.id > ?
        ORDER BY m.created_at";
        
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $chat_id, $last_id);
$stmt->execute();
$result = $stmt->get_result();

// Mark new messages as read if they're not from the current user
markMessagesAsRead($conn, $chat_id, $user_id);

// Output the new messages
while ($message = $result->fetch_assoc()) {
    $is_sender = $message['sender_id'] == $user_id;
    echo '<div class="message ' . ($is_sender ? 'sent' : 'received') . '" data-id="' . $message['id'] . '">';
    
    if ($chat_id == 1 && !$is_sender) {
        echo '<div class="message-sender">' . htmlspecialchars($message['sender_name']) . '</div>';
    }
    
    echo '<div class="message-content">';
    echo htmlspecialchars($message['message']);
    echo '<div class="message-time">';
    echo formatMessageTime($message['created_at']);
    
    if ($is_sender) {
        echo '<span class="message-status">';
        echo '<i class="fas fa-check' . ($message['read'] ? ' text-read' : '') . '"></i>';
        echo '</span>';
    }
    
    echo '</div>'; // close message-time
    echo '</div>'; // close message-content
    echo '</div>'; // close message
}

$stmt->close();
?> 