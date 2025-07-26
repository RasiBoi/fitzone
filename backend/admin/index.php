<?php
/**
 * FitZone Fitness Center
 * Backend Dashboard
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

// Get user details from database
$db = getDb();
$user = $db->fetchSingle(
    "SELECT * FROM users WHERE id = ?",
    [$user_id]
);

// If user not found, logout and redirect
if (!$user) {
    logout();
    redirect('../../login.php');
}

// Get counts from database for dashboard stats
$total_members = $db->fetchSingle("SELECT COUNT(*) as count FROM users WHERE role = 'member'");
$total_trainers = $db->fetchSingle("SELECT COUNT(*) as count FROM trainers");
$total_classes = $db->fetchSingle("SELECT COUNT(*) as count FROM fitness_classes");

// Calculate total revenue from membership subscriptions using the price column
$total_revenue = $db->fetchSingle("SELECT SUM(price) as total FROM member_subscriptions");
$revenue_amount = $total_revenue ? $total_revenue['total'] : 0;

// Set page title based on user role
$page_title = 'Dashboard';
$role_display = ucfirst($user_role);  // Capitalize first letter

// Determine which content to show based on user role (This is for customization later)
$role_content = '';
switch ($user_role) {
    case 'admin':
        $role_content = 'Admin Dashboard Content';
        break;
    case 'trainer':
        $role_content = 'Trainer Dashboard Content';
        break;
    case 'member':
        $role_content = 'Member Dashboard Content';
        break;
    default:
        $role_content = 'Welcome to FitZone Dashboard';
}

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current active sidebar item
$active_page = 'dashboard';
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
                    <?php if ($user_role === 'admin'): ?>
                    <!-- Admin Stats -->
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $total_members['count']; ?></div>
                                        <div class="stat-title">Total Members</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $total_trainers['count']; ?></div>
                                        <div class="stat-title">Trainers</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number"><?php echo $total_classes['count']; ?></div>
                                        <div class="stat-title classes">Classes</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number">Rs. <?php echo number_format($revenue_amount, 2); ?></div>
                                        <div class="stat-title">Revenue</div>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fa-solid fa-coins"></i> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Additional Dashboard Cards - Row 1 -->
                <div class="row mt-4">
                    <?php if ($user_role === 'admin'): ?>
                    
                    <!-- Quick Action Cards -->
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                                </div>
                                <h5>Add Member</h5>
                                <p class="mb-3 text-light">Register a new member to the gym</p>
                                <a href="members.php?action=add" class="btn btn-outline-light">Add Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-calendar-plus fa-3x text-success"></i>
                                </div>
                                <h5>Create Class</h5>
                                <p class="mb-3 text-light">Add a new fitness class to schedule</p>
                                <a href="classes.php?action=add" class="btn btn-outline-light">Create Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-clipboard-list fa-3x text-info"></i>
                                </div>
                                <h5>Manage Plans</h5>
                                <p class="mb-3 text-light">Update membership plans and pricing</p>
                                <a href="membership.php" class="btn btn-outline-light">Manage Now</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Additional Dashboard Cards - Row 2 -->
                <div class="row mt-4">
                    <?php if ($user_role === 'admin'): ?>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-user-tie fa-3x text-warning"></i>
                                </div>
                                <h5>Add Trainer</h5>
                                <p class="mb-3 text-light">Register a new trainer for the gym</p>
                                <a href="trainers.php?action=add" class="btn btn-outline-light">Add Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-chart-bar fa-3x text-danger"></i>
                                </div>
                                <h5>View Reports</h5>
                                <p class="mb-3 text-light">Access revenue and membership reports</p>
                                <a href="reports.php" class="btn btn-outline-light">View Reports</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card action-card bg-dark text-white">
                            <div class="card-body text-center">
                                <div class="action-icon mb-3">
                                    <i class="fas fa-cog fa-3x text-secondary"></i>
                                </div>
                                <h5>Settings</h5>
                                <p class="mb-3 text-light">Configure application settings</p>
                                <a href="settings.php" class="btn btn-outline-light">Go to Settings</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Activities -->
                <div class="row mt-4">
                    <?php if ($user_role === 'admin'): ?>
                    <!-- Recent Memberships -->
                    <div class="col-md-12">
                        <div class="card bg-dark text-white">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Membership Subscriptions</h5>
                                <a href="membership.php" class="btn btn-sm btn-outline-light">View All</a>
                            </div>
                            <div class="card-body">
                                <?php
                                $recent_subs = $db->fetchAll(
                                    "SELECT s.*, u.username, u.first_name, u.last_name 
                                    FROM member_subscriptions s
                                    LEFT JOIN users u ON s.user_id = u.id
                                    ORDER BY s.created_at DESC
                                    LIMIT 5"
                                );
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Member</th>
                                                <th>Plan</th>
                                                <th>Duration</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_subs)): ?>
                                                <?php foreach ($recent_subs as $sub): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($sub['membership_type']); ?></td>
                                                        <td><?php echo ucfirst($sub['duration']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($sub['start_date'])); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($sub['end_date'])); ?></td>
                                                        <td>
                                                            <a href="members.php?view=<?php echo $sub['user_id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Member">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No recent subscriptions.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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