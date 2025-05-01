<?php
// Inisialisasi session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk memeriksa apakah dark mode aktif
function isDarkMode() {
    // Cek di session dulu
    if (isset($_SESSION['dark_mode'])) {
        // Pastikan nilai session dikonversi ke boolean
        return $_SESSION['dark_mode'] === true || $_SESSION['dark_mode'] === 1 || $_SESSION['dark_mode'] === '1';
    }
    
    // Default: tidak aktif
    return false;
}

// Fungsi untuk debug session
function debugSession() {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}
?> 