<?php
/**
 * FitZone Fitness Center
 * Get Class Members AJAX Handler
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
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';
    exit;
}

// Get current user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Initialize database connection
$db = getDb();

// Get trainer details
$trainer = $db->fetchSingle(
    "SELECT * FROM trainers WHERE user_id = ?",
    [$user_id]
);

// Get class ID from request
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

if (!$class_id || !$trainer) {
    header('HTTP/1.1 400 Bad Request');
    echo '<div class="alert alert-danger">Invalid request or trainer not found</div>';
    exit;
}

// Verify this class belongs to the trainer
$class = $db->fetchSingle(
    "SELECT * FROM fitness_classes WHERE id = ? AND trainer_id = ?",
    [$class_id, $trainer['id']]
);

if (!$class) {
    header('HTTP/1.1 403 Forbidden');
    echo '<div class="alert alert-danger">You do not have permission to access this class</div>';
    exit;
}

// Get all members enrolled in this class
$enrolled_members = $db->fetchAll(
    "SELECT u.id, u.first_name, u.last_name, u.email, u.profile_image, 
            u.created_at as joined_date,
            COUNT(cb.id) as total_bookings,
            SUM(CASE WHEN cb.status = 'attended' THEN 1 ELSE 0 END) as attended_count
     FROM users u
     JOIN class_bookings cb ON u.id = cb.user_id
     WHERE cb.class_id = ? AND u.role = 'member'
     GROUP BY u.id
     ORDER BY u.first_name ASC",
    [$class_id]
);
?>

<?php if (!empty($enrolled_members)): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th>Attendance Rate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrolled_members as $member): 
                    $attendance_rate = $member['total_bookings'] > 0 
                        ? round(($member['attended_count'] / $member['total_bookings']) * 100) 
                        : 0;
                        
                    $attendance_class = 'bg-danger';
                    if ($attendance_rate >= 80) {
                        $attendance_class = 'bg-success';
                    } elseif ($attendance_rate >= 60) {
                        $attendance_class = 'bg-warning';
                    }
                    
                    $member_image = isset($member['profile_image']) && !empty($member['profile_image']) 
                        ? '../../uploads/profile/' . $member['profile_image'] 
                        : '../../assets/images/testimonials/client-1.jpg';
                ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $member_image; ?>" alt="Member" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <span><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($member['joined_date'])); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                    <div class="progress-bar <?php echo $attendance_class; ?>" role="progressbar" style="width: <?php echo $attendance_rate; ?>%" aria-valuenow="<?php echo $attendance_rate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span><?php echo $attendance_rate; ?>%</span>
                            </div>
                            <small class="text-muted"><?php echo $member['attended_count'] . '/' . $member['total_bookings']; ?> sessions</small>
                        </td>
                        <td>
                            <a href="members.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-4">
        <i class="fas fa-users fa-3x mb-3 text-muted"></i>
        <h5>No Members Enrolled</h5>
        <p>There are no members enrolled in this class yet.</p>
    </div>
<?php endif; ?>