<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: chat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WhatsApp Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>WhatsApp Clone</h1>
            <p>Create a new account</p>
            <div class="theme-toggle">
                <label class="switch">
                    <input type="checkbox" id="dark-mode-toggle">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        
        <div class="login-form">
            <form action="auth/register.php" method="POST">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="error"><?php echo $_GET['error']; ?></div>
                <?php } ?>
                
                <?php if (isset($_GET['success'])) { ?>
                    <div class="success"><?php echo $_GET['success']; ?></div>
                <?php } ?>
                
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username (Tag: @username)</label>
                    <div class="input-with-icon">
                        <span class="input-icon">@</span>
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="index.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/js/theme.js"></script>
</body>
</html> 