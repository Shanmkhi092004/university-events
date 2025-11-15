<?php
/**
 * Login Test & Debug
 * Tests the full login flow to pinpoint where it's failing.
 * 
 * This simulates a login attempt and shows each step.
 */

require_once __DIR__ . '/../config/db.php';

session_start();

echo "<h1>Login Flow Debug</h1>";

// Get credentials from form
$username = isset($_POST['test_username']) ? trim($_POST['test_username']) : 'admin';
$password = isset($_POST['test_password']) ? trim($_POST['test_password']) : 'admin123';

echo "<form method='POST' style='border:1px solid #ddd;padding:15px;margin-bottom:20px;width:50%'>";
echo "<label>Username: <input type='text' name='test_username' value='" . htmlspecialchars($username) . "' style='padding:5px;width:200px'></label><br>";
echo "<label>Password: <input type='password' name='test_password' value='" . htmlspecialchars($password) . "' style='padding:5px;width:200px'></label><br>";
echo "<button type='submit' style='margin-top:10px;padding:8px 15px;background:#0066cc;color:white;border:none;border-radius:3px;cursor:pointer'>Test Login</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Login Simulation Steps</h2>";

    // Step 1: Check credentials provided
    echo "<p><strong>Step 1: Input validation</strong></p>";
    if (empty($username) || empty($password)) {
        echo "<p style='color:red'>✗ FAIL: Username or password empty</p>";
    } else {
        echo "<p style='color:green'>✓ PASS: Both username and password provided</p>";
        echo "<p>  Username: <code>$username</code></p>";
        echo "<p>  Password: <code>" . str_repeat('*', strlen($password)) . "</code> (length: " . strlen($password) . ")</p>";
    }

    // Step 2: Query database
    echo "<p><strong>Step 2: Database query for user</strong></p>";
    $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<p style='color:red'>✗ FAIL: Prepare error - " . htmlspecialchars($conn->error) . "</p>";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            echo "<p style='color:green'>✓ PASS: User found in database</p>";
            echo "<p>  ID: " . $admin['id'] . "</p>";
            echo "<p>  Username: " . htmlspecialchars($admin['username']) . "</p>";
            echo "<p>  Password hash (first 60 chars): " . htmlspecialchars(substr($admin['password'], 0, 60)) . "</p>";

            // Step 3: Verify password
            echo "<p><strong>Step 3: Password verification with password_verify()</strong></p>";
            $match = password_verify($password, $admin['password']);

            if ($match) {
                echo "<p style='color:green'>✓ PASS: password_verify() returned true</p>";
                echo "<p>  The password hash is valid and matches the plaintext password.</p>";

                // Step 4: Check if session would be set
                echo "<p><strong>Step 4: Session would be set to:</strong></p>";
                echo "<p>  \$_SESSION['is_admin'] = true</p>";
                echo "<p>  \$_SESSION['admin_username'] = '" . htmlspecialchars($admin['username']) . "'</p>";
                echo "<p>  \$_SESSION['admin_id'] = " . $admin['id'] . "</p>";

                echo "<div style='background:#c8e6c9;border:2px solid #4caf50;padding:15px;border-radius:5px;margin-top:20px'>";
                echo "<h3>✓ Login would succeed!</h3>";
                echo "<p>If login is still failing, the issue may be:</p>";
                echo "<ul>";
                echo "<li>Session handling/headers already sent</li>";
                echo "<li>Redirect after setting session not working</li>";
                echo "<li>Browser not accepting session cookies</li>";
                echo "</ul>";
                echo "</div>";
            } else {
                echo "<p style='color:red'>✗ FAIL: password_verify() returned false</p>";
                echo "<p>  The plaintext password does NOT match the hash.</p>";
                echo "<p>  This means the password in the database is either:</p>";
                echo "<ul>";
                echo "<li>Not a valid bcrypt hash</li>";
                echo "<li>Corrupted or truncated</li>";
                echo "<li>For a different password than what you entered</li>";
                echo "</ul>";

                echo "<div style='background:#ffcdd2;border:2px solid #f44336;padding:15px;border-radius:5px;margin-top:20px'>";
                echo "<h3>✗ Login would fail</h3>";
                echo "<p><strong>Solution:</strong> Run <code>scripts/fix_password_final.php</code> to regenerate the password hash.</p>";
                echo "</div>";
            }
        } else {
            echo "<p style='color:red'>✗ FAIL: User not found in database</p>";
            echo "<p>  Query: <code>SELECT id, username, password FROM admin_users WHERE username = '" . htmlspecialchars($username) . "'</code></p>";
            echo "<p>  Check that the username is spelled correctly and exists in the admin_users table.</p>";
        }
        $stmt->close();
    }
}

$conn->close();

?>
<hr>
<p style='font-size:12px;color:#666'>
  <strong>If login is working:</strong> Delete these test files:<br>
  &nbsp;&nbsp;• <code>scripts/debug_login.php</code><br>
  &nbsp;&nbsp;• <code>scripts/reset_passwords.php</code><br>
  &nbsp;&nbsp;• <code>scripts/fix_password_final.php</code><br>
  &nbsp;&nbsp;• <code>scripts/test_login.php</code>
</p>
```
