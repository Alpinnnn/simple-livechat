<?php
/**
 * Get all chats for a user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return array Array of chats
 */
function getUserChats($conn, $user_id) {
    $chats = [];
    
    // Get all chats the user is part of
    $query = "SELECT c.*, 
            CASE 
                WHEN c.chat_type = 'direct' THEN 
                    (SELECT u.fullname FROM users u 
                    JOIN chat_participants cp ON u.id = cp.user_id 
                    WHERE cp.chat_id = c.id AND u.id != ?)
                ELSE c.name
            END as name,
            CASE 
                WHEN c.chat_type = 'direct' THEN 
                    (SELECT u.is_online FROM users u 
                    JOIN chat_participants cp ON u.id = cp.user_id 
                    WHERE cp.chat_id = c.id AND u.id != ?)
                ELSE 0
            END as online,
            CASE 
                WHEN c.chat_type = 'direct' THEN 
                    (SELECT u.profile_image FROM users u 
                    JOIN chat_participants cp ON u.id = cp.user_id 
                    WHERE cp.chat_id = c.id AND u.id != ?)
                ELSE 'default-avatar.png'
            END as profile_image,
            (SELECT m.message FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message,
            (SELECT m.sender_id FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message_sender_id,
            (SELECT m.created_at FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message_time,
            (SELECT 
                CASE 
                    WHEN m.sender_id = ? THEN 
                        EXISTS(SELECT 1 FROM message_status ms WHERE ms.message_id = m.id AND ms.is_read = 1)
                    ELSE 0
                END
            FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message_read,
            (SELECT COUNT(*) FROM messages m 
            LEFT JOIN message_status ms ON m.id = ms.message_id AND ms.user_id = ?
            WHERE m.chat_id = c.id AND m.sender_id != ? AND (ms.is_read = 0 OR ms.is_read IS NULL)) as unread_count
            FROM chats c
            JOIN chat_participants cp ON c.id = cp.chat_id
            WHERE cp.user_id = ?
            ORDER BY last_message_time DESC";
            
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }
    
    $stmt->close();
    return $chats;
}

/**
 * Get chat by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $chat_id Chat ID
 * @param int $user_id Current user ID
 * @return array|null Chat data or null if not found
 */
function getChatById($conn, $chat_id, $user_id) {
    // Handle global chat
    if ($chat_id === 'global') {
        $query = "SELECT id, name, chat_type, created_at, 1 as participant, 0 as online FROM chats WHERE id = 1";
        $stmt = $conn->prepare($query);
    } else {
        $query = "SELECT c.*, 
                CASE 
                    WHEN c.chat_type = 'direct' THEN 
                        (SELECT u.fullname FROM users u 
                        JOIN chat_participants cp ON u.id = cp.user_id 
                        WHERE cp.chat_id = c.id AND u.id != ?)
                    ELSE c.name
                END as name,
                CASE 
                    WHEN c.chat_type = 'direct' THEN 
                        (SELECT u.is_online FROM users u 
                        JOIN chat_participants cp ON u.id = cp.user_id 
                        WHERE cp.chat_id = c.id AND u.id != ?)
                    ELSE 0
                END as online,
                CASE 
                    WHEN c.chat_type = 'direct' THEN 
                        (SELECT u.profile_image FROM users u 
                        JOIN chat_participants cp ON u.id = cp.user_id 
                        WHERE cp.chat_id = c.id AND u.id != ?)
                    ELSE 'default-avatar.png'
                END as profile_image,
                EXISTS(SELECT 1 FROM chat_participants cp WHERE cp.chat_id = c.id AND cp.user_id = ?) as participant
                FROM chats c
                WHERE c.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $chat_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $chat = $result->fetch_assoc();
        
        // Only return chat if user is participant
        if ($chat['participant']) {
            return $chat;
        }
    }
    
    $stmt->close();
    return null;
}

/**
 * Get messages for a chat
 * 
 * @param mysqli $conn Database connection
 * @param int $chat_id Chat ID
 * @param int $limit Limit number of messages (optional)
 * @param int $offset Offset for pagination (optional)
 * @return array Array of messages
 */
function getChatMessages($conn, $chat_id, $limit = 50, $offset = 0) {
    $messages = [];
    
    // Special case for global chat
    if ($chat_id === 'global') {
        $chat_id = 1;
    }
    
    $query = "SELECT m.*, u.fullname as sender_name, u.username as sender_username,
            EXISTS(SELECT 1 FROM message_status ms WHERE ms.message_id = m.id AND ms.is_read = 1) as `read`
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.chat_id = ?
            ORDER BY m.created_at
            LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $chat_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    $stmt->close();
    return $messages;
}

/**
 * Mark messages as read
 * 
 * @param mysqli $conn Database connection
 * @param int $chat_id Chat ID
 * @param int $user_id User ID
 * @return void
 */
function markMessagesAsRead($conn, $chat_id, $user_id) {
    // Special case for global chat
    if ($chat_id === 'global') {
        $chat_id = 1;
    }
    
    // Get unread messages for this chat where user is not the sender
    $query = "SELECT m.id FROM messages m
            LEFT JOIN message_status ms ON m.id = ms.message_id AND ms.user_id = ?
            WHERE m.chat_id = ? AND m.sender_id != ? AND (ms.is_read = 0 OR ms.is_read IS NULL)";
            
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $chat_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $message_id = $row['id'];
        
        // Check if status entry exists
        $check_query = "SELECT id FROM message_status WHERE message_id = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $message_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing entry
            $update_query = "UPDATE message_status SET is_read = 1, read_at = NOW() WHERE message_id = ? AND user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ii", $message_id, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Insert new entry
            $insert_query = "INSERT INTO message_status (message_id, user_id, is_read, read_at) VALUES (?, ?, 1, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ii", $message_id, $user_id);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        $check_stmt->close();
    }
    
    $stmt->close();
}

/**
 * Format message time for display
 * 
 * @param string $datetime Datetime string
 * @return string Formatted time
 */
function formatMessageTime($datetime) {
    if (!$datetime) {
        return '';
    }
    
    $timestamp = strtotime($datetime);
    $now = time();
    $today = strtotime('today');
    $yesterday = strtotime('yesterday');
    
    if ($timestamp >= $today) {
        // Today - show time only
        return date('H:i', $timestamp);
    } elseif ($timestamp >= $yesterday) {
        // Yesterday
        return 'Yesterday';
    } elseif ($now - $timestamp < 604800) {
        // Within a week - show day name
        return date('D', $timestamp);
    } else {
        // Older than a week - show date
        return date('d/m/Y', $timestamp);
    }
}
?> 