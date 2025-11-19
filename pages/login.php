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
                    $_SESSION['login_error'] = 'The username or password you entered is incorrect. Please try again.';
                    header('Location: login.php?redirect=' . urlencode($redirect));
                    exit;
                }
            } else {
                $_SESSION['login_error'] = 'The username or password you entered is incorrect. Please try again.';
                header('Location: login.php?redirect=' . urlencode($redirect));
                exit;
            }
            $stmt->close();
        } else {
            $_SESSION['login_error'] = 'Database error. Please try again.';
            header('Location: login.php?redirect=' . urlencode($redirect));
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'Please enter both username and password.';
        header('Location: login.php?redirect=' . urlencode($redirect));
        exit;
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
    <link rel="icon" href="../images.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans relative min-h-screen">
<body class="bg-gray-50 font-sans relative overflow-hidden" style="height: fit-content;">
    <!-- Full-page background image -->
    <div class="fixed inset-0 -z-10 bg-cover bg-center" style="background-image: url('../bg.jpeg'); filter: blur(2px) brightness(0.7);"></div>
    <header class="bg-white/20 backdrop-blur-2xl shadow-2xl rounded-b-3xl relative z-10 border-b-2 border-white/30 py-10 flex flex-col items-center">
        <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-fuchsia-600 to-pink-500 drop-shadow-2xl tracking-tight text-center mb-2" style="letter-spacing: 0.04em;">
             <span class="text-indigo-900">University Events Management</span>
        </h1>
        <p class="text-lg md:text-xl font-semibold text-indigo-800/90 drop-shadow-sm text-center mt-2 tracking-wide">Admin Portal Login</p>
    </header>
    <div class="flex justify-center px-4" style="align-items: flex-start; min-height: 0; margin-top: 40px;">
        <div class="max-w-md w-full bg-white/20 backdrop-blur-2xl border border-white/40 shadow-2xl p-10 rounded-3xl relative overflow-hidden mt-[10px]">
            <div class="flex flex-col items-center mb-6">
                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-tr from-indigo-500 to-pink-400 shadow-lg mb-3">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3zm6 0c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3z"/></svg>
                </span>
                <h1 class="text-3xl font-extrabold text-indigo-700 mb-1 tracking-tight drop-shadow">Login</h1>
                <p class="text-base text-gray-600 mb-2">Sign in to manage university events</p>
            </div>

            <?php
            $showError = '';
            if (!empty($_SESSION['login_error'])) {
                $showError = $_SESSION['login_error'];
                unset($_SESSION['login_error']);
            }
            if ($showError): ?>
                <div class="flex items-center gap-2 bg-gradient-to-r from-red-100 via-pink-100 to-yellow-100 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 shadow-lg animate-shake">
                    <svg class="h-6 w-6 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414-1.414A9 9 0 105.636 18.364l1.414 1.414A9 9 0 1018.364 5.636z" /></svg>
                    <span class="font-semibold"><?php echo htmlspecialchars($showError); ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                <div>
                    <label class="block text-sm font-semibold text-indigo-700 mb-1">Username</label>
                    <input name="username" class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 bg-white/90 text-gray-800 placeholder-gray-400 transition" placeholder="Enter your username" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-indigo-700 mb-1">Password</label>
                    <input name="password" type="password" class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:outline-none focus:border-pink-400 focus:ring-2 focus:ring-pink-100 bg-white/90 text-gray-800 placeholder-gray-400 transition" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-pink-500 hover:from-pink-500 hover:to-indigo-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 active:scale-95 text-lg tracking-wide">
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                    Sign in
                </button>
            </form>

            <a href="upcoming_events.php" class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-100 to-pink-100 hover:from-pink-200 hover:to-indigo-200 text-indigo-700 font-bold py-3 px-8 rounded-xl shadow transition-all duration-300 text-lg tracking-wide border border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7'/></svg>
                Back to Events
            </a>

            <!-- <p class="text-xs text-gray-500 mt-6 text-center">Default admin: <span class="font-semibold text-indigo-600">admin</span> <span class="mx-1">/</span> <span class="font-semibold text-pink-500">admin123</span></p> -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-200 rounded-full opacity-20 blur-2xl animate-pulse"></div>
        </div>
    </div>

</body>
</html>
