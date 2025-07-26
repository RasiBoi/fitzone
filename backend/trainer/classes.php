<?php
/**
 * FitZone Fitness Center
 * Trainer Classes Management
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
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Initialize database connection
$db = getDb();

// Debugging - log start of processing
error_log("Starting classes.php processing");

// Ensure users table exists
$db->query("
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `first_name` VARCHAR(50) NULL,
        `last_name` VARCHAR(50) NULL,
        `role` ENUM('admin', 'trainer', 'member') NOT NULL DEFAULT 'member',
        `profile_image` VARCHAR(255) NULL,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Ensure trainers table exists
$db->query("
    CREATE TABLE IF NOT EXISTS `trainers` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `specialization` VARCHAR(100) NULL,
        `bio` TEXT NULL,
        `experience` INT NULL,
        `certification` VARCHAR(255) NULL,
        `phone` VARCHAR(20) NULL,
        `instagram_handle` VARCHAR(50) NULL,
        `facebook_handle` VARCHAR(50) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if user exists in users table, create default trainer user if not
if ($user_id == 0) {
    // Create a default user if not logged in (for testing purposes)
    $default_user = $db->fetchSingle("SELECT id FROM users WHERE username = 'trainer'");
    
    if (!$default_user) {
        error_log("Creating default trainer user");
        $db->query("
            INSERT INTO users (username, email, password, first_name, last_name, role)
            VALUES ('trainer', 'trainer@fitzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Default', 'Trainer', 'trainer')
        ");
        
        $user_id = $db->lastInsertId();
        
        // Create trainer record
        $db->query("
            INSERT INTO trainers (user_id, specialization, bio, experience)
            VALUES (?, 'Fitness', 'Default trainer bio', 3)
        ", [$user_id]);
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = 'trainer';
        $_SESSION['user_role'] = 'trainer';
    } else {
        $user_id = $default_user['id'];
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = 'trainer';
        $_SESSION['user_role'] = 'trainer';
    }
}

// Get user details
$user = $db->fetchSingle(
    "SELECT * FROM users WHERE id = ?",
    [$user_id]
);

// If user not found, logout and redirect
if (!$user) {
    error_log("User not found: $user_id");
    logout();
    redirect('../../login.php');
}

// Get trainer details from trainers table
$trainer = $db->fetchSingle(
    "SELECT * FROM trainers WHERE user_id = ?",
    [$user_id]
);

// If trainer details don't exist, create a default trainer record
if (!$trainer) {
    error_log("Creating trainer record for user: $user_id");
    $db->query("
        INSERT INTO trainers (user_id, specialization, bio, experience)
        VALUES (?, 'General Fitness', 'Trainer bio not yet provided', 1)
    ", [$user_id]);
    
    $trainer = $db->fetchSingle(
        "SELECT * FROM trainers WHERE user_id = ?",
        [$user_id]
    );
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle class deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $class_id = (int)$_GET['delete'];
    
    // Verify the class belongs to this trainer
    $class_to_delete = $db->fetchSingle("SELECT * FROM fitness_classes WHERE id = ? AND trainer_id = ?", [$class_id, $trainer['id']]);
    
    if ($class_to_delete) {
        $result = $db->query("DELETE FROM fitness_classes WHERE id = ?", [$class_id]);
        
        if ($result) {
            $success_message = "Class deleted successfully.";
        } else {
            $error_message = "Failed to delete class.";
        }
    } else {
        $error_message = "You don't have permission to delete this class.";
    }
}

// Handle form submission for class creation/editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_class') {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $difficulty = trim($_POST['difficulty']);
    $schedule_days = trim($_POST['schedule_days']);
    $schedule_times = trim($_POST['schedule_times']);
    
    // Set the trainer as the current trainer's name
    $trainer_name = $user['first_name'] . ' ' . $user['last_name'];
    
    // Validate inputs
    if (empty($name) || empty($description) || empty($duration) || empty($difficulty) || empty($schedule_days) || empty($schedule_times)) {
        $error_message = "Please fill in all required fields with valid information.";
    } else {
        // Handle image upload
        $image_name = '';
        $upload_error = false;
        
        // Check if a new image was uploaded
        if (!empty($_FILES['image']['name'])) {
            // Create upload directory if it doesn't exist
            $upload_dir = '../../assets/images/Classes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;
            
            // Check file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error_message = "Only JPG, JPEG & PNG files are allowed.";
                $upload_error = true;
            }
            
            // Check file size (2MB max)
            if ($_FILES['image']['size'] > 2097152) {
                $error_message = "Image size should be less than 2MB.";
                $upload_error = true;
            }
            
            if (!$upload_error) {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error_message = "Failed to upload image.";
                    $upload_error = true;
                }
            }
        }
        
        if (empty($error_message)) {
            if ($edit_id > 0) {
                // Verify the class exists before editing
                $class_to_edit = $db->fetchSingle("SELECT * FROM fitness_classes WHERE id = ?", [$edit_id]);
                
                if (!$class_to_edit) {
                    $error_message = "Class not found.";
                } else {
                    // Update existing class
                    if (!empty($_FILES['image']['name']) && !$upload_error) {
                        // Update with new image
                        $result = $db->query(
                            "UPDATE fitness_classes SET name = ?, description = ?, duration = ?, difficulty = ?, 
                            trainer = ?, schedule_days = ?, schedule_times = ?, image = ?, updated_at = NOW() 
                            WHERE id = ?",
                            [$name, $description, $duration, $difficulty, $trainer_name, $schedule_days, $schedule_times, $image_name, $edit_id]
                        );
                    } else {
                        // Update without changing image
                        $result = $db->query(
                            "UPDATE fitness_classes SET name = ?, description = ?, duration = ?, difficulty = ?, 
                            trainer = ?, schedule_days = ?, schedule_times = ?, updated_at = NOW() 
                            WHERE id = ?",
                            [$name, $description, $duration, $difficulty, $trainer_name, $schedule_days, $schedule_times, $edit_id]
                        );
                    }
                    
                    if ($result) {
                        $success_message = "Class updated successfully.";
                    } else {
                        $error_message = "Failed to update class.";
                    }
                }
            } else {
                // Add new class
                if (empty($_FILES['image']['name']) || $upload_error) {
                    $error_message = "Please upload an image for the class.";
                } else {
                    $result = $db->query(
                        "INSERT INTO fitness_classes (name, description, duration, difficulty, trainer, schedule_days, schedule_times, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$name, $description, $duration, $difficulty, $trainer_name, $schedule_days, $schedule_times, $image_name]
                    );
                    
                    if ($result) {
                        $success_message = "New class added successfully.";
                    } else {
                        $error_message = "Failed to add class.";
                    }
                }
            }
        }
    }
}

// Get class to edit
$edit_class = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_class = $db->fetchSingle("SELECT * FROM fitness_classes WHERE id = ?", [$edit_id]);
    
    if (!$edit_class) {
        $error_message = "Class not found or you don't have permission to edit it.";
    }
}

// Ensure fitness_classes table exists
$db->query("
    CREATE TABLE IF NOT EXISTS `fitness_classes` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT NULL,
        `trainer_id` INT NOT NULL,
        `day_of_week` VARCHAR(20) NOT NULL,
        `start_time` TIME NOT NULL,
        `end_time` TIME NOT NULL,
        `location` VARCHAR(100) NOT NULL,
        `capacity` INT NOT NULL DEFAULT 20,
        `image` VARCHAR(255) NULL,
        `notes` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Create class_bookings table if it doesn't exist
$db->query("
    CREATE TABLE IF NOT EXISTS `class_bookings` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `class_id` INT NOT NULL,
        `booking_date` DATE NOT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'confirmed',
        `notes` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check if there are any classes for this trainer, if not, create sample classes
$has_classes = $db->fetchSingle(
    "SELECT COUNT(*) as count FROM fitness_classes WHERE trainer_id = ?",
    [$trainer['id']]
);

if ($has_classes['count'] == 0) {
    // Create some sample classes for this trainer
    $sample_classes = [
        [
            'name' => 'Morning Yoga',
            'description' => 'Start your day with energizing yoga poses and breathing exercises.',
            'day_of_week' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'location' => 'Studio A',
            'capacity' => 15
        ],
        [
            'name' => 'HIIT Workout',
            'description' => 'High-intensity interval training to burn calories and improve fitness.',
            'day_of_week' => 'Wednesday',
            'start_time' => '17:30:00',
            'end_time' => '18:30:00',
            'location' => 'Main Gym',
            'capacity' => 20
        ],
        [
            'name' => 'Weekend Strength',
            'description' => 'Focus on building strength and muscle tone with weights and resistance training.',
            'day_of_week' => 'Saturday',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'location' => 'Weight Room',
            'capacity' => 12
        ]
    ];
    
    foreach ($sample_classes as $class) {
        $db->query(
            "INSERT INTO fitness_classes (name, description, trainer_id, day_of_week, start_time, end_time, location, capacity) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $class['name'], 
                $class['description'], 
                $trainer['id'], 
                $class['day_of_week'], 
                $class['start_time'], 
                $class['end_time'], 
                $class['location'], 
                $class['capacity']
            ]
        );
    }
    
    $success_message = "Sample classes have been created for you. You can view and manage them below.";
}

// Get today's weekday name
$today_weekday = date('l'); // e.g., Monday, Tuesday, etc.

// Fetch classes for today
$today_classes = $db->fetchAll("
    SELECT * FROM fitness_classes 
    WHERE trainer_id = ? AND day_of_week = ? 
    ORDER BY start_time ASC
", [$trainer['id'], $today_weekday]);

// Fetch all classes for this trainer
$classes = $db->fetchAll("
    SELECT * FROM fitness_classes 
    WHERE trainer_id = ? 
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time ASC
", [$trainer['id']]);

// Check if viewing a specific class
$view_class = null;
$members = [];
$booking_date = date('Y-m-d');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $class_id = (int)$_GET['id'];
    
    // Get requested booking date if provided
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $booking_date = $_GET['date'];
    }
    
    // Get class details
    $view_class = $db->fetchSingle("
        SELECT * FROM fitness_classes 
        WHERE id = ? AND trainer_id = ?
    ", [$class_id, $trainer['id']]);
    
    if ($view_class) {
        // Get members enrolled in this class
        $members = $db->fetchAll("
            SELECT u.*, cb.status, cb.id as booking_id
            FROM users u
            JOIN class_bookings cb ON u.id = cb.user_id
            WHERE cb.class_id = ? AND cb.booking_date = ?
            ORDER BY u.first_name ASC, u.last_name ASC
        ", [$class_id, $booking_date]);
    }
}

// Process attendance form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    $class_id = (int)$_POST['class_id'];
    $booking_date = $_POST['booking_date'];
    
    // Check if class belongs to this trainer
    $class_check = $db->fetchSingle("
        SELECT id FROM fitness_classes WHERE id = ? AND trainer_id = ?
    ", [$class_id, $trainer['id']]);
    
    if ($class_check) {
        // Update class notes if provided
        if (isset($_POST['class_notes'])) {
            $class_notes = trim($_POST['class_notes']);
            $db->query("
                UPDATE fitness_classes SET notes = ? WHERE id = ?
            ", [$class_notes, $class_id]);
        }
        
        // Update attendance status for each member
        if (isset($_POST['attendance']) && is_array($_POST['attendance'])) {
            foreach ($_POST['attendance'] as $user_id => $status) {
                // Update attendance status
                $db->query("
                    UPDATE class_bookings 
                    SET status = ? 
                    WHERE user_id = ? AND class_id = ? AND booking_date = ?
                ", [$status, $user_id, $class_id, $booking_date]);
            }
        }
        
        $success_message = "Attendance records updated successfully.";
        
        // Refresh member list with updated status
        if ($view_class) {
            $members = $db->fetchAll("
                SELECT u.*, cb.status, cb.id as booking_id
                FROM users u
                JOIN class_bookings cb ON u.id = cb.user_id
                WHERE cb.class_id = ? AND cb.booking_date = ?
                ORDER BY u.first_name ASC, u.last_name ASC
            ", [$class_id, $booking_date]);
        }
    } else {
        $error_message = "You don't have permission to update this class.";
    }
}

// Set page title based on view
$page_title = $view_class ? 'Class Details' : 'Manage Classes';
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
                        <h4><?php echo $page_title; ?></h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <?php if ($view_class): ?>
                                    <li class="breadcrumb-item"><a href="classes.php">Classes</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Class Details</li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active" aria-current="page">Classes</li>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                    <?php if (!$view_class): ?>
                    <div>
                        <a href="schedule.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-calendar-alt"></i> View Schedule
                        </a>
                        <button class="btn btn-role-primary" data-bs-toggle="modal" data-bs-target="#addClassModal" id="addNewClassBtn">
                            <i class="fas fa-plus"></i> Add Class
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($view_class): ?>
                <!-- Single Class View with Attendance Taking -->
                <div class="row">
                    <!-- Class Details Card -->
                    <div class="col-md-4">
                        <div class="card bg-dark text-white">
                            <div class="card-header">
                                <h5 class="mb-0">Class Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <?php 
                                    $class_image = !empty($view_class['image']) ? '../../uploads/classes/' . $view_class['image'] : '../../assets/images/Classes/yoga.jpg';
                                    ?>
                                    <img src="<?php echo $class_image; ?>" alt="<?php echo $view_class['name']; ?>" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Name</h6>
                                    <p class="mb-0"><?php echo htmlspecialchars($view_class['name']); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Schedule</h6>
                                    <p class="mb-0">
                                        <?php echo htmlspecialchars($view_class['day_of_week']); ?>,
                                        <?php 
                                        $start_time = date("h:i A", strtotime($view_class['start_time']));
                                        $end_time = date("h:i A", strtotime($view_class['end_time']));
                                        echo $start_time . ' - ' . $end_time; 
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Location</h6>
                                    <p class="mb-0"><?php echo htmlspecialchars($view_class['location']); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Capacity</h6>
                                    <?php 
                                    // Get current enrollment
                                    $enrolled = $db->fetchSingle(
                                        "SELECT COUNT(DISTINCT user_id) as count FROM class_bookings WHERE class_id = ?",
                                        [$view_class['id']]
                                    );
                                    $enrolled_count = $enrolled ? $enrolled['count'] : 0;
                                    $capacity = $view_class['capacity'];
                                    $percentage = min(100, round(($enrolled_count / $capacity) * 100));
                                    $progress_class = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar <?php echo $progress_class; ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span><?php echo $enrolled_count; ?>/<?php echo $capacity; ?></span>
                                    </div>
                                </div>
                                
                                <div class="mb-0">
                                    <h6 class="text-muted mb-1">Description</h6>
                                    <p class="mb-0"><?php echo htmlspecialchars(isset($view_class['description']) ? $view_class['description'] : 'No description available.'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Class Notes Form -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Class Notes</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <input type="hidden" name="update_attendance" value="1">
                                    <input type="hidden" name="class_id" value="<?php echo $view_class['id']; ?>">
                                    <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
                                    
                                    <div class="mb-3">
                                        <textarea class="form-control" name="class_notes" rows="4" placeholder="Add notes about this class session..."><?php echo htmlspecialchars(isset($view_class['notes']) ? $view_class['notes'] : ''); ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Notes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance Taking -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Attendance for <?php echo date('F j, Y', strtotime($booking_date)); ?></h5>
                                <div class="input-group" style="max-width: 200px;">
                                    <input type="date" class="form-control" id="attendance-date" value="<?php echo $booking_date; ?>">
                                    <button class="btn btn-outline-secondary" type="button" id="change-date">Go</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (count($members) > 0): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="update_attendance" value="1">
                                        <input type="hidden" name="class_id" value="<?php echo $view_class['id']; ?>">
                                        <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
                                        
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Member</th>
                                                        <th>Email</th>
                                                        <th class="text-center">Attendance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($members as $member): 
                                                        // Set default status if not present
                                                        $status = isset($member['status']) ? $member['status'] : 'pending';
                                                        
                                                        $member_image = isset($member['profile_image']) && !empty($member['profile_image']) 
                                                            ? '../../uploads/profile/' . $member['profile_image'] 
                                                            : '../../assets/images/testimonials/client-1.jpg';
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img src="<?php echo $member_image; ?>" alt="Member" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                    <div>
                                                                        <div class="fw-bold"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></div>
                                                                        <a href="members.php?id=<?php echo $member['id']; ?>" class="small text-muted">View Profile</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                                                            <td>
                                                                <div class="btn-group attendance-buttons w-100" role="group">
                                                                    <input type="radio" class="btn-check" name="attendance[<?php echo $member['id']; ?>]" id="attended_<?php echo $member['id']; ?>" value="attended" <?php echo ($status === 'attended') ? 'checked' : ''; ?>>
                                                                    <label class="btn btn-outline-success" for="attended_<?php echo $member['id']; ?>">Present</label>
                                                                    
                                                                    <input type="radio" class="btn-check" name="attendance[<?php echo $member['id']; ?>]" id="no-show_<?php echo $member['id']; ?>" value="no-show" <?php echo ($status === 'no-show') ? 'checked' : ''; ?>>
                                                                    <label class="btn btn-outline-danger" for="no-show_<?php echo $member['id']; ?>">Absent</label>
                                                                    
                                                                    <input type="radio" class="btn-check" name="attendance[<?php echo $member['id']; ?>]" id="pending_<?php echo $member['id']; ?>" value="pending" <?php echo ($status === 'pending' || $status === null) ? 'checked' : ''; ?>>
                                                                    <label class="btn btn-outline-secondary" for="pending_<?php echo $member['id']; ?>">Pending</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Attendance
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-users-slash fa-4x mb-3 text-muted"></i>
                                        <h5>No Members Enrolled</h5>
                                        <p>There are no members enrolled in this class yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Classes List -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Today's Classes</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($today_classes)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Time</th>
                                                    <th>Location</th>
                                                    <th>Enrolled</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($today_classes as $class): 
                                                    // Get current enrollment
                                                    $enrolled = $db->fetchSingle(
                                                        "SELECT COUNT(DISTINCT user_id) as count FROM class_bookings WHERE class_id = ?",
                                                        [$class['id']]
                                                    );
                                                    $enrolled_count = $enrolled ? $enrolled['count'] : 0;
                                                    $capacity = $class['capacity'];
                                                    
                                                    $start_time = date("h:i A", strtotime($class['start_time']));
                                                    $end_time = date("h:i A", strtotime($class['end_time']));
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                                                        <td><?php echo $start_time . ' - ' . $end_time; ?></td>
                                                        <td><?php echo htmlspecialchars($class['location']); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $enrolled_count >= $capacity ? 'bg-danger' : 'bg-primary'; ?>">
                                                                <?php echo $enrolled_count . '/' . $capacity; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="?id=<?php echo $class['id']; ?>&date=<?php echo date('Y-m-d'); ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-clipboard-check"></i> Take Attendance
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="far fa-calendar-times fa-3x mb-3"></i>
                                        <h5>No Classes Today</h5>
                                        <p>You don't have any classes scheduled for today.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- All Classes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All My Classes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($classes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Day</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Enrolled</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Group classes by day for better organization
                                        $classes_by_day = [];
                                        foreach ($classes as $class) {
                                            $classes_by_day[$class['day_of_week']][] = $class;
                                        }
                                        
                                        // Set custom day order
                                        $day_order = [
                                            'Monday' => 1,
                                            'Tuesday' => 2,
                                            'Wednesday' => 3,
                                            'Thursday' => 4,
                                            'Friday' => 5,
                                            'Saturday' => 6,
                                            'Sunday' => 7
                                        ];
                                        
                                        // Sort by day
                                        uksort($classes_by_day, function($a, $b) use ($day_order) {
                                            return $day_order[$a] <=> $day_order[$b];
                                        });
                                        
                                        foreach ($classes_by_day as $day => $day_classes):
                                            // Day header
                                            echo '<tr class="table-secondary">';
                                            echo '<td colspan="6"><strong>' . $day . '</strong></td>';
                                            echo '</tr>';
                                            
                                            // Sort classes by time
                                            usort($day_classes, function($a, $b) {
                                                return $a['start_time'] <=> $b['start_time'];
                                            });
                                            
                                            foreach ($day_classes as $class): 
                                                // Get current enrollment
                                                $enrolled = $db->fetchSingle(
                                                    "SELECT COUNT(DISTINCT user_id) as count FROM class_bookings WHERE class_id = ?",
                                                    [$class['id']]
                                                );
                                                $enrolled_count = $enrolled ? $enrolled['count'] : 0;
                                                $capacity = $class['capacity'];
                                                
                                                $start_time = date("h:i A", strtotime($class['start_time']));
                                                $end_time = date("h:i A", strtotime($class['end_time']));
                                        ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($class['name']); ?></td>
                                                    <td><?php echo $day; ?></td>
                                                    <td><?php echo $start_time . ' - ' . $end_time; ?></td>
                                                    <td><?php echo htmlspecialchars($class['location']); ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                <div class="progress-bar <?php echo $enrolled_count >= $capacity ? 'bg-danger' : 'bg-primary'; ?>" 
                                                                     role="progressbar" 
                                                                     style="width: <?php echo min(100, round(($enrolled_count / $capacity) * 100)); ?>%" 
                                                                     aria-valuenow="<?php echo $enrolled_count; ?>" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="<?php echo $capacity; ?>"></div>
                                                            </div>
                                                            <span><?php echo $enrolled_count . '/' . $capacity; ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="?id=<?php echo $class['id']; ?>&date=<?php echo date('Y-m-d'); ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-dumbbell fa-4x mb-3 text-muted"></i>
                                <h5>No Classes Found</h5>
                                <p>You don't have any assigned classes yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">
                        <?php echo $edit_class ? 'Edit Class' : 'Add New Class'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="classForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save_class">
                        
                        <?php if ($edit_class): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $edit_class['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Class Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['name']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (e.g., 45 min) *</label>
                                    <input type="text" class="form-control" id="duration" name="duration" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['duration']) : '45 min'; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Difficulty Level *</label>
                                    <select class="form-select" id="difficulty" name="difficulty" required>
                                        <option value="">Select Difficulty</option>
                                        <option value="Beginner" <?php echo ($edit_class && $edit_class['difficulty'] == 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                                        <option value="Intermediate" <?php echo ($edit_class && $edit_class['difficulty'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="Advanced" <?php echo ($edit_class && $edit_class['difficulty'] == 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                                        <option value="All Levels" <?php echo ($edit_class && $edit_class['difficulty'] == 'All Levels') ? 'selected' : ''; ?>>All Levels</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="schedule_days" class="form-label">Schedule Days (e.g., Mon, Wed, Fri) *</label>
                                    <input type="text" class="form-control" id="schedule_days" name="schedule_days" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['schedule_days']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="schedule_times" class="form-label">Schedule Times (e.g., 8:30 AM, 7:00 PM) *</label>
                                    <input type="text" class="form-control" id="schedule_times" name="schedule_times" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['schedule_times']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Class Image <?php echo $edit_class ? '(Leave empty to keep current)' : '*'; ?></label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" <?php echo $edit_class ? '' : 'required'; ?>>
                                    <div class="form-text">Recommended size: 800x600px. Max 2MB.</div>
                                </div>
                                
                                <?php if ($edit_class && !empty($edit_class['image'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Current Image</label>
                                        <div>
                                            <img src="../../assets/images/Classes/<?php echo $edit_class['image']; ?>" 
                                                 alt="Current Image" style="max-width: 100%; max-height: 150px;">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $edit_class ? htmlspecialchars($edit_class['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-role-primary">
                            <?php echo $edit_class ? 'Update Class' : 'Add Class'; ?>
                        </button>
                    </div>
                </form>
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
        
        // Date change button
        if (document.getElementById('change-date')) {
            document.getElementById('change-date').addEventListener('click', function() {
                const date = document.getElementById('attendance-date').value;
                if (date) {
                    window.location.href = '?id=<?php echo $view_class ? $view_class['id'] : ''; ?>&date=' + date;
                }
            });
        }
    });
    </script>
</body>
</html>
