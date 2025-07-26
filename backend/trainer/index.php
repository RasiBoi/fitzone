<?php
/**
 * FitZone Fitness Center
 * Trainer Dashboard
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

// Check if user is logged in and is a trainer
if (!isLoggedIn() || $_SESSION['user_role'] !== 'trainer') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get user details
$user = $db->fetchSingle(
    "SELECT * FROM users WHERE id = ?",
    [$user_id]
);

// If user not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Get trainer details from trainers table
$trainer = $db->fetchSingle(
    "SELECT * FROM trainers WHERE user_id = ?",
    [$user_id]
);

// If trainer details don't exist, create a default record
if (!$trainer) {
    // Create a default trainer record linked to the user
    $db->query(
        "INSERT INTO trainers (user_id, specialization, bio, experience, is_active) 
         VALUES (?, ?, ?, ?, ?)",
        [$user_id, 'General Fitness', 'Fitness trainer at FitZone', 1, 1]
    );
    
    // Fetch the newly created trainer record
    $trainer = $db->fetchSingle(
        "SELECT * FROM trainers WHERE user_id = ?",
        [$user_id]
    );
}

// Create trainers table if it doesn't exist
$db->query("
    CREATE TABLE IF NOT EXISTS `trainers` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `specialization` VARCHAR(255) NULL,
        `bio` TEXT NULL,
        `certifications` TEXT NULL,
        `experience` INT DEFAULT 1,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `user_id` (`user_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Get counts from database for dashboard stats
$total_classes_query = "SELECT COUNT(*) as count FROM fitness_classes WHERE trainer_id = ?";
$total_classes = $db->fetchSingle($total_classes_query, [isset($trainer['id']) ? $trainer['id'] : $user_id]);
$classes_count = $total_classes ? $total_classes['count'] : 0;

// Get members in trainer's classes (distinct count)
$members_in_classes = $db->fetchSingle(
    "SELECT COUNT(DISTINCT cb.user_id) as count 
     FROM class_bookings cb
     JOIN fitness_classes fc ON cb.class_id = fc.id
     WHERE fc.trainer_id = ? 
     AND cb.status = 'confirmed'",
    [isset($trainer['id']) ? $trainer['id'] : $user_id]
);
$members_count = $members_in_classes ? $members_in_classes['count'] : 0;

// Set page title
$page_title = 'Trainer Dashboard';
$active_page = 'dashboard';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';
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
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Trainer Sidebar -->
        <?php include '../../includes/sidebar-trainer.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Dashboard Content -->
                <div class="row">
                    <!-- Trainer Stats -->
                    <div class="col-md-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $classes_count; ?></div>
                                        <div class="stat-title">My Classes</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $members_count; ?></div>
                                        <div class="stat-title">Active Members</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $trainer ? $trainer['experience'] : 0; ?> Years</div>
                                        <div class="stat-title">Experience</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Dashboard Cards - Row 1 -->
                <div class="row mt-4">
                    <!-- Quick Action Cards -->
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                                </div>
                                <h5>View Schedule</h5>
                                <p class="mb-3 text-light">Check your upcoming classes and schedule</p>
                                <a href="schedule.php" class="btn btn-outline-light">View Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-users fa-3x text-success"></i>
                                </div>
                                <h5>View Members</h5>
                                <p class="mb-3 text-light">See members enrolled in your classes</p>
                                <a href="members.php" class="btn btn-outline-light">View Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-user-circle fa-3x text-info"></i>
                                </div>
                                <h5>Update Profile</h5>
                                <p class="mb-3 text-light">Manage your trainer profile</p>
                                <a href="profile.php" class="btn btn-outline-light">Update Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Row for Classes -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-dark text-white">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">My Classes</h5>
                                <a href="classes.php" class="btn btn-sm btn-outline-light">View All</a>
                            </div>
                            <div class="card-body">
                                <?php
                                $trainer_classes = $db->fetchAll(
                                    "SELECT * FROM fitness_classes 
                                     WHERE trainer_id = ? 
                                     ORDER BY day_of_week ASC, start_time ASC 
                                     LIMIT 5",
                                    [isset($trainer['id']) ? $trainer['id'] : $user_id]
                                );
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Class Name</th>
                                                <th>Day</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Enrolled</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($trainer_classes)): ?>
                                                <?php foreach ($trainer_classes as $class): 
                                                    // Count enrolled members
                                                    $enrolled = $db->fetchSingle(
                                                        "SELECT COUNT(*) as count FROM class_bookings WHERE class_id = ? AND status = 'confirmed'",
                                                        [$class['id']]
                                                    );
                                                    $enrolled_count = $enrolled ? $enrolled['count'] : 0;
                                                    
                                                    $start_time = date("h:i A", strtotime($class['start_time']));
                                                    $end_time = date("h:i A", strtotime($class['end_time']));
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($class['day_of_week']); ?></td>
                                                        <td><?php echo $start_time . ' - ' . $end_time; ?></td>
                                                        <td><?php echo htmlspecialchars($class['location']); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $enrolled_count >= $class['capacity'] ? 'bg-danger' : 'bg-primary'; ?>">
                                                                <?php echo $enrolled_count . '/' . $class['capacity']; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No classes assigned yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
            } else {
                document.querySelector('body').classList.remove('sidebar-collapsed');
            }
        }
        
        // Check on load
        checkScreenSize();
        
        // Check on resize
        window.addEventListener('resize', checkScreenSize);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>