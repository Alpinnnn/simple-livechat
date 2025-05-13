<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & FAQ - WhatsApp Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link rel="stylesheet" href="assets/css/settings.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .help-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            min-height: 100vh;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
        }
        
        .faq-question {
            font-weight: 600;
            color: #128c7e;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-answer {
            display: none;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: A4px;
        }
        
        .faq-answer.active {
            display: block;
        }
        
        .feature-list {
            list-style-type: none;
            padding-left: 0;
        }
        
        .feature-list li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        
        .feature-list li::before {
            content: "\f00c";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #128c7e;
        }
        
        .dark-mode .help-container {
            background-color: #1f1f1f;
            color: #e0e0e0;
        }
        
        .dark-mode .faq-item {
            border-bottom-color: #333;
        }
        
        .dark-mode .faq-question {
            color: #00a884;
        }
        
        .dark-mode .faq-answer {
            background-color: #2a2a2a;
        }
        
        .dark-mode .feature-list li::before {
            color: #00a884;
        }
    </style>
</head>
<body>
    <div class="help-container">
        <div class="settings-header">
            <a href="<?php echo isset($_SESSION['user_id']) ? 'chat.php' : 'index.php'; ?>" class="back-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Help & FAQ</h1>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label">Dark Mode</span>
            </div>
        </div>
        
        <div class="settings-section">
            <h2>About WhatsApp Clone</h2>
            <p>WhatsApp Clone is a real-time chat application that mimics the popular WhatsApp messaging platform. 
            This application allows users to communicate through direct messages and participate in a global chat room.</p>
            
            <h3>Features</h3>
            <ul class="feature-list">
                <li>User registration and authentication</li>
                <li>Direct messaging between users</li>
                <li>Global chat room</li>
                <li>User search with @ mentions</li>
                <li>Real-time message updates</li>
                <li>Message read status indicators</li>
                <li>WhatsApp-like UI design</li>
                <li>Online status indicators</li>
                <li>Dark mode support</li>
                <li>Responsive mobile design</li>
            </ul>
        </div>
        
        <div class="settings-section">
            <h2>Frequently Asked Questions</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    How do I start a new chat?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>To start a new chat:</p>
                    <ol>
                        <li>Click on the menu icon in the top left (three dots)</li>
                        <li>Select "New Chat"</li>
                        <li>Search for a user by their username or name</li>
                        <li>Click on the user you want to chat with</li>
                    </ol>
                    <p>Alternatively, you can also click the "Start a new chat" button on the welcome screen.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How does the global chat work?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>The global chat is a public chat room where all users of the application can send and receive messages. 
                    Everyone who has registered for the application is automatically added to the global chat.</p>
                    <p>To access the global chat:</p>
                    <ol>
                        <li>Click on the menu icon in the top left (three dots)</li>
                        <li>Select "Global Chat"</li>
                    </ol>
                    <p>Alternatively, you can find the global chat in your chat list with a globe icon.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How do I know if someone has read my message?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>When you send a message, you'll see check marks next to your message that indicate its status:</p>
                    <ul>
                        <li><i class="fas fa-check"></i> Single gray check: Message has been sent</li>
                        <li><i class="fas fa-check text-read"></i> Double blue check: Message has been read</li>
                    </ul>
                    <p>These indicators work in both direct chats and the global chat.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How do I search for users?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>You can search for users when starting a new chat:</p>
                    <ol>
                        <li>Click on "New Chat" from the menu</li>
                        <li>Type the username or full name of the person you're looking for</li>
                        <li>Results will appear as you type</li>
                    </ol>
                    <p>You can search for users by their username (with or without the @ symbol) or by their full name.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How do I change my profile picture?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>To change your profile picture:</p>
                    <ol>
                        <li>Go to Settings by clicking on the menu icon and selecting "Settings"</li>
                        <li>In the Profile Settings section, click "Change Picture"</li>
                        <li>Select an image from your device</li>
                        <li>The image will be uploaded automatically</li>
                    </ol>
                    <p>Supported image formats are JPEG, PNG, and GIF.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How do I activate dark mode?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>You can activate dark mode in two ways:</p>
                    <ol>
                        <li>From Settings: Go to Settings and toggle the Dark Mode switch</li>
                        <li>From any page: Look for the Dark Mode toggle in the top right corner of the page</li>
                    </ol>
                    <p>Your dark mode preference will be saved and applied across all pages of the application.</p>
                </div>
            </div>
        </div>
        
        <div class="settings-section">
            <h2>Contact Support</h2>
            <p>If you have any questions or need assistance, please contact us at support@whatsappclone.com.</p>
        </div>
    </div>
    
    <script src="assets/js/theme.js"></script>
    <script>
        // Toggle FAQ answers
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                answer.classList.toggle('active');
                
                // Toggle icon
                const icon = question.querySelector('i');
                if (answer.classList.contains('active')) {
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                } else {
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                }
            });
        });
    </script>
</body>
</html> 