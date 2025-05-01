<?php
// File untuk debugging session

require_once '../includes/functions.php';

// Cek password untuk security
$allowed = false;
if (isset($_GET['token']) && $_GET['token'] === 'debug12345') {
    $allowed = true;
}

if (!$allowed) {
    header('HTTP/1.0 403 Forbidden');
    echo "Akses ditolak";
    exit;
}

// Tampilkan informasi session
echo "<h2>Session Debug</h2>";
debugSession();

// Tampilkan status dark mode
echo "<h3>Dark Mode Status</h3>";
echo "isDarkMode(): " . (isDarkMode() ? 'true' : 'false') . "<br>";

// Toggle dark mode link
echo "<h3>Actions</h3>";
echo "<a href='?token=debug12345&action=toggle'>Toggle Dark Mode</a> | ";
echo "<a href='?token=debug12345&action=clear'>Clear Session</a> | ";
echo "<a href='?token=debug12345&action=set_true'>Set Dark Mode True</a> | ";
echo "<a href='?token=debug12345&action=set_false'>Set Dark Mode False</a>";

// Process actions
if (isset($_GET['action'])) {
    switch($_GET['action']) {
        case 'toggle':
            $_SESSION['dark_mode'] = !isDarkMode();
            echo "<p>Dark mode " . (isDarkMode() ? 'enabled' : 'disabled') . "</p>";
            break;
        case 'clear':
            session_unset();
            echo "<p>Session cleared</p>";
            break;
        case 'set_true':
            $_SESSION['dark_mode'] = true;
            echo "<p>Dark mode set to true</p>";
            break;
        case 'set_false':
            $_SESSION['dark_mode'] = false;
            echo "<p>Dark mode set to false</p>";
            break;
    }
    
    // Refresh the page to show updated info
    echo "<script>setTimeout(function() { window.location.href = '?token=debug12345'; }, 1000);</script>";
}
?> 