    -- Create the database
    CREATE DATABASE IF NOT EXISTS whatsapp_clone;
    USE whatsapp_clone;

    -- Users table
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100) NOT NULL,
        profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
        status VARCHAR(100) DEFAULT 'Hey there! I am using WhatsApp Clone',
        is_online BOOLEAN DEFAULT FALSE,
        last_seen TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Chats table (for direct messages and global chat)
    CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        chat_type ENUM('direct', 'group', 'global') NOT NULL DEFAULT 'direct',
        name VARCHAR(100),  -- Used for group chats
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Insert global chat
    INSERT INTO chats (id, chat_type, name) VALUES (1, 'global', 'Global Chat') ON DUPLICATE KEY UPDATE name = 'Global Chat';

    -- Chat participants (for direct or group chats)
    CREATE TABLE IF NOT EXISTS chat_participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        chat_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (chat_id) REFERENCES whatsapp_clone.chats(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES whatsapp_clone.users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_participant (chat_id, user_id)
    );

    -- Messages table
    CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        chat_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (chat_id) REFERENCES whatsapp_clone.chats(id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES whatsapp_clone.users(id) ON DELETE CASCADE
    );

    -- Message read status
    CREATE TABLE IF NOT EXISTS message_status (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message_id INT NOT NULL,
        user_id INT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        read_at TIMESTAMP NULL,
        FOREIGN KEY (message_id) REFERENCES whatsapp_clone.messages(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES whatsapp_clone.users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_status (message_id, user_id)
    );

    -- User sessions
    CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_id VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES whatsapp_clone.users(id) ON DELETE CASCADE
    );

    -- Insert admin user (Password: admin123)
    INSERT INTO users (username, password, fullname) VALUES 
    ('admin', '$2y$10$r8YhH0TDIa9gDjV93XFxpOJvJI2QK2W2/Hf1oJDpS8NYVbPZE0GQ.', 'Admin User') 
    ON DUPLICATE KEY UPDATE password = '$2y$10$r8YhH0TDIa9gDjV93XFxpOJvJI2QK2W2/Hf1oJDpS8NYVbPZE0GQ.';

    -- Create second user for testing (Password: test123)
    INSERT INTO users (username, password, fullname) VALUES 
    ('test', '$2y$10$B6xNqK3eXCZoN8w3rQUiE.M/cHk2IHwkpD9JUdlRX3VBn45LpwY3O', 'Test User') 
    ON DUPLICATE KEY UPDATE password = '$2y$10$B6xNqK3eXCZoN8w3rQUiE.M/cHk2IHwkpD9JUdlRX3VBn45LpwY3O';

    -- Add admin user to global chat
    INSERT INTO chat_participants (chat_id, user_id) VALUES (1, 1) ON DUPLICATE KEY UPDATE joined_at = CURRENT_TIMESTAMP;

    -- Add test user to global chat
    INSERT INTO chat_participants (chat_id, user_id) VALUES (1, 2) ON DUPLICATE KEY UPDATE joined_at = CURRENT_TIMESTAMP;

    -- Create indexes for better performance
    CREATE INDEX idx_messages_chat_id ON messages(chat_id);
    CREATE INDEX idx_messages_sender_id ON messages(sender_id);
    CREATE INDEX idx_message_status_message_id ON message_status(message_id);
    CREATE INDEX idx_message_status_user_id ON message_status(user_id);
    CREATE INDEX idx_chat_participants_chat_id ON chat_participants(chat_id);
    CREATE INDEX idx_chat_participants_user_id ON chat_participants(user_id);