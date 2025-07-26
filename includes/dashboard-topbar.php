<?php
/**
 * FitZone Fitness Center
 * Dashboard Topbar Component
 * This provides a consistent topbar for all dashboard pages
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

// Get page title (default if not set)
$page_title = isset($page_title) ? $page_title : 'Dashboard';
$role_display = isset($user_role) ? ucfirst($user_role) : '';
$profile_image = isset($profile_image) ? $profile_image : '../../assets/images/trainers/trainer-1.jpg';
$username = isset($username) ? $username : '';
$user = isset($user) ? $user : [];
?>

<!-- Top Navigation Bar -->
<div class="topbar">
    <div class="d-flex align-items-center">
        <div class="toggle-sidebar me-3">
            <i class="fas fa-bars"></i>
        </div>
        <div class="topbar-title">
            <?php echo $role_display; ?> <?php echo $page_title; ?>
        </div>
    </div>
    
    <div class="topbar-right">
        <div class="dropdown">
            <div class="user-dropdown" data-bs-toggle="dropdown">
                <img src="<?php echo $profile_image; ?>" alt="Profile" class="profile-img">
                <span class="username d-none d-sm-inline"><?php echo htmlspecialchars($username); ?></span>
                <i class="fas fa-chevron-down ms-1 small"></i>
            </div>
            
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $profile_image; ?>" alt="Profile" class="profile-img me-2">
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars(isset($user['first_name']) ? $user['first_name'] : '') . ' ' . htmlspecialchars(isset($user['last_name']) ? $user['last_name'] : ''); ?></div>
                            <div class="small text-orange"><?php echo ucfirst($user_role); ?></div>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>