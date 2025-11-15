<?php
session_start();

// Include database connection
include '../config/db.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'post_event.php';

// If already logged in, redirect to requested page
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: ' . $redirect);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        // Query admin_users table
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                
                // Verify password using bcrypt
                if (password_verify($password, $admin['password'])) {
                    // Successful login
                    $_SESSION['is_admin'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_id'] = $admin['id'];
                    
                    // Redirect back to requested page
                    header('Location: ' . (isset($_POST['redirect']) ? $_POST['redirect'] : $redirect));
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
            $stmt->close();
        } else {
            $error = 'Database error. Please try again.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - University Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow">
            <h1 class="text-2xl font-bold text-indigo-700 mb-4">Admin Login</h1>
            <p class="text-sm text-gray-600 mb-6">Sign in with your admin credentials to manage events.</p>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input name="username" class="mt-1 mb-4 w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-200" required>

                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input name="password" type="password" class="mt-1 mb-4 w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-200" required>

                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded font-semibold hover:bg-indigo-700">Sign in</button>
            </form>

            <p class="text-xs text-gray-500 mt-4">Default admin: <strong>admin</strong> (password: <strong>admin123</strong>)</p>
        </div>
    </div>
</body>
</html>
