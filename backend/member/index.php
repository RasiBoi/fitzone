<?php
/**
 * FitZone Fitness Center
 * Member Dashboard Home Page
 */

// Enable error reporting to help diagnose issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Check if user is logged in, if not redirect to login page
if (!isLoggedIn()) {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Verify this is actually a member
if ($user_role !== 'member') {
    // Redirect to appropriate dashboard based on role
    if ($user_role === 'admin') {
        redirect('../admin/index.php');
    } elseif ($user_role === 'trainer') {
        redirect('../trainer/index.php');
    } else {
        redirect('../../login.php');
    }
}

// Get database connection
$db = getDb();

try {
    $user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
    
    // If user not found, try with user_id column instead of id
    if (!$user) {
        $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    }
    
    // If user still not found, logout and redirect
    if (!$user) {
        logout();
        redirect('../../login.php');
    }
} catch (Exception $e) {
    // If there's an error, continue anyway
}

// Check for active membership
$has_membership = false;
$days_remaining = 0;
try {
    $subscription = $db->fetchSingle(
        "SELECT * FROM member_subscriptions 
        WHERE user_id = ? 
        AND status = 'active' 
        AND end_date >= CURDATE() 
        ORDER BY end_date DESC 
        LIMIT 1",
        [$user_id]
    );
    
    if ($subscription) {
        $has_membership = $subscription;
        $end_date = new DateTime($subscription['end_date']);
        $today = new DateTime('today');
        $interval = $today->diff($end_date);
        $days_remaining = $interval->days;
    }
} catch (Exception $e) {
    // Silently handle error
}

// Fetch upcoming classes (if needed)
$upcoming_classes = [];
if ($has_membership) {
    try {
        $upcoming_classes = $db->fetchAll(
            "SELECT b.*, c.name as class_name, c.start_time, c.day_of_week, c.location
            FROM class_bookings b 
            JOIN fitness_classes c ON b.class_id = c.id 
            WHERE b.user_id = ? AND b.status = 'confirmed' AND b.booking_date >= CURDATE() 
            ORDER BY b.booking_date ASC, c.start_time ASC
            LIMIT 3",
            [$user_id]
        );
    } catch (Exception $e) {
        // Silently handle error
    }
}

// Create user_stats table if it doesn't exist (for progress data)
$db->query("
    CREATE TABLE IF NOT EXISTS `user_stats` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `date` DATE NOT NULL,
        `weight` DECIMAL(5,2) NULL,
        `body_fat` DECIMAL(5,2) NULL,
        `workout_duration` INT NULL,
        `calories_burned` INT NULL,
        `notes` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if the table fitness_classes exists and create it if it doesn't
$db->query("
    CREATE TABLE IF NOT EXISTS `fitness_classes` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT NULL,
        `trainer_id` INT NULL,
        `capacity` INT NOT NULL DEFAULT 20,
        `day_of_week` VARCHAR(10) NOT NULL,
        `start_time` TIME NOT NULL,
        `end_time` TIME NOT NULL,
        `location` VARCHAR(100) NOT NULL,
        `image` VARCHAR(255) NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if table class_bookings exists and create it if it doesn't
$db->query("
    CREATE TABLE IF NOT EXISTS `class_bookings` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `class_id` INT NOT NULL,
        `booking_date` DATE NOT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'confirmed',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Fetch latest stats
$latest_stats = null;
try {
    $latest_stats = $db->fetchSingle(
        "SELECT * FROM user_stats WHERE user_id = ? ORDER BY date DESC LIMIT 1",
        [$user_id]
    );
} catch (Exception $e) {
    // Silently handle error
}

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current active sidebar item
$active_page = 'dashboard';
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
</head>
<body class="role-member">
    <div class="dashboard-container">
        <!-- Sidebar -->
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
                        <a href="membership.php" class="<?php echo $active_page === 'membership' ? 'active' : ''; ?>">
                            <span class="menu-icon"><i class="fas fa-id-card"></i></span>
                            <span>My Membership</span>
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
                        <a href="progress.php" class="<?php echo $active_page === 'progress' ? 'active' : ''; ?>">
                            <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                            <span>My Progress</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="nutrition.php" class="<?php echo $active_page === 'nutrition' ? 'active' : ''; ?>">
                            <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                            <span>Nutrition Plans</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="messages.php" class="<?php echo $active_page === 'messages' ? 'active' : ''; ?>">
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
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation Bar -->
            <div class="topbar">
                <div class="d-flex align-items-center">
                    <div class="toggle-sidebar me-3">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="topbar-title">
                        Dashboard
                    </div>
                </div>
                
                <div class="topbar-right">
                    <div class="dropdown">
                        <div class="user-dropdown d-flex align-items-center" data-bs-toggle="dropdown">
                            <img src="<?php echo $profile_image; ?>" alt="Profile" class="profile-img">
                            <span class="username d-none d-sm-inline ms-2"><?php echo htmlspecialchars($username); ?></span>
                            <i class="fas fa-chevron-down ms-2 small"></i>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end" style="background-color: #1A1A1A; border: 1px solid #333333;">
                            <li><a class="dropdown-item" href="profile.php" style="color: #CCCCCC;"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="../../logout.php" style="color: #CCCCCC;"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Welcome Banner -->
                <div class="welcome-banner mb-4">
                    <img src="<?php echo $profile_image; ?>" alt="Profile" class="welcome-avatar">
                    <div class="welcome-message">
                        <h3>Welcome back, <?php echo htmlspecialchars(isset($user['first_name']) ? $user['first_name'] : $username); ?>!</h3>
                        <p class="mb-0">
                            <?php if ($has_membership): ?>
                                You have <?php echo $days_remaining; ?> days remaining on your <?php echo $has_membership['membership_type']; ?> membership.
                            <?php else: ?>
                                You don't have an active membership. Visit the membership page to get started.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Stats Overview -->
                <div class="row mb-4">
                    <!-- Membership Status Widget -->
                    <div class="col-md-6 mb-3">
                        <div class="membership-info <?php echo $has_membership ? 'active' : ''; ?>">
                            <div class="text-center mb-3">
                                <div class="membership-status-icon <?php echo $has_membership ? 'membership-active-icon' : 'membership-inactive-icon'; ?>">
                                    <i class="<?php echo $has_membership ? 'fas fa-check' : 'fas fa-exclamation'; ?>"></i>
                                </div>
                                <h5>Membership Status</h5>
                                <p class="mb-1">
                                    <?php if ($has_membership): ?>
                                        <span class="badge bg-success">Active</span> <?php echo $has_membership['membership_type']; ?> Plan
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Inactive</span> No active membership
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <?php if ($has_membership): ?>
                                <div class="px-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Membership Validity:</span>
                                        <span><?php echo $days_remaining; ?> days remaining</span>
                                    </div>
                                    <div class="progress">
                                        <?php 
                                        $total_days = 30; // Assuming monthly subscription
                                        if ($has_membership['duration'] === '6month') {
                                            $total_days = 180;
                                        } elseif ($has_membership['duration'] === '12month') {
                                            $total_days = 365;
                                        }
                                        $used_days = $total_days - $days_remaining;
                                        $percentage = ($used_days / $total_days) * 100;
                                        ?>
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="small text-muted">Start: <?php echo date('M d, Y', strtotime($has_membership['start_date'])); ?></span>
                                        <span class="small text-muted">End: <?php echo date('M d, Y', strtotime($has_membership['end_date'])); ?></span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <a href="membership.php" class="btn btn-sm btn-outline-success">View Details</a>
                                </div>
                            <?php else: ?>
                                <div class="text-center">
                                    <p>Get access to all our facilities and classes with a membership plan.</p>
                                    <a href="membership.php" class="btn btn-primary">Get Membership</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Stats Widgets -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="stat-box">
                                    <div class="stat-icon stat-icon-green">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                    <div class="stat-value"><?php echo count($upcoming_classes); ?></div>
                                    <div class="stat-label">Upcoming Classes</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-box">
                                    <div class="stat-icon stat-icon-blue">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-value"><?php echo $latest_stats ? $latest_stats['workout_duration'] : '0'; ?></div>
                                    <div class="stat-label">Workout Minutes</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon stat-icon-orange">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="stat-value"><?php echo $latest_stats ? $latest_stats['calories_burned'] : '0'; ?></div>
                                    <div class="stat-label">Calories Burned</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon stat-icon-purple">
                                        <i class="fas fa-weight"></i>
                                    </div>
                                    <div class="stat-value"><?php echo $latest_stats ? $latest_stats['weight'] : '--'; ?></div>
                                    <div class="stat-label">Weight (kg)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <!-- Upcoming Classes -->
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">Upcoming Classes</div>
                                <a href="schedule.php" class="btn btn-sm btn-outline-success">View All</a>
                            </div>
                            <div class="widget-body p-0">
                                <?php if (empty($upcoming_classes)): ?>
                                    <div class="empty-widget">
                                        <div class="empty-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <div class="empty-text">You don't have any upcoming classes scheduled</div>
                                        <a href="schedule.php" class="btn btn-primary">Book a Class</a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($upcoming_classes as $class): ?>
                                        <div class="schedule-card">
                                            <div class="schedule-header">
                                                <p class="schedule-date">
                                                    <i class="far fa-calendar-alt me-2"></i>
                                                    <?php echo date('l, F j', strtotime($class['booking_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="schedule-body">
                                                <h5 class="schedule-class"><?php echo htmlspecialchars($class['class_name']); ?></h5>
                                                <div class="schedule-meta">
                                                    <i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($class['start_time'])); ?>
                                                </div>
                                                <div class="schedule-meta">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($class['location']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($upcoming_classes)): ?>
                                <div class="widget-footer">
                                    <a href="schedule.php" class="btn btn-sm btn-outline-primary">Manage My Schedule</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Quick Access Links -->
                    <div class="col-lg-5">
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">Quick Access</div>
                            </div>
                            <div class="widget-body">
                                <div class="quick-links">
                                    <a href="classes.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <i class="fas fa-dumbbell"></i>
                                        </div>
                                        <div class="quick-link-title">Classes</div>
                                        <div class="quick-link-desc">Browse and book fitness classes</div>
                                    </a>
                                    
                                    <a href="schedule.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div class="quick-link-title">Schedule</div>
                                        <div class="quick-link-desc">View and manage your schedule</div>
                                    </a>
                                    
                                    <a href="progress.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="quick-link-title">Progress</div>
                                        <div class="quick-link-desc">Track your fitness progress</div>
                                    </a>
                                    
                                    <a href="nutrition.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <div class="quick-link-title">Nutrition</div>
                                        <div class="quick-link-desc">View nutrition plans and tips</div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tips Section -->
                <div class="widget">
                    <div class="widget-header">
                        <div class="widget-title">Fitness Tips</div>
                    </div>
                    <div class="widget-body">
                        <div class="alert alert-success bg-transparent border-success mb-0" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Tip of the Day</h5>
                            <p>Staying hydrated is crucial for optimal performance. Aim to drink at least 8 glasses of water each day, and more if you're exercising intensively.</p>
                            <hr>
                            <p class="mb-0">Remember to warm up properly before exercise and cool down afterward to prevent injury and reduce muscle soreness.</p>
                        </div>
                    </div>
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
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
    });
    </script>
</body>
</html>