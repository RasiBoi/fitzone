<?php
/**
 * FitZone Fitness Center
 * Membership Section for Homepage
 */

// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Make sure we have database connection
if (!isset($db)) {
    require_once 'includes/db_connect.php';
    $db = getDb();
}

// Fetch membership plans from database
$membership_plans = $db->fetchAll("SELECT * FROM membership_plans WHERE is_active = 1 ORDER BY price_1month", []);

// Define features for each plan (in real app, this would come from database)
$plan_features = [
    1 => [ // Basic plan features
        ['name' => 'Basic Gym Access', 'included' => true],
        ['name' => 'Locker Room Access', 'included' => true],
        ['name' => 'Fitness Assessment', 'included' => true],
        ['name' => 'Group Classes', 'included' => false],
        ['name' => 'Personal Training', 'included' => false],
        ['name' => 'Nutrition Consultation', 'included' => false]
    ],
    2 => [ // Standard plan features
        ['name' => 'Full Gym Access', 'included' => true],
        ['name' => 'Locker Room Access', 'included' => true],
        ['name' => 'Fitness Assessment', 'included' => true],
        ['name' => 'Group Classes (2/week)', 'included' => true],
        ['name' => 'Personal Training', 'included' => false],
        ['name' => 'Nutrition Consultation', 'included' => true]
    ],
    3 => [ // Premium plan features
        ['name' => 'Full 24/7 Gym Access', 'included' => true],
        ['name' => 'Premium Locker & Towel Service', 'included' => true],
        ['name' => 'Advanced Fitness Assessment', 'included' => true],
        ['name' => 'Unlimited Group Classes', 'included' => true],
        ['name' => 'Personal Training (2 sessions/month)', 'included' => true],
        ['name' => 'Nutrition Consultation', 'included' => true]
    ]
];
?>

<section id="membership-section" class="membership-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="section-heading mb-5">
                    <span class="section-subtitle">OUR MEMBERSHIPS</span>
                    <h2 class="section-title">Choose Your <span class="text-primary">Fitness Plan</span></h2>
                    <div class="section-separator"><span></span></div>
                </div>
                <p class="membership-intro">
                    We offer flexible membership options designed to fit your lifestyle and fitness goals.
                    Join our fitness community today and start your transformation.
                </p>
            </div>
        </div>
        
        <!-- Membership Toggle Switch -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 col-md-10 text-center">
                <div class="membership-duration-selector">
                    <div class="duration-tabs">
                        <div class="duration-option active" data-duration="1month">
                            <span class="duration-text">1 Month</span>
                        </div>
                        <div class="duration-option" data-duration="6month">
                            <span class="duration-text">6 Months</span>
                        </div>
                        <div class="duration-option" data-duration="12month">
                            <span class="duration-text">12 Months</span>
                        </div>
                        <div class="tab-slider"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Membership Plans -->
        <div class="row membership-cards">
            <?php foreach ($membership_plans as $plan): ?>
            <div class="col-lg-4 mb-4">
                <div class="membership-card <?php echo $plan['is_popular'] ? 'popular' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo $plan['id'] * 100; ?>">
                    <?php if ($plan['is_popular']): ?>
                    <div class="popular-badge">Most Popular</div>
                    <?php endif; ?>
                    
                    <div class="card-header">
                        <h3 class="plan-name"><?php echo htmlspecialchars($plan['type']); ?></h3>
                    </div>
                    
                    <div class="card-price">
                        <div class="price-block price-1month">
                            <span class="currency">Rs.</span>
                            <span class="amount"><?php echo number_format($plan['price_1month']); ?></span>
                            <span class="period">/month</span>
                        </div>
                        <div class="price-block price-6month" style="display:none;">
                            <span class="currency">Rs.</span>
                            <span class="amount"><?php echo number_format($plan['price_6month'] / 6); ?></span>
                            <span class="period">/month</span>
                            <div class="total-note">Total: Rs. <?php echo number_format($plan['price_6month']); ?></div>
                        </div>
                        <div class="price-block price-12month" style="display:none;">
                            <span class="currency">Rs.</span>
                            <span class="amount"><?php echo number_format($plan['price_12month'] / 12); ?></span>
                            <span class="period">/month</span>
                            <div class="total-note">Total: Rs. <?php echo number_format($plan['price_12month']); ?></div>
                        </div>
                    </div>
                    
                    <div class="card-features">
                        <ul>
                            <?php foreach ($plan_features[$plan['id']] as $feature): ?>
                            <li class="<?php echo $feature['included'] ? 'included' : 'excluded'; ?>">
                                <i class="fas <?php echo $feature['included'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                <?php echo htmlspecialchars($feature['name']); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="card-action">
                        <a href="#" class="btn btn-primary btn-rounded plan-link" data-plan="<?php echo $plan['id']; ?>" data-duration="1month">Choose Plan</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Background Shapes -->
    <div class="membership-shape membership-shape-1"></div>
    <div class="membership-shape membership-shape-2"></div>
