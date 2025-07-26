<?php
// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Get database connection
require_once 'includes/db_connect.php';
$db = getDb();

// First, get all classes from database and log them
$query = "SELECT * FROM fitness_classes ORDER BY id DESC";
$all_classes = $db->fetchAll($query);

// Process the classes to remove duplicates by name
$featured_classes = [];
$class_names = [];

// Add debug info to error log
error_log("Found " . count($all_classes) . " classes in total");

// Manual strict deduplication
if ($all_classes) {
    foreach ($all_classes as $class) {
        if (!isset($class['name']) || empty(trim($class['name']))) {
            continue; // Skip records without a valid name
        }
        
        // Don't skip any classes - we want all of them to show
        // Just transform the data and add to featured classes
        $featured_classes[] = $class;
    }
    
    // Transform the data to match the expected format
    foreach ($featured_classes as &$class) {
        $class['schedule'] = [$class['schedule_days'], $class['schedule_times']];
        
        // Fix image path by adding the proper directory prefix if not already included
        if (!empty($class['image'])) {
            // Only add prefix if image doesn't already have a path
            if (strpos($class['image'], 'http') !== 0 && 
                strpos($class['image'], 'assets/') !== 0 && 
                strpos($class['image'], '/') !== 0) {
                $class['image'] = 'assets/images/Classes/' . $class['image'];
            }
        } else {
            // Set a default image if none is provided
            $class['image'] = 'assets/images/Classes/yoga.jpg';
        }
    }
} else {
    // Fallback data in case of database error
    $featured_classes = [];
    error_log('Failed to fetch fitness classes from database');
}

// Log total unique classes found
error_log("Processing " . count($featured_classes) . " classes for display");
?>

<!-- Static class count to verify deduplication is working -->
<!-- Found <?php echo count($featured_classes); ?> unique classes -->

<section id="classes-section" class="classes-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 mx-auto text-center mb-5">
                <div class="section-heading mb-5">
                    <span class="section-subtitle">OUR CLASSES</span>
                    <h2 class="section-title">Energize With Our <span class="text-primary">Fitness Classes</span></h2>
                    <div class="section-separator"><span></span></div>
                    <p class="classes-intro">
                        Join our dynamic group fitness classes led by expert instructors designed to challenge, motivate, 
                        and transform your body. With options for all fitness levels, you'll find the perfect class to meet your goals.
                    </p>
                </div>
            </div>
        </div>

        <!-- Classes carousel with horizontal scrolling -->
        <div class="classes-carousel-container">
            <?php if (count($featured_classes) > 0): ?>
                <div class="classes-carousel-nav">
                    <button class="classes-prev-btn" id="classesPrevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="classes-next-btn" id="classesNextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="classes-carousel">
                    <?php 
                    // Final display - ensure no duplicates were introduced
                    $displayed_names = []; 
                    
                    foreach ($featured_classes as $class): 
                        // Skip if somehow a duplicate made it through
                        $current_name = trim($class['name']);
                        if (in_array($current_name, $displayed_names)) continue;
                        $displayed_names[] = $current_name;
                    ?>
                        <div class="class-item">
                            <div class="class-card">
                                <div class="class-image">
                                    <img src="<?php echo $class['image']; ?>" alt="<?php echo $class['name']; ?>" class="img-fluid">
                                    <div class="class-duration">
                                        <i class="far fa-clock"></i> <?php echo $class['duration']; ?>
                                    </div>
                                    <div class="class-overlay">
                                        <div class="class-difficulty <?php echo strtolower($class['difficulty']); ?>">
                                            <?php echo $class['difficulty']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="class-content">
                                    <h3 class="class-title"><?php echo $class['name']; ?></h3>
                                    <div class="class-trainer">
                                        <i class="fas fa-user"></i> <?php echo $class['trainer']; ?>
                                    </div>
                                    <p class="class-description">
                                        <?php echo $class['description']; ?>
                                    </p>
                                    <div class="class-schedule">
                                        <div class="schedule-days">
                                            <i class="fas fa-calendar-week"></i>
                                            <span><?php echo $class['schedule'][0]; ?></span>
                                        </div>
                                        <div class="schedule-time">
                                            <i class="far fa-clock"></i>
                                            <span><?php echo $class['schedule'][1]; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p>No classes found. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Background Elements for Visual Interest -->
    <div class="classes-shape classes-shape-1"></div>
    <div class="classes-shape classes-shape-2"></div>

    <!-- Inline CSS for horizontal scrolling -->
    <style>
    .classes-carousel-container {
        position: relative;
        padding: 0 50px;
    }

    .classes-carousel {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        gap: 20px;
        padding: 15px 5px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    .classes-carousel::-webkit-scrollbar {
        display: none;
    }

    .classes-carousel .class-item {
        flex: 0 0 300px;
        max-width: 300px;
        transition: all 0.3s ease;
    }

    .classes-carousel .class-card {
        height: 100%;
        margin-bottom: 0;
    }

    .classes-carousel-nav {
        position: absolute;
        width: 100%;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
    }

    .classes-prev-btn, .classes-next-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(244, 137, 21, 0.9);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        pointer-events: auto;
        transition: all 0.3s ease;
    }

    .classes-prev-btn:hover, .classes-next-btn:hover {
        background-color: rgba(244, 137, 21, 1);
        transform: scale(1.1);
    }

    .classes-prev-btn {
        margin-left: 10px;
    }

    .classes-next-btn {
        margin-right: 10px;
    }

    @media (max-width: 768px) {
        .classes-carousel .class-item {
            flex: 0 0 260px;
            max-width: 260px;
        }
    }
    </style>

    <!-- Add JavaScript for carousel navigation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('.classes-carousel');
        const prevBtn = document.getElementById('classesPrevBtn');
        const nextBtn = document.getElementById('classesNextBtn');
        
        if (carousel && prevBtn && nextBtn) {
            // Move left on previous button click
            prevBtn.addEventListener('click', function() {
                carousel.scrollBy({ left: -320, behavior: 'smooth' });
            });
            
            // Move right on next button click
            nextBtn.addEventListener('click', function() {
                carousel.scrollBy({ left: 320, behavior: 'smooth' });
            });
            
            // Hide navigation buttons if there are few items
            const classItems = carousel.querySelectorAll('.class-item');
            if (classItems.length <= 3) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            }
        }
    });
    </script>
</section>