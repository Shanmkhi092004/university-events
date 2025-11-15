<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php?redirect=manage_admins.php');
    exit;
}

include '../config/db.php';

$message = '';
$message_type = '';

// Handle adding new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $new_username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $new_password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    $errors = [];

    if (empty($new_username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($new_username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (empty($new_password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if username already exists
        $check_sql = "SELECT id FROM admin_users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt) {
            $check_stmt->bind_param("s", $new_username);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Username already exists. Please choose a different username.";
            }
            $check_stmt->close();
        }

        if (empty($errors)) {
            // Hash password and insert new admin
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $created_by = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin';
            
            // Store timestamp at insertion so created_at is populated
            $insert_sql = "INSERT INTO admin_users (username, password, created_by, created_at) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            
            if ($insert_stmt) {
                $insert_stmt->bind_param("sss", $new_username, $hashed_password, $created_by);
                
                if ($insert_stmt->execute()) {
                    $message = "Admin user '$new_username' created successfully!";
                    $message_type = "success";
                    // Clear form fields
                    $new_username = '';
                    $new_password = '';
                    $confirm_password = '';
                } else {
                    $message = "Error creating admin user: " . $insert_stmt->error;
                    $message_type = "error";
                }
                $insert_stmt->close();
            } else {
                $message = "Error preparing statement: " . $conn->error;
                $message_type = "error";
            }
        } else {
            $message = "Please fix the following errors:<br>" . implode("<br>", $errors);
            $message_type = "error";
        }
    }
}

// Handle deleting admin (only allow if not the current user)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_admin') {
    $admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 0;

    if ($admin_id === intval($_SESSION['admin_id'] ?? 0)) {
        $message = "You cannot delete your own account.";
        $message_type = "error";
    } elseif ($admin_id > 0) {
        $delete_sql = "DELETE FROM admin_users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt) {
            $delete_stmt->bind_param("i", $admin_id);
            
            if ($delete_stmt->execute()) {
                $message = "Admin user deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Error deleting admin user: " . $delete_stmt->error;
                $message_type = "error";
            }
            $delete_stmt->close();
        }
    }
}

// Fetch all admin users
$admins_sql = "SELECT id, username, created_at, created_by FROM admin_users ORDER BY created_at DESC";
$admins_result = $conn->query($admins_sql);
$admins = [];
if ($admins_result && $admins_result->num_rows > 0) {
    while ($admin = $admins_result->fetch_assoc()) {
        $admins[] = $admin;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Users - University Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-slate-800 leading-relaxed">
    <header class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white py-12 shadow-lg mb-8">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-center tracking-tight text-slate-50">Manage Admin Users</h1>
        <p class="text-center mt-2 text-lg text-slate-100/90">Add and manage administrator accounts</p>
    </header>

    <nav class="bg-gray-800 mb-8 rounded-lg shadow-md">
        <div class="max-w-4xl mx-auto px-4 flex flex-wrap items-center justify-between">
            <div class="flex flex-wrap">
                <a href="post_event.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Post New Event</a>
                <a href="upcoming_events.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300">View Upcoming Events</a>
                <a href="manage_admins.php" class="block text-gray-100 hover:bg-indigo-500 px-6 py-3 text-lg font-medium transition-colors duration-300 active:bg-indigo-500">Manage Admins</a>
            </div>
            <div>
                <a href="logout.php" class="block text-gray-100 hover:bg-red-500 px-6 py-3 text-lg font-medium transition-colors duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 pb-12">
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded <?php echo $message_type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Add New Admin Form -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-indigo-700 mb-6">Add New Admin User</h2>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="action" value="add_admin">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Username:</label>
                        <input type="text" name="username" required 
                               value="<?php echo isset($_POST['username']) && $_POST['action'] === 'add_admin' ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Enter new username (min. 3 characters)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password:</label>
                        <input type="password" name="password" required 
                               placeholder="Enter password (min. 6 characters)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password:</label>
                        <input type="password" name="confirm_password" required 
                               placeholder="Confirm password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-300">
                        Create Admin User
                    </button>
                </form>
            </div>

            <!-- Current Admin Users -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-indigo-700 mb-6">Current Admin Users (<?php echo count($admins); ?>)</h2>
                
                <?php if (!empty($admins)): ?>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <?php foreach ($admins as $admin): ?>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold text-indigo-700"><?php echo htmlspecialchars($admin['username']); ?></p>
                                        <p class="text-sm text-gray-600">Created: <?php
                                            $ca = $admin['created_at'] ?? '';
                                            // Treat zero-date or empty values as unknown
                                            if (empty($ca) || $ca === '0000-00-00' || $ca === '0000-00-00 00:00:00') {
                                                echo 'Unknown';
                                            } else {
                                                $ts = strtotime($ca);
                                                if ($ts === false) {
                                                    echo 'Unknown';
                                                } else {
                                                    echo htmlspecialchars(date('M j, Y H:i', $ts));
                                                }
                                            }
                                        ?></p>
                                        <p class="text-sm text-gray-600">By: <?php echo htmlspecialchars($admin['created_by'] ?? 'System'); ?></p>
                                    </div>
                                    <?php if (intval($admin['id']) !== intval($_SESSION['admin_id'] ?? 0)): ?>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_admin">
                                            <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this admin user?');" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded">You</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No admin users found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
