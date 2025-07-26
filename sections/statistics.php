<?php
/**
 * FitZone Fitness Center
 * Statistics Section
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Hardcoded statistics values
$stats = [
    'trainer_count' => 50,
    'active_clients' => 600,
    'cert_count' => 30,
    'total_experience' => 5
];
?>

<!-- Trainer Statistics Section -->
<div class="trainer-stats-section counter-section">
    <div class="stats-container">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-number">
                <span class="counter-number" data-target="<?php echo $stats['trainer_count']; ?>">0</span>+
            </div>
            <div class="stat-label">Expert Trainers</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">
                <span class="counter-number" data-target="<?php echo $stats['active_clients']; ?>">0</span>+
            </div>
            <div class="stat-label">Happy Clients</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="stat-number">
                <span class="counter-number" data-target="<?php echo $stats['cert_count']; ?>">0</span>+
            </div>
            <div class="stat-label">Certifications</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number">
                <span class="counter-number" data-target="<?php echo $stats['total_experience']; ?>">0</span>+
            </div>
            <div class="stat-label">Years Experience</div>
        </div>
    </div>
</div>

<style>
/* Statistics Section */
.trainer-stats-section {
    margin-top: -110px;
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center; /* Center the statistics container horizontally */
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    padding: 30px;
    background: rgba(65, 31, 9, 0.09);
    backdrop-filter: blur(10px);
    border-radius: 120px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-left: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    max-width: 1400px; /* Set a max-width to ensure proper centering */
    width: 100%; /* Ensure it takes full width up to max-width */
}

.stat-item {
    text-align: center;
    padding: 20px;
    position: relative;
}

.stat-item::after {
    content: '';
    position: absolute;
    top: 20%;
    right: 0;
    height: 60%;
    width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.2), transparent);
}

.stat-item:last-child::after {
    display: none;
}

.stat-icon {
    font-size: 24px;
    color: var(--primary);
    margin-bottom: 15px;
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 10px;
    text-shadow: 0 0 10px var(--primary);
}

.stat-label {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-item:nth-child(2)::after {
        display: none;
    }
}

@media (max-width: 575.98px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .stat-item::after {
        display: none;
    }
    
    .stat-number {
        font-size: 30px;
    }
    
    .stat-icon {
        font-size: 20px;
    }
}
</style>

<!-- Counter Up Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter-number');
    let started = false;

    function startCount(counter) {
        const target = parseInt(counter.getAttribute('data-target'), 10);
        const increment = target / 100; // Smoother increment
        let current = 0;

        const updateCounter = () => {
            current += increment;
            counter.textContent = Math.floor(Math.min(current, target));
            if (current < target) {
                requestAnimationFrame(updateCounter);
            }
        };

        updateCounter();
    }

    const observerCallback = (entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !started) {
                started = true;
                // Add a small delay before starting the count
                setTimeout(() => {
                    counters.forEach(counter => startCount(counter));
                }, 100);
            }
        });
    };

    // Create Intersection Observer
    const observer = new IntersectionObserver(observerCallback, { threshold: 0.5 });

    // Observe the counter section
    const counterSection = document.querySelector('.counter-section');
    if (counterSection) {
        observer.observe(counterSection);
    }
});
</script>