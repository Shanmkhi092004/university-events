<?php
session_start();
// Only allow admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

// Get event ID
if (!isset($_GET['id'])) {
    header('Location: upcoming_events.php');
    exit;
}
$event_id = intval($_GET['id']);

// Fetch event details
$stmt = $conn->prepare('SELECT event_title, event_date, location, details FROM events WHERE event_id = ?');
$stmt->bind_param('i', $event_id);
$stmt->execute();
$stmt->bind_result($event_title, $event_date, $location, $details);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    header('Location: upcoming_events.php');
    exit;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_title = trim($_POST['event_title']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);
    $details = trim($_POST['details']);
    $sql = 'UPDATE events SET event_title=?, event_date=?, location=?, details=? WHERE event_id=?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $event_title, $event_date, $location, $details, $event_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: upcoming_events.php?edited=1');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100" style="background-image: url('../events_bg.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="w-full max-w-lg p-8 rounded-3xl shadow-2xl bg-white/30 backdrop-blur-2xl border border-white/40 flex flex-col gap-6">
        <h2 class="text-3xl font-extrabold text-center text-indigo-700 mb-2 drop-shadow">Edit Event</h2>
        <form method="post" class="flex flex-col gap-5">
            <div>
                <label class="block text-indigo-900 font-semibold mb-1">Title</label>
                <input type="text" name="event_title" value="<?php echo htmlspecialchars($event_title); ?>" required class="w-full px-4 py-2 rounded-xl border border-indigo-200 bg-white/60 focus:ring-2 focus:ring-indigo-400 focus:outline-none text-lg shadow-sm" />
            </div>
            <div>
                <label class="block text-indigo-900 font-semibold mb-1">Date & Time</label>
                <input type="datetime-local" name="event_date" value="<?php echo date('Y-m-d\\TH:i', strtotime($event_date)); ?>" required class="w-full px-4 py-2 rounded-xl border border-indigo-200 bg-white/60 focus:ring-2 focus:ring-indigo-400 focus:outline-none text-lg shadow-sm" />
            </div>
            <div>
                <label class="block text-indigo-900 font-semibold mb-1">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required class="w-full px-4 py-2 rounded-xl border border-indigo-200 bg-white/60 focus:ring-2 focus:ring-indigo-400 focus:outline-none text-lg shadow-sm" />
            </div>
            <div>
                <label class="block text-indigo-900 font-semibold mb-1">Details</label>
                <textarea name="details" required rows="4" class="w-full px-4 py-2 rounded-xl border border-indigo-200 bg-white/60 focus:ring-2 focus:ring-indigo-400 focus:outline-none text-lg shadow-sm"><?php echo htmlspecialchars($details); ?></textarea>
            </div>
            <div class="flex gap-4 mt-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-pink-500 hover:from-indigo-600 hover:to-pink-600 text-white font-bold py-2 rounded-xl shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-pink-300">Save Changes</button>
                <a href="upcoming_events.php" class="flex-1 bg-white/70 hover:bg-white/90 text-indigo-700 font-bold py-2 rounded-xl shadow-md border border-indigo-200 text-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-200">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
