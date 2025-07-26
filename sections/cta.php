<?php
// Prevent direct access
if (!defined('FITZONE_APP')) {
    die('Direct access to this file is not allowed.');
}

// Set current date and time
$current_datetime = '2025-04-02 20:22:53';
$current_user = 'kaveeshawi';

// Demo classes data - in a real implementation, you would fetch this from a database
$featured_classes = [
    [
        'name' => 'HIIT Fusion',
        'image' => 'assets/images/classes/hiit.jpg',
        'description' => 'High-intensity interval training that combines cardio and strength exercises for maximum calorie burn.',
        'duration' => '45 min',
        'difficulty' => 'Advanced',
        'trainer' => 'Sarah Johnson',
        'schedule' => ['Mon, Wed, Fri', '6:00 AM, 5:30 PM']
    ],
    [
        'name' => 'Power Yoga',
        'image' => 'assets/images/classes/yoga.jpg',
        'description' => 'A dynamic, fitness-based approach to vinyasa-style yoga that builds strength, flexibility and mental focus.',
        'duration' => '60 min',
        'difficulty' => 'Intermediate',
        'trainer' => 'Michael Chen',
        'schedule' => ['Tue, Thu, Sat', '7:30 AM, 6:00 PM']
    ],
    [
        'name' => 'Spinning',
        'image' => 'assets/images/classes/spinning.jpg',
        'description' => 'High-energy indoor cycling workout that combines rhythm-based choreography with visualization and motivational coaching.',
        'duration' => '50 min',
        'difficulty' => 'All Levels',
        'trainer' => 'Jason Reynolds',
        'schedule' => ['Mon, Wed, Fri', '7:00 AM, 6:30 PM']
    ],
    [
        'name' => 'Body Sculpt',
        'image' => 'assets/images/classes/sculpt.jpg',
        'description' => 'Full-body resistance training focusing on building muscle tone, strength and endurance using weights and bodyweight.',
        'duration' => '55 min',
        'difficulty' => 'Intermediate',
        'trainer' => 'Emma Williams',
        'schedule' => ['Tue, Thu', '8:30 AM, 7:00 PM']
    ]
];

?>

<section id="cta-section" class="classes-section">
    <div class="container">

        <!-- Contact Form Section -->
        <div class="row mt-5 pt-5 contact-form-row">
            <div class="col-lg-5">
                <div class="contact-info">
                    <div class="section-heading">
                        <span class="section-subtitle">GET IN TOUCH</span>
                        <h2 class="section-title">Interested in a <span class="text-primary">Class?</span></h2>
                        <div class="section-separator"><span></span></div>
                    </div>
                    <p class="contact-description">
                        Have questions about our classes or want personalized fitness advice? Fill out the form and our team of fitness experts will get back to you within 24 hours.
                    </p>
                    <div class="contact-details">
                        <div class="contact-detail-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Our Location</h4>
                                <p>123 Fitness Street, Workout City, 10001</p>
                            </div>
                        </div>
                        <div class="contact-detail-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Phone Number</h4>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-detail-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h4>Email Address</h4>
                                <p>info@fitzone.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form-container">
                    <form id="classContactForm" class="contact-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Your Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter subject">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea id="message" name="message" rows="5" class="form-control" placeholder="Type your message here..." required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-contact">
                                    <span>Send Message</span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background Elements for Visual Interest -->
    <div class="classes-shape classes-shape-1"></div>
    <div class="classes-shape classes-shape-2"></div>
</section>