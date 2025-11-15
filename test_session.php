<?php
session_start();

// Test setting session variables
$_SESSION['is_admin'] = true;
$_SESSION['admin_username'] = 'admin';
$_SESSION['admin_id'] = 1;

echo "Session variables set successfully!<br>";
echo "is_admin: " . $_SESSION['is_admin'] . "<br>";
echo "admin_username: " . $_SESSION['admin_username'] . "<br>";
echo "admin_id: " . $_SESSION['admin_id'] . "<br>";

// Include database connection to verify admin_users table
include 'config/db.php';

$sql = "SELECT * FROM admin_users";
$result = $conn->query($sql);

echo "<h3>Current Admin Users:</h3>";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Username: " . $row['username'] . ", Created At: " . $row['created_at'] . ", Created By: " . $row['created_by'] . "<br>";
    }
} else {
    echo "No admin users found.";
}

$conn->close();
?>
