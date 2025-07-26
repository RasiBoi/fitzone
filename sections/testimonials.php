<?php
/**
 * FitZone Fitness Center
 * Testimonials Section
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Current timestamp and user
$current_datetime = '2025-04-03 19:29:23';
$current_user = 'kaveeshawi';

// Fetch testimonials from database
try {
    $db = getDb();
    
    // Get active testimonials ordered by date (newest first) with limit
    $query = "SELECT t.*, COALESCE(t.client_name, CONCAT(u.first_name, ' ', u.last_name)) as display_name
              FROM testimonials t
              LEFT JOIN users u ON t.user_id = u.id
              WHERE t.is_active = 1
              ORDER BY t.created_at DESC
              LIMIT 6";
    
    $testimonials = $db->fetchAll($query);
} catch (Exception $e) {
    error_log("Database error in testimonials.php: " . $e->getMessage());
    $testimonials = []; // Empty array if error occurs
}
?>

<section id="testimonials" class="testimonials-section">
    <div class="container">
        <!-- Section Header -->
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-1">
                <div class="section-heading">
                    <span class="section-subtitle">TESTIMONIALS</span>
                    <h2 class="section-title">What Our <span class="text-primary">Clients Say</span></h2>
                    <div class="section-separator mx-auto"><span></span></div>
                    <p class="section-desc">
                        Real experiences from members who have transformed through our expert guidance.
                    </p>
                </div>
            </div>
        </div>

        <!-- Testimonials Slider -->
        <?php if (!empty($testimonials)): ?>
        <div class="testimonials-slider">
            <div class="swiper-container testimonial-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <div class="testimonial-content">
                                <p class="testimonial-text"><?php echo htmlspecialchars(isset($testimonial['content']) ? $testimonial['content'] : ''); ?></p>
                            </div>
                            
                            <div class="testimonial-client">
                                <?php if (!empty($testimonial['client_photo'])): ?>
                                <div class="client-photo">
                                    <img src="<?php echo htmlspecialchars($testimonial['client_photo']); ?>" alt="<?php echo htmlspecialchars(isset($testimonial['display_name']) ? $testimonial['display_name'] : 'Client'); ?>">
                                </div>
                                <?php endif; ?>
                                
                                <div class="client-info">
                                    <h4 class="client-name"><?php echo htmlspecialchars(isset($testimonial['display_name']) ? $testimonial['display_name'] : 'Anonymous Client'); ?></h4>
                                    <p class="client-title"><?php echo htmlspecialchars(isset($testimonial['client_title']) ? $testimonial['client_title'] : ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination Dots -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Initialize Swiper -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Testimonial Slider with external navigation
    const testimonialSwiper = new Swiper('.testimonial-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoHeight: false, // Fixed height for all slides
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        // Remove built-in navigation
        navigation: {
            nextEl: null,
            prevEl: null,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 30,
            }
        },
        on: {
            init: function() {
                // Make sure all slides have equal height
                setTimeout(() => {
                    const tallestSlide = Math.max(...Array.from(document.querySelectorAll('.testimonial-card')).map(card => card.offsetHeight));
                    document.querySelectorAll('.testimonial-card').forEach(card => {
                        card.style.minHeight = tallestSlide + 'px';
                    });
                    
                    // Truncate text if needed
                    document.querySelectorAll('.testimonial-text').forEach(text => {
                        // Set max height to ensure it fits in container
                        text.style.maxHeight = '320px';
                        text.style.overflow = 'hidden';
                        text.style.textOverflow = 'ellipsis';
                        text.style.display = '-webkit-box';
                        text.style.webkitBoxOrient = 'vertical';
                        text.style.webkitLineClamp = '6'; // Limit to 4 lines
                    });
                }, 100);
            }
        }
    });
    
    // Connect external navigation buttons
    document.querySelector('.testimonial-prev-btn').addEventListener('click', function() {
        testimonialSwiper.slidePrev();
    });
    
    document.querySelector('.testimonial-next-btn').addEventListener('click', function() {
        testimonialSwiper.slideNext();
    });
});
</script>

<style>
/* Testimonials Section */
.testimonials-section {
    padding: 80px 0 20px 0 ;
    background-color: #111;
    position: relative;
    overflow: hidden;
}

/* Rating Stars */
.testimonial-rating {
    margin-bottom: 5px;
    color: #555;
}

.testimonial-rating .fa-star.filled {
    color: var(--primary);
}

/* Testimonial Content */
.testimonial-content {
    margin-bottom: 0px;
    flex-grow: 1; /* Allow content to grow and push client info to bottom */
}

.testimonial-text {
    font-size: 16px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.8);
    font-style: italic;
    position: relative;
}

.testimonial-text::before {
    content: '\201C';
    font-size: 50px;
    line-height: 0;
    color: var(--primary);
    opacity: 0.3;
    position: absolute;
    top: 10px;
    left: -15px;
}

/* Client Info */
.testimonial-client {
    display: flex;
    align-items: center;
    margin-top: auto; /* Push to bottom of flex container */
}

.client-photo {
    width: 60px;
    height: 60px;
    overflow: hidden;
    border-radius: 50%;
    margin-right: 15px;
    border: 2px solid var(--primary);
    flex-shrink: 0; /* Don't shrink the photo */
}

.client-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.client-info {
    flex-grow: 1; /* Allow text to fill available space */
}

.client-name {
    font-size: 18px;
    font-weight: 600;
    color: #fff;
    margin-bottom: 2px;
}

.client-title {
    font-size: 14px;
    color: var(--primary);
    margin: 0;
}

/* Testimonial Slider */
.testimonials-slider {
    position: relative;
    padding: 20px 0 60px;
}

.swiper-container {
    overflow: hidden;
    padding-bottom: 0px; /* Space for pagination */
}

/* Pagination */
.swiper-pagination {
    bottom: 5px;
    position: absolute;
}

.swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.3);
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background: var(--primary);
}

/* Responsive styles */
@media (max-width: 991.98px) {
    .testimonial-text {
        font-size: 14px;
    }
    
    .client-name {
        font-size: 16px;
    }
    
    .testimonial-prev-btn,
    .testimonial-next-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

@media (max-width: 767.98px) {
    .testimonial-card {
        padding: 20px;
    }
    
    .client-photo {
        width: 50px;
        height: 50px;
    }
    
    .testimonial-prev-btn,
    .testimonial-next-btn {
        top: auto;
        bottom: 0;
        transform: none;
    }
    
    .testimonial-prev-btn {
        left: 30%;
    }
    
    .testimonial-next-btn {
        right: 30%;
    }
}

@media (max-width: 575.98px) {
    .section-title {
        font-size: 28px;
    }
    
    .testimonial-prev-btn {
        left: 25%;
    }
    
    .testimonial-next-btn {
        right: 25%;
    }
}
</style>