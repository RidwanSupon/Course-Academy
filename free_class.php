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

// --- Collect and sanitize form data ---
$course_id       = intval($_POST['course_id'] ?? 0);
$user_id         = intval($_POST['user_id'] ?? 0) ?: null; // Use null if 0 or not set
$name            = trim($_POST['name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$preferred_date  = $_POST['preferred_date'] ?? null;
$preferred_time  = $_POST['preferred_time'] ?? null;
$message         = trim($_POST['message'] ?? null);

// Basic validation
if ($course_id === 0 || empty($name) || empty($email) || empty($phone)) {
    // Redirect back to the course page if validation fails
    header("Location: course.php?id={$course_id}&error=missing_fields");
    exit;
}

try {
    $pdo->beginTransaction();

    // Check for existing request from the same user for the same course
    // Only check if user is logged in
    if ($user_id) {
        $stmt = $pdo->prepare("
            SELECT id FROM free_class_requests
            WHERE course_id = ? AND user_id = ? AND status IN ('New', 'Contacted', 'Scheduled')
            LIMIT 1
        ");
        $stmt->execute([$course_id, $user_id]);
        
        if ($stmt->fetch()) {
            $pdo->rollBack();
            // User already has an active request, redirect
            header("Location: course.php?id={$course_id}&error=already_requested");
            exit;
        }
    }
    
    // Insert new free class request
    $stmt = $pdo->prepare("
        INSERT INTO free_class_requests 
        (course_id, user_id, name, email, phone, preferred_date, preferred_time, message)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $course_id,
        $user_id, // Null if guest, otherwise the logged-in user_id
        $name,
        $email,
        $phone,
        $preferred_date,
        $preferred_time,
        $message
    ]);

    $pdo->commit();

    // Redirect back to course page with success flag
    header("Location: course.php?id={$course_id}&free_requested=true");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Free Class Request Error: " . $e->getMessage());
    // Redirect with a generic error
    header("Location: course.php?id={$course_id}&error=request_failed");
    exit;
}