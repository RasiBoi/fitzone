/**
 * FitZone Fitness Center
 * Main JavaScript File
 */

// Mobile sidebar toggle functionality
function initSidebar() {
    // Create toggle button for mobile
    const body = document.body;
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'sidebar-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    
    // Create overlay for closing sidebar when clicking outside
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    
    // Append to body
    body.appendChild(toggleBtn);
    body.appendChild(overlay);
    
    // Toggle sidebar on button click
    toggleBtn.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar-container');
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
    
    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar-container');
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
}

// Back to top button functionality
function initBackToTop() {
    var btn = $('#back-to-top');
    
    $(window).scrollTop() > 300 ? btn.addClass('show') : btn.removeClass('show');
    
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
}

// Enhanced Search Functionality
function initSearchFunctionality() {
    // Get DOM elements
    const searchIcon = document.getElementById('search-toggle');
    const searchDropdown = document.getElementById('search-dropdown');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    
    // Check if search elements exist
    if (!searchIcon || !searchDropdown || !searchForm || !searchInput || !searchResults) {
        console.error("Search elements not found in the DOM");
        return;
    }
    
    // Create loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'search-loading';
    loadingIndicator.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    searchResults.parentNode.insertBefore(loadingIndicator, searchResults.nextSibling);
    
    // Create no results message
    const noResultsTemplate = `
        <div class="search-empty-result">
            <i class="fas fa-search me-2"></i>No results found
        </div>
    `;
    
    // Toggle search dropdown when clicking the search icon
    searchIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        searchDropdown.classList.toggle('active');
        
        // Focus the search input when opened
        if (searchDropdown.classList.contains('active')) {
            setTimeout(() => {
                searchInput.focus();
            }, 100);
        } else {
            // Clear results when closing
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchDropdown.contains(e.target) && !searchIcon.contains(e.target)) {
            searchDropdown.classList.remove('active');
        }
    });
    
    // Close search dropdown on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchDropdown.classList.contains('active')) {
            searchDropdown.classList.remove('active');
        }
    });
    
    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    
    // Perform AJAX search when typing in search input
    searchInput.addEventListener('input', debounce(function() {
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }
        
        // Get site URL from meta tag
        const siteUrl = document.querySelector('meta[name="site-url"]')?.content || 'http://localhost/fitzone/';
        
        // Show loading indicator
        loadingIndicator.style.display = 'block';
        searchResults.style.display = 'none';
        
        // Perform AJAX search using full site URL
        fetch(`${siteUrl}includes/search.php?q=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading indicator
            loadingIndicator.style.display = 'none';
            
            // Display results
            searchResults.style.display = 'block';
            
            if (data.total > 0) {
                // Create search info
                let searchInfo = `<div class="search-info">
                    <span>Found ${data.total} results</span>
                    <a href="search.php?q=${encodeURIComponent(searchTerm)}" class="view-all">View all</a>
                </div>`;
                
                // Define section icons
                const sectionIcons = {
                    'about': 'info-circle',
                    'services': 'concierge-bell',
                    'classes': 'dumbbell',
                    'trainers': 'user-friends',
                    'membership': 'id-card',
                    'testimonials': 'quote-right',
                    'blog': 'blog',
                    'cta': 'phone-alt',
                    'hero': 'home',
                    'statistics': 'chart-bar'
                };
                
                // Create results HTML
                let resultsHTML = '';
                data.results.slice(0, 5).forEach(result => {
                    let badgeColor = '';
                    let badgeIcon = result.section_key in sectionIcons ? sectionIcons[result.section_key] : 'search';
                    
                    switch (result.type) {
                        case 'section':
                            badgeColor = 'bg-primary';
                            break;
                        case 'feature':
                            badgeColor = 'bg-success';
                            break;
                        case 'card':
                            badgeColor = 'bg-info';
                            break;
                        default:
                            badgeColor = 'bg-secondary';
                    }
                    
                    resultsHTML += `
                        <a href="${result.url}" class="text-decoration-none">
                            <div class="search-result-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="search-result-badge ${badgeColor}">
                                        <i class="fas fa-${badgeIcon}"></i> ${result.type.charAt(0).toUpperCase() + result.type.slice(1)}
                                    </span>
                                    <span class="search-result-section">
                                        <i class="fas fa-${badgeIcon}"></i> ${result.section_key.charAt(0).toUpperCase() + result.section_key.slice(1)}
                                    </span>
                                </div>
                                <div class="search-result-title">${result.title}</div>
                                <p class="search-result-description">${result.description}</p>
                            </div>
                        </a>
                    `;
                });
                
                // If there are more results than shown
                if (data.total > 5) {
                    resultsHTML += `
                        <a href="search.php?q=${encodeURIComponent(searchTerm)}" class="text-decoration-none">
                            <div class="search-result-item text-center">
                                <span class="text-primary">View all ${data.total} results</span>
                            </div>
                        </a>
                    `;
                }
                
                searchResults.innerHTML = searchInfo + resultsHTML;
                
                // If the search returns results, add a click event to the results
                // that will close the search dropdown when clicked
                const resultItems = searchResults.querySelectorAll('.search-result-item');
                resultItems.forEach(item => {
                    item.addEventListener('click', function() {
                        searchDropdown.classList.remove('active');
                    });
                });
                
            } else {
                searchResults.innerHTML = `
                    <div class="search-empty-result">
                        <i class="fas fa-search me-2"></i>No results found for "${searchTerm}"
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            loadingIndicator.style.display = 'none';
            searchResults.style.display = 'block';
            searchResults.innerHTML = `
                <div class="search-empty-result">
                    <i class="fas fa-exclamation-circle me-2"></i>Error performing search
                </div>
            `;
        });
    }, 300));
    
    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        const searchTerm = searchInput.value.trim();
        
        if (!searchTerm || searchTerm.length < 2) {
            e.preventDefault();
            showNotification('Please enter at least 2 characters to search', 'error');
        }
        
        // If not prevented, the form submits to search.php normally
    });
    
    // Add smooth scrolling for search result clicks
    document.addEventListener('click', function(e) {
        // Check if the clicked element is a search result link with a hash URL
        let target = e.target;
        while (target && target !== document) {
            if (target.tagName === 'A' && target.getAttribute('href') && target.getAttribute('href').includes('#')) {
                const href = target.getAttribute('href');
                const isHashLink = href.includes('#') && href.split('#')[1].length > 0;
                
                if (isHashLink) {
                    e.preventDefault();
                    
                    // Get the target element ID
                    let targetId = href.split('#')[1];
                    
                    // If it's a full URL, extract just the hash part
                    if (targetId.includes('/')) {
                        targetId = targetId.split('/').pop();
                    }
                    
                    // Try to find the target element on the current page
                    const targetElement = document.getElementById(targetId);
                    
                    // Get site URL from the meta tag
                    const siteUrl = document.querySelector('meta[name="site-url"]')?.content || 'http://localhost/fitzone/';
                    
                    if (targetElement) {
                        // Close search dropdown if open
                        if (searchDropdown) {
                            searchDropdown.classList.remove('active');
                        }
                        
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
                        
                        // Highlight the element temporarily
                        targetElement.classList.add('section-highlight');
                        setTimeout(() => {
                            targetElement.classList.remove('section-highlight');
                        }, 2000);
                        
                        // Update URL - always use the full fitzone URL path
                        history.pushState(null, null, siteUrl + '#' + targetId);
                    } else {
                        // If the target doesn't exist on current page, redirect to the homepage with the hash
                        window.location.href = siteUrl + '#' + targetId;
                    }
                    return;
                }
            }
            target = target.parentNode;
        }
    });
    
    // Show notification
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `search-notification ${type === 'error' ? 'error' : ''}`;
        notification.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${message}`;
        document.body.appendChild(notification);
        
        // Show then hide notification
        setTimeout(() => {
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }, 10);
    }
}

// Membership duration toggle functionality
function initMembershipDurationToggle() {
    // Get DOM elements
    const durationOptions = document.querySelectorAll('.duration-option');
    const priceBlocks = document.querySelectorAll('.price-block');
    const planLinks = document.querySelectorAll('.plan-link');
    
    // Check if membership duration elements exist
    if (!durationOptions.length) {
        console.log("Membership duration elements not found in the DOM");
        return;
    }
    
    durationOptions.forEach(option => {
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
                const url = new URL(link.href);
                url.searchParams.set('duration', duration);
                link.href = url.toString();
            });
        });
    });
}

// Initialize all scripts
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded, initializing scripts...");
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Only add mobile controls if screen width is below 768px
    if (window.innerWidth <= 768) {
        initSidebar();
    }
    
    // Check window resize to add/remove sidebar for responsive design
    window.addEventListener('resize', function() {
        // If sidebar toggle already exists, do nothing
        if (document.querySelector('.sidebar-toggle')) {
            return;
        }
        
        // If window width is now mobile size, add sidebar toggle
        if (window.innerWidth <= 768) {
            initSidebar();
        }
    });
    
    // Initialize back to top button
    if (typeof $ !== 'undefined') { // Check if jQuery is loaded
        initBackToTop();
    }
    
    // Initialize search functionality
    initSearchFunctionality();
    
    // Initialize membership duration toggle
    initMembershipDurationToggle();
    
    // Smooth scrolling navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                // Get header height for offset
                const headerHeight = document.getElementById('custom-navbar').offsetHeight;
                
                // Calculate the position to scroll to (element position - header height)
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                
                // Smooth scroll to the target
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Update URL without page reload
                history.pushState(null, null, `#${targetId}`);
            }
        });
    });
    
    // Handle navigation from other pages to home page with hash
    if (location.hash) {
        setTimeout(function() {
            const targetId = location.hash.substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerHeight = document.getElementById('custom-navbar').offsetHeight;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }, 100); // Small delay to ensure DOM is fully loaded
    }
    
    // Add styles for search target highlighting
    const style = document.createElement('style');
    style.textContent = `
        @keyframes target-fade {
            0% { background-color: rgba(255, 235, 59, 0.7); }
            100% { background-color: rgba(255, 235, 59, 0); }
        }
        
        .search-target-highlight {
            animation: target-fade 2s ease-out;
        }
        
        .search-result-section {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .section-highlight {
            animation: target-fade 2s ease-out;
        }
    `;
    document.head.appendChild(style);
});