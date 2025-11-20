<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Events - University Events</title>    
    <link rel="icon" href="../images.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal { display: none; }
        .modal.active { display: flex; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-100 min-h-screen relative overflow-x-hidden font-sans text-slate-800 leading-relaxed" style="background-image: url('../events_bg.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <!-- <header class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white py-14 shadow-2xl mb-10 rounded-b-3xl relative z-10">
        <h1 class="text-5xl font-extrabold text-center drop-shadow-lg tracking-tight">University Events Management</h1>
        <p class="text-center mt-3 text-xl opacity-95 font-medium">View and discover past events and announcements</p>
    </header> -->

    <header class="bg-white/20 backdrop-blur-2xl shadow-2xl rounded-b-3xl relative z-10 border-b-2 border-white/30 py-10 flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-fuchsia-600 to-pink-500 drop-shadow-2xl tracking-tight text-center mb-2" style="letter-spacing: 0.04em;">
            <span class="text-white">University Events Management</span>
        </h1>
        <p class="text-lg md:text-xl font-semibold text-white drop-shadow-sm text-center mt-2 tracking-wide">View and discover past events and announcements</p>
    </header>
    <?php include '../partials/navbar.php'; ?>
    <div class="max-w-4xl mx-auto px-4 pb-16">
        <h2 class="text-4xl font-extrabold mb-10 text-white text-center tracking-tight flex items-center justify-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-8 w-8 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>Past Events</h2>
        <?php
        include '../config/db.php';
        $current_date = date('Y-m-d H:i:s');
        $sql_past = "SELECT event_id, event_title, event_date, location, details FROM events WHERE event_date < ? ORDER BY event_date DESC";
        $stmt_past = $conn->prepare($sql_past);
        if ($stmt_past) {
            $stmt_past->bind_param("s", $current_date);
            if ($stmt_past->execute()) {
                $result_past = $stmt_past->get_result();
                if ($result_past->num_rows > 0) {
                    echo "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10'>";
                    $events_past = [];
                    while ($row = $result_past->fetch_assoc()) {
                        $events_past[] = $row;
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

                    foreach ($events_past as $row) {
                        $event_date = new DateTime($row['event_date']);
                        $formatted_date = $event_date->format('F j, Y \a\t g:i A');
                        $bg_url = getEventBgImage($row['event_title']);
                        echo "<div class='bg-white/95 rounded-3xl shadow-xl border-l-8 border-indigo-200 p-7 group relative overflow-hidden' style=\"background-image:url('$bg_url');background-size:cover;background-position:center;\">";
                        echo "<div class='absolute inset-0 bg-white/80 group-hover:bg-white/60 transition-all duration-300'></div>";
                        echo "<div class='relative z-10'>";
                        echo "<h3 class='text-2xl font-extrabold text-indigo-400 mb-4 flex items-center gap-2 group-hover:text-pink-500 transition-colors duration-300'><svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6 text-indigo-200' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>" . htmlspecialchars($row['event_title']) . "</h3>";
                        echo "<div class='bg-indigo-50 inline-block px-4 py-1 rounded-full text-sm text-indigo-400 font-semibold mb-4 shadow-sm'>" . $formatted_date . "</div>";
                        echo "<div class='text-purple-700 font-medium mb-2 flex items-center gap-2'><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-purple-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 2c-2.21 0-4 1.79-4 4 0 2.21 4 7 4 7s4-4.79 4-7c0-2.21-1.79-4-4-4z'/></svg>" . htmlspecialchars($row['location']) . "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='bg-white/95 rounded-3xl shadow-xl p-12 text-center text-gray-400 text-lg font-medium'>No past events found.</div>";
                }
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>";
                echo "Error fetching past events: " . $stmt_past->error;
                echo "</div>";
            }
            $stmt_past->close();
        } else {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>";
            echo "Error preparing statement for past events: " . $conn->error;
            echo "</div>";
        }
        $conn->close();
        ?>
    </div>
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-pink-200 rounded-full opacity-30 blur-2xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-indigo-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-purple-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
    </div>
</body>
</html>
