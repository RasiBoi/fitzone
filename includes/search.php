<?php
// Define application constant to prevent direct access to included files
define('FITZONE_APP', true);

// Include necessary files
require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/functions.php';

// Check if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Initialize results array and counters
$results = array();
$total_results = 0;

// Map section IDs to readable names
$section_names = array(
    'about-section' => 'About Us',
    'services-section' => 'Services',
    'classes-section' => 'Classes',
    'membership-section' => 'Membership',
    'cta-section' => 'Contact',
    'hero-section' => 'Home'
);

// Search only if query is provided
if (!empty($search_query)) {
    // List of section files to search
    $section_files = array(
        'about' => dirname(__FILE__) . '/../sections/about.php',
        'services' => dirname(__FILE__) . '/../sections/services.php',
        'classes' => dirname(__FILE__) . '/../sections/classes.php',
        'membership' => dirname(__FILE__) . '/../sections/membership.php',
        'cta' => dirname(__FILE__) . '/../sections/cta.php',
        'hero' => dirname(__FILE__) . '/../sections/hero.php'
    );

    // Search through each section file
    foreach ($section_files as $section_key => $file_path) {
        // Check if file exists
        if (!file_exists($file_path)) {
            continue;
        }

        // Get file content
        $file_content = file_get_contents($file_path);
        
        // Skip if empty or file read error
        if ($file_content === false) {
            continue;
        }
        
        // Extract text content from HTML
        $section_text = strip_tags($file_content);
        $section_id = $section_key . '-section';
        
        // Search for the query in the text
        if (stripos($section_text, $search_query) !== false) {
            // Get section title from HTML
            preg_match('/<h2[^>]*>(.*?)<\/h2>/si', $file_content, $title_matches);
            $section_title = !empty($title_matches[1]) ? strip_tags($title_matches[1]) : $section_names[$section_id];
            
            // Extract paragraphs that contain the search term
            preg_match_all('/<p[^>]*>(.*?)<\/p>/si', $file_content, $paragraph_matches);
            $matching_paragraphs = array();
            
            foreach ($paragraph_matches[1] as $paragraph) {
                $paragraph_text = strip_tags($paragraph);
                if (stripos($paragraph_text, $search_query) !== false) {
                    $matching_paragraphs[] = $paragraph_text;
                }
            }
            
            // If no matching paragraphs, get the first paragraph
            if (empty($matching_paragraphs) && !empty($paragraph_matches[1])) {
                $matching_paragraphs[] = strip_tags($paragraph_matches[1][0]);
            }
            
            // Generate description
            $description = !empty($matching_paragraphs) ? $matching_paragraphs[0] : substr($section_text, 0, 200) . '...';
            
            // Add to results
            $results[] = array(
                'type' => 'section',
                'id' => $section_id,
                'title' => $section_title,
                'description' => $description,
                'image' => null,
                'section_key' => $section_key
            );
            
            $total_results++;
        }
        
        // Search for individual elements like features, cards, list items
        // Features
        preg_match_all('/<h4[^>]*>(.*?)<\/h4>/si', $file_content, $feature_titles);
        preg_match_all('/<div class="[^"]*feature[^"]*">(.*?)<\/div>/si', $file_content, $feature_divs);
        
        if (!empty($feature_titles[1])) {
            foreach ($feature_titles[1] as $index => $feature_title) {
                $title = strip_tags($feature_title);
                
                // Skip if title doesn't contain search term
                if (stripos($title, $search_query) === false) {
                    // Try to get associated paragraph
                    $feature_content = isset($feature_divs[1][$index]) ? $feature_divs[1][$index] : '';
                    preg_match('/<p[^>]*>(.*?)<\/p>/si', $feature_content, $feature_paragraph);
                    
                    $paragraph_text = isset($feature_paragraph[1]) ? strip_tags($feature_paragraph[1]) : '';
                    
                    // If paragraph doesn't contain search term either, skip
                    if (stripos($paragraph_text, $search_query) === false) {
                        continue;
                    }
                }
                
                // Extract description
                preg_match('/<p[^>]*>(.*?)<\/p>/si', isset($feature_divs[1][$index]) ? $feature_divs[1][$index] : '', $feature_description);
                $description = isset($feature_description[1]) ? strip_tags($feature_description[1]) : '';
                
                // Add to results
                $results[] = array(
                    'type' => 'feature',
                    'id' => $section_id,
                    'title' => $title,
                    'description' => $description,
                    'image' => null,
                    'section_key' => $section_key
                );
                
                $total_results++;
            }
        }
    }
}

