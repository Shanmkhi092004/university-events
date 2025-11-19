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
                    // Clear form fields and error messages
                    $new_username = '';
                    $new_password = '';
                    $confirm_password = '';
                    // Redirect to avoid resubmission and clear messages on refresh
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                    exit;
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
            $message =  implode("<br>", $errors);
            $message_type = "error";
        }
    }
}

// Handle deleting admin (only allow if not the current user)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_admin') {
    $admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 0;
    $redirect_url = $_SERVER['PHP_SELF'];
    if ($admin_id === intval($_SESSION['admin_id'] ?? 0)) {
        $redirect_url .= '?error=own';
    } elseif ($admin_id > 0) {
        $delete_sql = "DELETE FROM admin_users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if ($delete_stmt) {
            $delete_stmt->bind_param("i", $admin_id);
            if ($delete_stmt->execute()) {
                $redirect_url .= '?deleted=1';
            } else {
                $redirect_url .= '?error=delete';
            }
            $delete_stmt->close();
        }
    }
    header('Location: ' . $redirect_url);
    exit;
}

// Fetch all admin users
if (isset($_GET['success']) && $_GET['success'] == 1) {
    if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        $message = "Admin user deleted successfully!";
        $message_type = "success";
    } else if (isset($_GET['error']) && $_GET['error'] == 'own') {
        $message = "You cannot delete your own account.";
        $message_type = "error";
    } else if (isset($_GET['error']) && $_GET['error'] == 'delete') {
        $message = "Error deleting admin user.";
        $message_type = "error";
    }
    $message = "Admin user added successfully!";
    $message_type = "success";
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $message = '';
    $message_type = '';
}
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
    <link rel="icon" href="../images.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-100 min-h-screen relative overflow-x-hidden font-sans text-slate-800 leading-relaxed" style="background-image: url('../events_bg.jpeg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <!-- <header class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white py-14 shadow-2xl mb-10 rounded-b-3xl relative z-10">
        <h1 class="text-5xl font-extrabold text-center drop-shadow-lg tracking-tight">Manage Admin Users</h1>
        <p class="text-center mt-3 text-xl opacity-95 font-medium">Add and manage administrator accounts</p>
    </header> -->

     <header class="bg-white/20 backdrop-blur-2xl shadow-2xl rounded-b-3xl relative z-10 border-b-2 border-white/30 py-10 flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-fuchsia-600 to-pink-500 drop-shadow-2xl tracking-tight text-center mb-2" style="letter-spacing: 0.04em;">
            <span class="text-white">University Events Management</span>
        </h1>
        <p class="text-lg md:text-xl font-semibold text-white drop-shadow-sm text-center mt-2 tracking-wide">Add and manage administrator accounts</p>
    </header>

    <?php include '../partials/navbar.php'; ?>

    <div class="max-w-4xl mx-auto px-4 pb-16">
        <?php if (!empty($message)): ?>
            <div id="admin-message" class="mb-6 p-4 rounded-xl shadow <?php echo $message_type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
            <script>
            // Hide admin message after 3 seconds
            document.addEventListener('DOMContentLoaded', function() {
                var msg = document.getElementById('admin-message');
                if (msg) {
                    setTimeout(function() {
                        msg.style.display = 'none';
                    }, 3000);
                }
            });
            </script>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Add New Admin Form -->
            <div class="bg-white/95 p-10 rounded-3xl shadow-2xl border border-purple-100 hover:shadow-pink-200 transition-all duration-300">
                <h2 class="text-3xl font-extrabold text-indigo-700 mb-8 flex items-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-7 w-7 text-indigo-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>Add Admin User</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                    <script>
                                    // Reset add admin form after successful admin creation
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var successMsg = document.getElementById('admin-success-message');
                                        if (successMsg && successMsg.classList.contains('bg-green-50')) {
                                            var form = successMsg.closest('form');
                                            if (!form) form = document.querySelector('form[action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"]');
                                            if (form) form.reset();
                                        }
                                    });
                                    </script>
                    <input type="hidden" name="action" value="add_admin">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Username:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-indigo-400">
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                            </span>
                            <input type="text" name="username" required 
                                value=""
                                placeholder="Enter new username (min. 3 characters)"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Password:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-pink-400">
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3zm6 0c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3z'/></svg>
                            </span>
                            <input type="password" name="password" required 
                                placeholder="Enter password (min. 6 characters)"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition">
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="block text-gray-700 font-semibold mb-2">Confirm Password:</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-400">
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>
                            </span>
                            <input type="password" name="confirm_password" required 
                                placeholder="Confirm password"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-200 transition">
                        </div>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-pink-500 hover:from-pink-500 hover:to-indigo-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 active:scale-95 text-lg tracking-wide">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                        Create Admin User
                    </button>
                </form>
            </div>
            <!-- Current Admin Users -->
            <div class="bg-white/95 p-10 rounded-3xl shadow-2xl border border-purple-100 hover:shadow-indigo-200 transition-all duration-300">
                <h2 class="text-3xl font-extrabold text-indigo-700 mb-8 flex items-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-7 w-7 text-indigo-500' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>Current Admin Users (<?php echo count($admins); ?>)</h2>
                <?php if (!empty($admins)): ?>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <?php foreach ($admins as $admin): ?>
                            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-indigo-700 text-lg flex items-center gap-2"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-indigo-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg><?php echo htmlspecialchars($admin['username']); ?></p>
                                    <p class="text-sm text-gray-600">Created: <?php
                                        $ca = $admin['created_at'] ?? '';
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
                                <div>
                                <?php if (intval($admin['id']) !== intval($_SESSION['admin_id'] ?? 0)): ?>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_admin">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this admin user?');" class="flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium"><svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/></svg>Delete</button>
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
        <!-- Decorative floating shapes -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
            <div class="absolute top-10 left-10 w-32 h-32 bg-pink-200 rounded-full opacity-30 blur-2xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-40 h-40 bg-indigo-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-purple-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
        </div>
    </div>
</body>
</html>
