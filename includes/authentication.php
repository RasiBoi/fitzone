<?php
/**
 * FitZone Fitness Center
 * Authentication System
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

/**
 * Authentication Class
 * Manages user authentication, registration, and session handling
 */
class Auth {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = getDb();
        
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            initializeSession();
        }
        
        // If user is logged in, get their information
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $user = $this->db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
            
            // Check if user has an active membership (only for members)
            if (isset($user['role']) && $user['role'] == ROLE_MEMBER) {
                $has_membership = hasActiveMembership($user_id);
                $days_remaining = $has_membership ? getMembershipDaysRemaining($user_id) : false;
                
                // Add membership status to user array for templates
                $user['membership_status'] = $has_membership ? 'active' : 'inactive';
                $user['membership_days'] = $days_remaining;
                
                // Save membership info in session for easy access
                $_SESSION['has_membership'] = $has_membership ? true : false;
                $_SESSION['membership_days'] = $days_remaining;
            }
        }
    }
    
    /**
     * Register a new user
     *
     * @param array $userData User data including username, email, password
     * @param string $role User role (default: member)
     * @return array Result with status and message
     */
    public function register($userData, $role = ROLE_MEMBER) {
        try {
            // Validate required fields
            $requiredFields = ['username', 'email', 'password', 'confirm_password'];
            foreach ($requiredFields as $field) {
                if (empty($userData[$field])) {
                    return ['status' => false, 'message' => 'All fields are required'];
                }
            }
            
            // Validate email format
            if (!isValidEmail($userData['email'])) {
                return ['status' => false, 'message' => 'Invalid email format'];
            }
            
            // Validate password strength
            if (!isStrongPassword($userData['password'])) {
                return ['status' => false, 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, and numbers'];
            }
            
            // Check if passwords match
            if ($userData['password'] !== $userData['confirm_password']) {
                return ['status' => false, 'message' => 'Passwords do not match'];
            }
            
            // Check if username already exists
            $existingUser = $this->db->fetchSingle(
                "SELECT user_id FROM users WHERE username = ?",
                [$userData['username']]
            );
            
            if ($existingUser) {
                return ['status' => false, 'message' => 'Username is already taken'];
            }
            
            // Check if email already exists
            $existingEmail = $this->db->fetchSingle(
                "SELECT user_id FROM users WHERE email = ?",
                [$userData['email']]
            );
            
            if ($existingEmail) {
                return ['status' => false, 'message' => 'Email address is already registered'];
            }
            
            // Generate email verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Hash the password
            $hashedPassword = hashPassword($userData['password']);
            
            // Start transaction
            $conn = $this->db->getConnection();
            $conn->beginTransaction();
            
            try {
                // Insert user data
                $this->db->query(
                    "INSERT INTO users (username, email, password, role, reset_token) VALUES (?, ?, ?, ?, ?)",
                    [
                        $userData['username'],
                        $userData['email'],
                        $hashedPassword,
                        $role,
                        $verificationToken
                    ]
                );
                
                $userId = $conn->lastInsertId();
                
                // Create user profile
                $this->db->query(
                    "INSERT INTO user_profiles (user_id, first_name, last_name) VALUES (?, ?, ?)",
                    [
                        $userId,
                        isset($userData['first_name']) ? $userData['first_name'] : '',
                        isset($userData['last_name']) ? $userData['last_name'] : ''
                    ]
                );
                
                // Commit transaction
                $conn->commit();
                
                // Send verification email (implementation depends on your email system)
                $this->sendVerificationEmail($userData['email'], $verificationToken);
                
                logAction('User registered', "User {$userData['username']} registered", $userId);
                
                return [
                    'status' => true,
                    'message' => 'Registration successful! Please check your email to verify your account.',
                    'user_id' => $userId
                ];
                
            } catch (Exception $e) {
                // Roll back transaction on error
                $conn->rollBack();
                error_log('Registration error: ' . $e->getMessage());
                return ['status' => false, 'message' => 'Registration failed. Please try again later.'];
            }
            
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred during registration'];
        }
    }
    
    /**
     * Send verification email to user
     *
     * @param string $email User email
     * @param string $token Verification token
     * @return bool Success status
     */
    private function sendVerificationEmail($email, $token) {
        // This is a placeholder for email functionality
        // Implement using your preferred email library
        
        $verifyUrl = SITE_URL . 'verify.php?email=' . urlencode($email) . '&token=' . $token;
        
        $subject = SITE_NAME . ' - Verify Your Email Address';
        
        $message = "Hello,\n\n";
        $message .= "Thank you for registering with " . SITE_NAME . ".\n\n";
        $message .= "Please click the link below to verify your email address:\n";
        $message .= $verifyUrl . "\n\n";
        $message .= "If you did not create this account, please ignore this email.\n\n";
        $message .= "Regards,\n";
        $message .= SITE_NAME . " Team";
        
        // For development purposes, log the verification URL
        if ($_SERVER['SERVER_NAME'] === 'localhost' || startsWith($_SERVER['SERVER_NAME'], '192.168.')) {
            error_log('Verification URL: ' . $verifyUrl);
            return true;
        }
        
        // Send email (replace with your email sending code)
        $headers = 'From: ' . ADMIN_EMAIL . "\r\n" .
                   'Reply-To: ' . ADMIN_EMAIL . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
        
        return mail($email, $subject, $message, $headers);
    }
    
    /**
     * Verify user email
     *
     * @param string $email User email
     * @param string $token Verification token
     * @return array Result with status and message
     */
    public function verifyEmail($email, $token) {
        try {
            // Find user with matching email and token
            $user = $this->db->fetchSingle(
                "SELECT user_id, email_verified FROM users WHERE email = ? AND reset_token = ?",
                [$email, $token]
            );
            
            if (!$user) {
                return ['status' => false, 'message' => 'Invalid verification link'];
            }
            
            // Check if already verified
            if ($user['email_verified']) {
                return ['status' => true, 'message' => 'Email already verified. You can log in now.'];
            }
            
            // Update user as verified
            $this->db->query(
                "UPDATE users SET email_verified = 1, reset_token = NULL WHERE user_id = ?",
                [$user['user_id']]
            );
            
            logAction('Email verified', "Email verified for user ID {$user['user_id']}", $user['user_id']);
            
            return ['status' => true, 'message' => 'Email verified successfully! You can now log in.'];
            
        } catch (Exception $e) {
            error_log('Email verification error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred during verification'];
        }
    }
    
    /**
     * Log in a user
     *
     * @param string $username Username or email
     * @param string $password Password
     * @param bool $remember Remember login
     * @return array Result with status and message
     */
    public function login($username, $password, $remember = false) {
        try {
            // Check if input is email or username
            $field = isValidEmail($username) ? 'email' : 'username';
            
            // Get user data
            $user = $this->db->fetchSingle(
                "SELECT id, username, email, password, first_name, last_name, role, is_active FROM users WHERE $field = ?",
                [$username]
            );
            
            // Check if user exists
            if (!$user) {
                return ['status' => false, 'message' => 'Invalid username or password'];
            }
            
            // Check if account is active
            if (!$user['is_active']) {
                return ['status' => false, 'message' => 'Your account is not active. Please contact support.'];
            }
            
            // Verify password
            if (!verifyPassword($password, $user['password'])) {
                return ['status' => false, 'message' => 'Invalid username or password'];
            }
            
            // Update last login time
            $this->db->query(
                "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                [$user['id']]
            );
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Set remember me cookie if requested
            if ($remember) {
                $this->setRememberMeCookie($user['id']);
            }
            
            logAction('User login', "User {$user['username']} logged in", $user['id']);
            
            return [
                'status' => true, 
                'message' => 'Login successful', 
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ];
            
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred during login'];
        }
    }
    
    /**
     * Set remember me cookie
     *
     * @param int $userId User ID
     * @return void
     */
    private function setRememberMeCookie($userId) {
        // Generate a random selector and token
        $selector = bin2hex(random_bytes(16));
        $token = bin2hex(random_bytes(32));
        
        // Calculate expiry (30 days)
        $expires = date('Y-m-d H:i:s', time() + 2592000);
        
        // Delete any existing remember me tokens for this user
        $this->db->query(
            "DELETE FROM user_remember_tokens WHERE user_id = ?",
            [$userId]
        );
        
        // Store the new token in the database
        $this->db->query(
            "INSERT INTO user_remember_tokens (user_id, selector, token_hash, expires) 
             VALUES (?, ?, ?, ?)",
            [
                $userId,
                $selector,
                hash('sha256', $token),
                $expires
            ]
        );
        
        // Set the cookie
        $cookieValue = $selector . ':' . $token;
        $cookieExpiry = time() + 2592000;
        
        setcookie(
            'remember_me',
            $cookieValue,
            [
                'expires' => $cookieExpiry,
                'path' => '/',
                'domain' => '',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
    
    /**
     * Check remember me cookie and log in user if valid
     *
     * @return bool True if user was logged in, false otherwise
     */
    public function checkRememberMe() {
        // Check if the user is already logged in
        if (isLoggedIn()) {
            return true;
        }
        
        // Check if the remember me cookie exists
        if (empty($_COOKIE['remember_me'])) {
            return false;
        }
        
        // Parse the cookie value
        list($selector, $token) = explode(':', $_COOKIE['remember_me']);
        
        if (empty($selector) || empty($token)) {
            $this->clearRememberMeCookie();
            return false;
        }
        
        // Look up the token in the database
        $tokenData = $this->db->fetchSingle(
            "SELECT user_id, token_hash, expires FROM user_remember_tokens WHERE selector = ?",
            [$selector]
        );
        
        // Check if token exists and is not expired
        if (!$tokenData || strtotime($tokenData['expires']) < time()) {
            $this->clearRememberMeCookie();
            return false;
        }
        
        // Verify the token
        if (!hash_equals($tokenData['token_hash'], hash('sha256', $token))) {
            $this->clearRememberMeCookie();
            return false;
        }
        
        // Get user data
        $user = $this->db->fetchSingle(
            "SELECT user_id, username, role, status, email_verified FROM users WHERE user_id = ? AND status = 'active'",
            [$tokenData['user_id']]
        );
        
        if (!$user || !$user['email_verified']) {
            $this->clearRememberMeCookie();
            return false;
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        // Update last login time
        $this->db->query(
            "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?",
            [$user['user_id']]
        );
        
        // Generate a new remember me token for security
        $this->setRememberMeCookie($user['user_id']);
        
        logAction('Auto login', "User {$user['username']} logged in via remember me cookie", $user['user_id']);
        
        return true;
    }
    
    /**
     * Clear remember me cookie
     *
     * @return void
     */
    private function clearRememberMeCookie() {
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
    
    /**
     * Log out the current user
     *
     * @return void
     */
    public function logout() {
        // Log the action before destroying session
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
            logAction('User logout', "User $username logged out", $userId);
            
            // Clear remember me tokens for this user
            $this->db->query(
                "DELETE FROM user_remember_tokens WHERE user_id = ?",
                [$userId]
            );
        }
        
        // Clear the remember me cookie
        $this->clearRememberMeCookie();
        
        // Destroy the session
        destroySession();
    }
    
    /**
     * Request password reset
     *
     * @param string $email User email
     * @return array Result with status and message
     */
    public function requestPasswordReset($email) {
        try {
            // Check if email exists
            $user = $this->db->fetchSingle(
                "SELECT user_id, username, email, status FROM users WHERE email = ?",
                [$email]
            );
            
            if (!$user) {
                // For security, don't reveal that the email doesn't exist
                return ['status' => true, 'message' => 'If your email address exists in our database, you will receive a password recovery link shortly.'];
            }
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                return ['status' => true, 'message' => 'If your email address exists in our database, you will receive a password recovery link shortly.'];
            }
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
            
            // Update user with reset token
            $this->db->query(
                "UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?",
                [$token, $expires, $user['user_id']]
            );
            
            // Send reset email
            $this->sendPasswordResetEmail($user['email'], $token);
            
            logAction('Password reset requested', "Password reset requested for user ID {$user['user_id']}");
            
            return ['status' => true, 'message' => 'If your email address exists in our database, you will receive a password recovery link shortly.'];
            
        } catch (Exception $e) {
            error_log('Password reset request error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred. Please try again later.'];
        }
    }
    
    /**
     * Send password reset email
     *
     * @param string $email User email
     * @param string $token Reset token
     * @return bool Success status
     */
    private function sendPasswordResetEmail($email, $token) {
        // This is a placeholder for email functionality
        
        $resetUrl = SITE_URL . 'reset_password.php?email=' . urlencode($email) . '&token=' . $token;
        
        $subject = SITE_NAME . ' - Password Reset Request';
        
        $message = "Hello,\n\n";
        $message .= "You have requested to reset your password for your account at " . SITE_NAME . ".\n\n";
        $message .= "Please click the link below to reset your password:\n";
        $message .= $resetUrl . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request this password reset, please ignore this email.\n\n";
        $message .= "Regards,\n";
        $message .= SITE_NAME . " Team";
        
        // For development purposes, log the reset URL
        if ($_SERVER['SERVER_NAME'] === 'localhost' || startsWith($_SERVER['SERVER_NAME'], '192.168.')) {
            error_log('Password reset URL: ' . $resetUrl);
            return true;
        }
        
        // Send email (replace with your email sending code)
        $headers = 'From: ' . ADMIN_EMAIL . "\r\n" .
                   'Reply-To: ' . ADMIN_EMAIL . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
        
        return mail($email, $subject, $message, $headers);
    }
    
    /**
     * Verify password reset token
     *
     * @param string $email User email
     * @param string $token Reset token
     * @return bool True if token is valid, false otherwise
     */
    public function verifyResetToken($email, $token) {
        try {
            $user = $this->db->fetchSingle(
                "SELECT user_id, reset_expires FROM users WHERE email = ? AND reset_token = ?",
                [$email, $token]
            );
            
            if (!$user) {
                return false;
            }
            
            // Check if token has expired
            return strtotime($user['reset_expires']) >= time();
            
        } catch (Exception $e) {
            error_log('Reset token verification error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reset user password
     *
     * @param string $email User email
     * @param string $token Reset token
     * @param string $password New password
     * @param string $confirmPassword Confirm password
     * @return array Result with status and message
     */
    public function resetPassword($email, $token, $password, $confirmPassword) {
        try {
            // Verify token first
            if (!$this->verifyResetToken($email, $token)) {
                return ['status' => false, 'message' => 'Invalid or expired reset link'];
            }
            
            // Check if passwords match
            if ($password !== $confirmPassword) {
                return ['status' => false, 'message' => 'Passwords do not match'];
            }
            
            // Validate password strength
            if (!isStrongPassword($password)) {
                return ['status' => false, 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, and numbers'];
            }
            
            // Get user ID
            $user = $this->db->fetchSingle(
                "SELECT user_id, username FROM users WHERE email = ? AND reset_token = ?",
                [$email, $token]
            );
            
            if (!$user) {
                return ['status' => false, 'message' => 'Invalid reset request'];
            }
            
            // Hash new password
            $hashedPassword = hashPassword($password);
            
            // Update user password and clear reset token
            $this->db->query(
                "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE user_id = ?",
                [$hashedPassword, $user['user_id']]
            );
            
            logAction('Password reset', "Password reset for user ID {$user['user_id']}", $user['user_id']);
            
            return ['status' => true, 'message' => 'Password has been reset successfully. You can now log in with your new password.'];
            
        } catch (Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while resetting your password'];
        }
    }
    
    /**
     * Change user password (when logged in)
     *
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @param string $confirmPassword Confirm new password
     * @return array Result with status and message
     */
    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        try {
            // Get user data
            $user = $this->db->fetchSingle(
                "SELECT user_id, password FROM users WHERE user_id = ?",
                [$userId]
            );
            
            if (!$user) {
                return ['status' => false, 'message' => 'User not found'];
            }
            
            // Verify current password
            if (!verifyPassword($currentPassword, $user['password'])) {
                return ['status' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Check if new passwords match
            if ($newPassword !== $confirmPassword) {
                return ['status' => false, 'message' => 'New passwords do not match'];
            }
            
            // Validate password strength
            if (!isStrongPassword($newPassword)) {
                return ['status' => false, 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, and numbers'];
            }
            
            // Hash new password
            $hashedPassword = hashPassword($newPassword);
            
            // Update password
            $this->db->query(
                "UPDATE users SET password = ? WHERE user_id = ?",
                [$hashedPassword, $userId]
            );
            
            logAction('Password changed', "Password changed for user ID {$userId}", $userId);
            
            return ['status' => true, 'message' => 'Password changed successfully'];
            
        } catch (Exception $e) {
            error_log('Password change error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while changing your password'];
        }
    }
    
    /**
     * Get user data by ID
     *
     * @param int $userId User ID
     * @return array|false User data or false if not found
     */
    public function getUserById($userId) {
        try {
            return $this->db->fetchSingle(
                "SELECT u.user_id, u.username, u.email, u.role, u.status, u.registration_date, u.last_login,
                        p.first_name, p.last_name, p.phone, p.address, p.dob, p.gender, 
                        p.emergency_contact, p.profile_picture
                FROM users u
                LEFT JOIN user_profiles p ON u.user_id = p.user_id
                WHERE u.user_id = ?",
                [$userId]
            );
        } catch (Exception $e) {
            error_log('Get user error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get current user data
     *
     * @return array|false User data or false if not logged in
     */
    public function getCurrentUser() {
        if (!isLoggedIn()) {
            return false;
        }
        
        return $this->getUserById($_SESSION['user_id']);
    }
    
    /**
     * Update user profile
     *
     * @param int $userId User ID
     * @param array $profileData Profile data
     * @return array Result with status and message
     */
    public function updateProfile($userId, $profileData) {
        try {
            // First, clean the input data
            $data = sanitizeArray($profileData);
            
            // Update user profile
            $this->db->query(
                "UPDATE user_profiles SET 
                    first_name = ?,
                    last_name = ?,
                    phone = ?,
                    address = ?,
                    dob = ?,
                    gender = ?,
                    emergency_contact = ?,
                    bio = ?
                WHERE user_id = ?",
                [
                    isset($data['first_name']) ? $data['first_name'] : '',
                    isset($data['last_name']) ? $data['last_name'] : '',
                    isset($data['phone']) ? $data['phone'] : null,
                    isset($data['address']) ? $data['address'] : null,
                    isset($data['dob']) ? $data['dob'] : null,
                    isset($data['gender']) ? $data['gender'] : null,
                    isset($data['emergency_contact']) ? $data['emergency_contact'] : null,
                    isset($data['bio']) ? $data['bio'] : null,
                    $userId
                ]
            );
            
            logAction('Profile updated', "Profile updated for user ID {$userId}", $userId);
            
            return ['status' => true, 'message' => 'Profile updated successfully'];
            
        } catch (Exception $e) {
            error_log('Profile update error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while updating your profile'];
        }
    }
    
    /**
     * Update user email
     *
     * @param int $userId User ID
     * @param string $newEmail New email address
     * @param string $password User's current password for verification
     * @return array Result with status and message
     */
    public function updateEmail($userId, $newEmail, $password) {
        try {
            // Validate email format
            if (!isValidEmail($newEmail)) {
                return ['status' => false, 'message' => 'Invalid email format'];
            }
            
            // Get user data
            $user = $this->db->fetchSingle(
                "SELECT password FROM users WHERE user_id = ?",
                [$userId]
            );
            
            if (!$user) {
                return ['status' => false, 'message' => 'User not found'];
            }
            
            // Verify password
            if (!verifyPassword($password, $user['password'])) {
                return ['status' => false, 'message' => 'Password is incorrect'];
            }
            
            // Check if email is already in use by another user
            $existingEmail = $this->db->fetchSingle(
                "SELECT user_id FROM users WHERE email = ? AND user_id != ?",
                [$newEmail, $userId]
            );
            
            if ($existingEmail) {
                return ['status' => false, 'message' => 'Email address is already in use'];
            }
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Update email and set as unverified
            $this->db->query(
                "UPDATE users SET email = ?, email_verified = 0, reset_token = ? WHERE user_id = ?",
                [$newEmail, $verificationToken, $userId]
            );
            
            // Send verification email
            $this->sendVerificationEmail($newEmail, $verificationToken);
            
            logAction('Email updated', "Email updated for user ID {$userId}", $userId);
            
            return [
                'status' => true, 
                'message' => 'Email updated. Please check your new email address to verify it.'
            ];
            
        } catch (Exception $e) {
            error_log('Email update error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while updating your email'];
        }
    }
    
    /**
     * Update profile picture
     *
     * @param int $userId User ID
     * @param array $fileData File data from $_FILES
     * @return array Result with status and message
     */
    public function updateProfilePicture($userId, $fileData) {
        try {
            // Upload directory
            $uploadDir = UPLOADS_PATH . '/profiles';
            
            // Upload image with resizing
            $result = uploadImage($fileData, $uploadDir, [300, 300]);
            
            if (!$result['status']) {
                return $result;
            }
            
            // Get old profile picture
            $user = $this->db->fetchSingle(
                "SELECT profile_picture FROM user_profiles WHERE user_id = ?",
                [$userId]
            );
            
            // Update profile with new image
            $this->db->query(
                "UPDATE user_profiles SET profile_picture = ? WHERE user_id = ?",
                [$result['filename'], $userId]
            );
            
            // Delete old profile picture if it exists
            if ($user && !empty($user['profile_picture'])) {
                $oldFile = $uploadDir . '/' . $user['profile_picture'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            logAction('Profile picture updated', "Profile picture updated for user ID {$userId}", $userId);
            
            return [
                'status' => true, 
                'message' => 'Profile picture updated successfully',
                'filename' => $result['filename']
            ];
            
        } catch (Exception $e) {
            error_log('Profile picture update error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'An error occurred while updating your profile picture'];
        }
    }
}

/**
 * Create authentication table if it doesn't exist
 */
function setupAuthTables() {
    $db = getDb();
    
    // Create remember me tokens table if it doesn't exist
    $db->query("CREATE TABLE IF NOT EXISTS user_remember_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        selector VARCHAR(255) NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        expires DATETIME NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX (selector),
        INDEX (expires)
    )");
}

// Initialize authentication tables
setupAuthTables();

// Create global auth instance
$auth = new Auth();

// Check for remember me cookie on initialization
$auth->checkRememberMe();
?>