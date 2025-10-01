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
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login - ILM Path</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="w-full max-w-md bg-white p-8 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Admin Login</h2>
    <?php if(!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <label class="block mb-2">Username</label>
      <input name="username" class="w-full border p-2 mb-3 rounded" required />
      <label class="block mb-2">Password</label>
      <input name="password" type="password" class="w-full border p-2 mb-4 rounded" required />
      <button class="w-full bg-indigo-600 text-white py-2 rounded">Login</button>
    </form>
    <p class="text-xs text-gray-500 mt-3">Default seed: user=admin pass=admin123 (change it)</p>
  </div>
</body>
</html>
