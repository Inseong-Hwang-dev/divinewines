/*!
 * Start Bootstrap - Business Frontpage v5.0.9 (https://startbootstrap.com/template/business-frontpage)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-business-frontpage/blob/master/LICENSE)
 */

document.addEventListener('DOMContentLoaded', function () {
    // Fade in the body to prevent FOUC
    document.body.classList.add('loaded');

    // Homepage navbar scroll behavior
    if (typeof IS_HOME !== 'undefined' && IS_HOME) {
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // Dropdown toggle behavior (desktop only)
    const dropdownToggles = document.querySelectorAll('.navbar-bottom .dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const navbar = document.querySelector('.navbar');
            navbar.classList.toggle('dropdown-open');
        });

        const dropdown = toggle.closest('.dropdown');
        if (dropdown) {
            dropdown.addEventListener('hidden.bs.dropdown', function () {
                const navbar = document.querySelector('.navbar');
                navbar.classList.remove('dropdown-open');
            });
        }
    });

    // Mobile full-screen menu toggle
    const toggleButton = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.mobile-menu');

    if (toggleButton && menu) {
        const hamburgerIcon = toggleButton.querySelector('.hamburger-icon');
        const closeIcon = toggleButton.querySelector('.close-icon');

        toggleButton.addEventListener('click', function () {
            const isOpen = menu.classList.contains('open');

            if (isOpen) {
                // Close menu with animation
                menu.style.opacity = '0';
                setTimeout(() => {
                    menu.classList.remove('open');
                    menu.style.display = 'none';
                    hamburgerIcon.style.display = 'inline-block';
                    closeIcon.style.display = 'none';
                    document.body.classList.remove('menu-open');
                }, 300);
            } else {
                // Open menu with animation
                menu.classList.add('open');
                menu.style.display = 'block';
                hamburgerIcon.style.display = 'none';
                closeIcon.style.display = 'inline-block';
                document.body.classList.add('menu-open');
                
                // Trigger reflow to ensure animation works
                void menu.offsetWidth;
                
                // Fade in
                menu.style.opacity = '1';
            }
        });
        
        // Close menu when clicking on a link
        const menuLinks = menu.querySelectorAll('.nav-link, .nav-icon-login');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                menu.style.opacity = '0';
                setTimeout(() => {
                    menu.classList.remove('open');
                    menu.style.display = 'none';
                    hamburgerIcon.style.display = 'inline-block';
                    closeIcon.style.display = 'none';
                    document.body.classList.remove('menu-open');
                }, 300);
            });
        });
    }
});

