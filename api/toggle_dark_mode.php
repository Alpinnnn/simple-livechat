<?php
header('Content-Type: application/json');

// Inisialisasi session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Konversi string ke boolean dengan benar
    $dark_mode = isset($_POST['dark_mode']) && ($_POST['dark_mode'] === 'true' || $_POST['dark_mode'] === '1');
    
    // Simpan preferensi dark mode di session
    $_SESSION['dark_mode'] = $dark_mode;
    
    // Log untuk debugging
    error_log("Dark Mode set to: " . ($dark_mode ? 'true' : 'false') . " (Value received: " . $_POST['dark_mode'] . ")");
    
    echo json_encode(['success' => true, 'dark_mode' => $dark_mode]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

// Fungsi untuk memeriksa apakah dark mode aktif
function isDarkMode() {
    return isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] === true;
}
?> 