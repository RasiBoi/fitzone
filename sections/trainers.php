<?php
/**
 * FitZone Fitness Center
 * Trainers Section with Creative Design
 * 
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Current timestamp and user from parameters
$current_datetime = '2025-04-03 04:42:10';
$current_user = 'kaveeshawi';

// Fetch trainers data
try {
    $db = getDb();
    
    // Get all active trainers ordered by experience
    $query = "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) as name 
              FROM trainers t 
              JOIN users u ON t.user_id = u.id 
              WHERE t.is_active = 1 
              ORDER BY t.experience DESC";
    
    $trainers = $db->fetchAll($query);
    $trainerCount = count($trainers);
    $needSlider = $trainerCount > 3;
    
    // Get statistics
    $statsQuery = "SELECT 
                    (SELECT COUNT(*) FROM trainers WHERE is_active = 1) as trainer_count,
                    (SELECT COUNT(*) FROM members WHERE status = 'active') as active_clients,
                    (SELECT COUNT(DISTINCT certification) FROM trainers WHERE is_active = 1) as cert_count,
                    (SELECT SUM(experience) FROM trainers WHERE is_active = 1) as total_experience";
    
    $stats = $db->fetchSingle($statsQuery);
} catch (Exception $e) {
    error_log("Database error in trainers.php: " . $e->getMessage());
    $trainers = [];
    $needSlider = false;
}
?>

<section id="trainers" class="trainers-section">
    <div class="container">
        <!-- Section Header -->
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <div class="section-heading">
                    <span class="section-subtitle">EXPERT INSTRUCTORS</span>
                    <h2 class="section-title">Meet Our <span class="text-primary">Professional Trainers</span></h2>
                    <div class="section-separator mx-auto"><span></span></div>
                    <p class="trainers-intro">
                        Our certified trainers bring years of experience and specialized expertise to help you achieve your fitness goals. 
                        Whether you're looking to build strength, lose weight, or improve flexibility, our team is here to guide you.
                    </p>
                </div>
            </div>
        </div>

        <!-- Trainers Display -->
        <?php if (!empty($trainers)): ?>
            <div class="trainer-display-section">
                <?php if ($needSlider): ?>
                <!-- Custom Trainer Slider -->
                <div class="trainer-slider-container">
                    <!-- Custom Slider Controls (Now positioned absolutely) -->
                    <div class="trainer-nav-controls">
                        <button id="trainerPrev" class="trainer-nav-btn trainer-prev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button id="trainerNext" class="trainer-nav-btn trainer-next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="trainer-slider-wrapper">
                        <?php 
                        // Group trainers into sets of 3
                        $trainerGroups = array_chunk($trainers, 3);
                        foreach($trainerGroups as $groupIndex => $trainerGroup): 
                        ?>
                        <div class="trainer-slide" data-slide="<?php echo $groupIndex; ?>">
                            <div class="trainer-slide-inner">
                                <?php foreach($trainerGroup as $trainer): ?>
                                <div class="trainer-card">
                                    <div class="trainer-card-inner">
                                        <div class="trainer-image-container">
                                            <div class="trainer-image-wrapper">
                                                <img src="<?php echo htmlspecialchars($trainer['image']); ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>" class="trainer-image">
                                                <div class="trainer-image-glow"></div>
                                            </div>
                                        </div>
                                        <div class="trainer-content">
                                            <h3 class="trainer-name"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                                            <div class="trainer-specialization"><?php echo htmlspecialchars($trainer['specialization']); ?></div>
                                            <div class="trainer-experience"><?php echo (int)$trainer['experience']; ?> Years Experience</div>
                                            <p class="trainer-bio"><?php echo htmlspecialchars($trainer['bio']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination Dots -->
                    <div class="trainer-pagination">
                        <?php for($i = 0; $i < ceil(count($trainers) / 3); $i++): ?>
                        <span class="trainer-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Custom Slider JavaScript -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const sliderWrapper = document.querySelector('.trainer-slider-wrapper');
                    const slides = document.querySelectorAll('.trainer-slide');
                    const dots = document.querySelectorAll('.trainer-dot');
                    const prevBtn = document.getElementById('trainerPrev');
                    const nextBtn = document.getElementById('trainerNext');
                    let currentSlide = 0;
                    
                    // Set initial slide
                    updateSlider();
                    
                    // Event listeners
                    prevBtn.addEventListener('click', prevSlide);
                    nextBtn.addEventListener('click', nextSlide);
                    
                    // Set up dots click handlers
                    dots.forEach(dot => {
                        dot.addEventListener('click', function() {
                            currentSlide = parseInt(this.getAttribute('data-index'));
                            updateSlider();
                        });
                    });
                    
                    // Auto slide every 7 seconds
                    const interval = setInterval(nextSlide, 7000);
                    
                    // Stop auto slide on user interaction
                    document.querySelector('.trainer-slider-container').addEventListener('mouseenter', () => {
                        clearInterval(interval);
                    });
                    
                    function prevSlide() {
                        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                        updateSlider();
                    }
                    
                    function nextSlide() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        updateSlider();
                    }
                    
                    function updateSlider() {
                        // Update slide position
                        slides.forEach(slide => {
                            slide.style.display = 'none';
                        });
                        slides[currentSlide].style.display = 'block';
                        
                        // Update dots
                        dots.forEach(dot => {
                            dot.classList.remove('active');
                        });
                        dots[currentSlide].classList.add('active');
                    }
                });
                </script>
                <?php else: ?>
                <!-- Standard grid for 3 or fewer trainers -->
                <div class="trainer-grid">
                    <?php foreach ($trainers as $trainer): ?>
                    <div class="trainer-card">
                        <div class="trainer-card-inner">
                            <div class="trainer-image-container">
                                <div class="trainer-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($trainer['image']); ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>" class="trainer-image">
                                    <div class="trainer-image-glow"></div>
                                </div>
                            </div>
                            <div class="trainer-content">
                                <h3 class="trainer-name"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                                <div class="trainer-specialization"><?php echo htmlspecialchars($trainer['specialization']); ?></div>
                                <div class="trainer-experience"><?php echo (int)$trainer['experience']; ?> Years Experience</div>
                                <p class="trainer-bio"><?php echo htmlspecialchars($trainer['bio']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>   
        <?php endif; ?>
    </div>
    
    <!-- Background Elements -->
    <div class="trainer-shape-1"></div>
    <div class="trainer-shape-2"></div>
</section>

<style>
/* Main Section Styling */
.trainers-section {
    position: relative;
    padding: 60px 0 ;
    overflow: hidden;
    background-color: #111;
}

