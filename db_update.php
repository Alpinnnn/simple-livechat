<?php
require_once 'config/database.php';

// Check if profile_image column exists in users table
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
if ($check_column->num_rows == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'default-avatar.png'";
    if ($conn->query($alter_query) === TRUE) {
        echo "Profile image column added successfully";
    } else {
        echo "Error adding profile image column: " . $conn->error;
    }
} else {
    echo "Profile image column already exists";
}

// Check if status column exists in users table
$check_status = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($check_status->num_rows == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE users ADD COLUMN status VARCHAR(255) DEFAULT 'Hey there! I am using WhatsApp Clone.'";
    if ($conn->query($alter_query) === TRUE) {
        echo "<br>Status column added successfully";
    } else {
        echo "<br>Error adding status column: " . $conn->error;
    }
} else {
    echo "<br>Status column already exists";
}

// Check if last_seen column exists in users table
$check_last_seen = $conn->query("SHOW COLUMNS FROM users LIKE 'last_seen'");
if ($check_last_seen->num_rows == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE users ADD COLUMN last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    if ($conn->query($alter_query) === TRUE) {
        echo "<br>Last seen column added successfully";
    } else {
        echo "<br>Error adding last seen column: " . $conn->error;
    }
} else {
    echo "<br>Last seen column already exists";
}

echo "<br><br><a href='index.php'>Go to Homepage</a>";
?> 