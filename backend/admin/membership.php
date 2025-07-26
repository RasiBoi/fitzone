<?php
/**
 * FitZone Fitness Center
 * Admin Membership Plans Management
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

// Handle plan deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $plan_id = (int)$_GET['delete'];
    
    // Check if plan is in use before deleting
    $plans_in_use = $db->fetchSingle(
        "SELECT COUNT(*) as count FROM member_subscriptions WHERE membership_plan_id = ?", 
        [$plan_id]
    );
    
    if ($plans_in_use && $plans_in_use['count'] > 0) {
        $error = "This membership plan cannot be deleted as it is currently in use by members.";
    } else {
        $result = $db->query("DELETE FROM membership_plans WHERE id = ?", [$plan_id]);
        
        if ($result) {
            $message = "Membership plan deleted successfully.";
        } else {
            $error = "Failed to delete membership plan.";
        }
    }
}

// Handle plan status toggle
if (isset($_GET['toggle']) && !empty($_GET['toggle'])) {
    $plan_id = (int)$_GET['toggle'];
    
    // Get current status
    $current = $db->fetchSingle("SELECT is_active FROM membership_plans WHERE id = ?", [$plan_id]);
    
    if ($current) {
        $new_status = $current['is_active'] ? 0 : 1;
        $result = $db->query(
            "UPDATE membership_plans SET is_active = ? WHERE id = ?", 
            [$new_status, $plan_id]
        );
        
        if ($result) {
            $status_text = $new_status ? "activated" : "deactivated";
            $message = "Membership plan $status_text successfully.";
        } else {
            $error = "Failed to update membership plan status.";
        }
    }
}

// Handle plan addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $type = sanitize($_POST['type']);
    $description = sanitize($_POST['description']);
    $price_1month = (float)$_POST['price_1month'];
    $price_6month = (float)$_POST['price_6month'];
    $price_12month = (float)$_POST['price_12month'];
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Basic validation
    if (empty($type) || empty($description)) {
        $error = "Plan type and description are required.";
    } elseif ($price_1month <= 0 || $price_6month <= 0 || $price_12month <= 0) {
        $error = "All price values must be greater than zero.";
    } else {
        // If setting as popular, unset any other popular plan
        if ($is_popular) {
            $db->query(
                "UPDATE membership_plans SET is_popular = 0 WHERE id != ?", 
                [$edit_id ? $edit_id : 0]
            );
        }
        
        if ($edit_id > 0) {
            // Update existing plan
            $result = $db->query(
                "UPDATE membership_plans SET type = ?, description = ?, price_1month = ?, price_6month = ?, price_12month = ?, is_popular = ?, is_active = ? WHERE id = ?",
                [$type, $description, $price_1month, $price_6month, $price_12month, $is_popular, $is_active, $edit_id]
            );
            
            if ($result) {
                $message = "Membership plan updated successfully.";
            } else {
                $error = "Failed to update membership plan.";
            }
        } else {
            // Add new plan
            $result = $db->query(
                "INSERT INTO membership_plans (type, description, price_1month, price_6month, price_12month, is_popular, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$type, $description, $price_1month, $price_6month, $price_12month, $is_popular, $is_active]
            );
            
            if ($result) {
                $message = "Membership plan added successfully.";
            } else {
                $error = "Failed to add membership plan.";
            }
        }
    }
}

// Get plan to edit
$edit_plan = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_plan = $db->fetchSingle("SELECT * FROM membership_plans WHERE id = ?", [$edit_id]);
}

// Get all membership plans
$membership_plans = $db->fetchAll("SELECT * FROM membership_plans ORDER BY price_1month ASC");

// Set page title
$page_title = 'Manage Membership Plans';
$active_page = 'membership';

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
        .plan-card {
            height: 100%;
            transition: all 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .plan-price {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .price-period {
            font-size: 14px;
            color: #6c757d;
        }
        .popular-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .membership-stats {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: white;
        }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-title {
            font-size: 14px;
            opacity: 0.8;
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
                        <h4>Manage Membership Plans</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Membership Plans</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-role-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal" id="addNewPlanBtn">
                        <i class="fas fa-plus"></i> Add Plan
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
                
                <!-- Membership Stats -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="membership-stats bg-primary">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php 
                                    $active_members = $db->fetchSingle("SELECT COUNT(*) as count FROM member_subscriptions WHERE status = 'active' AND end_date >= CURDATE()");
                                    ?>
                                    <div class="stat-number"><?php echo $active_members['count']; ?></div>
                                    <div class="stat-title">Active Subscriptions</div>
                                </div>
                                <div><i class="fas fa-users fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="membership-stats bg-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?php echo count($membership_plans); ?></div>
                                    <div class="stat-title">Total Plans</div>
                                </div>
                                <div><i class="fas fa-id-card fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Membership Plans List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Membership Plans</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Monthly Price</th>
                                        <th>6-Month Price</th>
                                        <th>12-Month Price</th>
                                        <th>Status</th>
                                        <th>Popular</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($membership_plans) > 0): ?>
                                        <?php foreach($membership_plans as $plan): ?>
                                            <tr>
                                                <td><?php echo $plan['id']; ?></td>
                                                <td><?php echo htmlspecialchars($plan['type']); ?></td>
                                                <td>Rs.<?php echo number_format($plan['price_1month'], 2); ?></td>
                                                <td>Rs.<?php echo number_format($plan['price_6month'], 2); ?></td>
                                                <td>Rs.<?php echo number_format($plan['price_12month'], 2); ?></td>
                                                <td>
                                                    <?php if ($plan['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($plan['is_popular']): ?>
                                                        <span class="badge bg-warning"><i class="fas fa-star"></i> Popular</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?php echo $plan['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?toggle=<?php echo $plan['id']; ?>" class="btn btn-sm <?php echo $plan['is_active'] ? 'btn-secondary' : 'btn-success'; ?>">
                                                        <i class="fas <?php echo $plan['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $plan['id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this membership plan?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No membership plans found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Subscription Statistics -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Subscriptions</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $recent_subs = $db->fetchAll(
                                    "SELECT s.*, u.username, p.type 
                                    FROM member_subscriptions s
                                    JOIN users u ON s.user_id = u.id
                                    LEFT JOIN membership_plans p ON s.membership_type = p.type
                                    ORDER BY s.created_at DESC
                                    LIMIT 5"
                                );
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Plan</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_subs)): ?>
                                                <?php foreach ($recent_subs as $sub): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($sub['username']); ?></td>
                                                        <td><?php echo htmlspecialchars($sub['membership_type']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($sub['start_date'])); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($sub['end_date'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No recent subscriptions.</td>
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
        </div>
    </div>

    <!-- Add/Edit Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">
                        <?php echo $edit_plan ? 'Edit Membership Plan' : 'Add New Membership Plan'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="" id="planForm">
                    <div class="modal-body">
                        <?php if ($edit_plan): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $edit_plan['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Plan Type *</label>
                            <input type="text" class="form-control" id="type" name="type" 
                                   value="<?php echo $edit_plan ? htmlspecialchars($edit_plan['type']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $edit_plan ? htmlspecialchars($edit_plan['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price_1month" class="form-label">Monthly Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price_1month" name="price_1month" 
                                           value="<?php echo $edit_plan ? $edit_plan['price_1month'] : ''; ?>" min="0" step="0.01" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="price_6month" class="form-label">6-Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price_6month" name="price_6month" 
                                           value="<?php echo $edit_plan ? $edit_plan['price_6month'] : ''; ?>" min="0" step="0.01" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="price_12month" class="form-label">12-Month Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="price_12month" name="price_12month" 
                                           value="<?php echo $edit_plan ? $edit_plan['price_12month'] : ''; ?>" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" 
                                  <?php echo ($edit_plan && $edit_plan['is_popular']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_popular">
                                Mark as Popular (highlighted in the membership section)
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                  <?php echo (!$edit_plan || ($edit_plan && $edit_plan['is_active'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                Active (visible to members)
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_plan ? 'Update Plan' : 'Add Plan'; ?>
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
            
            <?php if ($edit_plan): ?>
            // Show edit modal automatically if edit parameter is present
            var addPlanModal = new bootstrap.Modal(document.getElementById('addPlanModal'));
            addPlanModal.show();
            <?php endif; ?>
            
            // Reset form when adding a new plan
            document.getElementById('addNewPlanBtn').addEventListener('click', function() {
                document.getElementById('planForm').reset();
                document.getElementById('addPlanModalLabel').textContent = 'Add New Membership Plan';
            });
        });
    </script>
</body>
</html>