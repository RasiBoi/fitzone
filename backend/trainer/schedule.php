<?php
/**
 * FitZone Fitness Center
 * Trainer Schedule Page
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

// Create fitness_classes table if it doesn't exist
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

// Days of the week in correct order
$days_of_week = [
    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
];

// Time slots for schedule
$time_slots = [];
$start_hour = 6; // 6 AM
$end_hour = 22; // 10 PM
$interval_minutes = 60; // 1 hour intervals

// Generate time slots
for ($hour = $start_hour; $hour < $end_hour; $hour++) {
    $time = sprintf('%02d:00:00', $hour);
    $display_time = date('g:i A', strtotime($time));
    $time_slots[] = [
        'time' => $time,
        'display' => $display_time
    ];
}

// Get all classes for this trainer
$classes = $db->fetchAll(
    "SELECT * FROM fitness_classes WHERE trainer_id = ? AND is_active = 1",
    [$trainer['id']]
);

// Organize classes by day and time
$schedule = [];
foreach ($days_of_week as $day) {
    $schedule[$day] = [];
}

foreach ($classes as $class) {
    $day = $class['day_of_week'];
    $start_time = $class['start_time'];
    $end_time = $class['end_time'];
    
    // Find the time slot index that this class belongs to
    $slot_index = -1;
    foreach ($time_slots as $index => $slot) {
        if ($start_time >= $slot['time'] && $start_time < ($index < count($time_slots) - 1 ? $time_slots[$index + 1]['time'] : '24:00:00')) {
            $slot_index = $index;
            break;
        }
    }
    
    if ($slot_index !== -1) {
        // Calculate duration in hours (for span)
        $start_datetime = new DateTime($start_time);
        $end_datetime = new DateTime($end_time);
        $interval = $start_datetime->diff($end_datetime);
        $duration_hours = $interval->h + ($interval->i / 60);
        $span = ceil($duration_hours);
        
        $schedule[$day][$slot_index] = [
            'class' => $class,
            'span' => $span
        ];
        
        // Block subsequent slots for multi-hour classes
        if ($span > 1) {
            for ($i = 1; $i < $span; $i++) {
                if ($slot_index + $i < count($time_slots)) {
                    $schedule[$day][$slot_index + $i] = 'blocked';
                }
            }
        }
    }
}

// Set page title
$page_title = 'My Schedule';
$active_page = 'schedule';

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
    <style>
        .schedule-table {
            min-width: 900px;
        }
        .schedule-table th, .schedule-table td {
            width: 12.5%;
        }
        .schedule-table th:first-child, .schedule-table td:first-child {
            width: 12.5%;
        }
        .time-slot {
            height: 100px;
            vertical-align: top;
            padding: 5px;
        }
        .time-label {
            text-align: center;
            padding-top: 10px;
            font-weight: 500;
        }
        .class-card {
            background-color: rgba(76, 175, 80, 0.2);
            border-left: 4px solid var(--primary);
            height: 100%;
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .class-card:hover {
            background-color: rgba(76, 175, 80, 0.3);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        .class-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        .class-time, .class-location {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }
        .class-enrolled {
            position: absolute;
            bottom: 5px;
            right: 8px;
            font-size: 12px;
        }
        .current-day {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .current-time {
            position: relative;
        }
        .current-time-marker {
            position: absolute;
            height: 2px;
            background-color: red;
            width: 100%;
            z-index: 10;
        }
        .empty-slot {
            height: 100%;
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .blocked-slot {
            display: none;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
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
                        <h4>My Schedule</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Schedule</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Weekly Schedule -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Weekly Class Schedule</h5>
                        <div>
                            <a href="classes.php" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-dumbbell"></i> Manage Classes
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered schedule-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($days_of_week as $day): ?>
                                            <?php 
                                            $is_current_day = date('l') === $day;
                                            $class = $is_current_day ? 'current-day' : ''; 
                                            ?>
                                            <th class="<?php echo $class; ?>"><?php echo $day; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($time_slots as $slot_index => $slot): ?>
                                        <tr>
                                            <td class="time-label">
                                                <?php echo $slot['display']; ?>
                                            </td>
                                            <?php 
                                            // Check if current time is within this slot
                                            $current_hour = (int)date('G');
                                            $slot_hour = (int)substr($slot['time'], 0, 2);
                                            $is_current_time_slot = $current_hour === $slot_hour;
                                            
                                            // Current day of week
                                            $current_day = date('l');
                                            
                                            // Calculate position of current time marker (in percent)
                                            $current_minute = (int)date('i');
                                            $marker_position = ($current_minute / 60) * 100;
                                            ?>
                                            
                                            <?php foreach ($days_of_week as $day): ?>
                                                <?php 
                                                $is_current_day = $current_day === $day;
                                                $has_time_marker = $is_current_day && $is_current_time_slot;
                                                $class = $is_current_day ? 'current-day' : '';
                                                $class .= $has_time_marker ? ' current-time' : '';
                                                ?>
                                                <td class="time-slot <?php echo $class; ?>">
                                                    <?php if ($has_time_marker): ?>
                                                        <div class="current-time-marker" style="top: <?php echo $marker_position; ?>%;"></div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (isset($schedule[$day][$slot_index]) && $schedule[$day][$slot_index] !== 'blocked'): ?>
                                                        <?php 
                                                        $class_info = $schedule[$day][$slot_index];
                                                        $class = $class_info['class'];
                                                        $span = $class_info['span'];
                                                        
                                                        // Get enrolled count
                                                        $enrolled = $db->fetchSingle(
                                                            "SELECT COUNT(*) as count FROM class_bookings WHERE class_id = ? AND status = 'confirmed'",
                                                            [$class['id']]
                                                        );
                                                        $enrolled_count = $enrolled ? $enrolled['count'] : 0;
                                                        $capacity = $class['capacity'];
                                                        $enrollment_status = $enrolled_count >= $capacity ? 'Full' : $enrolled_count . '/' . $capacity;
                                                        
                                                        $start_time = date("g:i A", strtotime($class['start_time']));
                                                        $end_time = date("g:i A", strtotime($class['end_time']));
                                                        ?>
                                                        
                                                        <div class="class-card">
                                                            <div class="class-name"><?php echo htmlspecialchars($class['name']); ?></div>
                                                            <div class="class-time">
                                                                <i class="far fa-clock"></i> <?php echo $start_time . ' - ' . $end_time; ?>
                                                            </div>
                                                            <div class="class-location">
                                                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($class['location']); ?>
                                                            </div>
                                                            <div class="class-enrolled">
                                                                <span class="badge <?php echo $enrolled_count >= $capacity ? 'bg-danger' : 'bg-primary'; ?>">
                                                                    <?php echo $enrollment_status; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                    <?php elseif ($schedule[$day][$slot_index] !== 'blocked'): ?>
                                                        <div class="empty-slot"></div>
                                                    <?php else: ?>
                                                        <div class="blocked-slot"></div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
    });
    </script>
</body>
</html>