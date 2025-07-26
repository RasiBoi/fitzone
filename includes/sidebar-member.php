<?php
/**
 * FitZone Fitness Center
 * Member Sidebar Template
 */

// Get current page for highlighting active links
$current_page = basename($_SERVER['PHP_SELF']);

// This simplified sidebar doesn't require the FITZONE_APP constant
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
                <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-user"></i></span>
                    <span>My Profile</span>
                </a>
            </li>
            
            <li>
                <a href="membership.php" class="<?php echo $current_page === 'membership.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-id-card"></i></span>
                    <span>My Membership</span>
                </a>
            </li>
            
            <li>
                <a href="classes.php" class="<?php echo $current_page === 'classes.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-dumbbell"></i></span>
                    <span>Classes</span>
                    <span class="menu-badge">New</span>
                </a>
            </li>
            
            <li>
                <a href="schedule.php" class="<?php echo $current_page === 'schedule.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span>My Schedule</span>
                </a>
            </li>
            
            <li>
                <a href="progress.php" class="<?php echo $current_page === 'progress.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span>My Progress</span>
                </a>
            </li>
            
            <li>
                <a href="nutrition.php" class="<?php echo $current_page === 'nutrition.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                    <span>Nutrition Plans</span>
                </a>
            </li>
            
            <li>
                <a href="messages.php" class="<?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span>
                    <span>Messages</span>
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