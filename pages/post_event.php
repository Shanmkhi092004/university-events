
<?php
session_start();
// If not logged in as admin, redirect to login
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    $redirect = 'post_event.php';
    header('Location: login.php?redirect=' . urlencode($redirect));
    exit;
}

// --- FORM PROCESSING AND HEADER REDIRECTS MUST HAPPEN BEFORE ANY OUTPUT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../config/db.php';
    $event_title = trim($_POST['event_title']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $details = trim($_POST['details']);
    $errors = [];
    if (empty($event_title)) {
        $errors[] = "Event title is required.";
    }
    if (empty($event_date)) {
        $errors[] = "Event date is required.";
    } else {
        $event_date = str_replace('T', ' ', $event_date);
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }
    if (empty($details)) {
        $errors[] = "Details are required.";
    }
    if (empty($errors)) {
        $sql = "INSERT INTO events (event_title, event_date, location, details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $event_title, $event_date, $location, $details);
            if ($stmt->execute()) {
                $_SESSION['event_message'] = "Event posted successfully! Your event has been added to the upcoming events list.";
                $_SESSION['event_message_type'] = "success";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $_SESSION['event_message'] = "Error: " . $stmt->error;
                $_SESSION['event_message_type'] = "error";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
            $stmt->close();
        } else {
            $_SESSION['event_message'] = "Error preparing statement: " . $conn->error;
            $_SESSION['event_message_type'] = "error";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['event_message'] = implode("<br>", $errors);
        $_SESSION['event_message_type'] = "error";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
// --- END FORM PROCESSING ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Event - University Events</title>
    <link rel="icon" href="../images.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-100 min-h-screen relative overflow-x-hidden" style="background-image: url('../events_bg.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

     <header class="bg-white/20 backdrop-blur-2xl shadow-2xl rounded-b-3xl relative z-10 border-b-2 border-white/30 py-10 flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-fuchsia-600 to-pink-500 drop-shadow-2xl tracking-tight text-center mb-2 flex items-center justify-center gap-3" style="letter-spacing: 0.04em;">
            <img src="../images.png" alt="Logo" class="inline-block h-12 w-12 md:h-14 md:w-14 mr-2 align-middle drop-shadow-lg bg-white/80 rounded-full p-1" />
            <span class="text-white align-middle">University Events Management</span>
        </h1>
        <p class="text-lg md:text-xl font-semibold text-white drop-shadow-sm text-center mt-2 tracking-wide">Manage upcoming events and announcements</p>
    </header>

    <?php include '../partials/navbar.php'; ?>

    <div class="max-w-2xl mx-auto px-4 pb-16">
        <h2 class="text-4xl font-extrabold mb-10 text-white text-center tracking-tight flex items-center justify-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-8 w-8 text-indigo-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4'/></svg>Post New Event</h2>
        
        <?php
        // Include database configuration
        include '../config/db.php';

        // Initialize message variable
        $message = '';
        $message_type = '';

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $event_title = trim($_POST['event_title']);
            $event_date = trim($_POST['event_date']);
            $location = trim($_POST['location']);
            $details = trim($_POST['details']);

            // Validate input
            $errors = [];

            if (empty($event_title)) {
                $errors[] = "Event title is required.";
            }
            if (empty($event_date)) {
                $errors[] = "Event date is required.";
            } else {
                // Convert datetime-local format to MySQL format
                $event_date = str_replace('T', ' ', $event_date);
            }
            if (empty($location)) {
                $errors[] = "Location is required.";
            }
            if (empty($details)) {
                $errors[] = "Details are required.";
            }

            // If no errors, insert into database
            if (empty($errors)) {
                $sql = "INSERT INTO events (event_title, event_date, location, details) 
                        VALUES (?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("ssss", $event_title, $event_date, $location, $details);
                    
                    if ($stmt->execute()) {
                        $_SESSION['event_message'] = "Event posted successfully! Your event has been added to the upcoming events list.";
                        $_SESSION['event_message_type'] = "success";
                        // PRG: Redirect to same page (GET)
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    } else {
                        $_SESSION['event_message'] = "Error: " . $stmt->error;
                        $_SESSION['event_message_type'] = "error";
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['event_message'] = "Error preparing statement: " . $conn->error;
                    $_SESSION['event_message_type'] = "error";
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            } else {
                $_SESSION['event_message'] = implode("<br>", $errors);
                $_SESSION['event_message_type'] = "error";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }

        // Display message if exists (from session)
        if (!empty($_SESSION['event_message'])) {
            $message_type = $_SESSION['event_message_type'] ?? '';
            $message = $_SESSION['event_message'];
            if ($message_type === 'success') {
                echo "<div id='event-success-message' class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6'>" . $message . "</div>";
            } else {
                echo "<div id='event-error-message' class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>Please fix the following errors:<br>" . $message . "</div>";
            }
            unset($_SESSION['event_message'], $_SESSION['event_message_type']);
        }
        ?>
        <script>
        // Hide success and error messages after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var successMsg = document.getElementById('event-success-message');
                if (successMsg) successMsg.style.display = 'none';
                var errorMsg = document.getElementById('event-error-message');
                if (errorMsg) errorMsg.style.display = 'none';
            }, 3000);
        });
        </script>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="bg-white/20 backdrop-blur-2xl border border-white/40 shadow-2xl p-10 rounded-3xl relative overflow-hidden hover:shadow-pink-200 transition-all duration-300">
                        <script>
                        // Reset form after successful event post
                        document.addEventListener('DOMContentLoaded', function() {
                            var successMsg = document.getElementById('event-success-message');
                            if (successMsg) {
                                var form = successMsg.closest('form');
                                if (!form) form = document.querySelector('form');
                                if (form) form.reset();
                            }
                        });
                        </script>
            <div class="mb-6">
                <label for="event_title" class="block text-white font-semibold mb-2">Event Title:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-400">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V5a4 4 0 00-8 0v2m8 0v2a4 4 0 01-8 0V7'/></svg>
                    </span>
                    <input type="text" id="event_title" name="event_title" required 
                        value="<?php echo isset($_POST['event_title']) ? htmlspecialchars($_POST['event_title']) : ''; ?>"
                        placeholder="Enter event title"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                </div>
            </div>

            <div class="mb-6">
                <label for="event_date" class="block text-white font-semibold mb-2">Event Date & Time:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-pink-400">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>
                    </span>
                    <input type="datetime-local" id="event_date" name="event_date" required
                        value="<?php echo isset($_POST['event_date']) ? htmlspecialchars($_POST['event_date']) : ''; ?>"
                        min="<?php echo date('Y-m-d\TH:i'); ?>"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition">
                </div>
            </div>

            <div class="mb-6">
                <label for="location" class="block text-white font-semibold mb-2">Location:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-400">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 12.414a4 4 0 10-1.414 1.414l4.243 4.243a1 1 0 001.414-1.414z'/></svg>
                    </span>
                    <input type="text" id="location" name="location" required
                        value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>"
                        placeholder="Enter event location (e.g., Auditorium A)"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-200 transition">
                </div>
            </div>

            <div class="mb-8">
                <label for="details" class="block text-white font-semibold mb-2">Event Details:</label>
                <div class="relative">
                    <span class="absolute left-3 top-4 text-indigo-300">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 16h8M8 12h8m-8-4h8M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6'/></svg>
                    </span>
                    <textarea id="details" name="details" required
                        placeholder="Enter event details and description"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 transition resize-vertical min-h-32"><?php echo isset($_POST['details']) ? htmlspecialchars($_POST['details']) : ''; ?></textarea>
                </div>
            </div>

            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-pink-500 hover:from-pink-500 hover:to-indigo-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 active:scale-95 text-lg tracking-wide">
                <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4'/></svg>
                Post Event
            </button>
        </form>
                <script>
                // Dynamically set min for datetime-local to prevent past dates/times
                document.addEventListener('DOMContentLoaded', function() {
                    const eventDateInput = document.getElementById('event_date');
                    function setMinDateTime() {
                        const now = new Date();
                        const pad = n => n.toString().padStart(2, '0');
                        const minDate = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                        eventDateInput.min = minDate;
                        // If current value is before min, reset
                        if (eventDateInput.value && eventDateInput.value < minDate) {
                            eventDateInput.value = minDate;
                        }
                    }
                    setMinDateTime();
                    eventDateInput.addEventListener('input', function() {
                        // If user picks today, keep min time as now; else, min time is 00:00
                        const picked = new Date(eventDateInput.value);
                        const now = new Date();
                        if (
                            picked.getFullYear() === now.getFullYear() &&
                            picked.getMonth() === now.getMonth() &&
                            picked.getDate() === now.getDate()
                        ) {
                            setMinDateTime();
                        } else {
                            // Set min to start of picked day
                            const pad = n => n.toString().padStart(2, '0');
                            const min = `${picked.getFullYear()}-${pad(picked.getMonth()+1)}-${pad(picked.getDate())}T00:00`;
                            eventDateInput.min = min;
                        }
                    });
                });
                </script>
                <!-- Decorative floating shapes -->
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
                    <div class="absolute top-10 left-10 w-32 h-32 bg-pink-200 rounded-full opacity-30 blur-2xl animate-pulse"></div>
                    <div class="absolute bottom-10 right-10 w-40 h-40 bg-indigo-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
                    <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-purple-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
                </div>
        </form>
    </div>
</body>
</html>
