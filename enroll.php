<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Collect form data
$course_id      = intval($_POST['course_id'] ?? 0);
$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$location       = trim($_POST['location'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$txn_id         = $_POST['transaction_id'] ?? null;

// Validate required fields
$errors = [];
if (!$course_id) $errors[] = "Invalid course.";
if (!$name) $errors[] = "Name is required.";
if (!$email) $errors[] = "Email is required.";
if (!$payment_method) $errors[] = "Payment method is required.";

if ($errors) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => implode('<br>', $errors)];
    header("Location: course.php?id=$course_id");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $user_id = $user['id'];
} else {
    // New user, insert into users table
    $password = $_POST['password'] ?? '';
    if (!$password) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Password is required for new users.'];
        header("Location: course.php?id=$course_id");
        exit;
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name,email,phone,password_hash) VALUES (?,?,?,?)");
    $stmt->execute([$name,$email,$phone,$password_hash]);
    $user_id = $pdo->lastInsertId();
    // Save user info in session
    $_SESSION['user_id']       = $user_id;
    $_SESSION['user_name']     = $name;
    $_SESSION['user_email']    = $email;
    $_SESSION['user_phone']    = $phone;
    $_SESSION['user_location'] = $location;
}

// Insert into enrollments
$stmt = $pdo->prepare("
    INSERT INTO enrollments (course_id, name, email, location, phone, payment_method, bkash_txn_id) 
    VALUES (?,?,?,?,?,?,?)
");
$stmt->execute([
    $course_id,
    $name,
    $email,
    $location,
    $phone,
    $payment_method,
    $txn_id
]);

// Redirect with success
header("Location: course.php?id=$course_id&enrolled=true");
exit;
