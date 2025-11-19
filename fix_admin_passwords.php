<?php
// fix_admin_passwords.php
// This script will hash all plain-text passwords in the admin_users table using bcrypt.

require_once __DIR__ . '/config/db.php';

$sql = "SELECT id, password FROM admin_users";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $password = $row['password'];
        // If the password is already a bcrypt hash, skip it
        if (preg_match('/^\$2y\$/', $password)) {
            continue;
        }
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $id);
        if ($update->execute()) {
            echo "Updated password for admin user ID $id\n";
        } else {
            echo "Failed to update password for admin user ID $id\n";
        }
        $update->close();
    }
    echo "Password update complete.\n";
} else {
    echo "No admin users found or query failed.\n";
}

$conn->close();
?>
