<?php
session_start();
require_once 'config.php';

// Fetch and sanitize POST data
$course_id      = isset($_POST['course_id']) ? intval($_POST['course_id']) : null;
$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$password       = trim($_POST['password'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$location       = trim($_POST['location'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'Cash';
$bkash_txn_id   = trim($_POST['transaction_id'] ?? '');

// Validate required fields
if (!$course_id || !$name || !$phone) {
    die('Error: Missing required fields.');
}

// bKash transaction ID is required if payment method is bKash
if ($payment_method === 'bKash' && empty($bkash_txn_id)) {
    die('Error: Please provide your bKash transaction ID.');
}

try {
    // Start transaction
    $pdo->beginTransaction();

    $userId = null;

    if (!empty($email)) {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $userId = $user['id'];

            // Auto-login if password matches
            if (!empty($password) && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $name;
            }

        } else {
            // Create new user
            $userPassword = !empty($password) ? $password : bin2hex(random_bytes(4));
            $passHash = password_hash($userPassword, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $passHash]);
            $userId = $pdo->lastInsertId();

            // Auto-login new user
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;

            // TODO: Optionally send credentials via email/SMS
            // sendEmail($email, $userPassword);
            // sendSMS($phone, $userPassword);
        }
    }

    // Insert enrollment record
    $stmt = $pdo->prepare("
        INSERT INTO enrollments 
        (course_id, name, email, location, phone, payment_method, bkash_txn_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$course_id, $name, $email, $location, $phone, $payment_method, $bkash_txn_id]);

    $pdo->commit();

    // Redirect with success
    header('Location: index.php?enrolled=1');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die('Error: ' . $e->getMessage());
}