// Function to highlight search terms in text
function highlightSearchTerms($text, $search) {
    if (empty($search)) {
        return $text;
    }
    
    $search_words = explode(' ', $search);
    foreach ($search_words as $word) {
        if (strlen($word) > 2) { // Only highlight words longer than 2 characters
            $word = preg_quote($word, '/');
            $text = preg_replace("/\b($word)\b/i", '<span class="search-highlight">$1</span>', $text);
        }
    }
    return $text;
}

// Map sections to section icons
$section_icons = [
    'about' => 'info-circle',
    'services' => 'concierge-bell',
    'classes' => 'dumbbell',
    'membership' => 'id-card',
    'cta' => 'phone-alt',
    'hero' => 'home'
];

// If it's an AJAX request, return JSON
if ($is_ajax) {
    header('Content-Type: application/json');
    
    // Process results to highlight search terms
    $processed_results = array();
    foreach ($results as $result) {
        $processed_result = $result;
        $processed_result['title'] = highlightSearchTerms(htmlspecialchars($result['title']), $search_query);
        
        // Limit and highlight description
        $description = strip_tags($result['description']);
        $description = substr($description, 0, 150) . (strlen($description) > 150 ? '...' : '');
        $processed_result['description'] = highlightSearchTerms(htmlspecialchars($description), $search_query);
        
        // Format url based on section - using SITE_URL which includes /fitzone/
        $processed_result['url'] = SITE_URL . 'index.php#' . $result['id'];
        
        $processed_results[] = $processed_result;
    }
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'total' => $total_results,
        'query' => $search_query,
        'results' => $processed_results
    ]);
    exit;
}

// If not AJAX, include header and display results in HTML
$current_page = 'search.php';
require_once dirname(__FILE__) . '/header.php';
?>

<div class="container search-results-container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="search-title">Search Results</h1>
            <?php if (!empty($search_query)): ?>
                <p class="search-info">
                    Found <?php echo $total_results; ?> results for: 
                    <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong>
                </p>
            <?php endif; ?>
            
            <!-- Search form again for convenience -->
            <form action="<?php echo SITE_URL; ?>search.php" method="get" class="mb-4">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search..." 
                           aria-label="Search" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($search_query)): ?>
        <div class="alert alert-info">
            Please enter a search term to find content on our website.
        </div>
    <?php elseif ($total_results === 0): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> No results found for "<strong><?php echo htmlspecialchars($search_query); ?></strong>".
            <p class="mt-2">Suggestions:</p>
            <ul>
                <li>Check the spelling of your search term.</li>
                <li>Try using more general keywords.</li>
                <li>Try different keywords related to your search.</li>
            </ul>
        </div>
    <?php else: ?>
        <!-- Simple list display of results with no summary tags -->
        <div class="search-results-list">
            <?php foreach ($results as $result): 
                $icon = isset($section_icons[$result['section_key']]) ? $section_icons[$result['section_key']] : 'search';
            ?>
                <div class="search-result-item">
                    <a href="<?php echo SITE_URL . 'index.php#' . $result['id']; ?>" data-section="<?php echo $result['id']; ?>">
                        <div class="result-content">
                            <div class="result-icon">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div class="result-info">
                                <h3 class="result-title">
                                    <?php echo highlightSearchTerms(htmlspecialchars($result['title']), $search_query); ?>
                                </h3>
                                <p class="result-description">
                                    <?php 
                                    $description = strip_tags($result['description']);
                                    $description = substr($description, 0, 150) . (strlen($description) > 150 ? '...' : '');
                                    echo highlightSearchTerms(htmlspecialchars($description), $search_query); 
                                    ?>
                                </p>
                                <div class="result-section">
                                    <?php echo ucfirst($result['section_key']); ?> Section
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add custom CSS for search results page with dark theme -->
<style>
.search-results-container {
    color: var(--text-primary);
}

