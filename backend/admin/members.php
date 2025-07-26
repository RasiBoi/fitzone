<?php
/**
 * FitZone Fitness Center - Admin Members Management
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

// Handle form submissions
$message = '';
$error = '';

// Handle member deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $member_id = (int)$_GET['delete'];
    $result = $db->query("DELETE FROM users WHERE id = ? AND role = 'member'", [$member_id]);
    
    if ($result) {
        $message = "Member deleted successfully.";
    } else {
        $error = "Failed to delete member.";
    }
}

// Handle member addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Validate inputs
    if (empty($username) || empty($first_name) || empty($last_name) || empty($email)) {
        $error = "Please fill in all required fields.";
    } else {
        // Check if username already exists (only for new members or if changing username)
        $username_check_query = "SELECT id FROM users WHERE username = ? AND id != ?";
        $existing_user = $db->fetchSingle($username_check_query, [$username, $edit_id ?: 0]);
        
        if ($existing_user) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            if ($edit_id > 0) {
                // Update existing member
                $result = $db->query(
                    "UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ? AND role = 'member'",
                    [$username, $first_name, $last_name, $email, $phone, $edit_id]
                );
                
                if ($result) {
                    $message = "Member updated successfully.";
                    // Redirect to remove the edit parameter from URL after successful update
                    header("Location: members.php?update_success=1");
                    exit;
                } else {
                    $error = "Failed to update member.";
                }
            } else {
                // Add new member
                $password = password_hash('fitzone123', PASSWORD_DEFAULT); // Default password
                $result = $db->query(
                    "INSERT INTO users (username, password, email, first_name, last_name, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'member')",
                    [$username, $password, $email, $first_name, $last_name, $phone]
                );
                
                if ($result) {
                    $message = "Member added successfully with default password.";
                } else {
                    $error = "Failed to add member.";
                }
            }
        }
    }
}

// Check for update success message
if (isset($_GET['update_success'])) {
    $message = "Member updated successfully.";
}

// Get member to edit
$edit_member = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_member = $db->fetchSingle("SELECT * FROM users WHERE id = ? AND role = 'member'", [$edit_id]);
}

// Get all members for listing
$members = $db->fetchAll("SELECT * FROM users WHERE role = 'member' ORDER BY id DESC");

// Set page title
$page_title = 'Manage Members';
$active_page = 'members';

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
                        <h4>Manage Members</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Members</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-role-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal" id="addNewMemberBtn">
                        <i class="fas fa-plus"></i> Add Member
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
                
                <!-- Members List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Members List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($members) > 0): ?>
                                        <?php foreach($members as $member): ?>
                                            <tr>
                                                <td><?php echo $member['id']; ?></td>
                                                <td><?php echo htmlspecialchars($member['username']); ?></td>
                                                <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                                <td><?php echo htmlspecialchars(isset($1) ? $1 : 'N/A'); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                                                <td>
                                                    <a href="?edit=<?php echo $member['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $member['id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this member?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No members found.</td>
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

    <!-- Add/Edit Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        <?php echo $edit_member ? 'Edit Member' : 'Add New Member'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="memberForm">
                    <div class="modal-body">
                        <?php if ($edit_member): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $edit_member['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo $edit_member ? htmlspecialchars($edit_member['username']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo $edit_member ? htmlspecialchars($edit_member['first_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo $edit_member ? htmlspecialchars($edit_member['last_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $edit_member ? htmlspecialchars($edit_member['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?php echo $edit_member ? htmlspecialchars(isset($1) ? $1 : '') : ''; ?>">
                        </div>
                        
                        <?php if (!$edit_member): ?>
                            <div class="alert alert-info">
                                A default password <strong>fitzone123</strong> will be set for new members.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-role-primary">
                            <?php echo $edit_member ? 'Update Member' : 'Add Member'; ?>
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
        
        <?php if ($edit_member): ?>
        // Show edit modal automatically if edit parameter is present
        var addMemberModal = new bootstrap.Modal(document.getElementById('addMemberModal'));
        addMemberModal.show();
        <?php endif; ?>
        
        // Reset form when adding a new member
        document.getElementById('addNewMemberBtn').addEventListener('click', function() {
            document.getElementById('memberForm').reset();
            document.getElementById('addMemberModalLabel').textContent = 'Add New Member';
            
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
