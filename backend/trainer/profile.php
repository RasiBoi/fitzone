<?php
/**
 * FitZone Fitness Center
 * Trainer Profile Management
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

// Initialize messages
$success_message = '';
$error_message = '';

// Check if first-time setup
$setup_mode = isset($_GET['setup']) && $_GET['setup'] == 1;

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

// Get trainer details
$trainer = $db->fetchSingle(
    "SELECT * FROM trainers WHERE user_id = ?",
    [$user_id]
);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Profile Update
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        // Validate inputs
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error_message = "Please fill in all required fields.";
        } else {
            // Update user details
            $result = $db->query(
                "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?",
                [$first_name, $last_name, $email, $phone, $user_id]
            );
            
            if ($result) {
                $success_message = "Your profile has been updated successfully.";
                
                // Reload user details
                $user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
            } else {
                $error_message = "Failed to update profile. Please try again.";
            }
        }
    }
    
    // Trainer Details Update
    elseif (isset($_POST['update_trainer_details'])) {
        $specialization = trim($_POST['specialization']);
        $bio = trim($_POST['bio']);
        $certifications = trim($_POST['certifications']);
        $experience = (int)$_POST['experience'];
        
        if ($trainer) {
            // Update existing trainer record
            $result = $db->query(
                "UPDATE trainers SET specialization = ?, bio = ?, certifications = ?, experience = ? WHERE user_id = ?",
                [$specialization, $bio, $certifications, $experience, $user_id]
            );
        } else {
            // Create new trainer record
            $result = $db->query(
                "INSERT INTO trainers (user_id, specialization, bio, certifications, experience) VALUES (?, ?, ?, ?, ?)",
                [$user_id, $specialization, $bio, $certifications, $experience]
            );
        }
        
        if ($result) {
            $success_message = "Your trainer profile has been updated successfully.";
            
            // Reload trainer details
            $trainer = $db->fetchSingle("SELECT * FROM trainers WHERE user_id = ?", [$user_id]);
            
            // Redirect to dashboard if in setup mode
            if ($setup_mode) {
                redirect('index.php');
            }
        } else {
            $error_message = "Failed to update trainer details. Please try again.";
        }
    }
    
    // Password Change
    elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate passwords
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match.";
        } elseif (strlen($new_password) < 8) {
            $error_message = "New password must be at least 8 characters long.";
        } else {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $result = $db->query(
                    "UPDATE users SET password = ? WHERE id = ?",
                    [$hashed_password, $user_id]
                );
                
                if ($result) {
                    $success_message = "Your password has been updated successfully.";
                } else {
                    $error_message = "Failed to update password. Please try again.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        }
    }
    
    // Profile Image Upload
    elseif (isset($_POST['upload_image'])) {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowed_types)) {
                $error_message = "Only JPG and PNG images are allowed.";
            } else {
                // Create uploads directory if it doesn't exist
                $upload_dir = '../../uploads/profile/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $filename = time() . '_' . basename($file['name']);
                $target_path = $upload_dir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    // Update user profile image
                    $result = $db->query(
                        "UPDATE users SET profile_image = ? WHERE id = ?",
                        [$filename, $user_id]
                    );
                    
                    if ($result) {
                        $success_message = "Profile image uploaded successfully.";
                        
                        // Reload user details
                        $user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
                    } else {
                        $error_message = "Failed to update profile image in database.";
                    }
                } else {
                    $error_message = "Failed to upload image. Please try again.";
                }
            }
        } else {
            $error_message = "Please select an image to upload.";
        }
    }
}

// Set page title
$page_title = $setup_mode ? 'Complete Your Profile' : 'My Profile';
$active_page = 'profile';

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
        .profile-cover-container {
            position: relative;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 60px;
        }
        .profile-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: linear-gradient(45deg, #313131, #000000);
        }
        .profile-image-container {
            position: absolute;
            bottom: -50px;
            left: 50px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--secondary);
            overflow: hidden;
        }
        .profile-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .profile-image-container:hover .profile-image-overlay {
            opacity: 1;
        }
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-info {
            padding-left: 190px;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <?php if (!$setup_mode): ?>
            <!-- Include Sidebar -->
            <?php include '../../includes/sidebar-trainer.php'; ?>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div class="main-content <?php echo $setup_mode ? 'w-100' : ''; ?>">
            <?php if (!$setup_mode): ?>
                <!-- Include Topbar -->
                <?php include '../../includes/dashboard-topbar.php'; ?>
            <?php else: ?>
                <!-- Setup Mode Header -->
                <div class="py-4 px-4 bg-dark">
                    <div class="d-flex align-items-center">
                        <img src="../../assets/images/fitzonelogo.png" alt="FitZone Logo" height="40" class="me-3">
                        <h4 class="m-0 text-white">Trainer Onboarding</h4>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Page Content -->
            <div class="content">
                <?php if (!$setup_mode): ?>
                    <!-- Page Header -->
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4><?php echo $page_title; ?></h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Setup Mode Header -->
                    <div class="my-4 text-center">
                        <h3>Complete Your Trainer Profile</h3>
                        <p class="text-muted">Please fill in your trainer details to get started</p>
                    </div>
                <?php endif; ?>
                
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
                
                <?php if (!$setup_mode): ?>
                <!-- Profile Header -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <!-- Profile Cover & Image -->
                        <div class="profile-cover-container">
                            <div class="profile-cover"></div>
                            <div class="profile-image-container">
                                <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="profile-image">
                                <div class="profile-image-overlay" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                    <i class="fas fa-camera fa-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Info -->
                        <div class="profile-info pb-4">
                            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                            <p class="text-muted">Fitness Trainer at FitZone</p>
                            <?php if ($trainer && !empty($trainer['specialization'])): ?>
                                <div class="mb-3">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($trainer['specialization']); ?></span>
                                    <span class="badge bg-secondary"><?php echo $trainer['experience']; ?> years experience</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Profile Tabs -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <?php if (!$setup_mode): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-controls="basic" aria-selected="true">
                                        Basic Info
                                    </button>
                                </li>
                            <?php endif; ?>
                            
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $setup_mode ? 'active' : ''; ?>" id="trainer-tab" data-bs-toggle="tab" data-bs-target="#trainer" type="button" role="tab" aria-controls="trainer" aria-selected="<?php echo $setup_mode ? 'true' : 'false'; ?>">
                                    Trainer Details
                                </button>
                            </li>
                            
                            <?php if (!$setup_mode): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                        Change Password
                                    </button>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="card-body">
                        <div class="tab-content">
                            <?php if (!$setup_mode): ?>
                                <!-- Basic Info Tab -->
                                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                                    <h5 class="mb-4">Update Basic Information</h5>
                                    <form method="post" action="">
                                        <input type="hidden" name="update_profile" value="1">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                                <div class="form-text">Username cannot be changed</div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email *</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="first_name" class="form-label">First Name *</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="last_name" class="form-label">Last Name *</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Trainer Details Tab -->
                            <div class="tab-pane fade <?php echo $setup_mode ? 'show active' : ''; ?>" id="trainer" role="tabpanel" aria-labelledby="trainer-tab">
                                <h5 class="mb-4"><?php echo $setup_mode ? 'Complete Your Trainer Profile' : 'Update Trainer Details'; ?></h5>
                                <form method="post" action="">
                                    <input type="hidden" name="update_trainer_details" value="1">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="specialization" class="form-label">Specialization *</label>
                                            <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($trainer['specialization'] ?? ''); ?>" placeholder="e.g., Yoga, CrossFit, Strength Training" required>
                                            <div class="form-text">Enter your main areas of expertise</div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="experience" class="form-label">Years of Experience *</label>
                                            <input type="number" class="form-control" id="experience" name="experience" value="<?php echo (int)($trainer['experience'] ?? 1); ?>" min="1" required>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="certifications" class="form-label">Certifications</label>
                                            <textarea class="form-control" id="certifications" name="certifications" rows="3" placeholder="List your certifications, one per line"><?php echo htmlspecialchars($trainer['certifications'] ?? ''); ?></textarea>
                                            <div class="form-text">Enter all professional certifications you hold</div>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="bio" class="form-label">Bio / About Me *</label>
                                            <textarea class="form-control" id="bio" name="bio" rows="5" placeholder="Write a brief introduction about yourself, your approach to fitness, and your coaching philosophy..." required><?php echo htmlspecialchars($trainer['bio'] ?? ''); ?></textarea>
                                            <div class="form-text">This bio will be visible to members</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <?php echo $setup_mode ? 'Complete Profile' : 'Save Changes'; ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <?php if (!$setup_mode): ?>
                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <h5 class="mb-4">Change Password</h5>
                                    <form method="post" action="">
                                        <input type="hidden" name="change_password" value="1">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="current_password" class="form-label">Current Password *</label>
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="new_password" class="form-label">New Password *</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                <div class="form-text">Password must be at least 8 characters long</div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!$setup_mode): ?>
        <!-- Upload Image Modal -->
        <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadImageModalLabel">Upload Profile Image</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="upload_image" value="1">
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">Select Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg, image/png" required>
                                <div class="form-text">Recommended size: 300x300 pixels (square image)</div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <div id="image_preview" style="display: none; margin: 0 auto;">
                                    <img id="preview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!$setup_mode): ?>
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
            
            // Image preview
            document.getElementById('profile_image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview').src = e.target.result;
                        document.getElementById('image_preview').style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        <?php endif; ?>
    });
    </script>
</body>
</html>