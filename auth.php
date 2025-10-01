<?php
require_once 'config.php';

// Return JSON response helper
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    // Login process
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        jsonResponse(false, 'Missing email or password.');
    }

    $stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        jsonResponse(true, 'Login successful.', ['user_name' => $user['name']]);
    } else {
        jsonResponse(false, 'Invalid email or password.');
    }

} elseif ($action === 'signup') {
    // Signup process
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$name || !$email || !$phone || !$password) {
        jsonResponse(false, 'All fields are required.');
    }

    // Check if email or phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1");
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Email or phone already registered.');
    }

    // Create new user
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $passwordHash]);

    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;

    jsonResponse(true, 'Signup successful.', ['user_name' => $name]);

} else {
    jsonResponse(false, 'Invalid action.');
}
