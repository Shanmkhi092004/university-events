<?php
/**
 * Diagnostic script to inspect `events` table schema and sample rows.
 * Run from browser or CLI. It uses the project's database config at ../config/db.php
 *
 * Usage (PowerShell):
 * php .\scripts\diagnose_events.php
 *
 * Or open in browser: http://localhost/php_project/university-events/scripts/diagnose_events.php
 *
 * After running, paste the output here so I can advise whether the ALTER migration is safe.
 * Delete this file after use for security.
 */

require_once __DIR__ . '/../config/db.php';

function print_box($title, $body) {
    echo "<h2>$title</h2>";
    echo "<pre style=\"background:#f6f8fa;border:1px solid #ddd;padding:12px;border-radius:6px;overflow:auto\">";
    echo htmlspecialchars($body);
    echo "</pre>";
}

try {
    // Show CREATE TABLE
    $res = $conn->query("SHOW CREATE TABLE events");
    if ($res && $row = $res->fetch_assoc()) {
        print_box('SHOW CREATE TABLE events', $row['Create Table']);
    } else {
        print_box('SHOW CREATE TABLE events', "Table 'events' does not exist or error: " . $conn->error);
    }

    // Show information_schema for event_id column
    $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY, EXTRA
            FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'event_id'";
    $res2 = $conn->query($sql);
    if ($res2 && $res2->num_rows > 0) {
        $out = [];
        while ($r = $res2->fetch_assoc()) {
            $out[] = $r;
        }
        print_box('information_schema.columns for events.event_id', print_r($out, true));
    } else {
        print_box('information_schema.columns for events.event_id', "No event_id column found or error: " . $conn->error);
    }

    // Show sample rows (first 20)
    $res3 = $conn->query("SELECT event_id, event_title, event_date, created_at FROM events ORDER BY event_id LIMIT 20");
    if ($res3) {
        $rows = [];
        while ($r = $res3->fetch_assoc()) $rows[] = $r;
        print_box('Sample rows (up to 20) from events', print_r($rows, true));
    } else {
        print_box('Sample rows (up to 20) from events', "Error: " . $conn->error);
    }

    // Count rows and min/max event_id
    $res4 = $conn->query("SELECT COUNT(*) AS cnt, MIN(event_id) AS min_id, MAX(event_id) AS max_id FROM events");
    if ($res4 && $r = $res4->fetch_assoc()) {
        print_box('Counts and event_id range', print_r($r, true));
    }

    $conn->close();

} catch (Exception $e) {
    echo "<pre>Exception: " . htmlspecialchars($e->getMessage()) . "</pre>";
}

?>