.search-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.search-info {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.search-highlight {
    background-color: rgba(244, 137, 21, 0.3);
    color: var(--primary);
    padding: 0 2px;
    border-radius: 2px;
}

/* Search result item styling */
.search-result-item {
    background-color: var(--dark-surface);
    border: 1px solid var(--dark-border);
    border-radius: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    overflow: hidden;
}

/* Simple slide effect on hover */
.search-result-item:hover {
    transform: translateX(10px);
    background: var(--dark-surface);
}

/* Section highlight effect when redirecting */
@keyframes section-highlight-animation {
    0% { background-color: rgba(244, 137, 21, 0.3); }
    100% { background-color: transparent; }
}

.section-highlight {
    animation: section-highlight-animation 2s ease-out;
}

.search-result-item a {
    text-decoration: none;
    color: var(--text-primary);
    display: block;
}

.result-content {
    display: flex;
    padding: 20px;
}

.result-icon {
    flex: 0 0 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(244, 137, 21, 0.1);
    border-radius: 50%;
    margin-right: 15px;
    color: var(--primary);
    font-size: 24px;
}

.result-info {
    flex: 1;
}

.result-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text-primary);
}

.result-description {
    font-size: 14px;
    color: var(--text-secondary);
    margin-bottom: 15px;
    line-height: 1.5;
}

.result-section {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--primary);
    border-top: 1px solid var(--dark-border);
    padding-top: 10px;
}

/* Alert styling */
.alert {
    background-color: var(--dark-surface);
    border-color: var(--dark-border);
    color: var(--text-primary);
}

.alert-info {
    border-left: 4px solid var(--primary);
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

/* Back to top button */
.back-to-top {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary);
    color: #fff;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    z-index: 1000;
    transition: all 0.3s ease;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

/* Input field styling */
.search-results-container .form-control {
    background-color: #333;
    border-color: var(--dark-border);
    color: var(--text-primary);
}

.search-results-container .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(244, 137, 21, 0.25);
}

/* Mobile responsiveness */
@media (max-width: 767px) {
    .result-content {
        flex-direction: column;
    }
    
    .result-icon {
        margin-bottom: 15px;
        margin-right: 0;
    }
    
    .search-result-item {
        margin-bottom: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add back to top button
    const backToTopBtn = document.createElement('div');
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(backToTopBtn);
    
    // Show/hide back to top button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    // Scroll to top when clicking the button
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Handle search result link clicks for direct navigation
    document.querySelectorAll('.search-result-item a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get target section ID from href
            const href = this.getAttribute('href');
            const targetId = href.includes('#') ? href.split('#')[1] : '';
            
            if (targetId) {
                // First try to find the element on the current page
                let targetElement = document.getElementById(targetId);
                
                if (!targetElement) {
                    // Always use the full SITE_URL path for redirection
                    window.location.href = '<?php echo SITE_URL; ?>#' + targetId;
                    return;
                }
                
                // If element exists on current page
                // Get header height for offset
                const headerHeight = document.getElementById('custom-navbar') ? 
                    document.getElementById('custom-navbar').offsetHeight : 0;
                
                // Calculate position
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight - 20;
                
                // Scroll to element
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Add a highlight effect to the target section
                targetElement.classList.add('section-highlight');
                setTimeout(() => {
                    targetElement.classList.remove('section-highlight');
                }, 2000);
                
                // Update URL without page reload - always use the full SITE_URL path
                history.pushState(null, null, '<?php echo SITE_URL; ?>#' + targetId);
            }
        });
    });
});
</script>

<?php
// Include footer
require_once dirname(__FILE__) . '/footer.php';
?>