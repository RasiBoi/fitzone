<?php
/**
 * FitZone Fitness Center
 * Logout Script
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

// Get user ID for logging purposes
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown user';

// If user is logged in, log the logout action
if ($user_id) {
    // Connect to database
    $db = getDb();
    
    // Log the action
    logAction('User logout', "User {$username} logged out", $user_id);
    
    // Clear any remember me tokens if they exist
    if (isset($_COOKIE['remember_me'])) {
        // Remove from database
        list($selector) = explode(':', $_COOKIE['remember_me']);
        
        $db->query(
            "DELETE FROM user_remember_tokens WHERE selector = ?",
            [$selector]
        );
        
        // Clear cookie
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
}

// Define a simple logout function if it doesn't exist in the files
if (!function_exists('logout')) {
    function logout() {
        // Destroy the session
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
}

// Destroy the session
logout();

// Set a flash message for login page
setFlashMessage('success', 'You have been successfully logged out.');

// Redirect to login page
redirect('login.php');