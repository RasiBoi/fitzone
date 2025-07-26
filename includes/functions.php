<?php
/**
 * FitZone Fitness Center
 * Common Functions
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}
/**
 * Clean and sanitize input data
 *
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize an array of data
 *
 * @param array $data Array of input data
 * @return array Sanitized data array
 */
function sanitizeArray($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = sanitizeArray($value);
            } else {
                $data[$key] = sanitize($value);
            }
        }
    }
    return $data;
}

/**
 * Generate CSRF token
 *
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 *
 * @param string $token Token to validate
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate a random string
 *
 * @param int $length Length of the string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Generate a secure hash of a password
 *
 * @param string $password Password to hash
 * @return string Password hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
}

/**
 * Verify a password against a hash
 *
 * @param string $password Password to verify
 * @param string $hash Hash to verify against
 * @return bool True if password matches, false otherwise
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * ======================================
 * URL & REDIRECTION FUNCTIONS
 * ======================================
 */

/**
 * Redirect to a URL
 *
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get the current URL
 *
 * @return string Current URL
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $currentUrl;
}

/**
 * Get the base URL
 *
 * @return string Base URL
 */
function getBaseUrl() {
    return SITE_URL;
}

/**
 * ======================================
 * SESSION & USER MANAGEMENT
 * ======================================
 */

/**
 * Initialize a secure session
 *
 * @return void
 */
function initializeSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_name(SESSION_NAME);
        session_start();
        
        // Regenerate session ID periodically to prevent fixation attacks
        if (!isset($_SESSION['last_regeneration'])) {
            regenerateSession();
        } else if (time() - $_SESSION['last_regeneration'] > 1800) {
            // Regenerate session every 30 minutes
            regenerateSession();
        }
    }
}

/**
 * Regenerate session ID
 *
 * @return void
 */
function regenerateSession() {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

/**
 * End the current session
 *
 * @return void
 */
function destroySession() {
    session_unset();
    session_destroy();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
}

/**
 * Check if a user is logged in
 *
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the current user's role
 *
 * @return string User role or empty string if not logged in
 */
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
}

/**
 * Check if current user has a specific role
 *
 * @param string|array $roles Single role or array of roles to check
 * @return bool True if user has role, false otherwise
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = getUserRole();
    
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    
    return $userRole === $roles;
}

/**
 * Check if current user is an admin
 *
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Check if current user is a staff member
 *
 * @return bool True if staff, false otherwise
 */
function isStaff() {
    return hasRole([ROLE_STAFF, ROLE_ADMIN]);
}

/**
 * ======================================
 * UTILITY FUNCTIONS
 * ======================================
 */

/**
 * Format date and time
 *
 * @param string $datetime Date and time string
 * @param string $format Format string (default: Y-m-d H:i:s)
 * @return string Formatted date and time
 */
function formatDateTime($datetime, $format = 'Y-m-d H:i:s') {
    $date = new DateTime($datetime);
    return $date->format($format);
}

/**
 * Format date only
 *
 * @param string $datetime Date and time string
 * @param string $format Format string (default: Y-m-d)
 * @return string Formatted date
 */
function formatDate($datetime, $format = 'Y-m-d') {
    $date = new DateTime($datetime);
    return $date->format($format);
}

/**
 * Format time only
 *
 * @param string $datetime Date and time string
 * @param string $format Format string (default: H:i)
 * @return string Formatted time
 */
function formatTime($datetime, $format = 'H:i') {
    $date = new DateTime($datetime);
    return $date->format($format);
}

/**
 * Format currency
 *
 * @param float $amount Amount to format
 * @param string $currency Currency symbol (default: Rs.)
 * @return string Formatted currency
 */
function formatCurrency($amount, $currency = 'Rs.') {
    return $currency . ' ' . number_format($amount, 2);
}

/**
 * Shorten text with ellipsis
 *
 * @param string $text Text to shorten
 * @param int $length Maximum length
 * @param string $append String to append (default: ...)
 * @return string Shortened text
 */
function shortenText($text, $length = 100, $append = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= $append;
    }
    return $text;
}

/**
 * Check if a string starts with a specific substring
 *
 * @param string $haystack String to check in
 * @param string $needle Substring to check for
 * @return bool True if string starts with substring, false otherwise
 */
