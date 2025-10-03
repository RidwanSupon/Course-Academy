<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// --- Collect form data ---
$course_id      = intval($_POST['course_id'] ?? 0);
$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$location       = trim($_POST['location'] ?? '');
$password       = $_POST['password'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$bkash_txn_id   = $_POST['transaction_id'] ?? null;

// Get user_id from session if already logged in
$user_id = $_SESSION['user_id'] ?? null;

try {
    $pdo->beginTransaction();

    /* -------------------------------------------------
     * 1. Handle Guest User Registration (if not logged in)
     * ------------------------------------------------- */
    if (!$user_id) {
        // Check if email or phone already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->execute([$email, $phone]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing && $password) {
            // Register new user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, phone, password_hash)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $phone, $password_hash]);

            $user_id = $pdo->lastInsertId();

            // Store session data
            $_SESSION['user_id']       = $user_id;
            $_SESSION['user_name']     = $name;
            $_SESSION['user_email']    = $email;
            $_SESSION['user_phone']    = $phone;
            $_SESSION['user_location'] = $location;

        } else {
            // Existing user found → use their record
            $user = $existing;
            if (!$user) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
                $stmt->execute([$email, $phone]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            $user_id = $user['id'] ?? null;

            // Store session data
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['user_name']     = $user['name'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['user_phone']    = $user['phone'];
            $_SESSION['user_location'] = $location;
        }
    }

    /* -------------------------------------------------
     * 2. Prevent duplicate enrollments
     * ------------------------------------------------- */
    $stmt = $pdo->prepare("
        SELECT id FROM enrollments
        WHERE course_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$course_id, $user_id]);

    if ($stmt->fetch()) {
        $pdo->rollBack();
        header("Location: course.php?id={$course_id}&enrolled=already");
        exit;
    }

    /* -------------------------------------------------
     * 3. Insert new enrollment
     * ------------------------------------------------- */
    $name     = $_SESSION['user_name'];
    $email    = $_SESSION['user_email'];
    $phone    = $_SESSION['user_phone'];
    $location = $_SESSION['user_location'];

    $stmt = $pdo->prepare("
        INSERT INTO enrollments
        (course_id, user_id, name, email, phone, location, payment_method, bkash_txn_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $course_id,
        $user_id,
        $name,
        $email,
        $phone,
        $location,
        $payment_method,
        $bkash_txn_id
    ]);

    $pdo->commit();

    // Redirect to course page with success flag
    header("Location: course.php?id={$course_id}&enrolled=true");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: " . $e->getMessage());
}
