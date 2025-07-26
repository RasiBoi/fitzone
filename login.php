<?php
/**
 * FitZone Fitness Center
 * Login Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Set current page for active menu highlighting
$current_page = 'login.php';

// Store redirect URL if provided
$redirect_to = '';
if (isset($_GET['redirect_to'])) {
    $redirect_to = sanitize($_GET['redirect_to']);
    // Store in session to preserve after POST
    $_SESSION['redirect_after_login'] = $redirect_to;
} elseif (isset($_SESSION['redirect_after_login'])) {
    $redirect_to = $_SESSION['redirect_after_login'];
}

// If user is already logged in, redirect to appropriate page
if (isLoggedIn()) {
    $userRole = getUserRole();
    
    // If there's a redirect URL, use it
    if (!empty($redirect_to)) {
        redirect($redirect_to);
    } else {
        // Otherwise redirect based on role
        if ($userRole === 'admin') {
            redirect('backend/admin/index.php');
        } elseif ($userRole === 'trainer') {
            redirect('backend/trainer/index.php');
        } else {
            redirect('backend/member/index.php');
        }
    }
}

// Process login form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $username = sanitize(isset($_POST['username']) ? $_POST['username'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate input
    if (empty($username)) {
        $error = 'Username or email is required.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } else {
        // Connect directly to database for login
        $db = getDb();
        
        // Check if input is email or username
        $field = isValidEmail($username) ? 'email' : 'username';
        
        // Get user data
        $user = $db->fetchSingle(
            "SELECT id, username, email, password, role, is_active FROM users WHERE $field = ?",
            [$username]
        );
        
        // Check if user exists and verify password
        if (!$user) {
            $error = 'Invalid username or password';
        } elseif (!$user['is_active']) {
            $error = 'Your account is not active. Please contact support.';
        } elseif (!verifyPassword($password, $user['password'])) {
            $error = 'Invalid username or password';
        } else {
            // Update last login time
            $db->query(
                "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                [$user['id']]
            );
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Set remember me cookie ONLY if requested
            if ($remember) {
                // Store the cookie securely 
                $selector = bin2hex(random_bytes(16));
                $token = bin2hex(random_bytes(32));
                $expires = time() + (86400 * 30); // 30 days
                
                setcookie(
                    'remember_me',
                    $selector . ':' . $token,
                    [
                        'expires' => $expires,
                        'path' => '/',
                        'domain' => '',
                        'secure' => !empty($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
                
                // Also store in database for proper validation
                $db->query(
                    "INSERT INTO user_remember_tokens (user_id, selector, token_hash, expires) 
                     VALUES (?, ?, ?, ?)",
                    [
                        $user['id'],
                        $selector,
                        hash('sha256', $token),
                        date('Y-m-d H:i:s', $expires)
                    ]
                );
            } else {
                // If remember not checked, clear any existing remember cookies
                if (isset($_COOKIE['remember_me'])) {
                    setcookie(
                        'remember_me',
                        '',
                        [
                            'expires' => time() - 3600,
                            'path' => '/',
                            'domain' => '',
                            'secure' => !empty($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]
                    );
                }
                
                // Remove any existing tokens from database
                $db->query(
                    "DELETE FROM user_remember_tokens WHERE user_id = ?",
                    [$user['id']]
                );
            }
            
            // Log the action
            logAction('User login', "User {$user['username']} logged in", $user['id']);
            
            // Set success message
            setFlashMessage('success', 'Login successful! Welcome back.');
            
            // Check if there's a redirect URL to use after login
            if (!empty($redirect_to)) {
                // Clear the redirect session variable 
                unset($_SESSION['redirect_after_login']);
                redirect($redirect_to);
            } else {
                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    redirect('backend/admin/index.php');
                } elseif ($user['role'] === 'trainer') {
                    redirect('backend/trainer/index.php');
                } else {
                    redirect('backend/member/index.php');
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <style>
        body {
            background: url('assets/images/login-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .card {
            background-color: rgba(18, 18, 18, 0.95);
            border: 1px solid var(--dark-border);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
        }
        
        .card-body {
            color: var(--text-primary);
        }
        
        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .input-group-text {
            background-color: rgba(244, 137, 21, 0.2);
            border: none;
            color: var(--primary);
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            padding: 0.75rem 1rem;
            height: auto;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(244, 137, 21, 0.3);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .text-muted {
            color: #fff !important;
        }
        
        /* Button styling */
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover, 
        .btn-primary:focus {
            background: var(--primary-dark);
            box-shadow: 0 0 10px rgba(244, 137, 21, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            color: rgba(255, 255, 255, 0.5);
            border-color: transparent;
            background-color: transparent;
        }
        
        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus {
            color: var(--primary);
            background-color: transparent;
        }
        
        #togglePassword {
            background-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            border: none;
            padding: 0.75rem 1rem;
        }
        
        #togglePassword:hover,
        #togglePassword:focus {
            color: var(--primary);
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .header-popup {
            position: fixed;
            top: 60px; /* Position it right under the Back to Home button */
            right: 15px;
            min-width: 280px;
            max-width: 350px;
            padding: 12px 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 9;
        }
        
        .header-popup.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .header-popup.success {
            background-color: #28a745;
            color: #fff;
        }
        
        .header-popup.error {
            background-color: #dc3545;
            color: #fff;
        }
        
        .header-popup-icon {
            margin-right: 10px;
        }
        
        .header-popup-message {
            font-size: 14px;
        }
    </style>
