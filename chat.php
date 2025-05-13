<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'functions/chat_functions.php';

// Get current user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get chats
$chats = getUserChats($conn, $user_id);

// Get current active chat if any
$active_chat_id = isset($_GET['chat']) ? $_GET['chat'] : null;
$active_chat = null;
$messages = [];

if ($active_chat_id) {
    $active_chat = getChatById($conn, $active_chat_id, $user_id);
    if ($active_chat) {
        $messages = getChatMessages($conn, $active_chat_id);
        // Mark messages as read
        markMessagesAsRead($conn, $active_chat_id, $user_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar with user's chats -->
        <div class="sidebar">
            <!-- User profile -->
            <div class="user-profile">
                <div class="profile-image">
                    <a href="profile.php">
                        <img src="<?php echo !empty($user['profile_image']) ? 'assets/images/avatars/' . $user['profile_image'] : 'assets/images/default-avatar.png'; ?>" alt="Profile">
                    </a>
                </div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
                    <p>@<?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                <div class="profile-actions">
                    <div class="dropdown">
                        <button class="dropdown-toggle"><i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu">
                            <a href="#" id="new-chat-btn"><i class="fas fa-comment"></i> New Chat</a>
                            <a href="#" id="new-group-btn"><i class="fas fa-users"></i> New Group</a>
                            <a href="#" id="global-chat-btn"><i class="fas fa-globe"></i> Global Chat</a>
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search bar -->
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-input" placeholder="Search or start new chat">
                </div>
            </div>
            
            <!-- Chats list -->
            <div class="chats-list">
                <!-- Global chat -->
                <a href="chat.php?chat=global" class="chat-item <?php echo ($active_chat_id == 'global') ? 'active' : ''; ?>">
                    <div class="chat-item-avatar">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-name">Global Chat</div>
                        <div class="chat-item-last-message">Join the global conversation</div>
                    </div>
                </a>
                
                <!-- User chats -->
                <?php foreach ($chats as $chat): ?>
                    <a href="chat.php?chat=<?php echo $chat['id']; ?>" class="chat-item <?php echo ($active_chat_id == $chat['id']) ? 'active' : ''; ?>">
                        <div class="chat-item-avatar">
                            <img src="<?php echo !empty($chat['profile_image']) ? 'assets/images/avatars/' . $chat['profile_image'] : 'assets/images/default-avatar.png'; ?>" alt="Avatar">
                            <?php if ($chat['online']): ?>
                                <span class="online-indicator"></span>
                            <?php endif; ?>
                        </div>
                        <div class="chat-item-info">
                            <div class="chat-item-name"><?php echo htmlspecialchars($chat['name']); ?></div>
                            <div class="chat-item-last-message">
                                <?php if ($chat['last_message']): ?>
                                    <?php if ($chat['last_message_sender_id'] == $user_id): ?>
                                        <span class="message-status">
                                            <i class="fas fa-check<?php echo $chat['last_message_read'] ? ' text-read' : ''; ?>"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars(substr($chat['last_message'], 0, 30)) . (strlen($chat['last_message']) > 30 ? '...' : ''); ?>
                                <?php else: ?>
                                    No messages yet
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="chat-item-meta">
                            <?php if ($chat['last_message_time']): ?>
                                <div class="chat-item-time"><?php echo formatMessageTime($chat['last_message_time']); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($chat['unread_count'] > 0): ?>
                                <div class="unread-badge"><?php echo $chat['unread_count']; ?></div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Chat area -->
        <div class="chat-area">
            <?php if ($active_chat): ?>
                <!-- Chat header -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <?php if ($active_chat_id == 'global'): ?>
                            <div class="chat-avatar">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="chat-contact">
                                <h3>Global Chat</h3>
                                <p>Everyone is here</p>
                            </div>
                        <?php else: ?>
                            <div class="chat-avatar">
                                <img src="<?php echo !empty($active_chat['profile_image']) ? 'assets/images/avatars/' . $active_chat['profile_image'] : 'assets/images/default-avatar.png'; ?>" alt="Avatar">
                                <?php if ($active_chat['online']): ?>
                                    <span class="online-indicator"></span>
                                <?php endif; ?>
                            </div>
                            <div class="chat-contact">
                                <h3><?php echo htmlspecialchars($active_chat['name']); ?></h3>
                                <p><?php echo $active_chat['online'] ? 'Online' : 'Offline'; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="chat-header-actions">
                        <button class="btn-icon"><i class="fas fa-search"></i></button>
                        <button class="btn-icon"><i class="fas fa-ellipsis-v"></i></button>
                    </div>
                </div>
                
                <!-- Chat messages -->
                <div class="chat-messages" id="chat-messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo ($message['sender_id'] == $user_id) ? 'sent' : 'received'; ?>">
                            <?php if ($active_chat_id == 'global' && $message['sender_id'] != $user_id): ?>
                                <div class="message-sender"><?php echo htmlspecialchars($message['sender_name']); ?></div>
                            <?php endif; ?>
                            <div class="message-content">
                                <?php echo htmlspecialchars($message['message']); ?>
                                <div class="message-time">
                                    <?php echo formatMessageTime($message['created_at']); ?>
                                    <?php if ($message['sender_id'] == $user_id): ?>
                                        <span class="message-status">
                                            <i class="fas fa-check<?php echo $message['read'] ? ' text-read' : ''; ?>"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Chat input -->
                <div class="chat-input">
                    <button class="btn-icon"><i class="far fa-smile"></i></button>
                    <button class="btn-icon"><i class="fas fa-paperclip"></i></button>
                    <form id="messageForm" action="functions/send_message.php" method="POST">
                        <input type="hidden" name="chat_id" value="<?php echo $active_chat_id; ?>">
                        <input type="text" name="message" id="message-input" placeholder="Type a message" autocomplete="off">
                        <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            <?php else: ?>
                <!-- No active chat selected -->
                <div class="no-chat-selected">
                    <div class="no-chat-content">
                        <img src="assets/images/whatsapp-bg.png" alt="Start chatting">
                        <h2>Keep your phone connected</h2>
                        <p>Select a chat to start messaging</p>
                        <button id="start-chat-btn" class="btn-primary">Start a new chat</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- New chat modal -->
    <div id="new-chat-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>New Chat</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="user-search" placeholder="Search users by @username">
                    </div>
                </div>
                <div id="search-results" class="user-list"></div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html> 