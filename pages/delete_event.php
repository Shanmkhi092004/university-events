<?php
session_start();
// Only allow admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: upcoming_events.php');
    exit;
}

$event_id = intval($_GET['id']);
include '../config/db.php';

// Delete event
$stmt = $conn->prepare('DELETE FROM events WHERE event_id = ?');
if ($stmt) {
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();

header('Location: upcoming_events.php?deleted=1');
exit;
