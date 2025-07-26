<?php
/**
 * FitZone Fitness Center
 * Member Schedule Page
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

$db = getDb();
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Create class_bookings table if it doesn't exist
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

// Create fitness_classes table if it doesn't exist
$db->query("
    CREATE TABLE IF NOT EXISTS `fitness_classes` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `trainer_id` INT,
        `capacity` INT NOT NULL DEFAULT 20,
        `day_of_week` VARCHAR(10) NOT NULL,
        `start_time` TIME NOT NULL,
        `end_time` TIME NOT NULL,
        `location` VARCHAR(100) NOT NULL,
        `image` VARCHAR(255),
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Add sample classes if none exist
$count = $db->fetchSingle("SELECT COUNT(*) as count FROM fitness_classes");
if (!$count || $count['count'] == 0) {
    $db->query("
        INSERT INTO fitness_classes (name, description, trainer_id, capacity, day_of_week, start_time, end_time, location, image) VALUES
        ('Yoga Flow', 'A gentle yoga class focusing on flowing movements and breathing techniques.', 1, 15, 'Monday', '08:00:00', '09:00:00', 'Studio A', 'yoga.jpg'),
        ('Spinning', 'High-intensity indoor cycling workout to get your heart pumping.', 2, 20, 'Monday', '17:30:00', '18:30:00', 'Cycling Room', 'spinning.jpg'),
        ('HIIT', 'High Intensity Interval Training for maximum calorie burn and strength.', 3, 12, 'Tuesday', '18:00:00', '19:00:00', 'Main Floor', 'hiit.jpg'),
        ('Body Sculpt', 'Full body resistance training to tone and shape your muscles.', 1, 15, 'Wednesday', '10:00:00', '11:00:00', 'Studio B', 'sculpt.jpg'),
        ('Yoga Flow', 'A gentle yoga class focusing on flowing movements and breathing techniques.', 1, 15, 'Thursday', '08:00:00', '09:00:00', 'Studio A', 'yoga.jpg'),
        ('Spinning', 'High-intensity indoor cycling workout to get your heart pumping.', 2, 20, 'Thursday', '17:30:00', '18:30:00', 'Cycling Room', 'spinning.jpg'),
        ('HIIT', 'High Intensity Interval Training for maximum calorie burn and strength.', 3, 12, 'Friday', '18:00:00', '19:00:00', 'Main Floor', 'hiit.jpg'),
        ('Weekend Warrior', 'A challenging mix of cardio and strength to jumpstart your weekend.', 2, 10, 'Saturday', '09:00:00', '10:30:00', 'Main Floor', 'hiit.jpg'),
        ('Gentle Yoga', 'A relaxed yoga session perfect for beginners or recovery.', 1, 15, 'Sunday', '10:00:00', '11:00:00', 'Studio A', 'yoga.jpg')
    ");
}

// Handle class booking
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_class'])) {
    $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
    $booking_date = isset($_POST['booking_date']) ? sanitize($_POST['booking_date']) : '';
    
    // Validate inputs
    if (!$class_id || empty($booking_date) || !validateDate($booking_date)) {
        $error_message = 'Invalid class or date selected.';
    } else {
        // Check if class exists and is active
        $class = $db->fetchSingle("SELECT * FROM fitness_classes WHERE id = ? AND is_active = 1", [$class_id]);
        
        if (!$class) {
            $error_message = 'Selected class not found or is no longer available.';
        } else {
            // Check if user already has a booking for this class on this date
            $existing_booking = $db->fetchSingle(
                "SELECT * FROM class_bookings WHERE user_id = ? AND class_id = ? AND booking_date = ?",
                [$user_id, $class_id, $booking_date]
            );
            
            if ($existing_booking) {
                $error_message = 'You already have a booking for this class on the selected date.';
            } else {
                // Check if class is at capacity
                $booking_count = $db->fetchSingle(
                    "SELECT COUNT(*) as count FROM class_bookings WHERE class_id = ? AND booking_date = ? AND status = 'confirmed'",
                    [$class_id, $booking_date]
                );
                
                if ($booking_count && $booking_count['count'] >= $class['capacity']) {
                    $error_message = 'This class is already at full capacity for the selected date. Please choose another class or date.';
                } else {
                    // Create booking
                    $result = $db->query(
                        "INSERT INTO class_bookings (user_id, class_id, booking_date, status) VALUES (?, ?, ?, 'confirmed')",
                        [$user_id, $class_id, $booking_date]
                    );
                    
                    if ($result) {
                        $success_message = "You've successfully booked the {$class['name']} class for " . date('l, F j, Y', strtotime($booking_date)) . ".";
                    } else {
                        $error_message = 'Failed to book the class. Please try again.';
                    }
                }
            }
        }
    }
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    
    // Validate booking ID
    if (!$booking_id) {
        $error_message = 'Invalid booking selected.';
    } else {
        // Check if booking exists and belongs to user
        $booking = $db->fetchSingle(
            "SELECT b.*, c.name as class_name, c.start_time, c.day_of_week 
            FROM class_bookings b 
            JOIN fitness_classes c ON b.class_id = c.id 
            WHERE b.id = ? AND b.user_id = ?",
            [$booking_id, $user_id]
        );
        
        if (!$booking) {
            $error_message = 'Selected booking not found or does not belong to you.';
        } else {
            // Cancel booking
            $result = $db->query(
                "UPDATE class_bookings SET status = 'cancelled' WHERE id = ?",
                [$booking_id]
            );
            
            if ($result) {
                $success_message = "Your booking for {$booking['class_name']} has been cancelled.";
            } else {
                $error_message = 'Failed to cancel the booking. Please try again.';
            }
        }
    }
}

// Fetch upcoming bookings (future dates and not cancelled)
$upcoming_bookings = $db->fetchAll(
    "SELECT b.*, c.name as class_name, c.description, c.start_time, c.end_time, c.location, c.image, c.day_of_week  
    FROM class_bookings b 
    JOIN fitness_classes c ON b.class_id = c.id 
    WHERE b.user_id = ? AND b.status = 'confirmed' AND b.booking_date >= CURDATE() 
    ORDER BY b.booking_date ASC, c.start_time ASC",
    [$user_id]
);

// Fetch past bookings (past dates or cancelled)
$past_bookings = $db->fetchAll(
    "SELECT b.*, c.name as class_name, c.start_time, c.location, c.day_of_week  
    FROM class_bookings b 
    JOIN fitness_classes c ON b.class_id = c.id 
    WHERE b.user_id = ? AND (b.booking_date < CURDATE() OR b.status = 'cancelled')  
    ORDER BY b.booking_date DESC, c.start_time ASC
    LIMIT 10",
    [$user_id]
);

// Get available classes for the next 7 days
$available_classes = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $day_of_week = date('l', strtotime($date));
    
    $classes_for_day = $db->fetchAll(
        "SELECT c.*, 
        (SELECT COUNT(*) FROM class_bookings WHERE class_id = c.id AND booking_date = ? AND status = 'confirmed') as current_bookings 
        FROM fitness_classes c 
        WHERE c.day_of_week = ? AND c.is_active = 1 
        ORDER BY c.start_time ASC",
        [$date, $day_of_week]
    );
    
    if ($classes_for_day) {
        $available_classes[$date] = [
            'day_name' => $day_of_week,
            'date' => $date,
            'formatted_date' => date('F j', strtotime($date)),
            'classes' => $classes_for_day
        ];
    }
}

// Helper function to validate date
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Set page title
$page_title = 'My Schedule';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current active sidebar item
$active_page = 'schedule';
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
                        My Schedule
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
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>My Schedule</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Schedule</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Tabs for Schedule Navigation -->
                <ul class="nav nav-tabs mb-4" id="scheduleTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                            <i class="fas fa-calendar-check me-2"></i>Upcoming Classes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="book-tab" data-bs-toggle="tab" data-bs-target="#book" type="button" role="tab" aria-controls="book" aria-selected="false">
                            <i class="fas fa-calendar-plus me-2"></i>Book a Class
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                            <i class="fas fa-history me-2"></i>Class History
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="scheduleTabContent">
                    <!-- Upcoming Classes Tab -->
                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                        <?php if (empty($upcoming_bookings)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="empty-state-message">
                                    You don't have any upcoming classes scheduled
                                </div>
                                <button class="btn btn-outline-success" data-bs-toggle="tab" data-bs-target="#book">
                                    <i class="fas fa-plus me-2"></i>Book a Class
                                </button>
                            </div>
                        <?php else: ?>
                            <?php
                            $grouped_bookings = [];
                            foreach ($upcoming_bookings as $booking) {
                                $date = $booking['booking_date'];
                                if (!isset($grouped_bookings[$date])) {
                                    $grouped_bookings[$date] = [];
                                }
                                $grouped_bookings[$date][] = $booking;
                            }
                            
                            foreach ($grouped_bookings as $date => $bookings):
                                $day_name = date('l', strtotime($date));
                                $formatted_date = date('F j, Y', strtotime($date));
                            ?>
                                <div class="schedule-date">
                                    <div class="schedule-date-header">
                                        <div class="schedule-date-day"><?php echo $day_name; ?></div>
                                        <div class="schedule-date-full"><?php echo $formatted_date; ?></div>
                                    </div>
                                    
                                    <?php foreach ($bookings as $booking): ?>
                                        <div class="upcoming-class-card">
                                            <div class="upcoming-class-header">
                                                <div class="upcoming-class-date">
                                                    <i class="fas fa-calendar me-2"></i><?php echo $booking['day_of_week']; ?>
                                                </div>
                                                <div class="upcoming-class-status">
                                                    <i class="fas fa-check me-1"></i>Confirmed
                                                </div>
                                            </div>
                                            <div class="upcoming-class-body">
                                                <div class="upcoming-class-time">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <?php echo date('g:i A', strtotime($booking['start_time'])); ?>
                                                    -
                                                    <?php echo date('g:i A', strtotime($booking['end_time'])); ?>
                                                </div>
                                                <div class="upcoming-class-info">
                                                    <div class="upcoming-class-name">
                                                        <?php echo htmlspecialchars($booking['class_name']); ?>
                                                    </div>
                                                    <div class="upcoming-class-meta">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <?php echo htmlspecialchars($booking['location']); ?>
                                                    </div>
                                                    <?php if (isset($booking['description']) && !empty($booking['description'])): ?>
                                                        <div class="upcoming-class-description mt-2">
                                                            <?php echo htmlspecialchars($booking['description']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="upcoming-class-actions">
                                                <form method="post" onsubmit="return confirm('Are you sure you want to cancel this class booking?');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <button type="submit" name="cancel_booking" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-times me-2"></i>Cancel Booking
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Book a Class Tab -->
                    <div class="tab-pane fade" id="book" role="tabpanel" aria-labelledby="book-tab">
                        <p class="mb-4">Select a class from the available schedule below to book your spot:</p>
                        
                        <?php if (empty($available_classes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No classes are currently scheduled for the next 7 days. Please check back later.
                            </div>
                        <?php else: ?>
                            <form id="bookingForm" method="post">
                                <input type="hidden" name="class_id" id="selected_class_id" value="">
                                <input type="hidden" name="booking_date" id="selected_booking_date" value="">
                                
                                <?php foreach ($available_classes as $date_data): ?>
                                    <div class="schedule-date">
                                        <div class="schedule-date-header">
                                            <div class="schedule-date-day"><?php echo $date_data['day_name']; ?></div>
                                            <div class="schedule-date-full"><?php echo $date_data['formatted_date']; ?></div>
                                        </div>
                                        
                                        <?php foreach ($date_data['classes'] as $class): ?>
                                            <div class="class-card">
                                                <div class="class-card-header">
                                                    <div class="class-time">
                                                        <?php echo date('g:i A', strtotime($class['start_time'])); ?>
                                                    </div>
                                                    <div class="class-name">
                                                        <?php echo htmlspecialchars($class['name']); ?>
                                                    </div>
                                                </div>
                                                <div class="class-body">
                                                    <?php
                                                    // Fix image path handling
                                                    $image = $class['image'];
                                                    if (!empty($image)) {
                                                        if (strpos($image, 'http') !== 0 && strpos($image, '/') !== 0) {
                                                            $image_url = '../../assets/images/Classes/' . $image;
                                                        } else {
                                                            $image_url = $image;
                                                        }
                                                    } else {
                                                        $image_url = '../../assets/images/Classes/yoga.jpg';
                                                    }
                                                    ?>
                                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($class['name']); ?>" class="class-image">
                                                    <div class="class-details">
                                                        <div class="class-description">
                                                            <?php echo htmlspecialchars($class['description']); ?>
                                                        </div>
                                                        <div class="class-meta me-3">
                                                            <i class="fas fa-clock"></i>
                                                            <?php 
                                                            $start_time = date('g:i A', strtotime($class['start_time']));
                                                            $end_time = date('g:i A', strtotime($class['end_time']));
                                                            echo $start_time . ' - ' . $end_time; 
                                                            ?>
                                                        </div>
                                                        <div class="class-meta">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <?php echo htmlspecialchars($class['location']); ?>
                                                        </div>
                                                        
                                                        <?php
                                                        $current_bookings = isset($class['current_bookings']) ? $class['current_bookings'] : 0;
                                                        $capacity = isset($class['capacity']) ? $class['capacity'] : 20;
                                                        $percentage = ($current_bookings / $capacity) * 100;
                                                        $spots_left = $capacity - $current_bookings;
                                                        ?>
                                                        
                                                        <div class="class-capacity">
                                                            <div class="capacity-bar">
                                                                <div class="capacity-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                            </div>
                                                            <div class="capacity-text">
                                                                <?php echo $spots_left; ?> spots left
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="class-actions">
                                                    <?php
                                                    // Check if class is full
                                                    $is_full = $spots_left <= 0;
                                                    
                                                    // Check if user already booked this class on this date
                                                    $already_booked = false;
                                                    foreach ($upcoming_bookings as $booking) {
                                                        if ($booking['class_id'] == $class['id'] && $booking['booking_date'] == $date_data['date']) {
                                                            $already_booked = true;
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    
                                                    <?php if ($already_booked): ?>
                                                        <button type="button" class="btn btn-success" disabled>
                                                            <i class="fas fa-check me-2"></i>Already Booked
                                                        </button>
                                                    <?php elseif ($is_full): ?>
                                                        <button type="button" class="btn btn-outline-secondary" disabled>
                                                            <i class="fas fa-times-circle me-2"></i>Class Full
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-success book-class-btn" 
                                                            data-class-id="<?php echo $class['id']; ?>" 
                                                            data-booking-date="<?php echo $date_data['date']; ?>"
                                                            data-class-name="<?php echo htmlspecialchars($class['name']); ?>"
                                                            data-class-time="<?php echo $start_time; ?>">
                                                            <i class="fas fa-calendar-plus me-2"></i>Book Class
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </form>
                            
                            <!-- Booking Confirmation Modal -->
                            <div class="modal fade" id="bookingConfirmModal" tabindex="-1" aria-labelledby="bookingConfirmModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-light">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="bookingConfirmModalLabel">Confirm Class Booking</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>You are about to book:</p>
                                            <div class="bg-secondary p-3 rounded mb-3">
                                                <h5 id="confirmClassName"></h5>
                                                <p class="mb-1"><i class="fas fa-calendar me-2"></i><span id="confirmDate"></span></p>
                                                <p class="mb-0"><i class="fas fa-clock me-2"></i><span id="confirmTime"></span></p>
                                            </div>
                                            <p>Do you want to confirm this booking?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-success" id="confirmBookingBtn">
                                                <i class="fas fa-check me-2"></i>Confirm Booking
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Class History Tab -->
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <?php if (empty($past_bookings)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="empty-state-message">
                                    You don't have any past class bookings
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Class</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($past_bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo date('m/d/Y', strtotime($booking['booking_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($booking['class_name']); ?></td>
                                                <td><?php echo date('g:i A', strtotime($booking['start_time'])); ?></td>
                                                <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                                <td>
                                                    <?php if ($booking['status'] === 'cancelled'): ?>
                                                        <span class="booking-status-cancelled">Cancelled</span>
                                                    <?php elseif (strtotime($booking['booking_date']) < strtotime('today')): ?>
                                                        <span class="booking-status-attended">Attended</span>
                                                    <?php else: ?>
                                                        <span class="booking-status-confirmed">Confirmed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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
        
        // Handle booking confirmation
        const bookingForm = document.getElementById('bookingForm');
        const bookingModal = new bootstrap.Modal(document.getElementById('bookingConfirmModal'));
        const selectedClassId = document.getElementById('selected_class_id');
        const selectedBookingDate = document.getElementById('selected_booking_date');
        const confirmClassName = document.getElementById('confirmClassName');
        const confirmDate = document.getElementById('confirmDate');
        const confirmTime = document.getElementById('confirmTime');
        const confirmBookingBtn = document.getElementById('confirmBookingBtn');
        
        // Add event listeners to all book class buttons
        const bookButtons = document.querySelectorAll('.book-class-btn');
        bookButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get data from button
                const classId = this.dataset.classId;
                const bookingDate = this.dataset.bookingDate;
                const className = this.dataset.className;
                const classTime = this.dataset.classTime;
                
                // Set values in form
                selectedClassId.value = classId;
                selectedBookingDate.value = bookingDate;
                
                // Set values in modal
                confirmClassName.textContent = className;
                confirmDate.textContent = new Date(bookingDate).toLocaleDateString('en-US', { 
                    weekday: 'long',
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric'
                });
                confirmTime.textContent = classTime;
                
                // Show modal
                bookingModal.show();
            });
        });
        
        // Handle confirm booking button
        confirmBookingBtn.addEventListener('click', function() {
            // Submit booking form
            bookingForm.book_class.value = 'book_class';
            bookingForm.submit();
        });
    });
    </script>
</body>
</html>