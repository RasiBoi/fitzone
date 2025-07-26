<?php
/**
 * FitZone Fitness Center - Admin Classes Management
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

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../../login.php');
}

// Get current user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Initialize messages and errors
$message = '';
$error = '';

// Handle class deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $class_id = (int)$_GET['delete'];
    
    // Don't delete image files from assets directory as they're part of core files
    $result = $db->query("DELETE FROM fitness_classes WHERE id = ?", [$class_id]);
    
    if ($result) {
        $message = "Class deleted successfully.";
    } else {
        $error = "Failed to delete class.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $difficulty = trim($_POST['difficulty']);
    $trainer = trim($_POST['trainer']);
    $schedule_days = trim($_POST['schedule_days']);
    $schedule_times = trim($_POST['schedule_times']);
    
    // Validate inputs
    if (empty($name) || empty($description) || empty($duration) || empty($difficulty) || empty($trainer) || empty($schedule_days) || empty($schedule_times)) {
        $error = "Please fill in all required fields.";
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
                $error = "Only JPG, JPEG & PNG files are allowed.";
                $upload_error = true;
            }
            
            // Check file size (2MB max)
            if ($_FILES['image']['size'] > 2097152) {
                $error = "Image size should be less than 2MB.";
                $upload_error = true;
            }
            
            if (!$upload_error) {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error = "Failed to upload image.";
                    $upload_error = true;
                }
            }
        }
        
        if ($error === '') {
            if ($edit_id > 0) {
                // Update existing class
                if (!empty($_FILES['image']['name']) && !$upload_error) {
                    // Delete old image if it exists
                    $old_image = $db->fetchSingle("SELECT image FROM fitness_classes WHERE id = ?", [$edit_id]);
                    if ($old_image && !empty($old_image['image'])) {
                        $old_image_path = '../../assets/images/Classes/' . $old_image['image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    
                    // Update with new image
                    $result = $db->query(
                        "UPDATE fitness_classes SET name = ?, description = ?, duration = ?, difficulty = ?, 
                        trainer = ?, schedule_days = ?, schedule_times = ?, image = ?, updated_at = NOW() 
                        WHERE id = ?",
                        [$name, $description, $duration, $difficulty, $trainer, $schedule_days, $schedule_times, $image_name, $edit_id]
                    );
                } else {
                    // Update without changing image
                    $result = $db->query(
                        "UPDATE fitness_classes SET name = ?, description = ?, duration = ?, difficulty = ?, 
                        trainer = ?, schedule_days = ?, schedule_times = ?, updated_at = NOW() 
                        WHERE id = ?",
                        [$name, $description, $duration, $difficulty, $trainer, $schedule_days, $schedule_times, $edit_id]
                    );
                }
                
                if ($result) {
                    $message = "Class updated successfully.";
                    // Redirect to remove the edit parameter
                    header("Location: classes.php?update_success=1");
                    exit;
                } else {
                    $error = "Failed to update class.";
                }
            } else {
                // Add new class
                if (empty($_FILES['image']['name']) || $upload_error) {
                    $error = "Please upload an image for the class.";
                } else {
                    $result = $db->query(
                        "INSERT INTO fitness_classes (name, description, duration, difficulty, trainer, schedule_days, schedule_times, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$name, $description, $duration, $difficulty, $trainer, $schedule_days, $schedule_times, $image_name]
                    );
                    
                    if ($result) {
                        $message = "Class added successfully.";
                    } else {
                        $error = "Failed to add class.";
                    }
                }
            }
        }
    }
}

// Check for update success message
if (isset($_GET['update_success'])) {
    $message = "Class updated successfully.";
}

// Get class to edit
$edit_class = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_class = $db->fetchSingle("SELECT * FROM fitness_classes WHERE id = ?", [$edit_id]);
}

// Get all classes for listing
$classes = $db->fetchAll("SELECT * FROM fitness_classes ORDER BY id DESC");

// Get all trainers for dropdown
$trainers = $db->fetchAll("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role = 'trainer' ORDER BY first_name ASC");

// Set page title
$page_title = 'Manage Classes';
$active_page = 'classes';

// Get profile image URL (default if not set)
$user = $db->fetchSingle("SELECT * FROM users WHERE id = ?", [$user_id]);
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
        .class-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .class-description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="role-<?php echo $user_role; ?>">
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../../includes/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Include Topbar -->
            <?php include '../../includes/dashboard-topbar.php'; ?>
            
            <!-- Page Content -->
            <div class="content">
                <!-- Page Header -->
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Manage Classes</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Classes</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-role-primary" data-bs-toggle="modal" data-bs-target="#addClassModal" id="addNewClassBtn">
                        <i class="fas fa-plus"></i> Add Class
                    </button>
                </div>
                
                <!-- Alert Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Classes List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Classes List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Duration</th>
                                        <th>Difficulty</th>
                                        <th>Trainer</th>
                                        <th>Schedule</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($classes) > 0): ?>
                                        <?php foreach($classes as $class): ?>
                                            <tr>
                                                <td>
                                                    <img src="../../assets/images/Classes/<?php echo $class['image']; ?>" alt="<?php echo htmlspecialchars($class['name']); ?>" class="class-image">
                                                </td>
                                                <td><?php echo htmlspecialchars($class['name']); ?></td>
                                                <td class="class-description" title="<?php echo htmlspecialchars($class['description']); ?>">
                                                    <?php echo htmlspecialchars($class['description']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($class['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($class['difficulty']); ?></td>
                                                <td><?php echo htmlspecialchars($class['trainer']); ?></td>
                                                <td>
                                                    <small>
                                                        <strong>Days:</strong> <?php echo htmlspecialchars($class['schedule_days']); ?><br>
                                                        <strong>Times:</strong> <?php echo htmlspecialchars($class['schedule_times']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?php echo $class['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $class['id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this class?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No classes found.</td>
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
                                    <label for="duration" class="form-label">Duration (e.g., 45 minutes) *</label>
                                    <input type="text" class="form-control" id="duration" name="duration" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['duration']) : ''; ?>" required>
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
                                
                                <div class="mb-3">
                                    <label for="trainer" class="form-label">Trainer *</label>
                                    <input type="text" class="form-control" id="trainer" name="trainer" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['trainer']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="schedule_days" class="form-label">Schedule Days (e.g., Mon, Wed, Fri) *</label>
                                    <input type="text" class="form-control" id="schedule_days" name="schedule_days" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['schedule_days']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="schedule_times" class="form-label">Schedule Times (e.g., 10:00 AM - 11:00 AM) *</label>
                                    <input type="text" class="form-control" id="schedule_times" name="schedule_times" 
                                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['schedule_times']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Class Image <?php echo $edit_class ? '(Leave empty to keep current)' : '*'; ?></label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" <?php echo $edit_class ? '' : 'required'; ?>>
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
        
        <?php if ($edit_class): ?>
        // Show edit modal automatically if edit parameter is present
        var addClassModal = new bootstrap.Modal(document.getElementById('addClassModal'));
        addClassModal.show();
        <?php endif; ?>
        
        // Reset form when adding a new class
        document.getElementById('addNewClassBtn').addEventListener('click', function() {
            document.getElementById('classForm').reset();
            document.getElementById('addClassModalLabel').textContent = 'Add New Class';
            
            // Remove any hidden edit_id field that might have been added
            var oldHiddenInput = document.querySelector('input[name="edit_id"]');
            if (oldHiddenInput) {
                oldHiddenInput.remove();
            }
        });
    });
    </script>
</body>
</html>