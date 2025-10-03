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
// We collect these variables once, at the top, from the POST request.
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
        $stmt = $pdo->prepare("SELECT id, name, email, phone FROM users WHERE email = ? OR phone = ? LIMIT 1");
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

            // Log the new user in and save their details
            $_SESSION['user_id']       = $user_id;
            $_SESSION['user_name']     = $name;
            $_SESSION['user_email']    = $email;
            $_SESSION['user_phone']    = $phone;
            $_SESSION['user_location'] = $location; // Note: location isn't stored in users table, only in session/enrollment

        } elseif ($existing) {
            // Existing user found → use their record for enrollment, but DON'T log them in fully here (no password check)
            $user_id = $existing['id'];
            
            // OPTIONAL: Update session with data from users table if it was an *existing* user who wasn't logged in.
            // This is non-critical for enrollment insertion but helpful for session consistency.
            $_SESSION['user_id']       = $user_id;
            $_SESSION['user_name']     = $existing['name'];
            $_SESSION['user_email']    = $existing['email'];
            $_SESSION['user_phone']    = $existing['phone'];
            $_SESSION['user_location'] = $location; // Use submitted location for session/enrollment
        } else {
            // This case handles a guest attempting to enroll without a password, but also not having an existing user record.
            // You might want a stronger validation/redirect here. For now, we continue and let enrollment fail if $user_id is null.
        }
    }

    // CRITICAL VALIDATION: Ensure we have a user ID before proceeding
    if (!$user_id) {
         $pdo->rollBack();
         header("Location: course.php?id={$course_id}&error=user_required"); // Redirect with error
         exit;
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
     * 3. Insert new enrollment (FIXED)
     * ------------------------------------------------- */
    // IMPORTANT: Use the variables collected from the POST data at the start.
    // We are NOT using $_SESSION here for name/email/phone/location because they 
    // might not be updated correctly for a logged-in user in this script flow.
    
    $stmt = $pdo->prepare("
        INSERT INTO enrollments
        (course_id, user_id, name, email, phone, location, payment_method, bkash_txn_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $course_id,
        $user_id, // This is the existing user_id from session or the new one from step 1
        $name,    // Use submitted name
        $email,   // Use submitted email
        $phone,   // Use submitted phone
        $location, // Use submitted location
        $payment_method,
        $bkash_txn_id
    ]);

    $pdo->commit();

    // Redirect to course page with success flag
    header("Location: course.php?id={$course_id}&enrolled=true");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // Log error for debugging purposes
    error_log("Enrollment Error: " . $e->getMessage());
    // Redirect with a generic error (optional)
    header("Location: course.php?id={$course_id}&error=failed");
    exit;
}