// Admin Dashboard - Sidebar Functionality

$(document).ready(function() {
    // Initialize MetisMenu
    $('#metismenu').metisMenu({
        toggle: true,
        preventDefault: false,
        activeClass: 'active',
        collapseClass: 'collapse',
        collapseInClass: 'in',
        collapsedClass: 'collapsed'
    });

    // Sidebar toggle functionality - using document delegation for better reliability
    $(document).on('click', '#toggleBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var sidebar = $('#sidebar');
        var mainContent = $('#mainContent');
        var isMobile = $(window).width() <= 768;

        if (isMobile) {
            sidebar.toggleClass('show');
            console.log('Mobile toggle - Show:', sidebar.hasClass('show'));
        } else {
            var isCollapsed = sidebar.hasClass('collapsed');
            
            if (isCollapsed) {
                // Expanding
                sidebar.removeClass('collapsed');
                mainContent.removeClass('expanded');
                console.log('Desktop: Expanding sidebar');
            } else {
                // Collapsing
                sidebar.addClass('collapsed');
                mainContent.addClass('expanded');
                console.log('Desktop: Collapsing sidebar');
            }
        }
        
        return false; // Prevent any default action
    });

    // Handle window resize
    $(window).resize(function() {
        var sidebar = $('#sidebar');
        var mainContent = $('#mainContent');
        var isMobile = $(window).width() <= 768;

        if (isMobile) {
            sidebar.removeClass('collapsed');
            mainContent.removeClass('expanded');
        } else {
            sidebar.removeClass('show');
        }
    });

    // Close sidebar on mobile when clicking outside
    $(document).on('click', function(e) {
        var sidebar = $('#sidebar');
        var toggleBtn = $('#toggleBtn');
        var isMobile = $(window).width() <= 768;
        
        if (isMobile && sidebar.hasClass('show')) {
            if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 &&
                !toggleBtn.is(e.target) && toggleBtn.has(e.target).length === 0) {
                sidebar.removeClass('show');
            }
        }
    });

    // Handle menu item clicks
    $('#metismenu a').on('click', function(e) {
        var $this = $(this);
        var href = $this.attr('href');

        // If it's not a submenu toggle and has a real href (Laravel routes or external links)
        if (href && href !== '#' && !$this.next('ul').length) {
            // Allow normal navigation to occur for real routes
            return true;
        }
        
        // If it's a hash link or submenu toggle, prevent default
        if (href === '#' || $this.next('ul').length) {
            e.preventDefault();
            
            // Remove active class from all items
            $('#metismenu li').removeClass('active');
            
            // Add active class to current item
            $this.closest('li').addClass('active');
            
            // Update page title
            var itemText = $this.find('span').text() || $this.text();
            $('.header h4').text(itemText);
            
            console.log('Navigation clicked:', itemText, href);
        }
    });

    // Handle submenu item clicks
    $('#metismenu ul li a').on('click', function(e) {
        var $this = $(this);
        var href = $this.attr('href');
        
        // If it's a real route (not # and not a submenu toggle), allow navigation
        if (href && href !== '#') {
            // Before navigating, set the active states
            setActiveMenuForSubmenu($this);
            // Allow normal navigation
            return true;
        }
        
        e.preventDefault();
        setActiveMenuForSubmenu($this);
        
        // Update page title
        var itemText = $this.text();
        $('.header h4').text(itemText);
        
        console.log('Submenu navigation clicked:', itemText, href);
    });
    
    // Function to set active menu for submenu items
    function setActiveMenuForSubmenu($submenuLink) {
        // Remove all active classes first
        $('#metismenu li').removeClass('mm-active');
        $('#metismenu ul').removeClass('mm-show');
        $('#metismenu a').attr('aria-expanded', 'false');
        
        // Mark the submenu item as active
        var submenuLi = $submenuLink.closest('li');
        submenuLi.addClass('mm-active');
        
        // Mark the parent menu as active and expanded
        var parentUl = $submenuLink.closest('ul');
        if (parentUl.length && !parentUl.hasClass('metismenu')) {
            var parentLi = parentUl.parent('li');
            if (parentLi.length) {
                parentLi.addClass('mm-active');
                parentLi.find('> a').attr('aria-expanded', 'true');
                parentUl.addClass('mm-show');
                
                console.log('Set active submenu:', $submenuLink.text(), 'Parent:', parentLi.find('> a span').text());
            }
        }
    }

    // Add smooth transitions
    $('.sidebar, .main-content').css('transition', 'all 0.3s ease');

    // Initialize tooltips for collapsed sidebar
    $('[data-toggle="tooltip"]').tooltip();

    // Custom animations for stats cards
    $('.stats-card, .quick-action-card').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Auto-refresh dashboard data (simulated)
    setInterval(function() {
        // Simulate real-time updates
        console.log('Dashboard data refreshed');
    }, 30000); // Refresh every 30 seconds
    
    // Fallback vanilla JS handler for toggle button
    var toggleBtn = document.getElementById('toggleBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            console.log('Toggle button clicked (vanilla JS fallback)');
        });
    }
    
    // Enhanced active menu item detection
    function setActiveMenuItem() {
        var currentPath = window.location.pathname;
        
        // Don't override server-side active states - just enhance them
        console.log('Current path:', currentPath);
        
        // Find any server-side active items and ensure their JavaScript state matches
        $('#metismenu li.mm-active').each(function() {
            var $li = $(this);
            var $parentUl = $li.closest('ul');
            
            // If this is a parent menu with active submenu
            if ($parentUl.hasClass('metismenu') && $li.find('ul.mm-show').length) {
                $li.find('> a').attr('aria-expanded', 'true');
                console.log('Synchronized server-side active parent:', $li.find('> a span').text());
            }
            
            // If this is an active submenu item
            if (!$parentUl.hasClass('metismenu')) {
                var $parentLi = $parentUl.parent('li');
                if ($parentLi.length) {
                    $parentLi.addClass('mm-active');
                    $parentLi.find('> a').attr('aria-expanded', 'true');
                    $parentUl.addClass('mm-show');
                    console.log('Synchronized server-side active submenu:', $li.find('a').text());
                }
            }
        });
        
        // Backup JavaScript-based detection for any missed items
        $('#metismenu a').each(function() {
            var href = $(this).attr('href');
            var $this = $(this);
            var $currentLi = $this.closest('li');
            
            // Skip if already marked as active by server
            if ($currentLi.hasClass('mm-active')) {
                return;
            }
            
            // Check if current path matches this href
            if (href && href !== '#' && currentPath === href) {
                var parentUl = $this.closest('ul');
                
                // Mark current item as active
                $currentLi.addClass('mm-active');
                
                // If it's in a submenu, also activate and expand the parent
                if (parentUl.length && !parentUl.hasClass('metismenu')) {
                    var parentLi = parentUl.parent('li');
                    if (parentLi.length) {
                        parentLi.addClass('mm-active');
                        parentLi.find('> a').attr('aria-expanded', 'true');
                        parentUl.addClass('mm-show');
                        
                        console.log('JS-activated submenu item:', currentPath, 'Parent:', parentLi.find('> a span').text());
                    }
                } else {
                    console.log('JS-activated main menu item:', currentPath);
                }
                
                return false; // Break the loop once found
            }
        });
    }
    
    // Call the function to set active menu on page load
    setActiveMenuItem();
});