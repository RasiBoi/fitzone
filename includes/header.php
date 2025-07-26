<?php
 // Prevent direct script access
if (!defined('FITZONE_APP')) {
    exit('Direct script access denied.');
}

// Initialize current_page if not set
if (!isset($current_page)) {
    $current_page = basename($_SERVER['PHP_SELF']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="site-url" content="<?php echo SITE_URL; ?>">
    <title>Fitzone Fitness Center</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo SITE_URL; ?>assets/images/favicon.png" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/navbar.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/home.css">

    <!-- Swiper library -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
</head>


<body>
    <!-- Main Navigation - New Design -->
    <nav id="custom-navbar" class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <!-- Logo on the left -->
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>assets/images/fitzone.png" alt="FitZone" height="35">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" 
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Navigation Links -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#hero-section">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#about-section">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($current_page, 'services') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#services-section">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'classes.php' ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#classes-section">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'membership/index.php' || $current_page == 'membership.php' ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#membership-section">Membership</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>#cta-section">Contact</a>
                    </li>
                </ul>
                
                <!-- Search and Login on the right -->
                <div class="d-flex align-items-center">
                    <!-- Search Icon and Dropdown -->
                    <div class="search-container">
                        <div class="search-icon" id="search-toggle">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="search-dropdown" id="search-dropdown">
                            <form class="search-form" id="search-form" action="<?php echo SITE_URL; ?>includes/search.php" method="get">
                                <input class="search-input" type="search" placeholder="Search..." aria-label="Search" name="q" id="search-input">
                                <button class="search-submit" type="submit">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                            <div class="search-results" id="search-results"></div>
                        </div>
                    </div>
                    
                    <!-- Login Button -->
                    <a href="<?php echo SITE_URL; ?>login.php" class="btn btn-login">Log In</a>
                </div>
            </div>
        </div>
    </nav>

<!-- Add search styles -->
<style>
.search-container {
    position: relative;
    margin-right: 15px;
}

.search-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #fff;
}

.search-icon:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.search-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 320px;
    background-color: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    visibility: hidden;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.search-dropdown.active {
    visibility: visible;
    opacity: 1;
    transform: translateY(5px);
}

.search-form {
    display: flex;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 30px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    outline: none;
}

.search-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.search-submit {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: #007bff;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-submit:hover {
    background: #0056b3;
}

.search-results {
    max-height: 300px;
    overflow-y: auto;
    margin-top: 10px;
    display: none;
}

.search-result-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-title {
    font-weight: 600;
    margin-bottom: 3px;
    color: #212529;
}

.search-result-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.4;
}

.search-result-badge {
    display: inline-block;
    padding: 2px 6px;
    margin-bottom: 5px;
    font-size: 0.7rem;
    font-weight: 500;
    border-radius: 3px;
}

.search-empty-result {
    padding: 15px 10px;
    text-align: center;
    color: #6c757d;
}

.search-highlight {
    background-color: #ffeb3b;
    padding: 0 2px;
    border-radius: 2px;
}

.search-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border-radius: 4px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    z-index: 1050;
    transform: translateX(150%);
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.search-notification.show {
    transform: translateX(0);
    opacity: 1;
}

.search-notification.error {
    background-color: #dc3545;
}

.search-notification i {
    margin-right: 8px;
}

.search-info {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 10px 0 5px;
    display: flex;
    justify-content: space-between;
}

/* Loading indicator */
.search-loading {
    display: none;
    text-align: center;
    padding: 10px;
}

.search-loading .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
    border-width: 0.2em;
}

@media (max-width: 576px) {
    .search-dropdown {
        width: 280px;
    }
}
</style>