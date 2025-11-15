<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events - University Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Global eventsData object must exist before any generated scripts assign to it
        window.eventsData = window.eventsData || {};
    </script>
    <style>
        .modal {
            display: none;
        }
        .modal.active {
            display: flex;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-slate-800 leading-relaxed">
    <header class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-12 shadow-lg mb-8">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-center tracking-tight text-slate-50">University Events Management</h1>
        <p class="text-center mt-2 text-lg text-slate-100/90">View and discover upcoming events and announcements</p>
    </header>

    <nav class="bg-gray-800 mb-8 rounded-lg shadow-md">
        <div class="max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-between">
            <div class="flex flex-wrap">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="post_event.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Post New Event</a>
                    <a href="manage_admins.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Manage Admins</a>
                <?php endif; ?>
                <a href="upcoming_events.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300 active:bg-indigo-500">View Upcoming Events</a>
            </div>
            <div>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="logout.php" class="block text-gray-100 hover:bg-red-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block text-gray-100 hover:bg-green-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-12">
        <h2 class="text-3xl font-bold mb-8 text-gray-800">Upcoming Events</h2>

        <?php
        // Include database configuration
        include '../config/db.php';

        // Get current date and time
        $current_date = date('Y-m-d H:i:s');

        // Query to fetch only future events, ordered by date
        $sql = "SELECT event_id, event_title, event_date, location, details 
                FROM events 
                WHERE event_date > ? 
                ORDER BY event_date ASC";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $current_date);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8'>";
                    
                    // Collect all events first
                    $events = [];
                    while ($row = $result->fetch_assoc()) {
                        $events[] = $row;
                    }
                    
                    // Display event cards
                    foreach ($events as $row) {
                        // Format the date for better display
                        $event_date = new DateTime($row['event_date']);
                        $formatted_date = $event_date->format('F j, Y \a\t g:i A');
                        $event_id = $row['event_id'];
                        
                        echo "<div class='bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border-l-4 border-indigo-500 p-6 cursor-pointer' role='button' tabindex='0' onclick='openModal(" . $event_id . ")' onkeydown=\"if(event.key==='Enter'||event.key===' ') openModal(" . $event_id . ")\">";
                        echo "<h3 class='text-xl font-semibold text-indigo-700 mb-3'>" . htmlspecialchars($row['event_title']) . "</h3>";
                        echo "<div class='bg-indigo-50 inline-block px-3 py-1 rounded text-sm text-indigo-700 font-medium mb-3'>" . $formatted_date . "</div>";
                        echo "<div class='text-purple-700 font-medium mb-3 flex items-center'>";
                        echo "<span class='mr-2 text-lg'>📍</span>" . htmlspecialchars($row['location']);
                        echo "</div>";
                        echo "</div>";
                    }
                    
                    echo "</div>";
                    
                    // Populate JavaScript object with event data
                    echo "<script>\n";
                    foreach ($events as $row) {
                        $event_date = new DateTime($row['event_date']);
                        $formatted_date = $event_date->format('F j, Y \a\t g:i A');
                        $event_id = $row['event_id'];
                        echo "eventsData[" . $event_id . "] = {
                            title: " . json_encode($row['event_title']) . ",
                            date: " . json_encode($formatted_date) . ",
                            location: " . json_encode($row['location']) . ",
                            details: " . json_encode($row['details']) . "
                        };\n";
                    }
                    echo "console.log('Events loaded:', eventsData);\n";
                    echo "</script>\n";
                } else {
                    echo "<div class='bg-white rounded-lg shadow-md p-8 text-center'>";
                    echo "<p class='text-gray-500 text-lg'>No upcoming events at the moment. Check back soon!</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>";
                echo "Error fetching events: " . $stmt->error;
                echo "</div>";
            }
            
            $stmt->close();
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>";
            echo "Error preparing statement: " . $conn->error;
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>

    <!-- Modal -->
    <div id="eventModal" class="modal fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 max-w-3xl w-full mx-4 transform-gpu transition-transform duration-200 scale-95 opacity-0 modal-content">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1 pr-2">
                    <h2 id="modalTitle" class="text-2xl sm:text-3xl font-extrabold text-indigo-700 leading-tight"></h2>
                    <div class="mt-2 text-sm text-slate-500" id="modalDateWrap"><span id="modalDate"></span></div>
                </div>
                <div class="flex-shrink-0">
                    <button onclick="closeModal()" aria-label="Close" class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Location</h3>
                    <div class="text-purple-600 font-medium flex items-center gap-2">
                        <span class="text-lg">📍</span>
                        <span id="modalLocation" class="text-gray-800"></span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Details</h3>
                    <p id="modalDetails" class="text-gray-700 leading-relaxed max-h-60 overflow-auto"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Use the global eventsData object populated by server-generated script
        var eventsData = window.eventsData || {};
        let modalTimeout;
        let currentEventId = null;

        function openModal(eventId) {
            clearTimeout(modalTimeout);
            currentEventId = eventId;
            console.log('Opening modal for event ID:', eventId);
            console.log('Event data:', eventsData[eventId]);
            
            if (eventsData[eventId]) {
                const event = eventsData[eventId];
                document.getElementById('modalTitle').textContent = event.title;
                document.getElementById('modalDate').textContent = event.date;
                document.getElementById('modalLocation').textContent = event.location;
                document.getElementById('modalDetails').textContent = event.details;
                const modal = document.getElementById('eventModal');
                const content = modal.querySelector('.modal-content');
                modal.classList.add('active');
                // animate content in
                content.classList.remove('scale-95','opacity-0');
                content.classList.add('scale-100','opacity-100');
            } else {
                console.error('No data found for event ID:', eventId);
            }
        }

        function closeModal() {
            const modal = document.getElementById('eventModal');
            const content = modal.querySelector('.modal-content');
            // animate content out then hide overlay
            content.classList.remove('scale-100','opacity-100');
            content.classList.add('scale-95','opacity-0');
            modalTimeout = setTimeout(() => {
                modal.classList.remove('active');
            }, 180);
        }

        // Close modal when clicking outside of it
        document.getElementById('eventModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });

        // Keep modal open when hovering over it
        document.querySelector('.modal > div').addEventListener('mouseenter', function() {
            clearTimeout(modalTimeout);
        });

        document.querySelector('.modal > div').addEventListener('mouseleave', function() {
            closeModal();
        });
    </script>
</body>
</html>
