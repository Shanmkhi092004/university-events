<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Event - University Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<?php
session_start();
// If not logged in as admin, redirect to login
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect to login page in the same folder and request return to this page
    $redirect = 'post_event.php';
    header('Location: login.php?redirect=' . urlencode($redirect));
    exit;
}
?>

    <header class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-12 shadow-lg mb-8">
        <h1 class="text-4xl font-bold text-center">University Events Management</h1>
        <p class="text-center mt-2 text-lg opacity-95">Manage upcoming events and announcements</p>
    </header>

    <nav class="bg-gray-800 mb-8 rounded-lg shadow-md">
        <div class="max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-between">
            <div class="flex flex-wrap">
                <a href="post_event.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300 active:bg-indigo-500">Post New Event</a>
                <a href="manage_admins.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Manage Admins</a>
                <a href="upcoming_events.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">View Upcoming Events</a>
            </div>
            <div>
                <a href="logout.php" class="block text-gray-100 hover:bg-red-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-12">
        <h2 class="text-3xl font-bold mb-8 text-gray-800">Post New Event</h2>
        
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
                        $message = "Event posted successfully! Your event has been added to the upcoming events list.";
                        $message_type = "success";
                        
                        // Clear form fields
                        $event_title = '';
                        $event_date = '';
                        $location = '';
                        $details = '';
                    } else {
                        $message = "Error: " . $stmt->error;
                        $message_type = "error";
                    }
                    $stmt->close();
                } else {
                    $message = "Error preparing statement: " . $conn->error;
                    $message_type = "error";
                }
            } else {
                $message = implode("<br>", $errors);
                $message_type = "error";
            }
        }

        // Display message if exists
        if (!empty($message)) {
            if ($message_type === 'success') {
                echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6'>" . $message . "</div>";
            } else {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>Please fix the following errors:<br>" . $message . "</div>";
            }
        }
        ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="bg-white p-8 rounded-lg shadow-md">
            <div class="mb-6">
                <label for="event_title" class="block text-gray-700 font-semibold mb-2">Event Title:</label>
                <input type="text" id="event_title" name="event_title" required 
                       value="<?php echo isset($_POST['event_title']) ? htmlspecialchars($_POST['event_title']) : ''; ?>"
                       placeholder="Enter event title"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-6">
                <label for="event_date" class="block text-gray-700 font-semibold mb-2">Event Date & Time:</label>
                <input type="datetime-local" id="event_date" name="event_date" required
                       value="<?php echo isset($_POST['event_date']) ? htmlspecialchars($_POST['event_date']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-6">
                <label for="location" class="block text-gray-700 font-semibold mb-2">Location:</label>
                <input type="text" id="location" name="location" required
                       value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>"
                       placeholder="Enter event location (e.g., Auditorium A)"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
            </div>

            <div class="mb-6">
                <label for="details" class="block text-gray-700 font-semibold mb-2">Event Details:</label>
                <textarea id="details" name="details" required
                          placeholder="Enter event details and description"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition resize-vertical min-h-32"><?php echo isset($_POST['details']) ? htmlspecialchars($_POST['details']) : ''; ?></textarea>
            </div>

            <button type="submit" class="bg-indigo-500 hover:bg-purple-600 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300 active:scale-95">Post Event</button>
        </form>
    </div>
</body>
</html>
