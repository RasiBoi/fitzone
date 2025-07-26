<?php
/**
 * FitZone Fitness Center
 * Signup Page
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
$current_page = 'signup.php';

// If user is already logged in, redirect to appropriate page
if (isLoggedIn()) {
    $userRole = getUserRole();
    if ($userRole === 'admin') {
        redirect('backend/admin/index.php');
    } elseif ($userRole === 'trainer') {
        redirect('backend/trainer/index.php');
    } else {
        redirect('backend/member/index.php');
    }
}

// Process signup form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $username = sanitize(isset($_POST['username']) ? $_POST['username'] : '');
    $email = sanitize(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $first_name = sanitize(isset($_POST['first_name']) ? $_POST['first_name'] : '');
    $last_name = sanitize(isset($_POST['last_name']) ? $_POST['last_name'] : '');
    $role = sanitize(isset($_POST['role']) ? $_POST['role'] : 'member');
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validation
    if (empty($username)) {
        $error = 'Username is required.';
    } elseif (empty($email)) {
        $error = 'Email is required.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif ($password != $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($first_name) || empty($last_name)) {
        $error = 'First and last name are required.';
    } elseif (!in_array($role, ['member', 'trainer'])) {
        $error = 'Please select a valid role.';
    } elseif (!$terms) {
        $error = 'You must agree to the Terms and Conditions.';
    } else {
        // Connect to database
        $db = getDb();
        
        // Check if username already exists
        $existingUser = $db->fetchSingle(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existingUser) {
            $error = 'Username or email already exists.';
        } else {
            // Hash password
            $passwordHash = hashPassword($password);
            
            // Set user is_active to 1 for all users (both members and trainers)
            $is_active = 1;
            
            // Insert new user into database
            $result = $db->query(
                "INSERT INTO users (username, email, password, first_name, last_name, role, is_active, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                [$username, $email, $passwordHash, $first_name, $last_name, $role, $is_active]
            );
            
            if ($result) {
                $userId = $db->lastInsertId();
                
                // Log the action
                logAction('User registration', "New user registered: $username with role: $role", $userId);
                
                // Set success message
                setFlashMessage('success', 'Registration successful! You can now log in.');
                
                // Redirect to login page
                redirect('login.php');
            } else {
                $error = 'An error occurred during registration. Please try again.';
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
    <title>Sign Up - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <style>
        body {
            background: url('assets/images/signup-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 2rem 0;
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
        
        .signup-container {
            max-width: 550px;
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
            background-color: rgba(0, 0, 0, 1);
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
        
        /* Fix for dropdown background color */
        select.form-control {
            background-color: #000;
            color: #fff;
            -webkit-appearance: none;
            appearance: none;
        }
        
        select.form-control option {
            background-color: #000;
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
        
        #togglePassword, #toggleConfirmPassword {
            background-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            border: none;
            padding: 0.75rem 1rem;
        }
        
        #togglePassword:hover, #toggleConfirmPassword:hover,
        #togglePassword:focus, #toggleConfirmPassword:focus {
            color: var(--primary);
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        /* Header popup notification positioning */
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

<div class="signup-container">
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="<?php echo SITE_URL; ?>assets/images/fitzone.png" alt="FitZone" class="mb-3" style="height: 45px;">
                <h3 class="card-title mb-1">Create an Account</h3>
                <p class="text-muted">Join FitZone Fitness Center today</p>
            </div>
            
            <form action="signup.php" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                placeholder="Enter first name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                placeholder="Enter last name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                        <input type="text" class="form-control" id="username" name="username" 
                            placeholder="Choose a username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                            placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">I want to join as</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-control" id="role" name="role" required>
                            <option value="member" <?php echo (isset($_POST['role']) && $_POST['role'] === 'member') ? 'selected' : ''; ?>>Member</option>
                            <option value="trainer" <?php echo (isset($_POST['role']) && $_POST['role'] === 'trainer') ? 'selected' : ''; ?>>Trainer</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                            placeholder="Create a password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                            placeholder="Confirm your password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary py-2">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold">Sign In</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility for password field
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
    
    // Toggle password visibility for confirm password field
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
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