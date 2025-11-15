<?php
/**
 * NUCLEAR OPTION: Wipe and Rebuild Admin User
 * 
 * This script:
 * 1. Deletes all rows from admin_users table
 * 2. Re-inserts the 'admin' user with a fresh bcrypt hash of 'admin123'
 * 3. Verifies the hash works with password_verify()
 * 
 * This is a destructive operation - use only if you don't have other admins to preserve.
 * Run ONCE, then delete this file.
 */

require_once __DIR__ . '/../config/db.php';

echo "<h1>🔨 Admin User Rebuild</h1>";
echo "<p style='color:red'><strong>⚠️ WARNING:</strong> This will DELETE ALL admin users and recreate the 'admin' user.</p>";

try {
    // Step 1: Show current state
    echo "<h2>Step 1: Current State</h2>";
    $check = $conn->query("SELECT COUNT(*) as cnt FROM admin_users");
    $check_row = $check->fetch_assoc();
    echo "<p>Current admin users in database: <strong>" . $check_row['cnt'] . "</strong></p>";

    // Step 2: Delete all admin users
    echo "<h2>Step 2: Deleting All Admin Users</h2>";
    $delete_result = $conn->query("DELETE FROM admin_users");
    if ($delete_result === false) {
        throw new Exception("Delete failed: " . $conn->error);
    }
    echo "<p style='color:orange'>⚠️ Deleted " . $conn->affected_rows . " rows</p>";

    // Step 3: Generate fresh bcrypt hash
    echo "<h2>Step 3: Generating Fresh Bcrypt Hash</h2>";
    $plaintext_password = 'admin123';
    $fresh_hash = password_hash($plaintext_password, PASSWORD_BCRYPT);
    echo "<p>Plaintext: <code>$plaintext_password</code></p>";
    echo "<p>New bcrypt hash: <code style='word-break:break-all'>" . htmlspecialchars($fresh_hash) . "</code></p>";

    // Step 4: Insert fresh admin user
    echo "<h2>Step 4: Inserting Fresh Admin User</h2>";
    $insert_sql = "INSERT INTO admin_users (username, password, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $username = 'admin';
    $created_by = 'system';
    $stmt->bind_param("sss", $username, $fresh_hash, $created_by);
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    echo "<p style='color:green'>✓ Inserted new 'admin' user (ID: " . $conn->insert_id . ")</p>";
    $stmt->close();

    // Step 5: Verify the insert
    echo "<h2>Step 5: Verification</h2>";
    $verify = $conn->query("SELECT id, username, password FROM admin_users WHERE username = 'admin'");
    if (!$verify || $verify->num_rows === 0) {
        throw new Exception("Verification failed: admin user not found after insert!");
    }

    $admin = $verify->fetch_assoc();
    echo "<p>Admin ID: " . $admin['id'] . "</p>";
    echo "<p>Username: " . htmlspecialchars($admin['username']) . "</p>";
    echo "<p>Hash (first 60 chars): " . htmlspecialchars(substr($admin['password'], 0, 60)) . "</p>";

    // Step 6: Test password_verify
    echo "<h2>Step 6: Test password_verify()</h2>";
    $test_pass = password_verify($plaintext_password, $admin['password']);
    echo "<p>Testing password_verify('<code>$plaintext_password</code>', <hash>): " . ($test_pass ? "<span style='color:green'><strong>✓ TRUE</strong></span>" : "<span style='color:red'><strong>✗ FALSE</strong></span>") . "</p>";

    if (!$test_pass) {
        throw new Exception("password_verify() still fails! There may be a PHP/database encoding issue.");
    }

    echo "<div style='background:#c8e6c9;border:2px solid #4caf50;padding:20px;border-radius:5px;margin-top:20px'>";
    echo "<h3>✓✓✓ SUCCESS! ✓✓✓</h3>";
    echo "<p>The admin user has been rebuilt with a fresh bcrypt hash.</p>";
    echo "<p><strong>You can now login with:</strong></p>";
    echo "<p style='font-size:16px'><code>Username: admin</code><br><code>Password: admin123</code></p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='../pages/login.php'>Login Page</a> and sign in</li>";
    echo "<li>Delete this file and all other test scripts from the scripts folder</li>";
    echo "<li>You're done!</li>";
    echo "</ol>";
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
  After this works, delete: <code>scripts/rebuild_admin.php</code>
</p>
```
