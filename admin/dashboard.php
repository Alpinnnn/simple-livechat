<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        .admin-header {
            background-color: #075E54;
        }
        .admin-sidebar {
            background-color: #f8f9fa;
            min-height: calc(100vh - 56px);
            box-shadow: 1px 0 5px rgba(0,0,0,0.1);
        }
        .user-item {
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .user-item:hover, .user-item.active {
            background-color: #e9ecef;
        }
        .admin-welcome {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #6c757d;
        }
        .dark-mode .admin-sidebar {
            background-color: #2d2d2d;
        }
        .dark-mode .user-item:hover, .dark-mode .user-item.active {
            background-color: #3a3a3a;
        }
    </style>
</head>
<body class="<?php echo isDarkMode() ? 'dark-mode' : ''; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark admin-header">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-comments me-2"></i> Chat App Admin
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card chat-container">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-headset me-2"></i> Panel Admin Chat</h5>
                    </div>
                    <div class="card-body d-flex flex-column p-0">
                        <div id="chat-messages" class="chat-messages admin-chat">
                            <!-- Pesan akan ditampilkan di sini -->
                            <div class="text-center p-4 text-muted">
                                <p><i class="fas fa-comments fa-2x mb-3"></i></p>
                                <p>Balas pesan pengguna untuk memberikan dukungan</p>
                            </div>
                        </div>
                        <div class="chat-input">
                            <form id="chat-form">
                                <div class="input-group">
                                    <input type="text" id="message" class="form-control" placeholder="Ketik pesan...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Buttons -->
    <div class="control-buttons">
        <a href="../index.php" class="admin-btn" title="Kembali ke Chat">
            <i class="fas fa-home"></i>
        </a>
        <!-- Settings Button -->
        <button class="settings-btn" id="settingsBtn">
            <i class="fas fa-cog"></i>
        </button>
    </div>

    <!-- Bottom Bar -->
    <div class="bottom-bar" id="bottomBar">
        <div class="bottom-bar-content">
            <h3>Pengaturan</h3>
            <div class="settings-item">
                <span>Mode Gelap</span>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle" <?php echo isDarkMode() ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
            </div>
            <div class="social-links">
                <a href="https://alpinnnn.vercel.app" target="_blank" class="social-btn website">
                    <i class="fas fa-globe"></i>
                    <span>Website</span>
                </a>
                <a href="https://github.com/Alpinnnn" target="_blank" class="social-btn github">
                    <i class="fab fa-github"></i>
                    <span>GitHub</span>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html> 