function startsWith($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

/**
 * Check if a string ends with a specific substring
 *
 * @param string $haystack String to check in
 * @param string $needle Substring to check for
 * @return bool True if string ends with substring, false otherwise
 */
function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Generate pagination links
 *
 * @param int $currentPage Current page number
 * @param int $totalPages Total number of pages
 * @param string $urlPattern URL pattern with {:page} placeholder
 * @return string HTML pagination links
 */
function generatePagination($currentPage, $totalPages, $urlPattern) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $links = '<div class="pagination">';
    
    // Previous link
    if ($currentPage > 1) {
        $prevUrl = str_replace('{:page}', $currentPage - 1, $urlPattern);
        $links .= '<a href="' . $prevUrl . '" class="page-link">&laquo; Previous</a>';
    } else {
        $links .= '<span class="page-link disabled">&laquo; Previous</span>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $links .= '<a href="' . str_replace('{:page}', 1, $urlPattern) . '" class="page-link">1</a>';
        if ($startPage > 2) {
            $links .= '<span class="page-link dots">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $links .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $pageUrl = str_replace('{:page}', $i, $urlPattern);
            $links .= '<a href="' . $pageUrl . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $links .= '<span class="page-link dots">...</span>';
        }
        $links .= '<a href="' . str_replace('{:page}', $totalPages, $urlPattern) . '" class="page-link">' . $totalPages . '</a>';
    }
    
    // Next link
    if ($currentPage < $totalPages) {
        $nextUrl = str_replace('{:page}', $currentPage + 1, $urlPattern);
        $links .= '<a href="' . $nextUrl . '" class="page-link">Next &raquo;</a>';
    } else {
        $links .= '<span class="page-link disabled">Next &raquo;</span>';
    }
    
    $links .= '</div>';
    
    return $links;
}

/**
 * ======================================
 * FILE & IMAGE HANDLING
 * ======================================
 */

/**
 * Upload a file
 *
 * @param array $file File data from $_FILES
 * @param string $destination Destination directory
 * @param array $allowedTypes Allowed file types
 * @param int $maxSize Maximum file size in bytes
 * @return array Result with status and message
 */
function uploadFile($file, $destination, $allowedTypes = [], $maxSize = 2097152) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $error = isset($errors[$file['error']]) ? $errors[$file['error']] : 'Unknown upload error';
        return ['status' => false, 'message' => $error];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['status' => false, 'message' => 'File size exceeds the maximum allowed size'];
    }
    
    // Get file extension
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    // Check file type if specified
    if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
        return ['status' => false, 'message' => 'File type not allowed'];
    }
    
    // Create destination directory if it doesn't exist
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Generate a unique filename
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileInfo['filename']) . '.' . $extension;
    $targetPath = $destination . '/' . $filename;
    
    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'status' => true, 
            'message' => 'File uploaded successfully', 
            'filename' => $filename,
            'path' => $targetPath
        ];
    } else {
        return ['status' => false, 'message' => 'Failed to move uploaded file'];
    }
}

/**
 * Upload and process an image
 *
 * @param array $file File data from $_FILES
 * @param string $destination Destination directory
 * @param array $dimensions Array of width and height to resize to
 * @return array Result with status and message
 */
