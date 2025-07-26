<?php
/**
 * FitZone Fitness Center
 * Dashboard Sidebar Component
 * This provides a consistent sidebar for all dashboard pages
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

// Get current page for highlighting active links
$current_page = basename($_SERVER['PHP_SELF']);
$active_page = isset($active_page) ? $active_page : '';

// Get profile image URL (default if not set)
$profile_image_url = isset($profile_image) ? $profile_image : '../../assets/images/trainers/trainer-1.jpg';
?>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="../../assets/images/fitzone.png" alt="FitZone">
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="index.php" class="<?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="profile.php" class="<?php echo $active_page === 'profile' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-user"></i></span>
                    <span>My Profile</span>
                </a>
            </li>
            
            <?php if ($user_role === 'admin' || $user_role === 'trainer'): ?>
            <li>
                <a href="members.php" class="<?php echo $active_page === 'members' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span>Members</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($user_role === 'admin'): ?>
            <li>
                <a href="trainers.php" class="<?php echo $active_page === 'trainers' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-user-tie"></i></span>
                    <span>Trainers</span>
                </a>
            </li>
            
            <li>
                <a href="membership.php" class="<?php echo $active_page === 'membership' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-id-card"></i></span>
                    <span>Membership Plans</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li>
                <a href="classes.php" class="<?php echo $active_page === 'classes' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-dumbbell"></i></span>
                    <span>Classes</span>
                    <?php if ($user_role === 'member'): ?>
                    <span class="menu-badge">New</span>
                    <?php endif; ?>
                </a>
            </li>
            
            <?php if ($user_role === 'member'): ?>
            <li>
                <a href="membership.php" class="<?php echo $active_page === 'membership' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-id-card"></i></span>
                    <span>My Membership</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($user_role === 'trainer'): ?>
            <li>
                <a href="schedules.php" class="<?php echo $active_page === 'schedules' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span>My Schedule</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($user_role === 'admin'): ?>
            <li>
                <a href="settings.php" class="<?php echo $active_page === 'settings' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-cog"></i></span>
                    <span>Settings</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li>
                <a href="../../logout.php">
                    <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>