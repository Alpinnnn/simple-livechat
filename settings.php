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
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate input
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All password fields are required";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match";
        } elseif (strlen($new_password) < 6) {
            $error_message = "New password must be at least 6 characters";
        } else {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $update_query = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $success_message = "Password changed successfully";
                } else {
                    $error_message = "Failed to change password";
                }
                
                $update_stmt->close();
            } else {
                $error_message = "Current password is incorrect";
            }
        }
    } elseif (isset($_POST['update_avatar']) && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        
        // Check for errors
        if ($file['error'] === 0) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $file['tmp_name']);
            finfo_close($file_info);
            
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
    <title>Settings - WhatsApp Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="settings-container">
        <div class="settings-header">
            <a href="chat.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
            <h1>Settings</h1>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label">Dark Mode</span>
            </div>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="settings-section">
            <h2>Profile Settings</h2>
            
            <div class="profile-avatar">
                <img src="assets/images/<?php echo !empty($user['profile_image']) && $user['profile_image'] !== 'default-avatar.png' ? 'avatars/' . $user['profile_image'] : 'default-avatar.png'; ?>" alt="Profile Picture">
                
                <form method="POST" enctype="multipart/form-data" class="avatar-form">
                    <label for="avatar-upload" class="avatar-upload-label">
                        <i class="fas fa-camera"></i>
                        <span>Change Picture</span>
                    </label>
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;">
                    <input type="submit" name="update_avatar" value="Upload" id="avatar-submit" style="display: none;">
                </form>
            </div>
            
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="@<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    <small>Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status Message</label>
                    <textarea id="status" name="status" rows="2"><?php echo htmlspecialchars($user['status']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="update_profile">Save Profile</button>
                </div>
            </form>
        </div>
        
        <div class="settings-section">
            <h2>Change Password</h2>
            
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <button type="submit" name="change_password">Change Password</button>
                </div>
            </form>
        </div>
        
        <div class="settings-section">
            <h2>Account Actions</h2>
            
            <div class="account-actions">
                <a href="profile.php" class="btn-profile">Edit Profile</a>
                <a href="auth/logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>
    
    <script src="assets/js/theme.js"></script>
    <script>
        // Handle avatar upload
        document.getElementById('avatar-upload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                document.getElementById('avatar-submit').click();
            }
        });
    </script>
</body>
</html> 