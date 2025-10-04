<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // find admin
    // NOTE: $pdo and e() function are assumed to be available from the included files.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=:u LIMIT 1");
    $stmt->execute(['u'=>$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ILM Path</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom style for a subtle background pattern or gradient */
        .login-bg {
            background-color: #f7f7f7;
            background-image: radial-gradient(#d1d5db 1px, transparent 0);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl transform hover:shadow-3xl transition duration-300">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">
                <span class="text-indigo-600">At-</span> Tatweer
            </h1>
            <p class="mt-2 text-gray-500">Admin Panel Access</p>
        </div>

        <h2 class="text-xl font-semibold text-gray-700 mb-6">Secure Sign In</h2>
        
        <?php if(!empty($error)): ?>
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 p-3 mb-4 rounded-lg">
                <p class="font-medium">Authentication Failed</p>
                <p class="text-sm mt-1"><?= e($error) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" novalidate>
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input 
                    name="username" 
                    id="username"
                    type="text"
                    placeholder="Enter your username"
                    class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-3 rounded-lg shadow-sm transition duration-150 ease-in-out" 
                    required 
                    autocomplete="username"
                />
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input 
                    name="password" 
                    id="password"
                    type="password" 
                    placeholder="••••••••"
                    class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-3 rounded-lg shadow-sm transition duration-150 ease-in-out" 
                    required 
                    autocomplete="current-password"
                />
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
            >
                Log In to Dashboard
            </button>
        </form>
        
        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400 mt-2">
                © <?= date('Y') ?> ILM Path. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>