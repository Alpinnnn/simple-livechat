<?php
// Database configuration
$host = "localhost";
$username = "root";  // Change this to your database username
$password = "";      // Change this to your database password
$database = "whatsapp_clone";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute the SQL file
$sql = file_get_contents('database.sql');

// Check if file was read successfully
if ($sql === false) {
    die("Error reading database.sql file");
}

// Execute SQL script
if ($conn->multi_query($sql)) {
    echo "<h1>WhatsApp Clone Setup</h1>";
    echo "<p>Database has been set up successfully!</p>";
    
    // Create images directory if not exists
    if (!file_exists('assets/images')) {
        mkdir('assets/images', 0777, true);
    }
    
    // Create default avatar if not exists
    $default_avatar = 'assets/images/default-avatar.png';
    if (!file_exists($default_avatar)) {
        // Create a simple default avatar
        $img = imagecreatetruecolor(200, 200);
        $background = imagecolorallocate($img, 0, 150, 136); // WhatsApp green
        $text_color = imagecolorallocate($img, 255, 255, 255);
        
        // Fill the background
        imagefilledrectangle($img, 0, 0, 200, 200, $background);
        
        // Add text
        $font = 5;
        $text = "User";
        $text_width = imagefontwidth($font) * strlen($text);
        $text_height = imagefontheight($font);
        $x = (200 - $text_width) / 2;
        $y = (200 - $text_height) / 2;
        
        imagestring($img, $font, $x, $y, $text, $text_color);
        
        // Save the image
        imagepng($img, $default_avatar);
        imagedestroy($img);
    }
    
    // Create chat background if not exists
    $chat_bg = 'assets/images/chat-bg.png';
    if (!file_exists($chat_bg)) {
        // Create a simple chat background
        $img = imagecreatetruecolor(400, 400);
        $background = imagecolorallocate($img, 229, 221, 213); // WhatsApp chat background
        $pattern = imagecolorallocate($img, 210, 210, 210);
        
        // Fill the background
        imagefilledrectangle($img, 0, 0, 400, 400, $background);
        
        // Add a subtle pattern
        for ($i = 0; $i < 400; $i += 10) {
            for ($j = 0; $j < 400; $j += 10) {
                imagefilledrectangle($img, $i, $j, $i+5, $j+5, $pattern);
            }
        }
        
        // Save the image
        imagepng($img, $chat_bg);
        imagedestroy($img);
    }
    
    // Create WhatsApp logo if not exists
    $whatsapp_bg = 'assets/images/whatsapp-bg.png';
    if (!file_exists($whatsapp_bg)) {
        // Create a simple WhatsApp logo
        $img = imagecreatetruecolor(300, 300);
        $background = imagecolorallocate($img, 255, 255, 255);
        $logo_color = imagecolorallocate($img, 0, 150, 136); // WhatsApp green
        
        // Fill the background
        imagefilledrectangle($img, 0, 0, 300, 300, $background);
        
        // Draw a circle for the logo
        imagefilledellipse($img, 150, 150, 200, 200, $logo_color);
        
        // Save the image
        imagepng($img, $whatsapp_bg);
        imagedestroy($img);
    }
    
    echo "<p>Default images have been created.</p>";
    echo "<p>You can now <a href='index.php'>login</a> with the following credentials:</p>";
    echo "<ul>";
    echo "<li>Username: admin, Password: admin123</li>";
    echo "<li>Username: test, Password: test123</li>";
    echo "</ul>";
    
    echo "<p>Or you can <a href='register.php'>register</a> a new account.</p>";
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?> 