.trainers-intro {
    margin-bottom: -40px;
}

/* Creative Background Shapes */
.trainer-shape-1, .trainer-shape-2 {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
    opacity: 0.2;
}

.trainer-shape-1 {
    top: -150px;
    right: -100px;
    width: 500px;
    height: 500px;
    background: linear-gradient(45deg, var(--primary), #ff5500);
}

.trainer-shape-2 {
    bottom: -200px;
    left: -150px;
    width: 600px;
    height: 600px;
    background: linear-gradient(45deg, #0088ff, var(--primary));
}

/* Trainer Display Section */
.trainer-display-section {
    position: relative;
    z-index: 1;
}

/* Custom Slider Container */
.trainer-slider-container {
    position: relative;
    overflow: visible; /* Changed to visible to allow nav buttons to be outside */
    padding: 20px 0 60px; /* Added bottom padding for pagination dots */
}

/* Navigation Controls - Now positioned absolutely */
.trainer-nav-controls {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    z-index: 10;
    pointer-events: none; /* This allows clicks to pass through the container */
    display: flex;
    justify-content: space-between;
    padding: 0 -20px; /* Negative padding to position buttons outside */
}

.trainer-nav-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid var(--primary);
    color: var(--primary);
    font-size: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    pointer-events: auto; /* Re-enable pointer events on buttons */
    position: relative;
    z-index: 20;
}

.trainer-prev {
    margin-left: -25px; /* Position slightly outside container */
}

.trainer-next {
    margin-right: -25px; /* Position slightly outside container */
}

.trainer-nav-btn:hover {
    background: var(--primary);
    color: #000;
}

/* Pagination dots now at bottom */
.trainer-pagination {
    display: flex;
    justify-content: center;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 20px 0;
}

.trainer-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    margin: 0 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.trainer-dot.active {
    background: var(--primary);
    transform: scale(1.2);
}

/* Custom Trainer Slider */
.trainer-slider-wrapper {
    position: relative;
}

.trainer-slide {
    display: none;
}

.trainer-slide-inner {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

/* Trainer Grid for 3 or less */
.trainer-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

/* Trainer Card - Fixed height for consistent sizing */
.trainer-card {
    position: relative;
    overflow: hidden;
    height: 450px; /* Fixed height for all trainer cards */
}

.trainer-card-inner {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    position: relative;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.trainer-card-inner::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 80%);
    transform: rotate(30deg);
    z-index: -1;
}

/* Trainer Image */
.trainer-image-container {
    position: relative;
    text-align: center;
    margin-bottom: 25px;
    flex-shrink: 0; /* Prevent image from shrinking */
}

.trainer-image-wrapper {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto;
}

.trainer-image {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary);
    position: relative;
    z-index: 2;
}

.trainer-image-glow {
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: var(--primary);
    border-radius: 50%;
    filter: blur(20px);
    opacity: 0.3;
    z-index: 1;
}

/* Trainer Content */
.trainer-content {
    text-align: center;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.trainer-name {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.trainer-specialization {
    color: var(--primary);
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    display: inline-block;
    padding: 5px 15px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 20px;
}

/* Experience now below specialization */
.trainer-experience {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.8);
    display: block;
}

.trainer-bio {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: auto; /* Push bio to the bottom of available space */
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .trainer-slide-inner,
    .trainer-grid,
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-item:nth-child(2)::after {
        display: none;
    }
    
    .trainer-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .trainer-card {
        height: 430px; /* Slightly adjust height for tablet view */
    }
}

@media (max-width: 767.98px) {
    .trainer-slide-inner,
    .trainer-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-number {
        font-size: 30px;
    }
    
    .trainer-image-wrapper,
    .trainer-image {
        width: 140px;
        height: 140px;
    }
    
    .trainer-card-inner {
        padding: 20px;
    }
    
    .trainer-nav-controls {
        display: none; /* Hide navigation on small screens */
    }
    
    .trainer-card {
        height: 400px; /* Further adjust height for mobile view */
    }
}

@media (max-width: 575.98px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .stat-item::after {
        display: none;
    }
}
</style>