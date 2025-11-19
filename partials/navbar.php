<?php
// Usage: include '../partials/navbar.php';
?>
<style>
@media (max-width: 767px) {
    #nav-links {
        background: #fff !important;
        box-shadow: 0 10px 40px 0 rgba(80, 0, 120, 0.10), 0 2px 4px 0 rgba(80, 0, 120, 0.10);
        border-top-right-radius: 1.5rem;
        border-bottom-right-radius: 1.5rem;
        z-index: 10001;
        opacity: 1 !important;
    }
}
</style>
<nav class="bg-white/90 backdrop-blur mb-10 rounded-2xl shadow-lg border border-purple-100 z-[10010] relative">
    <div class="max-w-4xl mx-auto px-4 flex items-center justify-between gap-4 flex-nowrap relative">
        <!-- Hamburger Button for Mobile -->
        <button id="nav-toggle" class="md:hidden flex items-center px-3 py-2 border rounded text-indigo-700 border-indigo-300 hover:bg-indigo-100 focus:outline-none absolute left-0 top-1/2 -translate-y-1/2 z-20" aria-label="Open navigation menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <div id="nav-links" class="flex gap-0 flex-col fixed top-0 left-0 w-3/4 max-w-xs h-auto bg-white shadow-2xl rounded-r-3xl p-8 z-[10020] transition-transform duration-300 -translate-x-full md:static md:w-auto md:max-w-none md:h-full md:bg-transparent md:shadow-none md:rounded-none md:p-0 md:z-auto md:translate-x-0 md:flex-row md:items-center md:gap-0">
         <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                <a href="post_event.php" class="flex items-center gap-2 text-gray-700 hover:bg-indigo-100 px-6 py-3 text-lg font-medium transition-colors duration-300<?php if(basename($_SERVER['PHP_SELF'])=='post_event.php'){echo ' text-indigo-700 bg-indigo-100 font-semibold';} ?>"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-indigo-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4'/></svg>Post New Event</a>
            <?php endif; ?>    
        <a href="upcoming_events.php" class="flex items-center gap-2 text-gray-700 hover:bg-indigo-100 px-6 py-3 text-lg font-medium transition-colors duration-300<?php if(basename($_SERVER['PHP_SELF'])=='upcoming_events.php'){echo ' text-indigo-700 bg-indigo-100 font-semibold';} ?>"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-pink-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>Upcoming Events</a>
            <a href="past_events.php" class="flex items-center gap-2 text-gray-700 hover:bg-indigo-100 px-6 py-3 text-lg font-medium transition-colors duration-300<?php if(basename($_SERVER['PHP_SELF'])=='past_events.php'){echo ' text-indigo-700 bg-indigo-100 font-semibold';} ?>"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>Past Events</a>
            <?php if (
                isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true &&
                isset($_SESSION['admin_username']) && $_SESSION['admin_username'] === 'masteradmin'
            ): ?>
                <a href="manage_admins.php" class="flex items-center gap-2 text-gray-700 hover:bg-purple-100 px-6 py-3 text-lg font-medium transition-colors z-20 duration-300<?php if(basename($_SERVER['PHP_SELF'])=='manage_admins.php'){echo ' text-indigo-700 bg-indigo-100 font-semibold';} ?>"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-indigo-700' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>Manage Admins</a>
               <?php endif; ?> 
                 <?php if (
                isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="logout.php" class="flex items-center gap-2 text-white bg-red-500 hover:bg-red-600 px-6 py-3 text-lg font-semibold rounded-2xl transition-colors duration-300 mt-4 md:mt-0"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1'/></svg>Logout</a>
                <?php else: ?>
                <a href="login.php" class="flex items-center gap-2 text-gray-700 hover:bg-indigo-100 px-6 py-3 text-lg font-medium transition-colors duration-300<?php if(basename($_SERVER['PHP_SELF'])=='post_event.php'){echo ' text-indigo-700 bg-indigo-100 font-semibold';} ?>"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-indigo-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4'/></svg>Post New Event</a> 
            <?php endif; ?>
        </div>
    </div>
    <script>
    // Hamburger menu toggle logic
    const navToggle = document.getElementById('nav-toggle');
    const navLinks = document.getElementById('nav-links');
    navToggle.addEventListener('click', function() {
        if (navLinks.classList.contains('-translate-x-full')) {
            navLinks.classList.remove('-translate-x-full');
            navLinks.classList.add('translate-x-0');
        } else {
            navLinks.classList.add('-translate-x-full');
            navLinks.classList.remove('translate-x-0');
        }
    });
    // Close nav on link click (mobile)
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                navLinks.classList.add('-translate-x-full');
                navLinks.classList.remove('translate-x-0');
            }
        });
    });
    // Hide nav if resizing to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            navLinks.classList.remove('-translate-x-full');
            navLinks.classList.remove('translate-x-0');
        } else {
            navLinks.classList.add('-translate-x-full');
            navLinks.classList.remove('translate-x-0');
        }
    });
    </script>
</nav>