</head>
<body>

<a href="index.php" class="btn btn-outline-light position-absolute" style="top: 15px; right: 15px; z-index: 10;">
    <i class="fas fa-home me-1"></i> Back to Home
</a>

<div id="header-popup-container">
    <!-- Popup notifications will be inserted here via JavaScript -->
</div>

<div class="login-container">
    <div class="card shadow border-0">
        <div class="card-body p-4 p-md-5 position-relative">
            
            <div class="text-center mb-4">
                <img src="<?php echo SITE_URL; ?>assets/images/fitzone.png" alt="FitZone" class="mb-3" style="height: 45px;">
                <h3 class="card-title mb-1">Welcome Back</h3>
                <p class="text-muted">Sign in to your account</p>
            </div>
            
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" 
                            placeholder="Enter your username or email" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="login.php" class="text-primary small">Forgot Password?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-dark"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                            placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary py-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="signup.php" class="text-primary fw-bold">Sign Up</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    <?php if (!empty($error)): ?>
    // Display error popup
    showHeaderPopup('<?php echo addslashes($error); ?>', 'error');
    <?php endif; ?>
    
    <?php 
    $messages = getFlashMessages();
    foreach ($messages as $message): 
        $type = $message['type'] === 'success' ? 'success' : 'error';
    ?>
    // Display flash message popup
    showHeaderPopup('<?php echo addslashes($message['message']); ?>', '<?php echo $type; ?>');
    <?php endforeach; ?>
    
    // Function to show header popup messages
    function showHeaderPopup(message, type) {
        const container = document.getElementById('header-popup-container');
        const popup = document.createElement('div');
        popup.className = `header-popup ${type}`;
        
        // Set icon based on type
        let icon = '';
        if (type === 'success') {
            icon = '<i class="fas fa-check-circle header-popup-icon"></i>';
        } else if (type === 'error') {
            icon = '<i class="fas fa-exclamation-circle header-popup-icon"></i>';
        }
        
        popup.innerHTML = `
            ${icon}
            <div class="header-popup-message">${message}</div>
        `;
        
        container.appendChild(popup);
        
        // Show popup with a small delay
        setTimeout(() => {
            popup.classList.add('show');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                popup.classList.remove('show');
                
                // Remove from DOM after animation completes
                setTimeout(() => {
                    container.removeChild(popup);
                }, 500);
            }, 5000);
        }, 100);
    }
});
</script>
</body>
</html>