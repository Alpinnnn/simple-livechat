<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

// Get current user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname']);
        $status = trim($_POST['status']);
        
        // Validate input
        if (empty($fullname)) {
            $error_message = "Full name cannot be empty";
        } else {
            // Update user profile
            $update_query = "UPDATE users SET fullname = ?, status = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssi", $fullname, $status, $user_id);
            
            if ($update_stmt->execute()) {
                $success_message = "Profile updated successfully";
                
                // Update session data
                $_SESSION['fullname'] = $fullname;
                
                // Refresh user data
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            } else {
                $error_message = "Failed to update profile";
            }
            
            $update_stmt->close();
        }
    } elseif (isset($_POST['update_avatar']) && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        
        // Check for errors
        if ($file['error'] === 0) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            $file_type = $file_info->file($file['tmp_name']);
            
            if (in_array($file_type, $allowed_types)) {
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
                $target_path = 'assets/images/avatars/' . $filename;
                
                // Create directory if it doesn't exist
                if (!file_exists('assets/images/avatars/')) {
                    mkdir('assets/images/avatars/', 0777, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    // Update database
                    $update_query = "UPDATE users SET profile_image = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("si", $filename, $user_id);
                    
                    if ($update_stmt->execute()) {
                        $success_message = "Profile picture updated successfully";
                        
                        // Refresh user data
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $user = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                    } else {
                        $error_message = "Failed to update database with new profile picture";
                    }
                    
                    $update_stmt->close();
                } else {
                    $error_message = "Failed to upload image";
                }
            } else {
                $error_message = "Invalid file type. Please upload a JPEG, PNG, or GIF image";
            }
        } else {
            $error_message = "Error uploading file";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - WhatsApp Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <a href="chat.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
            <h1>My Profile</h1>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label">Dark Mode</span>
            </div>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="profile-section">
            <div class="profile-avatar-section">
                <div class="profile-avatar">
                    <img src="<?php echo !empty($user['profile_image']) ? 'assets/images/avatars/' . $user['profile_image'] : 'assets/images/default-avatar.png'; ?>" 
                         alt="Profile Picture" id="profile-preview">
                </div>
                <form method="POST" enctype="multipart/form-data" class="avatar-form">
                    <label for="avatar-upload" class="avatar-upload-btn">
                        <i class="fas fa-camera"></i>
                        <span>Change Photo</span>
                    </label>
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
                    <input type="submit" name="update_avatar" value="Upload" id="avatar-submit" style="display: none;">
                </form>
            </div>
            
            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-at"></i>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    <small>Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status">Status Message</label>
                    <div class="input-with-icon">
                        <i class="fas fa-comment"></i>
                        <textarea id="status" name="status" rows="2"><?php echo htmlspecialchars($user['status'] ?? 'Hey there! I am using WhatsApp Clone.'); ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_profile" class="primary-btn">Save Profile</button>
                    <a href="settings.php" class="secondary-btn">More Settings</a>
                </div>
            </form>
        </div>
        
        <div class="profile-footer">
            <p>Last seen: <?php echo date('F j, Y g:i a', strtotime($user['last_seen'] ?? 'now')); ?></p>
            <a href="auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <script src="assets/js/theme.js"></script>
    <script>
        // Handle avatar upload preview
        const avatarUpload = document.getElementById('avatar-upload');
        const profilePreview = document.getElementById('profile-preview');
        const avatarSubmit = document.getElementById('avatar-submit');
        
        avatarUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size should not exceed 5MB');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    // Submit form automatically
                    avatarSubmit.click();
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 