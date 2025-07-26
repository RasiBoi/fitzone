<?php
/**
 * FitZone Fitness Center
 * Member Classes Page
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

// Check if user has an active membership
$has_membership = false;
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
        $has_membership = true;
    }
} catch (Exception $e) {
    // Silently handle error
}

// Handle class booking
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_class'])) {
    if (!$has_membership) {
        $error_message = 'You need an active membership to book classes. Please purchase a membership first.';
    } else {
        $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        
        // Check if class exists
        $class = $db->fetchSingle("SELECT * FROM classes WHERE id = ?", [$class_id]);
        
        if (!$class) {
            $error_message = 'Selected class not found.';
        } else {
            // Check if user already booked this class
            $existing_booking = $db->fetchSingle(
                "SELECT * FROM class_bookings 
                WHERE user_id = ? AND class_id = ?",
                [$user_id, $class_id]
            );
            
            if ($existing_booking) {
                $error_message = 'You have already booked this class.';
            } else {
                // Check if class is full
                $current_bookings = $db->fetchSingle(
                    "SELECT COUNT(*) as count FROM class_bookings WHERE class_id = ?",
                    [$class_id]
                );
                
                if ($current_bookings['count'] >= $class['capacity']) {
                    $error_message = 'This class is already full. Please choose another class.';
                } else {
                    // Book the class
                    $result = $db->query(
                        "INSERT INTO class_bookings (user_id, class_id, booking_date) 
                        VALUES (?, ?, NOW())",
                        [$user_id, $class_id]
                    );
                    
                    if ($result) {
                        $success_message = 'Class booked successfully! You are now registered for ' . $class['name'] . '.';
                    } else {
                        $error_message = 'Failed to book class. Please try again.';
                    }
                }
            }
        }
    }
}

// Cancel class booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    
    // Check if booking exists and belongs to user
    $booking = $db->fetchSingle(
        "SELECT b.*, c.name as class_name 
        FROM class_bookings b 
        JOIN classes c ON b.class_id = c.id 
        WHERE b.id = ? AND b.user_id = ?",
        [$booking_id, $user_id]
    );
    
    if (!$booking) {
        $error_message = 'Booking not found or you do not have permission to cancel it.';
    } else {
        // Check if cancellation is allowed (e.g. not within 12 hours of class)
        $class_time = strtotime($booking['class_date'] . ' ' . $booking['start_time']);
        $now = time();
        $hours_diff = ($class_time - $now) / 3600;
        
        if ($hours_diff < 12) {
            $error_message = 'Cancellations must be made at least 12 hours before the class starts.';
        } else {
            // Cancel booking
            $result = $db->query(
                "DELETE FROM class_bookings WHERE id = ? AND user_id = ?",
                [$booking_id, $user_id]
            );
            
            if ($result) {
                $success_message = 'Your booking for ' . $booking['class_name'] . ' has been cancelled.';
            } else {
                $error_message = 'Failed to cancel booking. Please try again.';
            }
        }
    }
}

// Get all available classes
$classes = [];
try {
    $classes = $db->fetchAll(
        "SELECT c.*, t.name as trainer_name, t.specialization 
        FROM classes c 
        LEFT JOIN trainers t ON c.trainer_id = t.id 
        WHERE c.status = 'active' 
        ORDER BY c.class_date ASC, c.start_time ASC"
    );
    
    // Ensure $classes is an array
    if (!is_array($classes)) {
        $classes = [];
    }
} catch (Exception $e) {
    // If table doesn't exist, create it with sample data
    $db->query("
        CREATE TABLE IF NOT EXISTS `classes` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `class_date` DATE NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            `capacity` INT NOT NULL DEFAULT 20,
            `trainer_id` INT,
            `location` VARCHAR(100),
            `status` ENUM('active', 'cancelled', 'completed') NOT NULL DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    $db->query("
        CREATE TABLE IF NOT EXISTS `trainers` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `specialization` VARCHAR(100),
            `bio` TEXT,
            `profile_image` VARCHAR(255),
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Create class_bookings table
    $db->query("
        CREATE TABLE IF NOT EXISTS `class_bookings` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `class_id` INT NOT NULL,
            `booking_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `status` ENUM('booked', 'cancelled', 'attended', 'no-show') NOT NULL DEFAULT 'booked',
            FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Insert sample trainers
    $db->query("
        INSERT IGNORE INTO trainers (id, name, specialization, bio) VALUES
        (1, 'John Smith', 'HIIT, CrossFit', 'John specializes in high intensity training and CrossFit. He has 8 years of experience in fitness training.'),
        (2, 'Sarah Johnson', 'Yoga, Pilates', 'Sarah is our yoga expert with over 10 years of experience in mindful movement practices.'),
        (3, 'Mike Wilson', 'Strength Training', 'Mike focuses on strength training and muscle building techniques with 6 years of experience.');
    ");
    
    // Insert sample classes
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $day_after = date('Y-m-d', strtotime('+2 days'));
    
    $db->query("
        INSERT IGNORE INTO classes (name, description, class_date, start_time, end_time, capacity, trainer_id, location, status) VALUES
        ('Morning HIIT', 'High Intensity Interval Training to kickstart your day', '$tomorrow', '07:00:00', '08:00:00', 15, 1, 'Studio A', 'active'),
        ('Yoga Flow', 'Relaxing yoga flow to improve flexibility and mindfulness', '$tomorrow', '10:00:00', '11:00:00', 20, 2, 'Studio B', 'active'),
        ('Strength & Conditioning', 'Build strength and improve conditioning', '$tomorrow', '18:00:00', '19:00:00', 12, 3, 'Main Gym', 'active'),
        ('Core Blast', 'Focus on core muscles for a stronger center', '$day_after', '08:00:00', '09:00:00', 15, 1, 'Studio A', 'active'),
        ('Pilates', 'Improve posture and core strength through Pilates', '$day_after', '11:00:00', '12:00:00', 15, 2, 'Studio B', 'active'),
        ('Evening HIIT', 'High Intensity Interval Training to end your day', '$day_after', '19:00:00', '20:00:00', 15, 1, 'Studio A', 'active')
    ");
    
    // Fetch classes again
    $classes = $db->fetchAll(
        "SELECT c.*, t.name as trainer_name, t.specialization 
        FROM classes c 
        LEFT JOIN trainers t ON c.trainer_id = t.id 
        WHERE c.status = 'active' 
        ORDER BY c.class_date ASC, c.start_time ASC"
    );
}

// Group classes by date
$classes_by_date = [];
if (is_array($classes)) {
    foreach ($classes as $class) {
        $date = $class['class_date'];
        if (!isset($classes_by_date[$date])) {
            $classes_by_date[$date] = [];
        }
        $classes_by_date[$date][] = $class;
    }
}

// Get user's booked classes
$booked_classes = [];
try {
    $booked_classes = $db->fetchAll(
        "SELECT b.id as booking_id, c.id as class_id, c.name, c.description, c.class_date, c.start_time, c.end_time, c.location, t.name as trainer_name
        FROM class_bookings b
        JOIN classes c ON b.class_id = c.id
        LEFT JOIN trainers t ON c.trainer_id = t.id
        WHERE b.user_id = ? AND b.status = 'booked' AND c.class_date >= CURDATE()
        ORDER BY c.class_date ASC, c.start_time ASC",
        [$user_id]
    );
} catch (Exception $e) {
    // Handle error silently
}

// Set page title
$page_title = 'Classes';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current active sidebar item
$active_page = 'classes';
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
                        Classes
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
                        <h4>Fitness Classes</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Classes</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <?php if (!$has_membership): ?>
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <h5>Active Membership Required</h5>
                            <p class="mb-0">You need an active membership to book fitness classes. <a href="membership.php" class="alert-link">Purchase a membership</a> to unlock this feature.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
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
                
                <!-- Tabs for Class Navigation -->
                <ul class="nav nav-tabs mb-4" id="classTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-classes-tab" data-bs-toggle="tab" data-bs-target="#all-classes" type="button" role="tab" aria-controls="all-classes" aria-selected="true">
                            <i class="fas fa-list me-2"></i>Available Classes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="my-bookings-tab" data-bs-toggle="tab" data-bs-target="#my-bookings" type="button" role="tab" aria-controls="my-bookings" aria-selected="false">
                            <i class="fas fa-calendar-check me-2"></i>My Bookings (<?php echo count($booked_classes); ?>)
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="classTabContent">
                    <!-- All Classes Tab -->
                    <div class="tab-pane fade show active" id="all-classes" role="tabpanel" aria-labelledby="all-classes-tab">
                        <?php if (empty($classes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No classes are currently available. Please check back later.
                            </div>
                        <?php else: ?>
                            <?php foreach ($classes_by_date as $date => $day_classes): ?>
                                <div class="class-date-header">
                                    <h5 class="mb-0">
                                        <?php 
                                        $date_obj = new DateTime($date);
                                        $today = new DateTime('today');
                                        $tomorrow = new DateTime('tomorrow');
                                        
                                        if ($date_obj->format('Y-m-d') === $today->format('Y-m-d')) {
                                            echo 'Today - ' . $date_obj->format('F j, Y');
                                        } elseif ($date_obj->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                                            echo 'Tomorrow - ' . $date_obj->format('F j, Y');
                                        } else {
                                            echo $date_obj->format('l, F j, Y');
                                        }
                                        ?>
                                    </h5>
                                </div>
                                
                                <div class="row">
                                    <?php foreach ($day_classes as $class): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card class-card h-100">
                                                <?php if ($class['capacity'] <= 5): ?>
                                                    <span class="badge bg-warning class-badge">Almost Full</span>
                                                <?php endif; ?>
                                                
                                                <div class="card-header">
                                                    <h5 class="mb-0"><?php echo htmlspecialchars($class['name']); ?></h5>
                                                </div>
                                                
                                                <div class="card-body">
                                                    <?php 
                                                    // Fix image path handling
                                                    $image = isset($class['image']) ? $class['image'] : '';
                                                    if (!empty($image)) {
                                                        if (strpos($image, 'http') !== 0 && strpos($image, '/') !== 0 && strpos($image, 'assets/') !== 0) {
                                                            $class_image_url = '../../assets/images/Classes/' . $image;
                                                        } else {
                                                            $class_image_url = $image;
                                                        }
                                                    } else {
                                                        $class_image_url = '../../assets/images/Classes/yoga.jpg';
                                                    }
                                                    ?>
                                                    <div class="class-image mb-3">
                                                        <img src="<?php echo $class_image_url; ?>" alt="<?php echo htmlspecialchars($class['name']); ?>" class="img-fluid rounded">
                                                    </div>
                                                    
                                                    <p><?php echo htmlspecialchars($class['description']); ?></p>
                                                    
                                                    <div class="class-info">
                                                        <div class="class-info-item">
                                                            <span class="class-info-icon"><i class="fas fa-clock"></i></span>
                                                            <span><?php echo date('g:i A', strtotime($class['start_time'])); ?> - <?php echo date('g:i A', strtotime($class['end_time'])); ?></span>
                                                        </div>
                                                        
                                                        <div class="class-info-item">
                                                            <span class="class-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                                                            <span><?php echo htmlspecialchars($class['location']); ?></span>
                                                        </div>
                                                        
                                                        <div class="class-info-item">
                                                            <span class="class-info-icon"><i class="fas fa-user"></i></span>
                                                            <span><?php echo htmlspecialchars($class['trainer_name']); ?></span>
                                                        </div>
                                                        
                                                        <div class="class-info-item">
                                                            <span class="class-info-icon"><i class="fas fa-users"></i></span>
                                                            <span>
                                                                <?php 
                                                                // Get number of bookings for this class
                                                                $bookings = $db->fetchSingle("SELECT COUNT(*) as count FROM class_bookings WHERE class_id = ?", [$class['id']]);
                                                                $spots_taken = $bookings ? $bookings['count'] : 0;
                                                                echo $spots_taken . '/' . $class['capacity'] . ' spots taken';
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer bg-transparent">
                                                    <?php 
                                                    // Check if user has already booked this class
                                                    $is_booked = false;
                                                    foreach ($booked_classes as $booked) {
                                                        if ($booked['class_id'] == $class['id']) {
                                                            $is_booked = true;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Check if class is full
                                                    $is_full = $spots_taken >= $class['capacity'];
                                                    ?>
                                                    
                                                    <?php if ($is_booked): ?>
                                                        <button class="btn btn-success w-100" disabled>
                                                            <i class="fas fa-check me-2"></i>Booked
                                                        </button>
                                                    <?php elseif ($is_full): ?>
                                                        <button class="btn btn-outline-secondary w-100" disabled>
                                                            <i class="fas fa-users-slash me-2"></i>Class Full
                                                        </button>
                                                    <?php elseif ($has_membership): ?>
                                                        <form method="post">
                                                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                                            <button type="submit" name="book_class" class="btn btn-primary w-100">
                                                                <i class="fas fa-calendar-plus me-2"></i>Book Class
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <a href="membership.php" class="btn btn-outline-primary w-100">
                                                            <i class="fas fa-lock me-2"></i>Get Membership to Book
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- My Bookings Tab -->
                    <div class="tab-pane fade" id="my-bookings" role="tabpanel" aria-labelledby="my-bookings-tab">
                        <?php if (empty($booked_classes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> You haven't booked any classes yet. Browse available classes and book your spots!
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Trainer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($booked_classes as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                                <td>
                                                    <?php 
                                                    $date_obj = new DateTime($booking['class_date']);
                                                    $today = new DateTime('today');
                                                    $tomorrow = new DateTime('tomorrow');
                                                    
                                                    if ($date_obj->format('Y-m-d') === $today->format('Y-m-d')) {
                                                        echo '<span class="badge bg-primary">Today</span>';
                                                    } elseif ($date_obj->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                                                        echo '<span class="badge bg-info">Tomorrow</span>';
                                                    } else {
                                                        echo $date_obj->format('m/d/Y');
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo date('g:i A', strtotime($booking['start_time'])); ?> - <?php echo date('g:i A', strtotime($booking['end_time'])); ?></td>
                                                <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['trainer_name']); ?></td>
                                                <td>
                                                    <form method="post" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                        <button type="submit" name="cancel_booking" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
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

    <!-- Class Details Modal -->
    <div class="modal fade" id="classDetailsModal" tabindex="-1" aria-labelledby="classDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="classDetailsModalLabel">Class Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="classDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="bookClassBtn">Book Class</button>
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