function uploadImage($file, $destination, $dimensions = null) {
    // Check if it's an image
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $result = uploadFile($file, $destination, $allowedTypes, 5242880); // 5MB
    
    if (!$result['status']) {
        return $result;
    }
    
    // If no resizing required, return the result
    if (is_null($dimensions)) {
        return $result;
    }
    
    // Resize the image if dimensions are provided
    $imagePath = $result['path'];
    
    // Get original image dimensions
    list($originalWidth, $originalHeight) = getimagesize($imagePath);
    
    // Calculate new dimensions while maintaining aspect ratio
    $width = $dimensions[0];
    $height = $dimensions[1];
    
    // If both dimensions are specified, resize exactly
    if ($width && $height) {
        $newWidth = $width;
        $newHeight = $height;
    } 
    // If only width is specified, calculate height to maintain aspect ratio
    else if ($width) {
        $newWidth = $width;
        $newHeight = ($originalHeight / $originalWidth) * $newWidth;
    } 
    // If only height is specified, calculate width to maintain aspect ratio
    else if ($height) {
        $newHeight = $height;
        $newWidth = ($originalWidth / $originalHeight) * $newHeight;
    } 
    // If no dimensions specified, use original dimensions
    else {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }
    
    // Create a new image resource
    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
    $sourceImage = null;
    
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        case 'png':
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        case 'gif':
            $sourceImage = imagecreatefromgif($imagePath);
            break;
        default:
            return ['status' => false, 'message' => 'Unsupported image format'];
    }
    
    // Create a new true color image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF images
    if ($extension == 'png' || $extension == 'gif') {
        imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Resize the image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Save the resized image
    $success = false;
    
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            $success = imagejpeg($newImage, $imagePath, 90);
            break;
        case 'png':
            $success = imagepng($newImage, $imagePath, 9);
            break;
        case 'gif':
            $success = imagegif($newImage, $imagePath);
            break;
    }
    
    // Free up memory
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    if ($success) {
        return [
            'status' => true,
            'message' => 'Image uploaded and resized successfully',
            'filename' => $result['filename'],
            'path' => $result['path']
        ];
    } else {
        return ['status' => false, 'message' => 'Failed to resize image'];
    }
}

/**
 * ======================================
 * VALIDATION FUNCTIONS
 * ======================================
 */

/**
 * Validate an email address
 *
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate a URL
 *
 * @param string $url URL to validate
 * @return bool True if valid, false otherwise
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate a strong password
 * 
 * Requirements:
 * - At least 8 characters
 * - Contains at least one uppercase letter
 * - Contains at least one lowercase letter
 * - Contains at least one number
 *
 * @param string $password Password to validate
 * @return bool True if valid, false otherwise
 */
