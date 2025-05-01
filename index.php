<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="<?php echo isDarkMode() ? 'dark-mode' : ''; ?>">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card chat-container">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i> Chat App</h5>
                    </div>
                    <div class="card-body d-flex flex-column p-0">
                        <div id="chat-messages" class="chat-messages">
                            <!-- Pesan akan ditampilkan di sini -->
                            <div class="text-center p-4 text-muted">
                                <p><i class="fas fa-comment-dots fa-2x mb-3"></i></p>
                                <p>Mulai percakapan sekarang!</p>
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
        <a href="admin/login.php" class="admin-btn" title="Admin Login">
            <i class="fas fa-user-shield"></i>
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
    <script src="assets/js/script.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html> 