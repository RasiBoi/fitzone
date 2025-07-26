<?php
/**
 * FitZone Fitness Center
 * Trainer Dashboard Sidebar
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Get current page for highlighting active menu item
$current_file = basename($_SERVER['SCRIPT_NAME']);
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
            
            <li>
                <a href="members.php" class="<?php echo $active_page === 'members' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span>Members</span>
                </a>
            </li>
            
            <li>
                <a href="classes.php" class="<?php echo $active_page === 'classes' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-dumbbell"></i></span>
                    <span>Classes</span>
                </a>
            </li>
            
            <li>
                <a href="schedule.php" class="<?php echo $active_page === 'schedule' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span>My Schedule</span>
                </a>
            </li>
            
            <li>
                <a href="../../logout.php">
                    <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>