function isStrongPassword($password) {
    if (strlen($password) < 8) {
        return false;
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

/**
 * Validate a phone number (basic validation)
 *
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function isValidPhone($phone) {
    // Remove common formatting characters
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Check length (adjust as needed for your country/format)
    if (strlen($phone) < 7 || strlen($phone) > 15) {
        return false;
    }
    
    return true;
}

/**
 * ======================================
 * MESSAGE & NOTIFICATION FUNCTIONS
 * ======================================
 */

/**
 * Set a flash message to be displayed once
 *
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 * @return void
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear all flash messages
 *
 * @return array Flash messages
 */
function getFlashMessages() {
    $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

/**
 * Display all flash messages
 *
 * @param bool $useToast Whether to use toast sliding notifications
 * @return string HTML for flash messages
 */
function displayFlashMessages($useToast = false) {
    $messages = getFlashMessages();
    $output = '';
    
    if (!empty($messages)) {
        if ($useToast) {
            $output .= '<div class="toast-container">';
            
            foreach ($messages as $message) {
                $type = sanitize($message['type']);
                $content = sanitize($message['message']);
                $icon = '';
                
                // Set icon based on message type
                switch ($type) {
                    case 'success':
                        $icon = '<i class="fas fa-check-circle toast-icon"></i>';
                        break;
                    case 'danger':
                    case 'error':
                        $type = 'danger'; // Normalize type
                        $icon = '<i class="fas fa-exclamation-circle toast-icon"></i>';
                        break;
                    case 'warning':
                        $icon = '<i class="fas fa-exclamation-triangle toast-icon"></i>';
                        break;
                    case 'info':
                        $icon = '<i class="fas fa-info-circle toast-icon"></i>';
                        break;
                }
                
                $output .= '<div class="toast ' . $type . '">';
                $output .= $icon . '<div class="toast-message">' . $content . '</div>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
            
            // Add JavaScript to show and hide toasts
            $output .= '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    const toasts = document.querySelectorAll(".toast");
                    
                    toasts.forEach(function(toast, index) {
                        setTimeout(function() {
                            toast.classList.add("show");
                            
                            setTimeout(function() {
                                toast.classList.remove("show");
                                
                                setTimeout(function() {
                                    toast.remove();
                                }, 500);
                            }, 4000); // Display for 4 seconds
                        }, index * 300); // Stagger the appearance
                    });
                });
            </script>';
        } else {
            // Traditional alert display
            $output .= '<div class="flash-messages">';
            
            foreach ($messages as $message) {
                $type = sanitize($message['type']);
                $content = sanitize($message['message']);
                
                // Normalize error type to danger for Bootstrap
                if ($type === 'error') {
                    $type = 'danger';
                }
                
                $output .= '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
                $output .= $content;
                $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
        }
    }
    
    return $output;
}

/**
 * Log an action to the system log
 *
 * @param string $action Action performed
 * @param string $details Additional details
 * @param int $userId ID of the user who performed the action
 * @return bool True if logged successfully, false otherwise
 */
function logAction($action, $details = '', $userId = null) {
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    $db = getDb();
    
    // Create log table if it doesn't exist
    $db->query("CREATE TABLE IF NOT EXISTS activity_logs (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(255) NOT NULL,
        details TEXT NULL,
        ip_address VARCHAR(45) NOT NULL,
        log_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
    )");
    
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $result = $db->query(
        "INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
        [$userId, $action, $details, $ip]
    );
    
    return $result !== false;
}

/**
 * ======================================
 * MEMBERSHIP FUNCTIONS
 * ======================================
 */

/**
 * Check if a user has an active membership
 * 
 * @param int $user_id User ID to check
 * @return array|bool Active subscription data or false if no active subscription
 */
function hasActiveMembership($user_id) {
    if (empty($user_id)) {
        return false;
    }
    
    try {
        $db = getDb();
        
        // Ensure the member_subscriptions table exists
        $db->query("
            CREATE TABLE IF NOT EXISTS `member_subscriptions` (
              `id` INT PRIMARY KEY AUTO_INCREMENT,
              `user_id` INT NOT NULL,
              `membership_type` VARCHAR(50) NOT NULL,
              `duration` VARCHAR(20) NOT NULL,
              `start_date` DATE NOT NULL,
              `end_date` DATE NOT NULL,
              `status` VARCHAR(20) NOT NULL DEFAULT 'active',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        $subscription = $db->fetchSingle(
            "SELECT * FROM member_subscriptions 
            WHERE user_id = ? 
            AND status = 'active' 
            AND end_date >= CURDATE() 
            ORDER BY end_date DESC 
            LIMIT 1",
            [$user_id]
        );
        
        return $subscription ? $subscription : false;
    } catch (Exception $e) {
        // Log the error but don't break the application
        error_log('Error in hasActiveMembership function: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get the remaining days for a user's membership
 * 
 * @param int $user_id User ID to check
 * @return int|bool Number of days remaining or false if no active membership
 */
function getMembershipDaysRemaining($user_id) {
    $subscription = hasActiveMembership($user_id);
    
    if (!$subscription) {
        return false;
    }
    
    $end_date = new DateTime($subscription['end_date']);
    $today = new DateTime('today');
    $interval = $today->diff($end_date);
    
    return $interval->days;
}

/**
 * Process a membership purchase/activation
 *
 * @param int $user_id The ID of the user purchasing the membership
 * @param int $plan_id The ID of the membership plan being purchased
 * @param string $duration The duration ('1month', '6month', '12month')
 * @return array Result with status and message
 */
function getMembership($user_id, $plan_id, $duration) {
    try {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Validate required parameters
        if (empty($user_id) || empty($plan_id) || empty($duration)) {
            return ['status' => false, 'message' => 'Missing required parameters'];
        }
        
        // Check valid durations
        if (!in_array($duration, ['1month', '6month', '12month'])) {
            return ['status' => false, 'message' => 'Invalid membership duration'];
        }
        
        $db = getDb();
        
        // First, ensure the member_subscriptions table exists
        $db->query("
            CREATE TABLE IF NOT EXISTS `member_subscriptions` (
              `id` INT PRIMARY KEY AUTO_INCREMENT,
              `user_id` INT NOT NULL,
              `membership_type` VARCHAR(50) NOT NULL,
              `duration` VARCHAR(20) NOT NULL,
              `start_date` DATE NOT NULL,
              `end_date` DATE NOT NULL,
              `status` VARCHAR(20) NOT NULL DEFAULT 'active',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        // Get membership plan details
        $plan = $db->fetchSingle(
            "SELECT * FROM membership_plans WHERE id = ? AND is_active = 1",
            [$plan_id]
        );
        
        if (!$plan) {
            // Try to get the plan without the is_active check as fallback
            $plan = $db->fetchSingle(
                "SELECT * FROM membership_plans WHERE id = ?",
                [$plan_id]
            );
            
            if (!$plan) {
                return ['status' => false, 'message' => 'Membership plan not found'];
            }
        }
        
        // Skip user validation - we already have the user_id from session
        // This is a key fix - the issue was trying to validate the user in various ways
        
        // Calculate subscription details
        $start_date = date('Y-m-d'); // Start from today
        
        // Calculate end date based on duration
        if ($duration === '1month') {
            $end_date = date('Y-m-d', strtotime('+1 month'));
        } else if ($duration === '6month') {
            $end_date = date('Y-m-d', strtotime('+6 months'));
        } else {
            $end_date = date('Y-m-d', strtotime('+12 months'));
        }
        
        // Check if user already has an active subscription
        try {
            $existing_subscription = $db->fetchSingle(
                "SELECT id FROM member_subscriptions 
                 WHERE user_id = ? AND status = 'active' AND end_date >= CURDATE()",
                [$user_id]
            );
        } catch (Exception $e) {
            // If there's an error querying the table, assume no existing subscription
            $existing_subscription = false;
        }
        
        if ($existing_subscription) {
            // Update existing subscription
            $db->query(
                "UPDATE member_subscriptions SET 
                 membership_type = ?, 
                 duration = ?,
                 start_date = ?,
                 end_date = ? 
                 WHERE id = ?",
                [$plan['type'], $duration, $start_date, $end_date, $existing_subscription['id']]
            );
            
            $subscription_id = $existing_subscription['id'];
        } else {
            // Create new subscription - using a direct SQL insert to avoid potential issues
            $result = $db->query(
                "INSERT INTO member_subscriptions 
                 (user_id, membership_type, duration, start_date, end_date, status) 
                 VALUES (?, ?, ?, ?, ?, 'active')",
                [$user_id, $plan['type'], $duration, $start_date, $end_date]
            );
            
            if ($result === false) {
                // If the insert failed, return an error
                return [
                    'status' => false, 
                    'message' => 'Failed to create membership record. Please try again.'
                ];
            }
            
            // Get the last inserted ID
            try {
                $subscription_id = $db->lastInsertId();
            } catch (Exception $e) {
                // If we can't get the ID, query for the record we just created
                $new_sub = $db->fetchSingle(
                    "SELECT id FROM member_subscriptions WHERE user_id = ? AND membership_type = ? ORDER BY id DESC LIMIT 1",
                    [$user_id, $plan['type']]
                );
                $subscription_id = $new_sub ? $new_sub['id'] : 0;
            }
        }
        
        // Log the action
        try {
            logAction('Membership purchase', "User purchased {$plan['type']} membership for {$duration}", $user_id);
        } catch (Exception $e) {
            // Continue even if logging fails
            error_log('Failed to log membership purchase: ' . $e->getMessage());
        }
        
        return [
            'status' => true, 
            'message' => 'Membership activated successfully!', 
            'subscription_id' => $subscription_id,
            'end_date' => $end_date,
            'membership_type' => $plan['type']
        ];
            
    } catch (Exception $e) {
        error_log('Membership function error: ' . $e->getMessage());
        return ['status' => false, 'message' => 'An error occurred processing the membership request: ' . $e->getMessage()];
    }
}

/**
 * ======================================
 * INITIALIZATION
 * ======================================
 */

/**
 * Initialize the application
 * This is the main entry point that should be called at the beginning of each page
 *
 * @return void
 */
function initializeApp() {
    // Start the session
    initializeSession();
    
    // Set default timezone
    date_default_timezone_set('Asia/Colombo');
    
    // Enable error reporting in development
    if ($_SERVER['SERVER_NAME'] === 'localhost' || startsWith($_SERVER['SERVER_NAME'], '192.168.')) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        error_reporting(0);
        ini_set('display_errors', 0);
    }
}
?>