</section>

<!-- Custom Styles for Membership Section -->
<style>
.membership-section {
    padding: 100px 0;
    background-color: var(--dark-bg);
    position: relative;
    overflow: hidden;
}

.membership-intro {
    color: var(--text-secondary);
    font-size: 1.05rem;
    line-height: 1.6;
    margin-bottom: 0px;
}

.membership-duration-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30px;
}

.duration-tabs {
    position: relative;
    display: flex;
    justify-content: space-between;
    width: 100%;
    max-width: 450px;
    background: rgba(30, 30, 40, 0.6);
    border-radius: 50px;
    padding: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3), inset 0 0 0 1px rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.4s ease;
}

.duration-tabs:hover {
    box-shadow: 0 15px 30px rgba(244, 137, 21, 0.15), inset 0 0 0 1px rgba(244, 137, 21, 0.2);
}

.duration-option {
    flex: 1;
    text-align: center;
    padding: 15px 0;
    font-weight: 600;
    font-size: 17px;
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    position: relative;
    z-index: 2;
    transition: all 0.4s ease;
    border-radius: 45px;
    user-select: none;
}

.duration-option:hover {
    color: rgba(255,255,255,0.9);
}

.duration-option.active {
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.tab-slider {
    position: absolute;
    top: 8px;
    left: 8px;
    width: calc(33.33% - 8px);
    height: calc(100% - 16px);
    background: linear-gradient(135deg, var(--primary), #f5a545);
    border-radius: 45px;
    transition: all 0.4s cubic-bezier(0.68, -0.6, 0.32, 1.6);
    z-index: 1;
    box-shadow: 0 5px 15px rgba(244, 137, 21, 0.3);
}

/* Membership Cards */
.membership-cards {
    position: relative;
    z-index: 5;
}

.membership-card {
    height: 100%;
    background-color: var(--dark-surface);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
    margin: 0 auto;
    max-width: 350px;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--dark-border);
}

.membership-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    border-color: rgba(244, 137, 21, 0.3);
}

.membership-card.popular {
    border: 2px solid var(--primary);
    transform: scale(1.03);
}

.membership-card.popular:hover {
    transform: scale(1.05) translateY(-10px);
    box-shadow: 0 25px 50px rgba(244, 137, 21, 0.2);
}

.popular-badge {
    position: absolute;
    top: 25px;
    right: -35px;
    background: var(--primary);
    color: white;
    padding: 5px 40px;
    font-size: 14px;
    font-weight: 600;
    transform: rotate(45deg);
    z-index: 1;
}

.card-header {
    padding: 25px 20px 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    background-color: rgba(0,0,0,0.2);
}

.plan-name {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
}

.card-price {
    padding: 25px 20px;
    text-align: center;
    background-color: rgba(0,0,0,0.1);
    position: relative;
}

.price-block {
    transition: all 0.3s ease;
}

.price-block.price-6month,
.price-block.price-12month {
    display: none;
}

.currency {
    font-size: 18px;
    font-weight: 500;
    position: relative;
    top: -10px;
    color: var(--text-primary);
}

