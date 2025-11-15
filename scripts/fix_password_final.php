<?php
/**
 * FINAL Password Fix - Direct approach
 * This script WILL regenerate the bcrypt hash in-place for 'admin' user.
 * 
 * If this doesn't work, there's a more serious DB issue.
 * Run it ONCE, then delete the file.
 */

require_once __DIR__ . '/../config/db.php';

echo "<h1>🔧 Direct Password Fix</h1>";

try {
    // Step 1: Show current state
    echo "<h2>Current Admin Users</h2>";
    $check = $conn->query("SELECT id, username, password FROM admin_users");
    if ($check && $check->num_rows > 0) {
        echo "<pre style='background:#f0f0f0;padding:10px;border-radius:5px;overflow:auto'>";
        while ($row = $check->fetch_assoc()) {
            echo "ID: " . $row['id'] . " | Username: " . $row['username'] . " | Hash (first 60 chars): " . substr($row['password'], 0, 60) . "\n";
        }
        echo "</pre>";
    }

    // Step 2: Generate fresh bcrypt hash for 'admin123'
    echo "<h2>Generating New Bcrypt Hash...</h2>";
    $plaintext = 'admin123';
    $new_hash = password_hash($plaintext, PASSWORD_BCRYPT);
    echo "<p>Plaintext: <code>$plaintext</code></p>";
    echo "<p>New bcrypt hash: <code style='word-break:break-all'>" . htmlspecialchars($new_hash) . "</code></p>";

    // Step 3: Update the admin user
    echo "<h2>Updating Database...</h2>";
    
    // First, ensure the table exists
    $ensure_table = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by VARCHAR(50),
        INDEX idx_username (username)
    )";
    $conn->query($ensure_table);
    
    // Check if admin exists
    $admin_check = $conn->query("SELECT COUNT(*) as cnt FROM admin_users WHERE username = 'admin'");
    $admin_row = $admin_check->fetch_assoc();
    
    if ($admin_row['cnt'] > 0) {
        // Update existing admin
        $update_sql = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
        $stmt = $conn->prepare($update_sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $new_hash);
        if ($stmt->execute()) {
            echo "<p style='color:green'><strong>✓ Updated existing 'admin' user</strong></p>";
            $stmt->close();
        } else {
            throw new Exception("Update failed: " . $stmt->error);
        }
    } else {
        // Insert new admin if doesn't exist
        $insert_sql = "INSERT INTO admin_users (username, password, created_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $created_by = 'system';
        $username = 'admin';
        $stmt->bind_param("sss", $username, $new_hash, $created_by);
        if ($stmt->execute()) {
            echo "<p style='color:green'><strong>✓ Created new 'admin' user</strong></p>";
            $stmt->close();
        } else {
            throw new Exception("Insert failed: " . $stmt->error);
        }
    }

    // Step 4: Verify
    echo "<h2>Verification</h2>";
    $verify = $conn->query("SELECT id, username, password FROM admin_users WHERE username = 'admin'");
    if ($verify && $verify->num_rows > 0) {
        $v_row = $verify->fetch_assoc();
        echo "<p><strong>Username:</strong> " . htmlspecialchars($v_row['username']) . "</p>";
        echo "<p><strong>Hash:</strong> <code style='word-break:break-all'>" . htmlspecialchars($v_row['password']) . "</code></p>";
        
        // Test password_verify locally
        $test = password_verify($plaintext, $v_row['password']);
        echo "<p><strong>Test password_verify():</strong> " . ($test ? "<span style='color:green'>✓ PASS</span>" : "<span style='color:red'>✗ FAIL</span>") . "</p>";
    }

    echo "<div style='background:#c8e6c9;border:2px solid #4caf50;padding:15px;border-radius:5px;margin-top:20px'>";
    echo "<h3>✓ Fix Complete!</h3>";
    echo "<p><strong>You can now login with:</strong></p>";
    echo "<p>Username: <code>admin</code></p>";
    echo "<p>Password: <code>admin123</code></p>";
    echo "<p><em>Try logging in now. If it still fails, there may be a session or redirect issue.</em></p>";
    echo "</div>";

    $conn->close();

} catch (Exception $e) {
    echo "<div style='background:#ffcdd2;border:2px solid #f44336;padding:15px;border-radius:5px'>";
    echo "<h3>✗ Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

?>
<hr>
<p style='font-size:12px;color:#666'>
  <strong>Next:</strong> Delete this file after use: <code>scripts/fix_password_final.php</code>
</p>
```
