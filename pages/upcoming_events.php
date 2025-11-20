<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events - University Events</title>
    <link rel="icon" href="../images.png" type="image/png">
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
        /* Modal background blur effect */
        #eventModal.bg-blur {
            position: fixed;
        }
        #eventModal.bg-blur::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            background: inherit;
            filter: blur(12px) brightness(0.7);
            pointer-events: none;
        }
        #eventModal.bg-blur .modal-content {
            position: relative;
            z-index: 1;
        }
        body.modal-open header,
        body.modal-open nav {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-100 min-h-screen relative overflow-x-hidden font-sans text-slate-800 leading-relaxed" style="background-image: url('../events_bg.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <!-- <header class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white py-14 shadow-2xl mb-10 rounded-b-3xl relative z-10">
        <h1 class="text-5xl font-extrabold text-center drop-shadow-lg tracking-tight">University Events Management</h1>
        <p class="text-center mt-3 text-xl opacity-95 font-medium">View and discover upcoming events and announcements</p>
    </header> -->

    <header class="bg-white/20 backdrop-blur-2xl shadow-2xl rounded-b-3xl relative z-10 border-b-2 border-white/30 py-10 flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-fuchsia-600 to-pink-500 drop-shadow-2xl tracking-tight text-center mb-2" style="letter-spacing: 0.04em;">
            <span class="text-white">University Events Management</span>
        </h1>
        <p class="text-lg md:text-xl font-semibold text-white drop-shadow-sm text-center mt-2 tracking-wide">View and discover upcoming events and announcements</p>
    </header>

    <?php include '../partials/navbar.php'; ?>

    <div class="max-w-4xl mx-auto px-4 pb-16">
        <h2 class="text-4xl font-extrabold mb-10 text-white text-center tracking-tight flex items-center justify-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-8 w-8 text-pink-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>Upcoming Events</h2>
        <!-- Decorative floating shapes -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-10 left-10 w-32 h-32 bg-pink-200 rounded-full opacity-30 blur-2xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-40 h-40 bg-indigo-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-purple-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
        </div>
</script>

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
                    echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10'>";
                    
                    // Collect all events first
                    $events = [];
                    while ($row = $result->fetch_assoc()) {
                        $events[] = $row;
                    }
                    

                    // Helper function to get background image URL based on event title
                    function getEventBgImage($title) {
                        $title = strtolower($title);
                        if (strpos($title, 'singing') !== false || strpos($title, 'music') !== false) {
                            return 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80'; // singing/music
                        } elseif (strpos($title, 'dance') !== false) {
                            return '../dance.jpeg';
                        } elseif (strpos($title, 'sports') !== false) {
                            return 'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=600&q=80';
                        } elseif (strpos($title, 'tech') !== false || strpos($title, 'robot') !== false) {
                            return 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80';
                        } elseif (strpos($title, 'art') !== false) {
                            return 'https://images.unsplash.com/photo-1465101178521-c1a9136a3b99?auto=format&fit=crop&w=600&q=80';
                        } elseif (strpos($title, 'quiz') !== false) {
                            return 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=600&q=80';
                        }else if (strpos($title, 'campus recruitment') !== false || strpos($title, 'hiring') !== false || strpos($title, 'drive') !== false) {
                            return '../drive.jpeg';
                         }elseif (strpos($title, 'test') !== false || strpos($title, 'assignments') !== false || strpos($title, 'exams') !== false) {
                            return '../test.jpeg';
                        }
                        // Default image
                        return 'https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=600&q=80';
                    }

                    // Display event cards
                    foreach ($events as $row) {
                        $event_date = new DateTime($row['event_date']);
                        $formatted_date = $event_date->format('F j, Y \a\t g:i A');
                        $event_id = $row['event_id'];
                        $bg_url = getEventBgImage($row['event_title']);
                        echo "<div class='bg-white/95 rounded-3xl shadow-xl hover:shadow-pink-200 transition-all duration-300 border-l-8 border-indigo-400 p-7 cursor-pointer group relative overflow-hidden flex flex-col justify-end' role='button' tabindex='0' onclick='openModal(" . $event_id . ")' onkeydown=\"if(event.key==='Enter'||event.key===' ') openModal(" . $event_id . ")\" style=\"background-image:url('$bg_url');background-size:cover;background-position:center;\">";
                        echo "<div class='absolute inset-0 bg-white/80 group-hover:bg-white/60 transition-all duration-300'></div>";
                        echo "<div class='relative z-10'>";
                        echo "<h3 class='text-2xl font-extrabold text-indigo-700 mb-4 flex items-center gap-2 group-hover:text-pink-500 transition-colors duration-300'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>" . htmlspecialchars($row['event_title']) . "</h3>";
                        echo "<div class='bg-indigo-50 inline-block px-4 py-1 rounded-full text-sm text-indigo-700 font-semibold mb-4 shadow-sm'>" . $formatted_date . "</div>";
                        echo "<div class='text-purple-700 font-medium mb-2 flex items-center gap-2'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-purple-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4 0 2.21 4 7 4 7s4-4.79 4-7c0-2.21-1.79-4-4-4z'/></svg>" . htmlspecialchars($row['location']) . "</div>";
                        // Show Edit/Delete buttons for admins only
                        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
                            echo "<div class='absolute left-0 right-0 bottom-2 flex gap-2  py-3  backdrop-blur-sm border-t border-indigo-100 rounded-b-3xl opacity-0 translate-y-0 group-hover:opacity-100 group-hover:translate-y-10 transition-all duration-300 z-20 pointer-events-auto'>";
                            echo "<a href='edit_event.php?id=" . $event_id . "' class='inline-flex items-center gap-2 px-4 py-1.5 rounded-lg font-semibold text-white bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300'>"
                                . "<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5h2m-1 0v14m-7-7h14'/></svg>Edit</a>";
                            echo "<a href='delete_event.php?id=" . $event_id . "' onclick=\"return confirm('Are you sure you want to delete this event?');\" class='inline-flex items-center gap-2 px-4 py-1.5 rounded-lg font-semibold text-white bg-gradient-to-r from-rose-500 via-pink-500 to-red-600 hover:from-rose-600 hover:to-red-700 shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-300'>"
                                . "<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/></svg>Delete</a>";
                            echo "</div>";
                        }
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
                    echo "<div class='bg-white/95 rounded-3xl shadow-xl p-12 text-center text-gray-500 text-lg font-medium'>No upcoming events at the moment. Check back soon!</div>";
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
    <div id="eventModal" class="modal fixed inset-0 items-center justify-center z-50">
        <!-- Blurred background image is set on #eventModal -->
        <div class="absolute inset-0 bg-black/40 z-0 pointer-events-none"></div>
        <div class="modal-content relative z-10 max-w-2xl w-full mx-4 p-0">
            <div class="bg-white/30 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/40 p-8 sm:p-12 flex flex-col gap-8">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div class="flex-1 pr-2">
                        <h2 id="modalTitle" class="text-4xl sm:text-5xl font-black text-white leading-tight flex items-center gap-3 drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)] font-sans"><svg xmlns='http://www.w3.org/2000/svg' class='h-10 w-10 text-pink-300' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg><span id="modalTitleText"></span></h2>
                        <div class="mt-3 text-lg text-white font-bold flex items-center gap-2 font-sans drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]" id="modalDateWrap"><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-indigo-200' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg><span id="modalDate"></span></div>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="closeModal()" aria-label="Close" class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white/80 border-2 border-gray-200 shadow-lg text-gray-500 hover:bg-red-500 hover:text-white hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-300 transition-all duration-200 group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="flex-1 pr-2">
                        <h3 class="text-lg font-extrabold text-white mb-3 flex items-center gap-2 uppercase tracking-wide font-sans drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]"><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-purple-200' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4 0 2.21 4 7 4 7s4-4.79 4-7c0-2.21-1.79-4-4-4z'/></svg>Location</h3>
                        <div class="text-white font-bold flex items-center gap-2 bg-purple-900/40 rounded-xl px-5 py-3 shadow-sm text-base font-sans drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]">
                            <span id="modalLocation" class="text-white font-semibold"></span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-white mb-3 flex items-center gap-2 uppercase tracking-wide font-sans drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]"><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-indigo-200' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>Details</h3>
                        <p id="modalDetails" class="text-white font-medium leading-relaxed max-h-60 overflow-auto bg-indigo-900/40 rounded-xl px-5 py-3 shadow-sm text-base font-sans drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]"></p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        // Use the global eventsData object populated by server-generated script
        var eventsData = window.eventsData || {};
        let modalTimeout;
        let currentEventId = null;


        // Helper to get background image URL based on event title (same as PHP)
        function getEventBgImageJS(title) {
            title = title.toLowerCase();
            if (title.includes('singing') || title.includes('music')) {
                return 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80';
            } else if (title.includes('dance')) {
                return '../dance.jpeg';
            } else if (title.includes('sports')) {
                return 'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=600&q=80';
            } else if (title.includes('tech') || title.includes('robot')) {
                return 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80';
            } else if (title.includes('art')) {
                return 'https://images.unsplash.com/photo-1465101178521-c1a9136a3b99?auto=format&fit=crop&w=600&q=80';
            } else if (title.includes('quiz')) {
                return 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=600&q=80';
            } else if (title.includes('campus recruitment') || title.includes('hiring') || title.includes('drive')) {
                return '../drive.jpeg';
            }else if (title.includes('test') || title.includes('assignments') || title.includes('exams')) {
                            return '../test.jpeg';
            }
            return 'https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=600&q=80';
        }

        function openModal(eventId) {
            clearTimeout(modalTimeout);
            currentEventId = eventId;
            if (eventsData[eventId]) {
                const event = eventsData[eventId];
                document.getElementById('modalTitleText').textContent = event.title;
                document.getElementById('modalDate').textContent = event.date;
                document.getElementById('modalLocation').textContent = event.location;
                document.getElementById('modalDetails').textContent = event.details;
                const modal = document.getElementById('eventModal');
                const content = modal.querySelector('.modal-content');
                // Set modal background image based on event title
                const bgUrl = getEventBgImageJS(event.title);
                modal.style.backgroundImage = `url('${bgUrl}')`;
                modal.style.backgroundSize = 'cover';
                modal.style.backgroundPosition = 'center';
                modal.classList.add('active', 'bg-blur');
                // animate content in
                content.classList.remove('scale-95','opacity-0');
                content.classList.add('scale-100','opacity-100');
                // Hide header and nav
                document.body.classList.add('modal-open');
            } else {
                console.error('No data found for event ID:', eventId);
            }
        }

        function closeModal() {
            const modal = document.getElementById('eventModal');
            const content = modal.querySelector('.modal-content');
            // animate content out then hide overlay
            content.classList.remove('scale-100','opacity-100');
            content.classList.add('scale-95','opacity-80');
            modalTimeout = setTimeout(() => {
                modal.classList.remove('active', 'bg-blur');
                // Remove modal background image
                modal.style.backgroundImage = '';
                // Show header and nav again
                document.body.classList.remove('modal-open');
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