.amount {
    font-size: 42px;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
}

.period {
    font-size: 16px;
    color: var(--text-secondary);
}

.total-note {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 5px;
}

.card-features {
    padding: 0px 20px;
    flex-grow: 1;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.card-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.card-features li {
    padding: 12px 0;
    display: flex;
    align-items: center;
    font-size: 15px;
    color: var(--text-primary);
}

.card-features li i {
    margin-right: 12px;
    font-size: 16px;
    width: 20px;
}

.card-features li.included i {
    color: var(--primary);
}

.card-features li.excluded {
    color: rgba(255,255,255,0.4);
    text-decoration: line-through;
}

.card-features li.excluded i {
    color: rgba(255,255,255,0.2);
}

.card-action {
    padding: 20px 25px 25px;
    text-align: center;
}

.btn-rounded {
    border-radius: 30px;
    padding: 12px 30px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-rounded:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(244, 137, 21, 0.3);
}

/* Background Shapes */
.membership-shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.1;
}

.membership-shape-1 {
    top: -100px;
    right: -100px;
    width: 500px;
    height: 500px;
    background: radial-gradient(var(--primary), transparent 70%);
}

.membership-shape-2 {
    bottom: -150px;
    left: -150px;
    width: 600px;
    height: 600px;
    background: radial-gradient(var(--primary), transparent 70%);
}

/* Media Queries */
@media (max-width: 991px) {
    .membership-section {
        padding: 80px 0;
    }
    
    .membership-card {
        margin-bottom: 30px;
    }
    
    .membership-card.popular {
        transform: none;
    }
    
    .membership-card.popular:hover {
        transform: translateY(-10px);
    }
    
    .section-title {
        font-size: 2.4rem;
    }
}

@media (max-width: 767px) {
    .membership-section {
        padding: 70px 0;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .plan-name {
        font-size: 22px;
    }
    
    .amount {
        font-size: 36px;
    }
}

@media (max-width: 575px) {
    .membership-section {
        padding: 60px 0;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .membership-intro {
        font-size: 1rem;
    }
    
    .plan-name {
        font-size: 20px;
    }
    
    .amount {
        font-size: 32px;
    }
    
    .card-features li {
        font-size: 14px;
    }
    
    .btn-rounded {
        padding: 10px 25px;
        font-size: 14px;
    }
    
    .duration-tabs {
        max-width: 320px;
    }
    .duration-option {
        font-size: 15px;
        padding: 12px 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle duration toggle
    const durationOptions = document.querySelectorAll('.duration-option');
    const priceBlocks = document.querySelectorAll('.price-block');
    const planLinks = document.querySelectorAll('.plan-link');
    const tabSlider = document.querySelector('.tab-slider');
    
    durationOptions.forEach((option, index) => {
        option.addEventListener('click', function() {
            const duration = this.dataset.duration;
            
            // Update active option
            durationOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update price blocks
            priceBlocks.forEach(block => block.style.display = 'none');
            document.querySelectorAll(`.price-block.price-${duration}`).forEach(block => block.style.display = 'block');
            
            // Update plan links
            planLinks.forEach(link => {
                link.setAttribute('data-duration', duration);
            });
            
            // Move tab slider
            tabSlider.style.transform = `translateX(${index * 100}%)`;
        });
    });
    
    // Handle plan selection and login check
    planLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get selected plan data
            const planId = this.getAttribute('data-plan');
            const duration = this.getAttribute('data-duration');
            
            // Check if user is logged in (this will be returned by PHP as a JS variable)
            const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
            
            if (isLoggedIn) {
                // Redirect to dashboard with plan information
                window.location.href = '<?php echo SITE_URL; ?>backend/member/index.php?selected_plan=' + planId + '&duration=' + duration;
            } else {
                // Redirect to login page with return URL containing plan information
                const returnUrl = encodeURIComponent('backend/member/index.php?selected_plan=' + planId + '&duration=' + duration);
                window.location.href = '<?php echo SITE_URL; ?>login.php?redirect_to=' + returnUrl;
            }
        });
    });
});
</script>