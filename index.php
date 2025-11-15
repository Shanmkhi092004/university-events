<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - University Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <header class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-12 shadow-lg mb-8">
        <h1 class="text-4xl font-bold text-center">University Events Management</h1>
        <p class="text-center mt-2 text-lg opacity-95">Your central hub for university events and announcements</p>
    </header>

    <nav class="bg-gray-800 mb-8 rounded-lg shadow-md">
        <div class="max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-between">
            <div class="flex flex-wrap">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="pages/post_event.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Post New Event</a>
                    <a href="pages/manage_admins.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Manage Admins</a>
                <?php endif; ?>
                <a href="pages/upcoming_events.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">View Upcoming Events</a>
            </div>
            <div>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="pages/logout.php" class="block text-gray-100 hover:bg-red-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Logout</a>
                <?php else: ?>
                    <a href="pages/login.php" class="block text-gray-100 hover:bg-green-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-12">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Welcome to the University Events System</h2>
        
        <div class="bg-white p-8 rounded-lg shadow-md mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">What is this system?</h3>
            <p class="text-gray-700 leading-relaxed mb-4">The University Events Management System is a centralized platform for posting and discovering university events and announcements. Whether you're looking for upcoming seminars, career fairs, concerts, or other campus activities, you'll find everything you need here.</p>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-4 mt-6">Quick Navigation</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li><strong>Post New Event:</strong> Admin access to add new events to the system</li>
                <li><strong>View Upcoming Events:</strong> Browse all upcoming events organized by date</li>
            </ul>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-4 mt-6">Features</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>✓ Easy event posting with form validation</li>
                <li>✓ Automatic filtering of future events</li>
                <li>✓ Beautiful, responsive event display</li>
                <li>✓ Secure database operations</li>
                <li>✓ Mobile-friendly interface</li>
            </ul>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-4 mt-6">Getting Started</h3>
            <p class="text-gray-700 leading-relaxed">Use the navigation menu above to either post a new event or view upcoming events. All events are automatically sorted by date, and only future events are displayed on the upcoming events page.</p>
        </div>

        <div class="text-center text-gray-500 text-sm mt-12">
            <p>&copy; 2025 University Events Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
