<?php
/**
 * FitZone Fitness Center
 * Trainer Members Management
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

// If trainer details don't exist, redirect to profile to complete setup
if (!$trainer) {
    redirect('profile.php?setup=1');
}

// Check if viewing a specific member
$view_member = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $member_id = (int)$_GET['id'];
    $view_member = $db->fetchSingle(
        "SELECT u.*, 
                COUNT(DISTINCT cb.class_id) as total_classes,
                SUM(CASE WHEN cb.status = 'attended' THEN 1 ELSE 0 END) as attended_classes
         FROM users u
         LEFT JOIN class_bookings cb ON u.id = cb.user_id
         LEFT JOIN fitness_classes fc ON cb.class_id = fc.id
         WHERE u.id = ? AND u.role = 'member' AND fc.trainer_id = ?
         GROUP BY u.id",
        [$member_id, $trainer['id']]
    );

    // If member not found or not in trainer's classes, redirect back
    if (!$view_member) {
        redirect('members.php');
    }

    // Get member's recent attendance in trainer's classes
    $member_classes = $db->fetchAll(
        "SELECT cb.*, fc.name as class_name, fc.day_of_week, fc.start_time, fc.end_time, fc.location
         FROM class_bookings cb
         JOIN fitness_classes fc ON cb.class_id = fc.id
         WHERE cb.user_id = ? AND fc.trainer_id = ?
         ORDER BY cb.booking_date DESC
         LIMIT 10",
        [$member_id, $trainer['id']]
    );

    // Set page title for member details page
    $page_title = 'Member Details';
} else {
    // Get all members who attend classes with this trainer
    $members = $db->fetchAll(
        "SELECT u.id, u.first_name, u.last_name, u.email, u.profile_image, 
                COUNT(DISTINCT cb.class_id) as total_classes,
                SUM(CASE WHEN cb.status = 'attended' THEN 1 ELSE 0 END) as attended_classes,
                MAX(cb.booking_date) as last_class_date
         FROM users u
         JOIN class_bookings cb ON u.id = cb.user_id
         JOIN fitness_classes fc ON cb.class_id = fc.id
         WHERE u.role = 'member' AND fc.trainer_id = ?
         GROUP BY u.id
         ORDER BY u.first_name ASC",
        [$trainer['id']]
    );

    // Set page title for members list
    $page_title = 'Class Members';
}

$active_page = 'members';

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
                        <h4><?php echo $page_title; ?></h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <?php if ($view_member): ?>
                                    <li class="breadcrumb-item"><a href="members.php">Members</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Member Details</li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active" aria-current="page">Members</li>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <?php if ($view_member): ?>
                <!-- Single Member View -->
                <div class="row">
                    <!-- Member Profile Card -->
                    <div class="col-md-4">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <?php
                                $member_image = isset($view_member['profile_image']) && !empty($view_member['profile_image']) 
                                    ? '../../uploads/profile/' . $view_member['profile_image'] 
                                    : '../../assets/images/testimonials/client-1.jpg'; 
                                ?>
                                <img src="<?php echo $member_image; ?>" alt="Member" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                <h5><?php echo htmlspecialchars($view_member['first_name'] . ' ' . $view_member['last_name']); ?></h5>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($view_member['email']); ?></p>
                                <p class="text-muted">Member since <?php echo date('M d, Y', strtotime($view_member['created_at'])); ?></p>
                                
                                <div class="d-flex justify-content-center">
                                    <div class="px-3 border-end">
                                        <h6><?php echo (int)$view_member['total_classes']; ?></h6>
                                        <p class="text-muted small mb-0">Classes</p>
                                    </div>
                                    
                                    <div class="px-3">
                                        <h6>
                                            <?php 
                                            $attendance_rate = $view_member['total_classes'] > 0 
                                                ? round(($view_member['attended_classes'] / $view_member['total_classes']) * 100) 
                                                : 0; 
                                            echo $attendance_rate . '%';
                                            ?>
                                        </h6>
                                        <p class="text-muted small mb-0">Attendance</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Member Notes Card -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Notes</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <input type="hidden" name="member_id" value="<?php echo $view_member['id']; ?>">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="notes" rows="5" placeholder="Add training notes about this member..."><?php echo htmlspecialchars(isset($view_member['trainer_notes']) ? $view_member['trainer_notes'] : ''); ?></textarea>
                                    </div>
                                    <button type="submit" name="save_notes" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Notes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Member Class History -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Class History</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($member_classes)): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($member_classes as $class): ?>
                                                    <?php
                                                    $status_class = 'bg-warning';
                                                    if ($class['status'] === 'attended') {
                                                        $status_class = 'bg-success';
                                                    } elseif ($class['status'] === 'no-show') {
                                                        $status_class = 'bg-danger';
                                                    }
                                                    
                                                    $start_time = date("h:i A", strtotime($class['start_time']));
                                                    $end_time = date("h:i A", strtotime($class['end_time']));
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <div class="fw-bold"><?php echo htmlspecialchars($class['class_name']); ?></div>
                                                                <div class="small text-muted"><?php echo htmlspecialchars($class['location']); ?></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php echo date('M d, Y', strtotime($class['booking_date'])); ?>
                                                            <div class="small text-muted"><?php echo $class['day_of_week']; ?></div>
                                                        </td>
                                                        <td><?php echo $start_time . ' - ' . $end_time; ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $status_class; ?>">
                                                                <?php echo ucfirst($class['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="far fa-calendar-times fa-3x mb-3"></i>
                                        <h5>No Class History</h5>
                                        <p>This member hasn't attended any of your classes yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Members List View -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Members Enrolled in My Classes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($members)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Email</th>
                                            <th>Classes</th>
                                            <th>Attendance Rate</th>
                                            <th>Last Class</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($members as $member): 
                                            $attendance_rate = $member['total_classes'] > 0 
                                                ? round(($member['attended_classes'] / $member['total_classes']) * 100) 
                                                : 0;
                                                
                                            $rate_class = 'bg-danger';
                                            if ($attendance_rate >= 80) {
                                                $rate_class = 'bg-success';
                                            } elseif ($attendance_rate >= 60) {
                                                $rate_class = 'bg-warning';
                                            }
                                            
                                            $member_image = isset($member['profile_image']) && !empty($member['profile_image']) 
                                                ? '../../uploads/profile/' . $member['profile_image'] 
                                                : '../../assets/images/testimonials/client-1.jpg';
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo $member_image; ?>" alt="Member" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <span><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                                <td><?php echo (int)$member['total_classes']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                            <div class="progress-bar <?php echo $rate_class; ?>" role="progressbar" style="width: <?php echo $attendance_rate; ?>%" aria-valuenow="<?php echo $attendance_rate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span><?php echo $attendance_rate; ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($member['last_class_date']) {
                                                        echo date('M d, Y', strtotime($member['last_class_date']));
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x mb-3 text-muted"></i>
                                <h5>No Members Found</h5>
                                <p>No members have booked your classes yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
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
    });
    </script>
</body>
</html>

