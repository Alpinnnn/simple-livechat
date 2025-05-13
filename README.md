# WhatsApp Clone Chat Application

A real-time WhatsApp-style chat application built with PHP and MySQL.

## Features

- User registration and authentication
- Direct messaging between users
- Global chat room
- User search with @ mentions
- Real-time message updates
- Message read status indicators
- WhatsApp-like UI design
- Online status indicators

## Requirements

- PHP 7.2 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Installation

1. Clone or download this repository to your web server's document root
2. Create a MySQL database for the application
3. Edit `config/database.php` with your database credentials:
   ```php
   $host = "localhost";
   $username = "your_database_username";
   $password = "your_database_password";
   $database = "whatsapp_clone";
   ```
4. Run the setup script by visiting `http://your-server/setup.php` in your browser
5. Once setup is complete, you can login with the following credentials:
   - Username: admin, Password: admin123
   - Username: test, Password: test123
   
## Usage

1. Login with your credentials or register a new account
2. Start a new chat by clicking the message icon and searching for a user
3. Send messages by typing in the chat box
4. View and join the global chat from the menu
5. Logout from the dropdown menu in the top left

## Directory Structure

```
whatsapp_clone/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── default-avatar.png
│       ├── chat-bg.png
│       └── whatsapp-bg.png
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── config/
│   └── database.php
├── functions/
│   ├── chat_functions.php
│   ├── send_message.php
│   ├── search_users.php
│   ├── start_chat.php
│   └── get_new_messages.php
├── index.php
├── register.php
├── chat.php
├── setup.php
├── database.sql
└── README.md
```

## Security Notes

- This application includes basic security measures like password hashing and prepared statements
- For production use, consider adding additional security features like CSRF protection
- Make sure to secure your server and database with proper configurations
- Always change the default passwords for admin and test accounts

## License

This project is licensed under the MIT License - see the LICENSE file for details. 