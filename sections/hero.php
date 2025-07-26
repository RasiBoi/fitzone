<?php
/**
 * FitZone Fitness Center
 * Hero Section
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}
?>

<section class="hero-section" id="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-particles" id="hero-particles"></div>
    
    <div class="container position-relative">
        <div class="row align-items-center hero-content">
            <div class="col-lg-6 hero-text-area" data-aos="fade-right" data-aos-duration="800">
                <div class="hero-subtitle">TRANSFORM YOUR PHYSIQUE</div>
                <h1 class="hero-title">
                    <span class="text-primary-gradient">ELEVATE</span> YOUR
                    <span class="d-block">FITNESS JOURNEY</span>
                </h1>
                <p class="hero-description">
                    Join FitZone and experience premium fitness with state-of-the-art equipment, 
                    expert trainers, and a motivating community ready to help you achieve your goals.
                </p>
                <div class="hero-buttons">
                    <a href="membership.php" class="btn btn-primary btn-lg hero-btn">
                        <span>JOIN NOW</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        
            <div class="col-lg-6 hero-image-area" data-aos="fade-left" data-aos-duration="800">
                <div class="hero-image-wrapper">
                    <div class="hero-image-container">
                        <img src="assets/images/hero-new.jpg" alt="FitZone Fitness Center" class="hero-image">
                    </div>
                    <div class="hero-shape-1"></div>
                    <div class="hero-shape-2"></div>
                    
                    <div class="hero-stat hero-stat-1">
                        <div class="hero-stat-number">500+</div>
                        <div class="hero-stat-label">Premium Equipment</div>
                    </div>
                    
                    <div class="hero-stat hero-stat-2">
                        <div class="hero-stat-number">50+</div>
                        <div class="hero-stat-label">Weekly Classes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add this to your main.js file or include as inline script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize particle animation if particlesJS is loaded
    if (typeof particlesJS !== 'undefined') {
        particlesJS('hero-particles', {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#F48915"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                },
                "opacity": {
                    "value": 0.3,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#F48915",
                    "opacity": 0.2,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 2,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "grab"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 140,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "push": {
                        "particles_nb": 4
                    },
                }
            },
            "retina_detect": true
        });
    }
});
</script>