<?php
/**
 * FitZone Fitness Center
 * Sidebar Template
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

// Get current page for highlighting active links
$current_page = basename($_SERVER['PHP_SELF']);
$current_directory = basename(dirname($_SERVER['PHP_SELF']));

// Get current user role for role-specific menu items
$user_role = getUserRole();
$is_admin = isAdmin();
$is_staff = hasRole(ROLE_STAFF);
$is_member = hasRole(ROLE_MEMBER);

// Get current user data if logged in
$current_user = null;
if (isLoggedIn() && isset($auth)) {
    $current_user = $auth->getCurrentUser();
}

// Check membership status
$has_membership = isset($current_user['membership_status']) && $current_user['membership_status'] === 'active';
?>

<div class="sidebar">
    <!-- User Profile Section -->
    <?php if (isLoggedIn() && $current_user): ?>
        <div class="sidebar-profile">
            <div class="text-center mb-3">
                <?php if (!empty($current_user['profile_picture'])): ?>
                    <img src="<?php echo SITE_URL; ?>assets/uploads/profiles/<?php echo $current_user['profile_picture']; ?>" 
                         alt="<?php echo htmlspecialchars($current_user['username']); ?>" 
                         class="profile-image rounded-circle">
                <?php else: ?>
                    <div class="profile-placeholder">
                        <?php 
                        $initials = '';
                        if (!empty($current_user['first_name']) && !empty($current_user['last_name'])) {
                            $initials = strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1));
                        } else {
                            $initials = strtoupper(substr($current_user['username'], 0, 2));
                        }
                        echo $initials;
                        ?>
                    </div>
                <?php endif; ?>
                
                <h5 class="mt-2 mb-0"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></h5>
                <p class="text-muted small mb-2">@<?php echo htmlspecialchars($current_user['username']); ?></p>
                
                <!-- Role Badge -->
                <?php if ($is_admin): ?>
                    <span class="badge bg-danger">Administrator</span>
                <?php elseif ($is_staff): ?>
                    <span class="badge bg-primary">Staff</span>
                <?php else: ?>
                    <span class="badge bg-success">Member</span>
                <?php endif; ?>
            </div>
            
            <!-- Quick Links -->
            <div class="quick-links d-flex justify-content-center mb-3">
                <a href="<?php echo SITE_URL; ?>member/profile.php" class="btn btn-sm btn-outline-secondary me-2" title="My Profile">
                    <i class="fas fa-user"></i>
                </a>
                <a href="<?php echo SITE_URL; ?>member/appointments.php" class="btn btn-sm btn-outline-secondary me-2" title="My Appointments">
                    <i class="fas fa-calendar-check"></i>
                </a>
                <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-sm btn-outline-danger" title="Log Out">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
        <hr>
    <?php endif; ?>
    
    <!-- Sidebar Menu -->
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
                <?php if ($has_membership): ?>
                <a href="classes.php" class="<?php echo $current_page === 'classes.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-dumbbell"></i></span>
                    <span>Classes</span>
                    <span class="menu-badge">New</span>
                </a>
                <?php else: ?>
                <a href="membership.php" class="locked-menu-item">
                    <span class="menu-icon"><i class="fas fa-dumbbell"></i></span>
                    <span>Classes</span>
                    <span class="menu-badge">New</span>
                </a>
                <?php endif; ?>
            </li>
            
            <li>
                <?php if ($has_membership): ?>
                <a href="schedule.php" class="<?php echo $current_page === 'schedule.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span>My Schedule</span>
                </a>
                <?php else: ?>
                <a href="membership.php" class="locked-menu-item">
                    <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span>My Schedule</span>
                </a>
                <?php endif; ?>
            </li>
            
            <li>
                <?php if ($has_membership): ?>
                <a href="progress.php" class="<?php echo $current_page === 'progress.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span>My Progress</span>
                </a>
                <?php else: ?>
                <a href="membership.php" class="locked-menu-item">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span>My Progress</span>
                </a>
                <?php endif; ?>
            </li>
            
            <li>
                <?php if ($has_membership): ?>
                <a href="nutrition.php" class="<?php echo $current_page === 'nutrition.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                    <span>Nutrition Plans</span>
                </a>
                <?php else: ?>
                <a href="membership.php" class="locked-menu-item">
                    <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                    <span>Nutrition Plans</span>
                </a>
                <?php endif; ?>
            </li>
            
            <li>
                <a href="messages.php" class="<?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span>
                    <span>Messages</span>
                </a>
            </li>
            
            <li>
                <a href="../logout.php">
                    <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Fitness Center Info -->
    <div class="sidebar-section mt-4">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
            <span>Center Info</span>
            <i class="fas fa-info-circle"></i>
        </h6>
        <div class="sidebar-info p-3">
            <div class="mb-2">
                <i class="fas fa-clock text-primary me-2"></i>
                <strong>Hours:</strong>
                <div class="ms-4 small">
                    Mon-Fri: 6:00AM - 10:00PM<br>
                    Sat: 7:00AM - 8:00PM<br>
                    Sun: 8:00AM - 6:00PM
                </div>
            </div>
            <div class="mb-2">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                <strong>Location:</strong>
                <div class="ms-4 small">
                    123 Fitness Avenue<br>
                    Kurunegala, Sri Lanka
                </div>
            </div>
            <div>
                <i class="fas fa-phone-alt text-primary me-2"></i>
                <strong>Call Us:</strong>
                <div class="ms-4 small">
                    +94 76 123 4567
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Date and Time -->
    <div class="sidebar-footer p-3 mt-4 text-center small text-muted">
        <div id="sidebar-date-time">
            <span id="sidebar-date"><?php echo date('Y-m-d'); ?></span><br>
            <span id="sidebar-time"><?php echo date('H:i:s'); ?></span>
        </div>
        <div class="mt-2">
            &copy; 2025 FitZone Fitness Center
        </div>
    </div>
</div>

<script>
// Update the time display
function updateSidebarTime() {
    var now = new Date();
    var hours = now.getHours().toString().padStart(2, '0');
    var minutes = now.getMinutes().toString().padStart(2, '0');
    var seconds = now.getSeconds().toString().padStart(2, '0');
    document.getElementById('sidebar-time').textContent = hours + ':' + minutes + ':' + seconds;
    setTimeout(updateSidebarTime, 1000);
}

// Start the clock when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    updateSidebarTime();
});
</script>