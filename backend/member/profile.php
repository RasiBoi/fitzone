<?php
/**
 * FitZone Fitness Center
 * Member Profile Page
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

// Connect to database
$db = getDb();

// Get user details
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
$id_field = 'id';

// If user not found, try with user_id column
if (!$user) {
    $user = $db->fetchSingle("SELECT * FROM users WHERE user_id = ?", [$user_id]);
    $id_field = 'user_id'; // Remember which field worked
}

// If user still not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Initialize variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Get form data
        $first_name = isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
        $address = isset($_POST['address']) ? sanitize($_POST['address']) : '';
        $city = isset($_POST['city']) ? sanitize($_POST['city']) : '';
        $date_of_birth = isset($_POST['date_of_birth']) ? sanitize($_POST['date_of_birth']) : '';
        
        // Validate form data (basic validation)
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error_message = 'First name, last name, and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Invalid email format.';
        } else {
            // Check if email is already in use by another user
            $existing_user = $db->fetchSingle("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user_id]);
            if ($existing_user) {
                $error_message = 'Email is already in use by another account.';
            } else {
                $profile_image = isset($user['profile_image']) ? $user['profile_image'] : ''; // Default to current value
                
                // Handle profile image upload if one was provided
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    // Create upload directory if it doesn't exist
                    $upload_dir = '../../uploads/profile/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Get file info
                    $file_tmp = $_FILES['profile_image']['tmp_name'];
                    $file_name = time() . '_' . $_FILES['profile_image']['name'];
                    $file_path = $upload_dir . $file_name;
                    
                    // Check if file is an actual image
                    $check = getimagesize($file_tmp);
                    if ($check !== false) {
                        // Try to move the uploaded file
                        if (move_uploaded_file($file_tmp, $file_path)) {
                            // Remove old profile image if it exists and is not default
                            if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.jpg') {
                                $old_file = $upload_dir . $user['profile_image'];
                                if (file_exists($old_file)) {
                                    @unlink($old_file);
                                }
                            }
                            $profile_image = $file_name;
                        } else {
                            $error_message = 'Failed to upload profile image. Check directory permissions.';
                        }
                    } else {
                        $error_message = 'File is not a valid image.';
                    }
                }
                
                // Only proceed with the update if there were no errors with image upload
                if (empty($error_message)) {
                    // Update user profile in database - include all form fields
                    $query = "UPDATE users SET 
                        first_name = ?, 
                        last_name = ?, 
                        email = ?,
                        phone = ?,
                        address = ?,
                        city = ?,
                        date_of_birth = ?";
                    
                    // Create params array with all fields
                    $params = [
                        $first_name, 
                        $last_name, 
                        $email,
                        $phone,
                        $address,
                        $city,
                        $date_of_birth
                    ];
                    
                    // Add profile image if it was changed
                    if ($profile_image) {
                        $query .= ", profile_image = ?";
                        $params[] = $profile_image;
                    }
                    
                    // Add the WHERE clause at the end
                    $query .= " WHERE $id_field = ?";
                    $params[] = $user_id;
                    
                    // Enable PDO error mode temporarily for debugging
                    $conn = $db->getConnection();
                    $old_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    try {
                        // Prepare and execute the statement directly
                        $stmt = $conn->prepare($query);
                        $update_success = $stmt->execute($params);
                        
                        if ($update_success) {
                            $success_message = 'Profile updated successfully.';
                            
                            // Force reload user data to reflect changes immediately
                            $user = $db->fetchSingle("SELECT * FROM users WHERE $id_field = ?", [$user_id]);
                            
                            // Update the image URL if it was changed
                            if ($profile_image) {
                                $profile_image_url = '../../uploads/profile/' . $profile_image;
                            }
                        } else {
                            $error_message = 'Failed to update profile. Database returned no error.';
                        }
                    } catch (PDOException $e) {
                        // Capture the specific database error
                        $error_message = 'Database error: ' . $e->getMessage();
                    } finally {
                        // Restore original error mode
                        $conn->setAttribute(PDO::ATTR_ERRMODE, $old_error_mode);
                    }
                }
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Get password data
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate password data
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'New password and confirmation do not match.';
        } else {
            // Verify current password
            $user_password = isset($user['password']) ? $user['password'] : '';
            if (!verifyPassword($current_password, $user_password)) {
                $error_message = 'Current password is incorrect.';
            } else {
                // Hash the new password
                $hashed_password = hashPassword($new_password);
                
                // Update the password in the database
                try {
                    $update_success = $db->query(
                        "UPDATE users SET password = ? WHERE $id_field = ?",
                        [$hashed_password, $user_id]
                    );
                    
                    if ($update_success) {
                        $success_message = 'Password changed successfully.';
                    } else {
                        $error_message = 'Failed to change password.';
                    }
                } catch (Exception $e) {
                    $error_message = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Set page title
$page_title = 'My Profile';

// Get profile image URL (default if not set)
$profile_image_url = '../../assets/images/trainers/trainer-1.jpg'; // Default image
if (!empty($user['profile_image'])) {
    $profile_image_path = '../../uploads/profile/' . $user['profile_image'];
    if (file_exists($profile_image_path)) {
        $profile_image_url = $profile_image_path;
    }
}

// Current active sidebar item
$active_page = 'profile';
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
                        My Profile
                    </div>
                </div>
                
                <div class="topbar-right">
                    <div class="dropdown">
                        <div class="user-dropdown" data-bs-toggle="dropdown">
                            <img src="<?php echo $profile_image_url; ?>" alt="Profile" class="profile-img">
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
                        <h4>My Profile</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Alert messages -->
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Profile content -->
                <div class="row">
                    <!-- Profile Photo Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Profile Photo</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="<?php echo $profile_image_url; ?>" alt="Profile" class="profile-avatar" id="profile-preview">
                                
                                <div class="mt-4">
                                    <h5><?php echo htmlspecialchars(isset($user['first_name']) ? $user['first_name'] : '') . ' ' . htmlspecialchars(isset($user['last_name']) ? $user['last_name'] : ''); ?></h5>
                                    <span class="badge bg-success">Member</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Information Card -->
                    <div class="col-md-8 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <!-- Profile form -->
                                <form method="post" action="" enctype="multipart/form-data">
                                    <!-- Profile image upload -->
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Change Profile Photo</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                        <small class="form-text text-white">Upload a new profile picture (JPG, PNG, or GIF).</small>
                                    </div>
                                    
                                    <!-- Basic info -->
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars(isset($user['first_name']) ? $user['first_name'] : ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars(isset($user['last_name']) ? $user['last_name'] : ''); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Contact info -->
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars(isset($user['email']) ? $user['email'] : ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars(isset($user['phone']) ? $user['phone'] : ''); ?>">
                                        </div>
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" 
                                               value="<?php echo htmlspecialchars(isset($user['address']) ? $user['address'] : ''); ?>">
                                    </div>
                                    
                                    <!-- Additional info -->
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city" name="city" 
                                                   value="<?php echo htmlspecialchars(isset($user['city']) ? $user['city'] : ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                   value="<?php echo htmlspecialchars(isset($user['date_of_birth']) ? $user['date_of_birth'] : ''); ?>">
                                        </div>
                                    </div>
                                    
                                    <!-- Submit button -->
                                    <div class="mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Security -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Account Security</h5>
                                <a href="#" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-1"></i> Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="change_password" class="btn btn-success">Change Password</button>
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
        
        // Preview profile image before upload
        const fileInput = document.getElementById('profile_image');
        const previewImg = document.getElementById('profile-preview');
        
        if (fileInput && previewImg) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        // Auto-collapse sidebar on small screens
        function checkScreenSize() {
            if (window.innerWidth < 992) {
                document.querySelector('body').classList.add('sidebar-collapsed');
            }
        }
        
        // Check on load and resize
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
    });
    </script>
</body>
</html>