$(document).ready(function() {
    // Voice recognition setup
    let recognition = null;
    let isListening = false;

    // Check if browser supports speech recognition
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript.trim();
            console.log('Voice input:', transcript); // Debug log
            const searchInput = $('#search-input');
            searchInput.val(transcript);
            searchInput.trigger('input');
        };

        recognition.onend = function() {
            console.log('Speech recognition ended'); // Debug log
            isListening = false;
            $('#voice-search-btn').removeClass('listening');
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            isListening = false;
            $('#voice-search-btn').removeClass('listening');
        };

        recognition.onstart = function() {
            console.log('Speech recognition started'); // Debug log
        };
    }

    // Voice search button handlers
    $('#voice-search-btn').on('mousedown touchstart', function(e) {
        e.preventDefault();
        if (!recognition) {
            alert('Voice search is not supported in your browser.');
            return;
        }
        if (!isListening) {
            try {
                recognition.start();
                isListening = true;
                $(this).addClass('listening');
            } catch (error) {
                console.error('Error starting speech recognition:', error);
            }
        }
    });

    $('#voice-search-btn').on('mouseup touchend', function(e) {
        e.preventDefault();
        if (isListening && recognition) {
            recognition.stop();
        }
    });

    // Prevent default behavior for touch events
    $('#voice-search-btn').on('touchmove', function(e) {
        e.preventDefault();
    });

    // Search sidebar functions
    function openSearchSidebar() {
        $('#search-sidebar').addClass('active');
        $('#search-input').focus();
    }

    function closeSearchSidebar() {
        $('#search-sidebar').removeClass('active');
        $('#search-input').val('');
        $('#search-suggestions').empty();
        $('#search-results').empty();
    }

    // Toggle search sidebar (desktop and mobile)
    $('#search-toggle, #search-toggle-mobile').click(function(e) {
        e.preventDefault();
        openSearchSidebar();
    });

    $('#search-sidebar-close').click(function() {
        closeSearchSidebar();
    });

    // Search history management
    const MAX_HISTORY_ITEMS = 5;
    let searchHistory = JSON.parse(localStorage.getItem('searchHistory') || '[]');

    function updateSearchHistory(query) {
        if (!query.trim()) return;
        
        // Remove if already exists
        searchHistory = searchHistory.filter(item => item !== query);
        
        // Add to beginning
        searchHistory.unshift(query);
        
        // Keep only last 5 items
        if (searchHistory.length > MAX_HISTORY_ITEMS) {
            searchHistory = searchHistory.slice(0, MAX_HISTORY_ITEMS);
        }
        
        // Save to localStorage
        localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
        
        // Update UI
        renderSearchHistory();
    }

    function renderSearchHistory() {
        const historyList = $('#search-history-list');
        historyList.empty();
        
        searchHistory.forEach(query => {
            const historyItem = $(`
                <div class="history-item">
                    <span class="search-text">${query}</span>
                    <span class="remove-history">×</span>
                </div>
            `);
            
            historyItem.find('.search-text').click(function() {
                $('#search-input').val(query);
                $('#search-input').trigger('input');
            });
            
            historyItem.find('.remove-history').click(function(e) {
                e.stopPropagation();
                searchHistory = searchHistory.filter(item => item !== query);
                localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
                renderSearchHistory();
            });
            
            historyList.append(historyItem);
        });
    }

    // Sort functionality
    function sortSearchResults(results, sortBy) {
        return results.sort((a, b) => {
            switch (sortBy) {
                case 'price_asc':
                    return parseFloat(a.price) - parseFloat(b.price);
                case 'price_desc':
                    return parseFloat(b.price) - parseFloat(a.price);
                case 'name_asc':
                    return a.name.localeCompare(b.name);
                case 'name_desc':
                    return b.name.localeCompare(a.name);
                default:
                    return 0; // Keep original order for relevance
            }
        });
    }

    // Autocomplete and search with debounce
    let debounceTimer;
    $('#search-input').on('input', function() {
        clearTimeout(debounceTimer);
        const query = $(this).val().trim();
        const searchResults = $('#search-results');
        const searchSuggestions = $('#search-suggestions');

        if (query.length < 2) {
            searchSuggestions.empty();
            searchResults.empty();
            return;
        }

        debounceTimer = setTimeout(() => {
            // Fetch autocomplete suggestions
            $.ajax({
                url: '/products/autocomplete',
                method: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function(response) {
                    searchSuggestions.empty();
                    const suggestions = response.suggestions || [];
                    if (suggestions.length > 0) {
                        suggestions.forEach(suggestion => {
                            searchSuggestions.append(`<div class="suggestion-item">${suggestion}</div>`);
                        });
                    } else {
                        searchSuggestions.append('<div class="suggestion-item">No suggestions found</div>');
                    }
                },
                error: function(xhr) {
                    console.error('Autocomplete error: Status=' + xhr.status + ', Response=' + xhr.responseText);
                    searchSuggestions.html('<div class="suggestion-item">Error fetching suggestions</div>');
                }
            });

            // Fetch search results
            $.ajax({
                url: '/products/search',
                method: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function(response) {
                    searchResults.empty();
                    console.log('Search response:', response);
                    if (response.products && response.products.length > 0) {
                        // Sort results
                        const sortBy = $('#search-sort').val();
                        const sortedProducts = sortSearchResults(response.products, sortBy);
                        
                        sortedProducts.forEach(product => {
                            console.log('Fetching form for product:', product);
                            // Fetch the form via AJAX
                            $.ajax({
                                url: '/products/cart-form',
                                method: 'GET',
                                data: { product_id: product.id },
                                dataType: 'html',
                                success: function(formHtml) {
                                    const resultItem = `
                                        <div class="search-result">
                                            <div class="search-result-image">
                                                ${product.image ? 
                                                    `<a href="/products/view/${product.id}" class="product-image-link">
                                                        <img src="/img/${product.image}" alt="${product.name}">
                                                    </a>` 
                                                    : `<a href="/products/view/${product.id}" class="product-image-link">
                                                        <img src="/img/sample.jpg" alt="${product.name}" class="product-image blurred">
                                                        <div class="no-image-overlay">No image available</div>
                                                    </a>`}
                                            </div>
                                            <div class="search-result-info">
                                                <h5>${product.name}</h5>
                                                <div class="price">$${parseFloat(product.price).toFixed(2)}</div>
                                                ${formHtml}
                                            </div>
                                        </div>`;
                                    searchResults.append(resultItem);
                                },
                                error: function(xhr) {
                                    console.error('Form fetch error for product ID ' + product.id + ': Status=' + xhr.status + ', Response=' + xhr.responseText);
                                    const resultItem = `
                                        <div class="search-result">
                                            <div class="search-result-image">
                                                ${product.image ? 
                                                    `<a href="/products/view/${product.id}" class="product-image-link">
                                                        <img src="/img/${product.image}" alt="${product.name}">
                                                    </a>` 
                                                    : `<a href="/products/view/${product.id}" class="product-image-link">
                                                        <img src="/img/sample.jpg" alt="${product.name}" class="product-image blurred">
                                                        <div class="no-image-overlay">No image available</div>
                                                    </a>`}
                                            </div>
                                            <div class="search-result-info">
                                                <h5>${product.name}</h5>
                                                <div class="price">$${parseFloat(product.price).toFixed(2)}</div>
                                                <p>Unable to add to cart at this time (Error ${xhr.status}).</p>
                                            </div>
                                        </div>`;
                                    searchResults.append(resultItem);
                                }
                            });
                        });
                        openSearchSidebar();
                        // Add to search history
                        updateSearchHistory(query);
                    } else {
                        searchResults.html('<p>No products found.</p>');
                        openSearchSidebar();
                    }
                },
                error: function(xhr) {
                    console.error('Search error: Status=' + xhr.status + ', Response=' + xhr.responseText);
                    searchResults.html('<p>Error fetching results. Please try again.</p>');
                    openSearchSidebar();
                }
            });
        }, 300);
    });

    // Handle suggestion click
    $(document).on('click', '.suggestion-item', function() {
        $('#search-input').val($(this).text());
        $('#search-input').trigger('input');
    });

    // Handle sort change
    $('#search-sort').change(function() {
        const query = $('#search-input').val().trim();
        if (query.length >= 2) {
            $('#search-input').trigger('input');
        }
    });

    // Initialize search history
    renderSearchHistory();
});
