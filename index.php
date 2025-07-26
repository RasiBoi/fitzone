<?php
/**
 * FitZone Fitness Center
 * Homepage/Landing Page
 */

// Define application constant
define('FITZONE_APP', true);

// Include required files
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/authentication.php';

// Check which sections have already been included
$included_sections = [];

// Function to include a section only once
function include_section_once($section_file) {
    global $included_sections;
    
    $section_path = 'sections/' . $section_file;
    if (!in_array($section_file, $included_sections)) {
        $included_sections[] = $section_file;
        include $section_path;
    }
}

// Set page title
$page_title = "FitZone - Premium Fitness Center";

// Set current page for navigation active state
$current_page = 'index.php';

// Add custom CSS for homepage
$extra_css = '<link rel="stylesheet" href="' . SITE_URL . 'assets/css/home.css">';

// Include header
include 'includes/header.php';

// Include simplified homepage sections
include_section_once('hero.php');
include_section_once('statistics.php');
include_section_once('about.php');
include_section_once('services.php');
include_section_once('classes.php');
include_section_once('membership.php');
include_section_once('trainers.php');
include_section_once('testimonials.php');
// include_section_once('blog.php');
include_section_once('cta.php');

// Include footer
include 'includes/footer.php';
?>