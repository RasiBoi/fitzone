<?php

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

// Get current year for copyright
$currentYear = date('Y');
?>

        </main> <!-- End of main content -->

        <!-- Footer -->
        <footer class="footer bg-black text-light mt-5 pt-5">
            <div class="container">
                <div class="row">
                    <!-- About Column -->
                    <div class="col-lg-4 mb-4">
                        <h4 class="mb-3" style="color: #F48915;">About</h4>
                        <img src="<?php echo SITE_URL; ?>assets/images/fitzone.png" alt="FitZone" height="20" class="mb-3">
                        <p class="text-light-50">
                            FitZone is Kurunegala's premier fitness center offering state-of-the-art equipment, expert trainers, and diverse fitness programs designed to help you achieve your health and wellness goals.
                        </p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-primary me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-primary me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-primary me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-primary me-3"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    
                    <!-- Quick Links Column - Added padding-left for more right alignment -->
                    <div class="col-lg-4 col-md-6 mb-4" style="padding-left: 140px;">
                        <h5 class="mb-3" style="color: #F48915;">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>" class="text-decoration-none text-light-50 hover-white">Home</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>about.php" class="text-decoration-none text-light-50 hover-white">About Us</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>services/index.php" class="text-decoration-none text-light-50 hover-white">Services</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>membership/index.php" class="text-decoration-none text-light-50 hover-white">Membership</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>blog/index.php" class="text-decoration-none text-light-50 hover-white">Blog</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>contact.php" class="text-decoration-none text-light-50 hover-white">Contact</a></li>
                        </ul>
                    </div>
                
                    
                    <!-- Contact Info Column -->
                    <div class="col-lg-4 mb-4">
                        <h5 class="mb-3" style="color: #F48915;">Contact Info</h5>
                        <ul class="list-unstyled footer-contact">
                            <li class="d-flex mb-3">
                                <i class="fas fa-map-marker-alt mt-1 me-3" style="color: #F48915;"></i>
                                <span>123 Fitness Avenue, Kurunegala,<br>North Western Province, Sri Lanka</span>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-phone-alt mt-1 me-3" style="color: #F48915;"></i>
                                <span>+94 76 123 4567</span>
                            </li>
                            <li class="d-flex mb-3">
                                <i class="fas fa-envelope mt-1 me-3" style="color: #F48915;"></i>
                                <span>info@fitzone.com</span>
                            </li>
                            <li class="d-flex">
                                <i class="fas fa-clock mt-1 me-3" style="color: #F48915;"></i>
                                <div>
                                    <p class="mb-1">Monday - Friday: 6:00 AM - 10:00 PM</p>
                                    <p class="mb-1">Saturday: 7:00 AM - 8:00 PM</p>
                                    <p class="mb-0">Sunday: 8:00 AM - 6:00 PM</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr class="mt-2 mb-2 border-dark">
                
                <!-- Bottom Footer with Centered Copyright -->
                <div class="bottom-footer pb-4 text-center">
                    <p class="mb-0">&copy; <?php echo $currentYear; ?> FitZone Fitness Center. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
        
        <!-- Back to Top Button -->
        <button id="back-to-top" class="btn rounded-circle">
            <i class="fas fa-arrow-up"></i>
        </button>
        
        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- jQuery (required for some custom functionality) -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        
        <!-- Custom JavaScript -->
        <script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
        
        <?php if (isset($extra_js)): ?>
            <!-- Page-specific JavaScript -->
            <?php echo $extra_js; ?>
        <?php endif; ?>
        
        <script>
            // Back to top button functionality
            $(document).ready(function() {
                var btn = $('#back-to-top');
                
                $(window).scroll(function() {
                    if ($(window).scrollTop() > 300) {
                        btn.addClass('show');
                    } else {
                        btn.removeClass('show');
                    }
                });
                
                btn.on('click', function(e) {
                    e.preventDefault();
                    $('html, body').animate({scrollTop:0}, '300');
                });
                
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
                
                // Search functionality is now handled in main.js
            });
            
            // Current server time display (if needed)
            function updateServerTime() {
                const timeElement = document.getElementById('server-time');
                if (timeElement) {
                    const now = new Date();
                    const options = { 
                        year: 'numeric', 
                        month: '2-digit', 
                        day: '2-digit',
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit',
                        hour12: false
                    };
                    timeElement.textContent = now.toLocaleString('en-US', options).replace(',', '');
                    setTimeout(updateServerTime, 1000);
                }
            }
            // Uncomment to enable server time display
            // updateServerTime();
        </script>
    </body>
</html>