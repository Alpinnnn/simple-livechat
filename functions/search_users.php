<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to search users.";
    exit();
}

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Get the search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Validate query
if (empty($query)) {
    exit();
}

// Remove @ symbol if present
if (substr($query, 0, 1) === '@') {
    $query = substr($query, 1);
}

// Search for users
$search_query = "SELECT id, username, fullname, profile_image, is_online 
                FROM users 
                WHERE id != ? AND (username LIKE ? OR fullname LIKE ?) 
                LIMIT 20";
                
$search_param = "%$query%";
$stmt = $conn->prepare($search_query);
$stmt->bind_param("iss", $user_id, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Check if any users found
if ($result->num_rows === 0) {
    echo "<p class='no-results'>No users found with username or name matching '$query'</p>";
    exit();
}

// Display results
while ($user = $result->fetch_assoc()) {
    // Check if already in a direct chat with this user
    $chat_query = "SELECT c.id FROM chats c
                   JOIN chat_participants cp1 ON c.id = cp1.chat_id AND cp1.user_id = ?
                   JOIN chat_participants cp2 ON c.id = cp2.chat_id AND cp2.user_id = ?
                   WHERE c.chat_type = 'direct'
                   LIMIT 1";
    $chat_stmt = $conn->prepare($chat_query);
    $chat_stmt->bind_param("ii", $user_id, $user['id']);
    $chat_stmt->execute();
    $chat_result = $chat_stmt->get_result();
    
    $existing_chat = $chat_result->num_rows > 0 ? $chat_result->fetch_assoc()['id'] : false;
    $chat_stmt->close();
    
    echo "<div class='user-item' data-id='" . $user['id'] . "'>";
    echo "<div class='user-avatar'>";
    echo "<img src='assets/images/" . htmlspecialchars($user['profile_image']) . "' alt='Avatar'>";
    if ($user['is_online']) {
        echo "<span class='online-indicator'></span>";
    }
    echo "</div>";
    echo "<div class='user-info'>";
    echo "<h4>" . htmlspecialchars($user['fullname']) . "</h4>";
    echo "<p>@" . htmlspecialchars($user['username']) . "</p>";
    echo "</div>";
    if ($existing_chat) {
        echo "<a href='chat.php?chat=" . $existing_chat . "' class='btn-sm'>Open Chat</a>";
    }
    echo "</div>";
}

$stmt->close();
?> 