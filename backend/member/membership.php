<?php
/**
 * FitZone Fitness Center
 * Member Membership Page
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

// Create membership_plans table if it doesn't exist
$db->query("
    CREATE TABLE IF NOT EXISTS `membership_plans` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `price_monthly` DECIMAL(10,2) NOT NULL,
        `price_quarterly` DECIMAL(10,2) NOT NULL,
        `price_annual` DECIMAL(10,2) NOT NULL,
        `features` TEXT,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Add sample membership plans if none exist
$count = $db->fetchSingle("SELECT COUNT(*) as count FROM membership_plans");
if (!$count || $count['count'] == 0) {
    $db->query("
        INSERT INTO membership_plans (name, description, price_monthly, price_quarterly, price_annual, features) VALUES
        ('Basic', 'Access to basic gym facilities and equipment', 39.99, 99.99, 359.88, 'Gym access during regular hours|Basic fitness equipment|Locker room access|Free fitness assessment'),
        ('Premium', 'Full access to all gym facilities and select classes', 59.99, 149.99, 539.88, 'All Basic features|Access to all fitness classes|Swimming pool access|Sauna & steam room|Unlimited guest passes|Personal trainer consultation (1x/month)'),
        ('Elite', 'VIP experience with all amenities and services', 99.99, 249.99, 899.88, 'All Premium features|24/7 gym access|Priority class booking|Personal trainer sessions (2x/month)|Nutrition planning|Massage therapy (1x/month)|VIP locker|Towel service')
    ");
}

// Create member_subscriptions table if it doesn't exist
$db->query("
    CREATE TABLE IF NOT EXISTS `member_subscriptions` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `membership_plan_id` INT NOT NULL,
        `payment_id` VARCHAR(100),
        `membership_type` VARCHAR(50) NOT NULL,
        `duration` VARCHAR(20) NOT NULL,
        `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `amount_paid` DECIMAL(10,2) NOT NULL,
        `start_date` DATE NOT NULL,
        `end_date` DATE NOT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Get membership plans
$membership_plans = $db->fetchAll("SELECT * FROM membership_plans WHERE is_active = 1 ORDER BY price_monthly ASC");

// Check if user has an active membership
$active_membership = $db->fetchSingle(
    "SELECT s.*, p.name as plan_name, p.description as plan_description, p.features as plan_features 
    FROM member_subscriptions s 
    JOIN membership_plans p ON s.membership_plan_id = p.id
    WHERE s.user_id = ? AND s.status = 'active' AND s.end_date >= CURDATE() 
    ORDER BY s.end_date DESC 
    LIMIT 1",
    [$user_id]
);

// Get membership history
$membership_history = $db->fetchAll(
    "SELECT s.*, p.name as plan_name 
    FROM member_subscriptions s 
    LEFT JOIN membership_plans p ON s.membership_plan_id = p.id
    WHERE s.user_id = ? 
    ORDER BY s.start_date DESC 
    LIMIT 10",
    [$user_id]
);

// Handle membership purchase
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_membership'])) {
    $plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
    $duration = isset($_POST['duration']) ? sanitize($_POST['duration']) : '';
    
    // Validate inputs
    if (!$plan_id || !in_array($duration, ['monthly', 'quarterly', 'annual'])) {
        $error_message = 'Invalid membership plan or duration selected.';
    } else {
        // Get plan details
        $plan = $db->fetchSingle("SELECT * FROM membership_plans WHERE id = ? AND is_active = 1", [$plan_id]);
        
        if (!$plan) {
            $error_message = 'Selected membership plan not found.';
        } else {
            // Set price based on duration
            $price = 0;
            switch ($duration) {
                case 'monthly':
                    $price = $plan['price_monthly'];
                    $months = 1;
                    break;
                case 'quarterly':
                    $price = $plan['price_quarterly'];
                    $months = 3;
                    break;
                case 'annual':
                    $price = $plan['price_annual'];
                    $months = 12;
                    break;
            }
            
            // Set dates
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime("+$months months"));
            
            // Simulate payment (in real-world scenario, integrate with payment gateway)
            $payment_id = 'PAY-' . strtoupper(substr(md5(time() . $user_id), 0, 10));
            
            // Create subscription record
            $result = $db->query(
                "INSERT INTO member_subscriptions 
                (user_id, membership_plan_id, payment_id, membership_type, duration, price, amount_paid, start_date, end_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')",
                [$user_id, $plan_id, $payment_id, $plan['name'], $duration, $price, $price, $start_date, $end_date]
            );
            
            if ($result) {
                $success_message = "Congratulations! Your {$plan['name']} membership has been successfully purchased. Your membership is now active.";
                
                // Get the newly created subscription
                $active_membership = $db->fetchSingle(
                    "SELECT s.*, p.name as plan_name, p.description as plan_description, p.features as plan_features 
                    FROM member_subscriptions s 
                    JOIN membership_plans p ON s.membership_plan_id = p.id
                    WHERE s.user_id = ? AND s.status = 'active' AND s.end_date >= CURDATE() 
                    ORDER BY s.end_date DESC 
                    LIMIT 1",
                    [$user_id]
                );
                
                // Get updated membership history
                $membership_history = $db->fetchAll(
                    "SELECT s.*, p.name as plan_name 
                    FROM member_subscriptions s 
                    LEFT JOIN membership_plans p ON s.membership_plan_id = p.id
                    WHERE s.user_id = ? 
                    ORDER BY s.start_date DESC 
                    LIMIT 10",
                    [$user_id]
                );
            } else {
                $error_message = 'Failed to process your membership purchase. Please try again.';
            }
        }
    }
}

// Handle membership cancelation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_membership'])) {
    $subscription_id = isset($_POST['subscription_id']) ? (int)$_POST['subscription_id'] : 0;
    
    // Check if subscription belongs to user and is active
    $subscription = $db->fetchSingle(
        "SELECT * FROM member_subscriptions WHERE id = ? AND user_id = ? AND status = 'active'",
        [$subscription_id, $user_id]
    );
    
    if (!$subscription) {
        $error_message = 'Invalid subscription selected or subscription is not active.';
    } else {
        // Cancel subscription
        $result = $db->query(
            "UPDATE member_subscriptions SET status = 'cancelled', updated_at = NOW() WHERE id = ?",
            [$subscription_id]
        );
        
        if ($result) {
            $success_message = 'Your membership has been cancelled. You will still have access until the end of your current billing period.';
            
            // Update active membership status
            $active_membership = $db->fetchSingle(
                "SELECT s.*, p.name as plan_name, p.description as plan_description, p.features as plan_features 
                FROM member_subscriptions s 
                JOIN membership_plans p ON s.membership_plan_id = p.id
                WHERE s.user_id = ? AND s.status = 'active' AND s.end_date >= CURDATE() 
                ORDER BY s.end_date DESC 
                LIMIT 1",
                [$user_id]
            );
            
            // Get updated membership history
            $membership_history = $db->fetchAll(
                "SELECT s.*, p.name as plan_name 
                FROM member_subscriptions s 
                LEFT JOIN membership_plans p ON s.membership_plan_id = p.id
                WHERE s.user_id = ? 
                ORDER BY s.start_date DESC 
                LIMIT 10",
                [$user_id]
            );
        } else {
            $error_message = 'Failed to cancel your membership. Please try again or contact support.';
        }
    }
}

// Calculate days remaining if there's an active membership
$days_remaining = 0;
if ($active_membership) {
    $end_date = new DateTime($active_membership['end_date']);
    $today = new DateTime('today');
    $interval = $today->diff($end_date);
    $days_remaining = $interval->days;
}

// Set page title
$page_title = 'My Membership';

// Get profile image URL (default if not set)
$profile_image = isset($user['profile_image']) && !empty($user['profile_image']) 
    ? '../../uploads/profile/' . $user['profile_image'] 
    : '../../assets/images/trainers/trainer-1.jpg';

// Current active sidebar item
$active_page = 'membership';
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
    <!-- Custom Dashboard styles -->
    <link href="../../assets/css/member-dashboard.css" rel="stylesheet">
</head>
<body>
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
                        My Membership
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
                        <h4>My Membership</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Membership</li>
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
                
                <!-- Tabs for Membership Navigation -->
                <ul class="nav nav-tabs mb-4" id="membershipTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $active_membership ? 'active' : ''; ?>" id="active-membership-tab" data-bs-toggle="tab" data-bs-target="#active-membership" type="button" role="tab" aria-controls="active-membership" aria-selected="<?php echo $active_membership ? 'true' : 'false'; ?>">
                            <i class="fas fa-id-card me-2"></i>Active Membership
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo !$active_membership ? 'active' : ''; ?>" id="available-plans-tab" data-bs-toggle="tab" data-bs-target="#available-plans" type="button" role="tab" aria-controls="available-plans" aria-selected="<?php echo !$active_membership ? 'true' : 'false'; ?>">
                            <i class="fas fa-list me-2"></i>Available Plans
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="membership-history-tab" data-bs-toggle="tab" data-bs-target="#membership-history" type="button" role="tab" aria-controls="membership-history" aria-selected="false">
                            <i class="fas fa-history me-2"></i>Membership History
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="membershipTabContent">
                    <!-- Active Membership Tab -->
                    <div class="tab-pane fade <?php echo $active_membership ? 'show active' : ''; ?>" id="active-membership" role="tabpanel" aria-labelledby="active-membership-tab">
                        <?php if ($active_membership): ?>
                            <div class="active-membership-banner">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5>
                                            <i class="fas fa-star me-2 text-warning"></i>
                                            <?php echo htmlspecialchars($active_membership['plan_name']); ?> Membership
                                        </h5>
                                        <p class="mb-2">
                                            <?php echo htmlspecialchars($active_membership['plan_description']); ?>
                                        </p>
                                        <div class="mb-3">
                                            <span class="membership-status-active">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        </div>
                                        <p class="mb-2">
                                            <strong>Started On:</strong> <?php echo date('F j, Y', strtotime($active_membership['start_date'])); ?><br>
                                            <strong>Expires On:</strong> <?php echo date('F j, Y', strtotime($active_membership['end_date'])); ?><br>
                                            <strong>Membership Type:</strong> <?php echo ucfirst($active_membership['duration']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <form method="post" onsubmit="return confirm('Are you sure you want to cancel your membership?');">
                                            <input type="hidden" name="subscription_id" value="<?php echo $active_membership['id']; ?>">
                                            <button type="submit" name="cancel_membership" class="btn btn-outline-danger">
                                                <i class="fas fa-times me-2"></i>Cancel Membership
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Days Remaining Countdown -->
                            <div class="countdown-container">
                                <h5 class="mb-3">Time Remaining in Your Membership</h5>
                                <div class="countdown-timer">
                                    <div class="countdown-item">
                                        <div class="countdown-number"><?php echo $days_remaining; ?></div>
                                        <div class="countdown-label">Days</div>
                                    </div>
                                    <div class="countdown-item">
                                        <div class="countdown-number"><?php echo floor($days_remaining / 7); ?></div>
                                        <div class="countdown-label">Weeks</div>
                                    </div>
                                    <div class="countdown-item">
                                        <div class="countdown-number"><?php echo ceil($days_remaining / 30); ?></div>
                                        <div class="countdown-label">Months</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Membership Features -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Membership Benefits</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $features = explode('|', $active_membership['plan_features']);
                                    echo '<ul class="membership-features">';
                                    foreach ($features as $feature) {
                                        echo '<li><i class="fas fa-check"></i> ' . htmlspecialchars($feature) . '</li>';
                                    }
                                    echo '</ul>';
                                    ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> You don't have an active membership. Check out our available plans and get started today!
                            </div>
                            <div class="text-center mb-4">
                                <button class="btn btn-primary" data-bs-toggle="tab" data-bs-target="#available-plans">
                                    <i class="fas fa-id-card me-2"></i>View Available Plans
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Available Plans Tab -->
                    <div class="tab-pane fade <?php echo !$active_membership ? 'show active' : ''; ?>" id="available-plans" role="tabpanel" aria-labelledby="available-plans-tab">
                        <!-- Option selection form (hidden form for purchase submission) -->
                        <form id="purchaseForm" method="post">
                            <input type="hidden" name="plan_id" id="selected_plan_id" value="<?php echo isset($membership_plans[0]['id']) ? $membership_plans[0]['id'] : ''; ?>">
                            <input type="hidden" name="duration" id="selected_duration" value="monthly">
                            
                            <?php if ($active_membership): ?>
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i> You already have an active membership that expires on <?php echo date('F j, Y', strtotime($active_membership['end_date'])); ?>. If you purchase a new plan, it will become effective after your current membership expires.
                                </div>
                            <?php endif; ?>
                            
                            <!-- Duration Selection -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Choose Membership Duration</h5>
                                </div>
                                <div class="card-body">
                                    <div class="duration-selection">
                                        <div class="duration-option selected" data-duration="monthly">
                                            <div class="duration-title">Monthly</div>
                                            <div class="duration-price">Pay Monthly</div>
                                            <div class="duration-savings">Standard Rate</div>
                                        </div>
                                        <div class="duration-option" data-duration="quarterly">
                                            <div class="duration-title">Quarterly</div>
                                            <div class="duration-price">Save 15%</div>
                                            <div class="duration-savings">Billed every 3 months</div>
                                        </div>
                                        <div class="duration-option" data-duration="annual">
                                            <div class="duration-title">Annual</div>
                                            <div class="duration-price">Save 25%</div>
                                            <div class="duration-savings">Best value</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Membership Plans -->
                            <div class="row">
                                <?php foreach ($membership_plans as $index => $plan): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card membership-card h-100 <?php echo $index === 0 ? 'selected' : ''; ?> plan-<?php echo strtolower($plan['name']); ?>" data-plan-id="<?php echo $plan['id']; ?>">
                                            <div class="card-header text-center">
                                                <h4 class="membership-name"><?php echo htmlspecialchars($plan['name']); ?></h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-4">
                                                    <p class="membership-price">$<span class="price-value"><?php echo number_format($plan['price_monthly'], 2); ?></span></p>
                                                    <p class="membership-price-period">per month</p>
                                                </div>
                                                
                                                <p><?php echo htmlspecialchars($plan['description']); ?></p>
                                                
                                                <h6 class="mt-4 mb-3">Key Benefits</h6>
                                                <?php 
                                                $features = explode('|', $plan['features']);
                                                echo '<ul class="membership-features">';
                                                foreach ($features as $feature) {
                                                    echo '<li><i class="fas fa-check"></i> ' . htmlspecialchars($feature) . '</li>';
                                                }
                                                echo '</ul>';
                                                ?>
                                                
                                                <div class="text-center mt-4">
                                                    <button type="button" class="btn btn-outline-success select-plan-btn">
                                                        <i class="fas fa-check-circle me-2"></i>Select Plan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Purchase Button -->
                            <div class="text-center mt-3 mb-5">
                                <button type="submit" name="purchase_membership" class="btn btn-success btn-lg">
                                    <i class="fas fa-shopping-cart me-2"></i>Purchase Membership
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Membership History Tab -->
                    <div class="tab-pane fade" id="membership-history" role="tabpanel" aria-labelledby="membership-history-tab">
                        <?php if (empty($membership_history)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> You don't have any membership history yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Duration</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Amount Paid</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($membership_history as $history): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars(isset($history['membership_type']) ? $history['membership_type'] : ''); ?></td>
                                                <td><?php echo ucfirst($history['duration']); ?></td>
                                                <td><?php echo date('m/d/Y', strtotime($history['start_date'])); ?></td>
                                                <td><?php echo date('m/d/Y', strtotime($history['end_date'])); ?></td>
                                                <td>$<?php echo number_format($history['amount_paid'], 2); ?></td>
                                                <td>
                                                    <?php if ($history['status'] === 'active' && strtotime($history['end_date']) >= time()): ?>
                                                        <span class="membership-status-active">Active</span>
                                                    <?php elseif ($history['status'] === 'cancelled'): ?>
                                                        <span class="membership-status-cancelled">Cancelled</span>
                                                    <?php else: ?>
                                                        <span class="membership-status-expired">Expired</span>
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
        
        // Handle membership plan selection
        const membershipCards = document.querySelectorAll('.membership-card');
        const planIdInput = document.getElementById('selected_plan_id');
        
        membershipCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                membershipCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Update hidden input with selected plan ID
                planIdInput.value = this.dataset.planId;
            });
        });
        
        // Handle duration selection
        const durationOptions = document.querySelectorAll('.duration-option');
        const durationInput = document.getElementById('selected_duration');
        const priceElements = document.querySelectorAll('.price-value');
        const periodElements = document.querySelectorAll('.membership-price-period');
        
        // Get price data from PHP
        const prices = {
            <?php foreach ($membership_plans as $plan): ?>
            <?php echo $plan['id']; ?>: {
                monthly: <?php echo $plan['price_monthly']; ?>,
                quarterly: <?php echo $plan['price_quarterly']; ?>,
                annual: <?php echo $plan['price_annual']; ?>
            },
            <?php endforeach; ?>
        };
        
        durationOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                durationOptions.forEach(o => o.classList.remove('selected'));
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input with selected duration
                const duration = this.dataset.duration;
                durationInput.value = duration;
                
                // Update prices for all plans
                membershipCards.forEach(card => {
                    const planId = card.dataset.planId;
                    const priceElement = card.querySelector('.price-value');
                    const periodElement = card.querySelector('.membership-price-period');
                    
                    if (priceElement && periodElement && prices[planId]) {
                        const price = prices[planId][duration];
                        priceElement.textContent = price.toFixed(2);
                        
                        // Update period text
                        switch(duration) {
                            case 'monthly':
                                periodElement.textContent = 'per month';
                                break;
                            case 'quarterly':
                                periodElement.textContent = 'per quarter';
                                break;
                            case 'annual':
                                periodElement.textContent = 'per year';
                                break;
                        }
                    }
                });
            });
        });
        
        // Select Plan Button Functionality
        const selectPlanBtns = document.querySelectorAll('.select-plan-btn');
        
        selectPlanBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.membership-card');
                
                // Trigger click on card to select it
                card.click();
                
                // Update button text
                selectPlanBtns.forEach(b => {
                    b.innerHTML = '<i class="fas fa-check-circle me-2"></i>Select Plan';
                    b.classList.remove('btn-success');
                    b.classList.add('btn-outline-success');
                });
                
                this.innerHTML = '<i class="fas fa-check-circle me-2"></i>Selected';
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-success');
            });
        });
    });
    </script>
</body>
</html>
