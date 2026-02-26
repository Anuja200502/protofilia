<?php
/**
 * Protofilia Admin - Login Page
 */
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $result = supabase_sign_in($email, $password);
        
        if ($result['status'] === 200 && isset($result['data']['access_token'])) {
            $_SESSION['admin_token'] = $result['data']['access_token'];
            $_SESSION['admin_email'] = $result['data']['user']['email'] ?? $email;
            $_SESSION['admin_id'] = $result['data']['user']['id'] ?? '';
            set_flash('success', 'Welcome back! 👋');
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            $errorMsg = $result['data']['error_description'] ?? $result['data']['msg'] ?? $result['data']['message'] ?? 'Unknown error';
            $error = 'Login failed (HTTP ' . $result['status'] . '): ' . $errorMsg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <span class="logo-icon">
                        <i data-lucide="hexagon" class="logo-hex"></i>
                        <span class="logo-letter">A</span>
                    </span>
                    <span class="logo-text"><?php echo SITE_NAME; ?></span>
                </div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to manage your portfolio</p>
            </div>
            
            <?php if ($error): ?>
            <div class="login-error">
                <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="admin@example.com" required
                           value="<?php echo sanitize($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="log-in"></i> Sign In
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 28px;">
                <a href="<?php echo SITE_URL; ?>" style="color: var(--text-muted); font-size: 13px; text-decoration: none;">
                    <i data-lucide="arrow-left" style="width:14px;height:14px;vertical-align: middle;"></i> 
                    Back to Portfolio
                </a>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>
