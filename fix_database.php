<?php
/**
 * Database Fix Script
 * This script fixes the events table by recreating it with proper AUTO_INCREMENT
 * and reinserting sample data
 */

include 'config/db.php';

try {
    // Drop the existing table
    $conn->query("DROP TABLE IF EXISTS events");
    
    // Create events table with proper AUTO_INCREMENT
    $create_table = "CREATE TABLE IF NOT EXISTS events (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        event_title VARCHAR(255) NOT NULL,
        event_date DATETIME NOT NULL,
        location VARCHAR(255) NOT NULL,
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table) === TRUE) {
        echo "<div style='background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<strong>✓ Table created successfully!</strong>";
        echo "</div>";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Insert sample data
    $insert_data = "INSERT INTO events (event_title, event_date, location, details) VALUES
    ('Physics Seminar', '2025-12-01 14:00:00', 'Auditorium A', 'Renowned physicist discussing quantum mechanics'),
    ('Career Fair 2025', '2025-12-05 09:00:00', 'Student Center', 'Meet with top companies and explore job opportunities'),
    ('Winter Concert', '2025-12-10 18:00:00', 'Concert Hall', 'Annual winter concert by the university orchestra'),
    ('Science Expo', '2025-12-15 10:00:00', 'Science Building', 'Student projects and demonstrations'),
    ('Graduation Ceremony', '2025-12-20 10:00:00', 'Main Stadium', 'Winter semester graduation ceremony')";
    
    if ($conn->query($insert_data) === TRUE) {
        echo "<div style='background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<strong>✓ Sample data inserted successfully!</strong>";
        echo "</div>";
    } else {
        throw new Exception("Error inserting data: " . $conn->error);
    }
    
    // Verify the data
    $verify = $conn->query("SELECT * FROM events ORDER BY event_id");
    
    if ($verify->num_rows > 0) {
        echo "<div style='background: #cfe2ff; border: 1px solid #0d6efd; color: #084298; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<strong>✓ Database verification successful!</strong><br>";
        echo "Total events: " . $verify->num_rows . "<br><br>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #0d6efd; color: white;'>";
        echo "<th style='border: 1px solid #0d6efd; padding: 10px;'>ID</th>";
        echo "<th style='border: 1px solid #0d6efd; padding: 10px;'>Title</th>";
        echo "<th style='border: 1px solid #0d6efd; padding: 10px;'>Date</th>";
        echo "</tr>";
        
        while ($row = $verify->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #0d6efd; padding: 10px;'>" . $row['event_id'] . "</td>";
            echo "<td style='border: 1px solid #0d6efd; padding: 10px;'>" . htmlspecialchars($row['event_title']) . "</td>";
            echo "<td style='border: 1px solid #0d6efd; padding: 10px;'>" . $row['event_date'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
        
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; color: #664d03; padding: 15px; border-radius: 4px;'>";
        echo "<strong>✓ Database fixed successfully!</strong><br>";
        echo "You can now go to <a href='pages/upcoming_events.php' style='color: #664d03; text-decoration: underline;'>Upcoming Events</a> page.<br>";
        echo "The hover modal should now work correctly with each event showing different data!";
        echo "</div>";
    } else {
        throw new Exception("Data verification failed - no events found");
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px;'>";
    echo "<strong>✗ Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Fix - University Events</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Fix</h1>
    </div>
</body>
</html>
