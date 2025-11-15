<?php
/**
 * Setup script to create admin_users table
 * Run this once to set up the database structure
 */

include 'config/db.php';

try {
    // Drop existing table if it exists (optional, for fresh setup)
    // $conn->query("DROP TABLE IF EXISTS admin_users");

    // Create admin_users table
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by VARCHAR(50),
        INDEX idx_username (username)
    )";

    if ($conn->query($sql) === TRUE) {
        echo "<div style='background: #d4edda; border: 1px solid #28a745; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<strong>✓ admin_users table created successfully!</strong>";
        echo "</div>";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Check if default admin exists
    $result = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'");
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        // Create default admin user with password 'admin123'
        $default_username = 'admin';
        $default_password = password_hash('admin123', PASSWORD_BCRYPT);
        
        $insert_sql = "INSERT INTO admin_users (username, password, created_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $default_username, $default_password, $default_username);
            
            if ($stmt->execute()) {
                echo "<div style='background: #cfe2ff; border: 1px solid #0d6efd; color: #084298; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
                echo "<strong>✓ Default admin user created!</strong><br>";
                echo "Username: <strong>admin</strong><br>";
                echo "Password: <strong>admin123</strong><br>";
                echo "Please change this password after first login!";
                echo "</div>";
            } else {
                throw new Exception("Error inserting default admin: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
    } else {
        echo "<div style='background: #cfe2ff; border: 1px solid #0d6efd; color: #084298; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>";
        echo "<strong>ℹ Default admin user already exists</strong>";
        echo "</div>";
    }

    // Display all admins
    $verify = $conn->query("SELECT id, username, created_at, created_by FROM admin_users ORDER BY created_at");
    
    if ($verify && $verify->num_rows > 0) {
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; color: #664d03; padding: 15px; border-radius: 4px;'>";
        echo "<strong>Current Admin Users:</strong><br><br>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #ffc107; color: #000;'>";
        echo "<th style='border: 1px solid #ffc107; padding: 10px;'>ID</th>";
        echo "<th style='border: 1px solid #ffc107; padding: 10px;'>Username</th>";
        echo "<th style='border: 1px solid #ffc107; padding: 10px;'>Created At</th>";
        echo "<th style='border: 1px solid #ffc107; padding: 10px;'>Created By</th>";
        echo "</tr>";
        
        while ($admin = $verify->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='border: 1px solid #ffc107; padding: 10px;'>" . $admin['id'] . "</td>";
            echo "<td style='border: 1px solid #ffc107; padding: 10px;'>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td style='border: 1px solid #ffc107; padding: 10px;'>" . $admin['created_at'] . "</td>";
            echo "<td style='border: 1px solid #ffc107; padding: 10px;'>" . ($admin['created_by'] ? htmlspecialchars($admin['created_by']) : 'System') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
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
    <title>Setup Admin Users - University Events</title>
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
        <h1>🔧 Admin Users Setup</h1>
    </div>
</body>
</html>
