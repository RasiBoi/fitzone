<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = isset(<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings']) ? <?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings'] : [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = isset(<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings']) ? <?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings'] : [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = isset(<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings']) ? <?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings'] : [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = isset(<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings']) ? <?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings'] : [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = isset(<?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings']) ? <?php
/**
 * FitZone Fitness Center
 * Admin Settings Page
 */

// Define constant to allow inclusion of necessary files
define('FITZONE_APP', true);

// Include configuration and helper files
require_once '../../includes/config.php';
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../../includes/authentication.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initializeSession();
}

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Create settings table if it doesn't exist
try {
    $db->query("CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_group VARCHAR(50) NOT NULL,
        display_name VARCHAR(255) NOT NULL,
        field_type VARCHAR(20) NOT NULL DEFAULT 'text',
        options TEXT NULL,
        required BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings if they don't exist
    $default_settings = [
        // General settings
        ['site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, true],
        ['site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, false],
        ['contact_email', 'info@fitzone.com', 'general', 'Contact Email', 'email', NULL, true],
        ['contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, true],
        ['address', '123 Fitness Avenue, Kurunegala, Sri Lanka', 'general', 'Address', 'textarea', NULL, true],
        ['opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, true],
        
        // Email settings
        ['smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, false],
        ['smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, false],
        ['smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, false],
        ['smtp_password', '', 'email', 'SMTP Password', 'password', NULL, false],
        ['email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, false],
        ['email_from_address', 'noreply@fitzone.com', 'email', 'Email From Address', 'email', NULL, false],
        
        // Membership settings
        ['trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, false],
        ['max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, false],
        ['cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, false],
        
        // Social media
        ['facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, false],
        ['instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, false],
        ['twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, false],
        ['youtube_url', '', 'social', 'YouTube URL', 'url', NULL, false],
        
        // System settings
        ['maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, false],
        ['currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, true],
        ['date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', true],
        ['time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', true],
        ['pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, true],
    ];
    
    foreach ($default_settings as $setting) {
        $exists = $db->fetchSingle("SELECT id FROM system_settings WHERE setting_key = ?", [$setting[0]]);
        
        if (!$exists) {
            $db->query(
                "INSERT INTO system_settings (setting_key, setting_value, setting_group, display_name, field_type, options, required) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                $setting
            );
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different form submissions based on the action
    if (isset($_POST['save_general_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'General settings saved successfully!';
            logAction('Settings updated', "Admin updated general settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_email_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Special handling for password
                if ($key === 'smtp_password' && empty($value)) {
                    // Don't update empty password
                    continue;
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Email settings saved successfully!';
            logAction('Settings updated', "Admin updated email settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_membership_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Membership settings saved successfully!';
            logAction('Settings updated', "Admin updated membership settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_social_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'Social media settings saved successfully!';
            logAction('Settings updated', "Admin updated social media settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['save_system_settings'])) {
        try {
            $settings = $_POST['settings'] ?? [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>POST['settings'] : [];
            foreach ($settings as $key => $value) {
                // Handle boolean values
                if ($key === 'maintenance_mode') {
                    $value = isset($value) && $value === '1' ? '1' : '0';
                }
                
                // Sanitize
                $key = sanitize($key);
                $value = is_array($value) ? json_encode($value) : sanitize($value);
                
                // Update the setting
                $db->query(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
            }
            
            $success_message = 'System settings saved successfully!';
            logAction('Settings updated', "Admin updated system settings", $user_id);
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch current settings grouped by category
$settings = [];
$settings_query = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, display_name");

// Group settings by their category
foreach ($settings_query as $setting) {
    $group = $setting['setting_group'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $setting;
}

// Set page title
$page_title = 'System Settings';
$active_page = 'settings';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current tab
$current_tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - FitZone Fitness Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Common Dashboard styles -->
    <link href="../../assets/css/dashboard.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <style>
        .settings-card {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .settings-card .card-header {
            font-weight: 600;
        }
        .nav-tabs {
            border-bottom: 1px solid #333;
        }
        .nav-tabs .nav-link {
            color: #aaa;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link.active {
            background: none;
            border-color: #f7931e;
            color: #fff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: #555;
            color: #fff;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>System Settings</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="card settings-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'general' ? 'active' : ''; ?>" 
                                   href="#general" data-bs-toggle="tab">
                                   <i class="fas fa-cog me-2"></i>General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'email' ? 'active' : ''; ?>" 
                                   href="#email" data-bs-toggle="tab">
                                   <i class="fas fa-envelope me-2"></i>Email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'membership' ? 'active' : ''; ?>" 
                                   href="#membership" data-bs-toggle="tab">
                                   <i class="fas fa-id-card me-2"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'social' ? 'active' : ''; ?>" 
                                   href="#social" data-bs-toggle="tab">
                                   <i class="fas fa-share-alt me-2"></i>Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_tab === 'system' ? 'active' : ''; ?>" 
                                   href="#system" data-bs-toggle="tab">
                                   <i class="fas fa-server me-2"></i>System
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- General Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'general' ? 'show active' : ''; ?>" id="general">
                                <form method="post" action="">
                                    <h5 class="mb-4">General Settings</h5>
                                    
                                    <?php if (isset($settings['general'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['general'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php elseif ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_general_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save General Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No general settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Email Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'email' ? 'show active' : ''; ?>" id="email">
                                <form method="post" action="">
                                    <h5 class="mb-4">Email Settings</h5>
                                    
                                    <?php if (isset($settings['email'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['email'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'password'): ?>
                                                        <input type="password" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               placeholder="<?php echo empty($setting['setting_value']) ? '' : '••••••••'; ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                        <small class="text-muted">Leave blank to keep current password</small>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_email_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Email Settings
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="testEmailBtn">
                                                <i class="fas fa-paper-plane me-2"></i> Test Email Configuration
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No email settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- Membership Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'membership' ? 'show active' : ''; ?>" id="membership">
                                <form method="post" action="">
                                    <h5 class="mb-4">Membership Settings</h5>
                                    
                                    <?php if (isset($settings['membership'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['membership'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'textarea'): ?>
                                                        <textarea class="form-control" id="<?php echo $setting['setting_key']; ?>" 
                                                                name="settings[<?php echo $setting['setting_key']; ?>]" rows="3"
                                                                <?php echo $setting['required'] ? 'required' : ''; ?>><?php echo htmlspecialchars(trim($setting['setting_value'])); ?></textarea>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_membership_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Membership Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No membership settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- Membership Plans Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">Membership Plans</h5>
                                    
                                    <?php
                                    // Fetch membership plans
                                    $membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Plan Name</th>
                                                    <th>Monthly Price</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($membership_plans)): ?>
                                                    <?php foreach($membership_plans as $plan): ?>
                                                        <tr>
                                                            <td><?php echo $plan['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($plan['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($plan['price_1month']); ?></td>
                                                            <td>
                                                                <?php if ($plan['is_active']): ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Plan">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Plan">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No membership plans found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                                            <i class="fas fa-plus me-2"></i> Add New Plan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'social' ? 'show active' : ''; ?>" id="social">
                                <form method="post" action="">
                                    <h5 class="mb-4">Social Media Settings</h5>
                                    
                                    <?php if (isset($settings['social'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['social'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <i class="fab <?php 
                                                            if (strpos($setting['setting_key'], 'facebook') !== false) echo 'fa-facebook';
                                                            elseif (strpos($setting['setting_key'], 'instagram') !== false) echo 'fa-instagram';
                                                            elseif (strpos($setting['setting_key'], 'twitter') !== false) echo 'fa-twitter';
                                                            elseif (strpos($setting['setting_key'], 'youtube') !== false) echo 'fa-youtube';
                                                            else echo 'fa-globe';
                                                        ?> me-2"></i>
                                                        <?php echo $setting['display_name']; ?>
                                                    </label>
                                                    <input type="url" class="form-control" 
                                                           id="<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                           placeholder="https://">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_social_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save Social Media Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No social media settings found.</div>
                                    <?php endif; ?>
                                </form>
                            </div>
                            
                            <!-- System Settings -->
                            <div class="tab-pane fade <?php echo $current_tab === 'system' ? 'show active' : ''; ?>" id="system">
                                <form method="post" action="">
                                    <h5 class="mb-4">System Settings</h5>
                                    
                                    <?php if (isset($settings['system'])): ?>
                                        <div class="row">
                                            <?php foreach ($settings['system'] as $setting): ?>
                                                <div class="col-md-6 mb-3">
                                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label">
                                                        <?php echo $setting['display_name']; ?>
                                                        <?php if ($setting['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                    </label>
                                                    
                                                    <?php if ($setting['field_type'] === 'boolean'): ?>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   id="<?php echo $setting['setting_key']; ?>" 
                                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                                   value="1" <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['setting_key']; ?>">
                                                                Enabled
                                                            </label>
                                                            <?php if ($setting['setting_key'] === 'maintenance_mode'): ?>
                                                                <div class="text-warning small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Enabling maintenance mode will make the site inaccessible to regular users
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif ($setting['field_type'] === 'select'): ?>
                                                        <select class="form-select" id="<?php echo $setting['setting_key']; ?>"
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                            <?php 
                                                            $options = explode(',', $setting['options']);
                                                            foreach ($options as $option): 
                                                            ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $setting['setting_value'] === $option ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $setting['field_type']; ?>" class="form-control" 
                                                               id="<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                               <?php echo $setting['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" name="save_system_settings" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Save System Settings
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">No system settings found.</div>
                                    <?php endif; ?>
                                    
                                    <!-- System Maintenance Section -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">System Maintenance</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-database me-2"></i>Database</h6>
                                                    <p class="card-text text-white-50">Manage database operations and optimize performance.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-sync-alt me-1"></i> Optimize Tables
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-download me-1"></i> Backup Database
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-file-alt me-2"></i>Logs</h6>
                                                    <p class="card-text text-white-50">View and manage system logs and activity history.</p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-search me-1"></i> View Logs
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Clear Logs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Membership Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="planForm">
                        <div class="mb-3">
                            <label for="planName" class="form-label">Plan Name *</label>
                            <input type="text" class="form-control" id="planName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="planDescription" class="form-label">Plan Description</label>
                            <textarea class="form-control" id="planDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price1Month" class="form-label">1 Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price1Month" name="price_1month" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price3Month" class="form-label">3 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price3Month" name="price_3month">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price6Month" class="form-label">6 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price6Month" name="price_6month">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="price12Month" class="form-label">12 Month Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price12Month" name="price_12month">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="savePlanBtn">Save Plan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Email Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Send a test email to verify your email configuration:</p>
                    <form id="testEmailForm">
                        <div class="mb-3">
                            <label for="testEmailAddress" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="testEmailAddress" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </form>
                    <div id="testEmailResult" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('body').classList.toggle('sidebar-collapsed');
        });
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Handle form submission by tab
        const settingsTabs = document.querySelectorAll('#settingsTabs .nav-link');
        
        settingsTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('href').substring(1);
                // Set the current tab parameter in URL
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                window.history.replaceState({}, '', url);
            });
        });
        
        // Handle test email button
        const testEmailBtn = document.getElementById('testEmailBtn');
        if (testEmailBtn) {
            testEmailBtn.addEventListener('click', function() {
                const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
                testEmailModal.show();
            });
        }
        
        // Handle sending test email
        const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
        if (sendTestEmailBtn) {
            sendTestEmailBtn.addEventListener('click', function() {
                const emailAddress = document.getElementById('testEmailAddress').value;
                const resultDiv = document.getElementById('testEmailResult');
                
                if (!emailAddress) {
                    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                    resultDiv.classList.add('alert-warning');
                    resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Please enter an email address.';
                    return;
                }
                
                // Simulate email sending (in a real app, this would be an AJAX call)
                sendTestEmailBtn.disabled = true;
                sendTestEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
                
                setTimeout(function() {
                    resultDiv.classList.remove('d-none', 'alert-warning', 'alert-danger');
                    resultDiv.classList.add('alert-success');
                    resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Test email sent successfully!';
                    
                    sendTestEmailBtn.disabled = false;
                    sendTestEmailBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Test Email';
                }, 2000);
                
                // In a real application, you'd make an AJAX call to a PHP endpoint that sends the test email
            });
        }
        
        // Handle saving new membership plan
        const savePlanBtn = document.getElementById('savePlanBtn');
        if (savePlanBtn) {
            savePlanBtn.addEventListener('click', function() {
                const planForm = document.getElementById('planForm');
                
                // Simple client-side validation
                const planName = document.getElementById('planName').value;
                const price1Month = document.getElementById('price1Month').value;
                
                if (!planName || !price1Month) {
                    alert('Please fill in all required fields');
                    return;
                }
                
                // In a real application, you would submit the form via AJAX
                alert('Plan would be saved here. In a real application this would submit to the server.');
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlanModal'));
                modal.hide();
                
                // Reset the form
                planForm.reset();
            });
        }
    });
    </script>
</body>
</html>
